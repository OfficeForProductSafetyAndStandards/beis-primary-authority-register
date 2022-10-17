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
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsLegalEntityForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['registered_name', 'par_data_legal_entity', 'registered_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the name of this legal entity.'
    ]],
    ['legal_entity_type', 'par_data_legal_entity', 'legal_entity_type', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must choose which type of legal entity this is.'
    ]],
    ['registered_number', 'par_data_legal_entity', 'registered_number', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the registered number for this legal entity.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Add a legal entity for your organisation';

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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Restrict access when partnership is active to users with administrator role.
    // @TODO Add back the permission to add/update when PAR-1915 is complete:
    // && !$user->hasPermission('amend active partnerships')
    if ($par_data_partnership->isActive()) {
      $this->accessResult = AccessResult::forbidden('This partnership is active therefore the legal entities cannot be changed.');
    }

    // Restrict business users who have already confirmed their business details.
    if ($par_data_partnership->getRawStatus() === 'confirmed_business' && !$account->hasPermission('approve partnerships')) {
      $this->accessResult = AccessResult::forbidden('This partnership has been confirmed by the business therefore the legal entities cannot be changed.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataLegalEntity $par_data_legal_entity
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {
    if ($par_data_legal_entity = $par_data_partnership_le->getLegalEntity()) {
      $this->getFlowDataHandler()->setParameter('par_data_legal_entity', $par_data_legal_entity);
    }
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Get the partnership.
    /* @var ParDataPartnership $partnership */
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Set the data for the legal entities.
    $legal_entity_name = $this->getFlowDataHandler()->getTempDataValue('registered_name');
    $legal_entity_number = $this->getFlowDataHandler()->getTempDataValue('registered_number');
    $legal_entity_type = $this->getFlowDataHandler()->getTempDataValue('legal_entity_type');

    // If a legal entity exists with the same registered_number
    // the existing entity will be returned.
    $legal_entity = ParDataLegalEntity::create([
      'registered_name' => $legal_entity_name,
      'registered_number' => $legal_entity_number,
      'legal_entity_type' => $legal_entity_type,
    ]);

    // If this is an existing legal entity check that it is not already active on the partnership.
    $partnership_legal_entities = $partnership->getPartnershipLegalEntities(TRUE);
    if (!$legal_entity->isNew() && !empty($partnership_legal_entities)) {
      // Set start and end dates for the period of the new PLE. If the partnership is
      // not yet active the from_date is NULL, once it is active the from_date is today's date.
      $start_date = $partnership->isActive() ? new DrupalDateTime('now') : NULL;
      $end_date = NULL;

      foreach ($partnership_legal_entities as $partnership_legal_entity) {
        if ($partnership_legal_entity->getLegalEntity()->id() === $legal_entity->id()
            && $partnership_legal_entity->isActiveDuringPeriod($start_date, $end_date)) {
          $id = $this->getElementId(['registered_number'], $form);
          $form_state->setErrorByName($this->getElementName('registered_number'), $this->wrapErrorMessage('This legal entity is already an active participant in the partnership.', $id));
          break;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    // Legal entities that accept registered numbers.
    $registered_number_types = [
      'limited_company',
      'public_limited_company',
      'limited_liability_partnership',
      'registered_charity',
      'partnership',
      'limited_partnership',
      'other',
    ];

    // Nullify registered number if not one of the types specified.
    if ($legal_entity && !in_array($this->getFlowDataHandler()->getTempDataValue('legal_entity_type'), $registered_number_types)) {
      $this->getFlowDataHandler()->setTempDataValue('registered_number', NULL);
    }

    // Edit existing legal entity / add new legal entity.
    if ($legal_entity) {
      $legal_entity->set('registered_name', $this->getFlowDataHandler()->getTempDataValue('registered_name'));
      $legal_entity->set('legal_entity_type', $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'));
      $legal_entity->set('registered_number', $this->getFlowDataHandler()->getTempDataValue('registered_number'));

      if ($legal_entity->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    else {
      // Create a new legal entity.
      $legal_entity = ParDataLegalEntity::create([
        'type' => 'legal_entity',
        'name' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        'registered_name' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        'registered_number' => $this->getFlowDataHandler()->getTempDataValue('registered_number'),
        'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'),
      ]);
      $legal_entity->save();

      // Now add the legal entity to the partnership.
      /* @var ParDataPartnership $par_data_partnership */
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_partnership->addLegalEntity($legal_entity);

      // Add the new legal entity to the organisation.
      /* @var \Drupal\par_data\Entity\ParDataOrganisation $par_data_organisation */
      $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
      $par_data_organisation->addLegalEntity($legal_entity);

      // Commit partnership/organisation changes.
      if ($legal_entity->id() && $par_data_partnership->save() && $par_data_organisation->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

  }
}
