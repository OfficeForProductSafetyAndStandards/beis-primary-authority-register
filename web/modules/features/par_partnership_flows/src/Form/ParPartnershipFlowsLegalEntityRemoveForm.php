<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipLegalEntity;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Form to delete a partnership_legal_entity.
 */
class ParPartnershipFlowsLegalEntityRemoveForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = "Update Partnership Information | Remove a legal entity from your organisation";

    return parent::titleCallback();
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {

    // Get the route parameters.
    $partnership = $route_match->getParameter('par_data_partnership');
    $partnership_legal_entity = $route_match->getParameter('par_data_partnership_le');

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Restrict access when partnership is active to users with administrator role.
    if ($partnership->isActive() && !$user->hasRole('senior_administration_officer')) {
      $this->accessResult = AccessResult::forbidden('This partnership is active therefore the legal entities cannot be changed.');
    }

    // Restrict business users who have already confirmed their business details.
    if ($partnership->getRawStatus() === 'confirmed_business' && !$account->hasPermission('approve partnerships')) {
      $this->accessResult = AccessResult::forbidden('This partnership has been confirmed by the business therefore the legal entities cannot be changed.');
    }

    // Prohibit deletion if partnership is active.
    if ($partnership->isActive()) {
      $this->accessResult = AccessResult::forbidden('Legal entities can not be removed from active partnerships.');
    }

    // Prohibit removing of the last legal entity.
    if (count($partnership->getPartnershipLegalEntity()) < 2) {
      $this->accessResult = AccessResult::forbidden('The last legal entity can\'t be removed.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {

    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    // Get the legal_entity referenced by the partnership_legal_entity.
    $legal_entity = $par_data_partnership_le?->getLegalEntity();

    // Legal entity details just displayed for reference on other operations.
    $form['registered_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Name of the legal entity'),
      '#markup' => $legal_entity->getName(),
    ];

    $values = $legal_entity_bundle->getAllowedValues('legal_entity_type');
    $form['legal_entity_type'] = [
      '#type' => 'item',
      '#title' => $this->t('Type of the legal entity'),
      '#markup' => $legal_entity->getType(),
    ];

    if (in_array($legal_entity->getType(),
                 ['limited_company', 'public_limited_company', 'limited_liability_partnership',
                  'registered_charity', 'partnership', 'limited_partnership', 'other'])) {
      $form['registered_number'] = [
        '#type' => 'item',
        '#title' => $this->t('Registration number of the legal entity'),
        '#markup' => $legal_entity->getRegisteredNumber(),
      ];
    }

    // Start date shown only for active partnerships.
    if ($par_data_partnership->isActive()) {
      $form['start_date'] = [
        '#type' => 'item',
        '#title' => $this->t('Start date'),
        '#markup' => $par_data_partnership_le->getStartDate(),
      ];
    }

    // End date shown only for active partnerships.
    if ($par_data_partnership->isActive()) {
      $form['end_date'] = [
        '#type' => 'item',
        '#title' => $this->t('End date'),
        '#markup' => $par_data_partnership_le->getEndDate(),
      ];
    }

    // Change label of the primary action button.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Remove');

    // Make sure to add the cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $partnership = $this->getFlowDataHandler()->getParameter('par_data_parnership');
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_parnership_le');

    if (!$partnership_legal_entity->delete()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The %ple could not be removed from %p.');
      $replacements = [
        '%ple' => $partnership_legal_entity->label(),
        '%p' => $partnership->label(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }
}
