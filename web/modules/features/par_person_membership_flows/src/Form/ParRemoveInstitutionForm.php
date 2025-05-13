<?php

namespace Drupal\par_person_membership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_roles\ParRoleManagerInterface;

/**
 * The form for the partnership details.
 */
class ParRemoveInstitutionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Confirm removal';

  /**
   * Get the role manager.
   */
  protected function getRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    $account = $this->getFlowDataHandler()->getParameter('user');
    $institution_type = $this->getFlowDataHandler()->getParameter('institution_type');
    $institution_id = $this->getFlowDataHandler()->getParameter('institution_id');
    $institution = \Drupal::entityTypeManager()->getStorage($institution_type)->load($institution_id);

    $this->getFlowDataHandler()->setParameter('institution', $institution);
    $this->getFlowDataHandler()->setFormPermValue('institution_name', $institution->label());
    $this->getFlowDataHandler()->setFormPermValue('user_mail', $account->getEmail());

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['institution'] = [
      '#type' => 'container',
      'name' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => 'govuk-form-group'],
        '#value' => $this->t("The user %user will be removed from the institution %institution", [
          '%user' => $this->getFlowDataHandler()->getFormPermValue('user_mail') ?? '',
          '%institution' => $this->getFlowDataHandler()->getFormPermValue('institution_name') ?? '',
        ]),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $account = $this->getFlowDataHandler()->getParameter('user');
    $institution = $this->getFlowDataHandler()->getParameter('institution');

    if ($institution instanceof ParDataMembershipInterface) {
      // Remove the memberships.
      $institution = $this->getRoleManager()->removeUserMembership($institution, $account);

      // Save the institution.
      $institution->save();
      // Save the user account.
      $account->save();

      // We also need to clear the relationships caches once
      // any new relationships have been saved.
      foreach ($this->getRoleManager()->getPeople($account) as $person) {
        $person->getRelationships(NULL, NULL, TRUE);
      }

      // Also invalidate the user account cache if there is one.
      \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The user %account could not be removed from the institution %institution');
      $replacements = [
        '%account' => $account->id(),
        '%institution' => $institution->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
