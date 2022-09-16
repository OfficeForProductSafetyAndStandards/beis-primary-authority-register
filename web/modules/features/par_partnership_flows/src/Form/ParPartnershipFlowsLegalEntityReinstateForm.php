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
 * Reinstate a partnership legal entity that has been revoked.
 */
class ParPartnershipFlowsLegalEntityReInstateForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Reinstate a legal entity for your organisation';

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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Partnership must be active.
    if (!$par_data_partnership->isActive()) {
      $this->accessResult = AccessResult::forbidden('This partnership is not active so legal entities cannot be reinstated.');
    }

    // Restrict access when partnership is active to users with administrator role.
    if ($par_data_partnership->isActive() && !$user->hasRole('senior_administration_officer')) {
      $this->accessResult = AccessResult::forbidden('This partnership is active and user\'s role does not allow changes to be made.');
    }

    // Only revoked PLEs may be reinstated.
    if (!$par_data_partnership_le->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('This legal entity is already active.');
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
      '#markup' => $par_data_legal_entity->getName(),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'item',
      '#title' => $this->t('Type of the legal entity'),
      '#markup' => $par_data_legal_entity->getType(),
    ];

    // Only show registered number for types that allow it.
    if (in_array($par_data_legal_entity->getTypeRaw(),
      ['limited_company', 'public_limited_company', 'limited_liability_partnership',
        'registered_charity', 'partnership', 'limited_partnership', 'other'])) {
      $form['registered_number'] = [
        '#type' => 'item',
        '#title' => $this->t('Registration number of the legal entity'),
        '#markup' => $par_data_legal_entity->getRegisteredNumber(),
      ];
    }

    // Only show start date if there is one.
    if ($start_date = $par_data_partnership_le->getStartDate()) {
      $form['start_date'] = [
        '#type' => 'item',
        '#title' => $this->t('Start date'),
        '#markup' => $this->getDateFormatter()->format($start_date->getTimestamp(), 'gds_date_format'),
      ];
    }

    $form['end_date'] = [
      '#type' => 'item',
      '#title' => $this->t('End date'),
      '#markup' => $this->getDateFormatter()->format($par_data_partnership_le->getEndDate()->getTimestamp(), 'gds_date_format'),
    ];

    // Change label of the primary action button.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Reinstate');

    // Make sure to add the cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($par_data_partnership_le);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    $partnership_legal_entity->unrevoke(TRUE);
  }
}
