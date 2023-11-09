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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL): AccessResult {

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Restrict access when partnership is active to users with administrator role.
    if ($par_data_partnership?->isActive() && !$account->hasPermission('amend active partnerships')) {
      $this->accessResult = AccessResult::forbidden('This partnership is active therefore the legal entities cannot be changed.');
    }

    // Restrict business users who have already confirmed their business details.
    if ($par_data_partnership->getRawStatus() === 'confirmed_business' && !$account->hasPermission('approve partnerships')) {
      $this->accessResult = AccessResult::forbidden('This partnership has been confirmed by the business therefore the legal entities cannot be changed.');
    }

    // If the partnership legal entity cannot be removed.
    if (!$par_data_partnership_le->isDeletable()) {
      $this->accessResult = AccessResult::forbidden('The Legal entity can not be removed from the partnership.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {

    $par_data_legal_entity = $par_data_partnership_le->getLegalEntity();

    // Legal entity details just displayed for reference.
    $form['registered_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Name of the legal entity'),
      '#title_tag' => 'h2',
      '#markup' => $par_data_legal_entity->getName(),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'item',
      '#title' => $this->t('Type of the legal entity'),
      '#title_tag' => 'h2',
      '#markup' => $par_data_legal_entity->getType(),
    ];

    // Only show registered number for types that allow it.
    if (in_array($par_data_legal_entity->getType(FALSE),
      ['limited_company', 'public_limited_company', 'limited_liability_partnership',
        'registered_charity', 'partnership', 'limited_partnership', 'other'])) {
      $form['registered_number'] = [
        '#type' => 'item',
        '#title' => $this->t('Registration number of the legal entity'),
        '#title_tag' => 'h2',
        '#markup' => $par_data_legal_entity->getRegisteredNumber(),
      ];
    }

    // Only show start date if there is one.
    if ($start_date = $par_data_partnership_le->getStartDate()) {
      $form['start_date'] = [
        '#type' => 'item',
        '#title' => $this->t('Start date'),
        '#title_tag' => 'h2',
        '#markup' => $this->getDateFormatter()->format($start_date->getTimestamp(), 'gds_date_format'),
      ];
    }

    if ($par_data_partnership_le->isRevoked()) {
      $form['end_date'] = [
        '#type' => 'item',
        '#title' => $this->t('End date'),
        '#title_tag' => 'h2',
        '#markup' => $this->getDateFormatter()->format($par_data_partnership_le->getEndDate()->getTimestamp(), 'gds_date_format'),
      ];
    }

    // Change label of the primary action button.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Remove');

    // Make sure to add the cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($par_data_partnership_le);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    // We can't reinstate a PLE if there is already an active PLE for the same LE.
    if (!$partnership_legal_entity->isDeletable()) {
      $id = $this->getElementId(['registered_name'], $form);
      $form_state->setErrorByName($this->getElementName('registered_number'), $this->wrapErrorMessage('This legal entity is not deletable.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /* @var ParDataPartnership $partnership */
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    if ($partnership_legal_entity->isDeletable()) {
      $partnership->removeLegalEntity($partnership_legal_entity);
    }
  }
}
