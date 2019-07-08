<?php

namespace Drupal\par_partnership_contact_remove_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_partnership_contact_remove_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_remove_flows\ParFormCancelTrait;
use Drupal\user\Entity\User;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Confirm removal of contact';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    if ($par_data_person) {
      $contact = "{$par_data_person->label()} ({$par_data_person->getEmail()})";
      $this->getFlowDataHandler()->setFormPermValue("contact_name", $contact);
    }

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($par_data_partnership) {
      $this->getFlowDataHandler()->setFormPermValue("partnership_label", lcfirst($par_data_partnership->label()));
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Contact to be removed'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['partnership_info']['partnership_between'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Please confirm you wish to remove @person from the @partnership.', [
        '@person' => $this->getFlowDataHandler()->getDefaultValues('contact_name', ''),
        '@partnership' => $this->getFlowDataHandler()->getDefaultValues('partnership_label', '')
      ]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Remove the contact from this partnership.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $type = $this->getFlowDataHandler()->getParameter('type');
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    switch ($type) {
      case 'authority':
        $field = 'field_authority_person';

        break;

      case 'organisation':
        $field = 'field_organisation_person';

        break;

    }

    if (isset($field)) {
      $authority_people = $par_data_partnership->get($field)->getValue();
      $key = array_search($par_data_person->id(), array_column($authority_people, 'target_id'));
      if ($key !== FALSE) {
        $par_data_partnership->get($field)->removeItem($key);
        $save = TRUE;
      }
    }

    if (isset($save) && $par_data_partnership->save()) {
      $this->getFlowDataHandler()->deleteStore();

      // Go to cancel route.
      switch ($this->getFlowDataHandler()->getParameter('type')) {
        case 'organisation':
          $completion_route = 'par_partnership_flows.organisation_details';

          break;

        case 'authority':
          $completion_route = 'par_partnership_flows.authority_details';

          break;
      }

      if ($completion_route) {
        $params = $this->getRouteParams();
        $form_state->setRedirect($completion_route, ['par_data_partnership' => $params['par_data_partnership']]);
      }
    }
    else {
      $message = $this->t('Person %person could not be removed as %type contact from %partnership');
      $replacements = [
        '%person' => $par_data_person->id(),
        '%type' => $type,
        '%partnership' => $par_data_partnership->label(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
