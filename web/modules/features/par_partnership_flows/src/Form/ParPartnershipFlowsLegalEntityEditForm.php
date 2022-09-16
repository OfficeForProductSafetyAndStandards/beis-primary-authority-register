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
class ParPartnershipFlowsLegalEntityEditForm extends ParBaseForm {

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

    $this->pageTitle = 'Update Partnership Information | Edit a legal entity for your organisation';

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

    // Restrict access when partnership is active to users with administrator role.
    if ($par_data_partnership->isActive() && !$user->hasRole('senior_administration_officer')) {
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
      $this->getFlowDataHandler()->setFormPermValue('partnership_legal_entity_start_date', $this->dateToArray($par_data_partnership_le->getStartDate()));
      $this->getFlowDataHandler()->setFormPermValue('partnership_legal_entity_end_date', $this->dateToArray($par_data_partnership_le->getEndDate()));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {

    $this->retrieveEditableValues($par_data_partnership_le);

    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    // Get the legal_entity referenced by the partnership_legal_entity.
    $par_data_legal_entity = $par_data_partnership_le?->getLegalEntity();
    $le_used_by_multiple_partnerships = $par_data_legal_entity?->hasMultiplePartnershipReferences();

    // Only show multiple partnerships message for add and edit operations.
    if ($le_used_by_multiple_partnerships) {
      $form['legal_entity_disabled']['intro'] = [
        '#type' => 'markup',
        '#markup' => "<p><b>" . $this->t("This legal entity cannot be updated as it is being used in another partnership.") . "</b></p>",
      ];
    }

    $form['registered_name'] = [
      '#disabled' => $le_used_by_multiple_partnerships,
      '#type' => 'textfield',
      '#title' => $this->t('Name of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_name"),
    ];

    $form['legal_entity_type'] = [
      '#disabled' => $le_used_by_multiple_partnerships,
      '#type' => 'select',
      '#title' => $this->t('Type of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_legal_entity_type"),
      '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
    ];

    $form['registered_number'] = [
      '#disabled' => $le_used_by_multiple_partnerships,
      '#type' => 'textfield',
      '#title' => $this->t('Registration number of the legal entity'),
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

    // Start date shown only for active partnerships.
    if ($par_data_partnership->isActive()) {
      $form['start_date'] = [
        '#disabled' => FALSE,
        '#type' => 'gds_date',
        '#title' => $this->t('Start date'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('partnership_legal_entity_start_date'),
        '#description' => $this->t('The date at which the participation of this legal entity in the partnership begins. ' .
                          'Leave blank if the association begins at start of the partnership.'),
      ];
    }

    // End date shown only for active partnerships.
    if ($par_data_partnership->isActive()) {
      $form['end_date'] = [
        '#disabled' => FALSE,
        '#type' => 'gds_date',
        '#title' => $this->t('End date'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('partnership_legal_entity_end_date'),
        '#description' => $this->t('The date at which the participation of this legal entity in the partnership ends.' .
                                   ' Leave blank if the association is to continue indefinitely.'),
      ];
    }

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

    // Get the partnership.
    /* @var ParDataPartnership $par_data_partnership */
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Get the partnership legal entity.
    /* @var ParDataPartnershipLegalEntity $par_data_partnership_le */
    $par_data_partnership_le = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    // Get the LE.
    $par_data_legal_entity = $par_data_partnership_le->getLegalEntity();

    // Get form values.
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');

    // Check that the period of this PLE does not overlap with any other PLE for the same partnership/LE combination.
    foreach ($par_data_partnership->getPartnershipLegalEntities() as $existing_partnership_le) {
      if ($existing_partnership_le->id() == $par_data_partnership_le->id()) { // Don't check against self.
        continue;
      }
      if ($existing_partnership_le->getLegalEntity()->id() == $par_data_legal_entity->id()) {
        if ($existing_partnership_le->isActiveDuringPeriod($start_date, $end_date)) {
          $id = $this->getElementId(['registered_name'], $form);
          $form_state->setErrorByName($this->getElementName('registered_name'), $this->wrapErrorMessage('This legal entity is already an active participant in the partnership.', $id));
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

    /* @var ParDataLegalEntity $legal_entity */
    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

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
    if (!in_array($this->getFlowDataHandler()->getTempDataValue('legal_entity_type'), $registered_number_types)) {
      $this->getFlowDataHandler()->setTempDataValue('registered_number', NULL);
    }

    // Update legal entity.
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

    // Update partnership legal entity.
    $partnership_legal_entity->setStartDate($this->getFlowDataHandler()->getTempDataValue('start_date'));
    $partnership_legal_entity->setEndDate($this->getFlowDataHandler()->getTempDataValue('end_date'));

    if ($partnership_legal_entity->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('When submitting form %form_id partnership legal entity %id could not be saved with start date %sd and end date %ed.');
      $replacements = [
        '%form_id' => $this->getFormId(),
        '%id' => $partnership_legal_entity->id(),
        '%sd' => $this->getFlowDataHandler()->getTempDataValue('start_date'),
        '%ed' => $this->getFlowDataHandler()->getTempDataValue('end_date'),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }
}
