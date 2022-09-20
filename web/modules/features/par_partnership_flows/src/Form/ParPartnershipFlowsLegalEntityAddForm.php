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
class ParPartnershipFlowsLegalEntityAddForm extends ParBaseForm {

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
    if ($par_data_partnership->isActive() && !$user->hasPermission('amend active partnerships')) {
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

    if ($par_data_partnership_le) {
      $par_data_legal_entity = $par_data_partnership_le->getLegalEntity();
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_registered_number", $par_data_legal_entity->get('registered_number')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->getFlowDataHandler()->setFormPermValue('legal_entity_id', $par_data_legal_entity->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {

    $this->retrieveEditableValues($par_data_partnership_le);

    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    // Only show intro for add operations.
    $form['legal_entity_intro_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What is a legal entity?'),
    ];

    $form['legal_entity_intro_fieldset']['intro'] = [
      '#type' => 'markup',
      '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
    ];

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the name of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_name"),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the type of legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_legal_entity_type"),
      '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
    ];

    $form['registered_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_number"),
      '#states' => [
        'visible' => [
          'select[name="legal_entity_type"]' => [
            ['value' => 'limited_company'],
            ['value' => 'public_limited_company'],
            ['value' => 'limited_liability_partnership'],
            ['value' => 'registered_charity'],
            ['value' => 'partnership'],
            ['value' => 'limited_partnership'],
            ['value' => 'other'],
          ],
        ],
      ],
    ];

    // Make sure to add the cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    parent::validateForm($form, $form_state);

    // Get the partnership.
    /* @var ParDataPartnership $partnership */
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Get the legal entity.
    /* @var ParDataLegalEntity $legal_entity */
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    // Get form values.
    $registered_name = $form_state->getValue('registered_name');
    $legal_entity_type = $form_state->getValue('legal_entity_type');
    $registered_number = $form_state->getValue('registered_number');

    // Set start and end dates for the period of the new PLE. If the partnership is
    // not yet active the from_date is NULL, once it is active the from_date is today's date.
    if (!$partnership->isActive()) {
      $start_date = NULL;
    }
    else {
      $start_date = new DrupalDateTime('now');
      $start_date->setTime(12, 0);
    }
    $end_date = NULL;

    /* @var ParDataOrganisation $organisation */
    $organisation = $partnership->getOrganisation(TRUE);

    // We look for an existing LE on the organisation with the entered name or registration number.
    $organisation_legal_entities = $organisation->getLegalEntity();
    foreach ($organisation_legal_entities as $organisation_legal_entity) {
      if ($organisation_legal_entity->getRegisteredNumber() == $registered_number ||
        $organisation_legal_entity->getName() == $registered_name) {
        $legal_entity = $organisation_legal_entity;
        break;
      }
    }

    // If we have an existing LE we check that the period of this PLE instance will not overlap with any other PLE
    // for the same partnership/LE combination.
    if ($legal_entity) {
      $partnership_legal_entities = $partnership->getPartnershipLegalEntities();
      /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
      foreach ($partnership_legal_entities as $partnership_legal_entity) {
        if ($partnership_legal_entity->getLegalEntity() === $legal_entity) {
          if ($partnership_legal_entity->isActiveDuringPeriod($start_date, $end_date)) {
            $id = $this->getElementId(['registered_name'], $form);
            $form_state->setErrorByName($this->getElementName('registered_name'), $this->wrapErrorMessage('This legal entity is already an active participant in the partnership.', $id));
            break;
          }
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
      $period_from = NULL;
      if ($par_data_partnership->isActive()) {
        $period_from = new DrupalDateTime('now');
        $period_from->setTime(12, 0);
      }
      $period_to = NULL;
      $par_data_partnership->addLegalEntity($legal_entity, $period_from, $period_to);

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
