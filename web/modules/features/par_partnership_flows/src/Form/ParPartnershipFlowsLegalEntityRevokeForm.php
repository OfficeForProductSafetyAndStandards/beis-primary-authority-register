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
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsLegalEntityRevokeForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Revoke a legal entity for your organisation';

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

    // Partnership legal entities that are already revoked cannot be revoked again.
    if ($partnership_legal_entity->getEndDate()) {
      $this->accessResult = AccessResult::forbidden('This legal entity has already been revoked.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {

    $legal_entity = $par_data_partnership_le->getLegalEntity();
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    // Legal entity details just displayed for reference.
    $form['registered_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Name of the legal entity'),
      '#markup' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_name"),
    ];

    $values = $legal_entity_bundle->getAllowedValues('legal_entity_type');
    $form['legal_entity_type'] = [
      '#type' => 'item',
      '#title' => $this->t('Type of the legal entity'),
      '#markup' => $values[$legal_entity->getType()],
    ];

    // Only show registered number for types that allow it.
    if (in_array($legal_entity->getType(),
                 ['limited_company', 'public_limited_company', 'limited_liability_partnership',
                  'registered_charity', 'partnership', 'limited_partnership', 'other'])) {
      $form['registered_number'] = [
        '#type' => 'item',
        '#title' => $this->t('Registration number of the legal entity'),
        '#markup' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_number"),
      ];
    }

    $form['start_date'] = [
      '#type' => 'item',
      '#title' => $this->t('Start date'),
      '#markup' => $par_data_partnership_le->getStartDate(),
    ];

    // End date.
    $form['end_date'] = [
      '#type' => 'item',
      '#title' => $this->t('Start date'),
      '#markup' => $par_data_partnership_le->getEndDate(),
    ];

    // Change label of the primary action button.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Reinstate');

    // Make sure to add the cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    parent::validateForm($form, $form_state);

    // Get the partnership legal entity.
    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    // Get form values.
    $end_date = $form_state->getValue('end_date');

    // End date can not be set to date before start date of the PLE if it has one.
    if ($start_date = $partnership_legal_entity->getStartDate()) {
      if ($end_date < $start_date) {
        $id = $this->getElementId(['start_date'], $form);
        $form_state->setErrorByName($this->getElementName('start_date'), $this->wrapErrorMessage('End date can not precede start date.', $id));
      }
    }

    // End date can not be set to date before start of partnership.
    else {
      $partnership = $partnership_legal_entity->getPartnership();
      $partnership_approved_date = $partnership->getApprovedDate();
      if ($end_date < $partnership_approved_date) {
        $id = $this->getElementId(['start_date'], $form);
        $form_state->setErrorByName($this->getElementName('start_date'), $this->wrapErrorMessage('End date can not precede the partnership approval date.', $id));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    $partnership_legal_entity->set('end_date', $this->getFlowDataHandler()->getTempDataValue('end_date'));

    if ($partnership_legal_entity->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('When submitting form %form_id the partnership legal entity %id could not be saved with end date %ed.');
      $replacements = [
        '%form_id' => $this->getFormId(),
        '%id' => $partnership_legal_entity->id(),
        '%ed' => $this->getFlowDataHandler()->getTempDataValue('end_date'),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

  protected function dateToArray(DrupalDateTime $date = NULL) {
    if (!$date) {
      return NULL;
    }

    $formatted_date = $date->format('Ymd');
    return [
      'year' => (integer)substr($formatted_date, 0, 4),
      'month' => (integer)substr($formatted_date, 4, 2),
      'day' => (integer)substr($formatted_date, 6, 2),
    ];
  }
}
