<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Access\AccessResult;

/**
 * The confirming the user is authorised to approve partnerships.
 */
class ParRdHelpDeskApproveConfirmForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_partnership';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_rd_help_desk_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL) {
    // 403 if the partnership is active/approved by RD.
    if ($par_data_partnership->getRawStatus() === 'confirmed_rd') {
      $this->accessResult = AccessResult::forbidden('The partnership is active.');
    }

    return parent::accessCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_authority = current($par_data_partnership->getAuthority());

    $this->retrieveEditableValues($par_data_partnership);

    $form['partnership_title'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Partnership between'),
      '#prefix' => '<div><h2>',
      '#suffix' => '</h2></div>',
    ];

    $form['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_organisation->get('organisation_name')->getString() . ' and ' . $par_data_authority->get('authority_name')->getString(),
      '#default_value' => $this->getDefaultValues('partnership_text'),
      '#prefix' => '<div><p>',
      '#suffix' => '</p></div>',
    ];

    $confirm_text_options[] = $this->t('I am authorised to approve this partnership');

    $form['confirm_authorisation_select'] = [
      '#type' => 'radios',
      '#title' => $this->t('Check to confirm you are authorised to approve this partnership'),
      '#options' => $confirm_text_options,
      '#required' => TRUE,
    ];

    $regulatory_functions = $this->getParDataManager()->getEntitiesByType('par_data_regulatory_function');
    $regulatory_function_options = $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions);
    $form['partnership_regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Regulatory function to which this relates'),
      '#options' => $regulatory_function_options,
      '#default_value' => $this->getDefaultValues('partnership_regulatory_functions', []),
      '#required' => TRUE,
    ];

    // Defensive coding we are dealing with an approved partnership we are not changing the state of
    // the entity so avoid confusion by disabling the regulatory functions.
    if ($par_data_partnership->getRawStatus() == 'confirmed_rd') {
      $form['partnership_regulatory_functions']['#disabled'] = TRUE;
      $form['confirm_authorisation_select']['#disabled'] = TRUE;

      $form['approved_partnership'] = [
        '#type' => 'markup',
        '#markup' => $this->t('This partnership has already been approved.'),
        '#prefix' => '<div><strong>',
        '#suffix' => '</strong></div>',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $partnership = $this->getRouteParam('par_data_partnership');
    $selected_regulatory_functions = $this->getTempDataValue('partnership_regulatory_functions');

    // We only want to update the status of none active partnerships.
    if ($partnership->getRawStatus() !== 'confirmed_rd') {

      $partnership->setParStatus('confirmed_rd');
      $partnership->set('field_regulatory_function', $selected_regulatory_functions);

      if (!$partnership->save()) {

        $message = $this->t('This %partnership could not be approved for %form_id');
        $replacements = [
          '%partnership' => $partnership->id(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
