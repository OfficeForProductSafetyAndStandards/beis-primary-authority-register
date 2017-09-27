<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementReferredAuthorityForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_referred_authority';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    if ($par_data_enforcement_notice) {
      $this->setState("approve:{$par_data_enforcement_notice->id()}");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    $this->retrieveEditableValues($par_data_enforcement_notice);

    // Discover all authorities that are responsible for
    // partnerships with this organisation.
    // @TODO Implement a better method to do this.
    $par_data_partnership = current($par_data_enforcement_notice->get('field_partnership')->referencedEntities());
    $par_data_authority = current($par_data_partnership->get('field_authority')->referencedEntities());
    $par_data_organisation = current($par_data_partnership->get('field_organisation')->referencedEntities());

    // Get all partnerships with the same organisation,
    // that aren't deleted and were transitioned.
    $conditions = [
      'name' => [
        'AND' => [
          ['field_organisation', $par_data_organisation->id()],
        ]
      ],
    ];

    $organisation_partnerships = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_partnership', $conditions);

    // Load all the authorities belonging to these partnerships.
    $authorities = [];
    foreach ($organisation_partnerships as $partnership) {
      $authority = current($partnership->get('field_authority')->referencedEntities());
      if ($partnership->isLiving() && $authority->isLiving()) {
        $authorities[$authority->id()] = $authority->label();
      }
    }

    // It is only possibly to refer
    if (count($authorities) >= 0) {
      $form['referred_to'] = [
        '#type' => 'radios',
        '#title' => $this->t('Choose an authority to refer to'),
        '#options' => $authorities,
        '#default_value' => $this->getDefaultValues('referred_to', ParDataEnforcementAction::APPROVED),
        '#required' => TRUE,
      ];
    }
    else {
      $form['help_text'] = [
        '#type' => 'markup',
        '#attributes' => ['class' => 'form-group'],
        '#markup' => 'There are no authorities that this action can be referred to, please return to the previous step and change your review.',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    try {

    }
    catch(\Exception $e) {
      $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %form_id: @detail.', ['%form_id' => $this->getFormId(), '@details' => $e->getMessage()]);
      $form_state->setError($form, 'An error occurred while checking your submission, please contact the helpdesk if this problem persists.');
    }
  }


}
