<?php
namespace Drupal\par_partnership_flows\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
/**
 * The partnership form for the about partnership details.
 */
class ParPartnershipFlowsApplicationAuthorityChecklistForm extends ParBaseForm {
  use ParPartnershipFlowsTrait;
  /**
   * {@inheritdoc}
   */
  public function retrieveEditableValues() {
  }
  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    // Load application type from previous step.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
    $applicationType = ucfirst($this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid));
    // Set page title.
    $this->pageTitle = "{$applicationType} partnership application";
    return parent::titleCallback();
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->retrieveEditableValues();
    // Load application type from previous step.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
    $applicationType = $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid);
    // Get Primary Authority Terms and Conditions URL.
    $terms_page = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/49');
    if ($applicationType == 'direct') {
      $form['section_one']['header'] = [
        '#type' => 'markup',
        '#markup' => $this->t('Please confirm the following'),
        '#prefix' => '<h3 class="heading-medium">',
        '#suffix' => '</h3>',
      ];
      $form['section_one']['business_eligible_for_partnership'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('The organisation is eligible to enter into a partnership'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('business_eligible_for_partnership', FALSE),
        '#return_value' => 'on',
      ];
      $form['section_one']['local_authority_suitable_for_nomination'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('My local authority is suitable for nomination as primary authority for the organisation'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('local_authority_suitable_for_nomination', FALSE),
        '#return_value' => 'on',
      ];
      $form['section_one']['written_summary_agreed'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('A written summary of partnership arrangements has been agreed with the organisation'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('written_summary_agreed', FALSE),
        '#return_value' => 'on',
      ];
      $form['section_one']['terms_organisation_agreed'] = [
        '#type' => 'checkbox',
        '#title' => t("My local authority agrees to the Primary Authority <a href='{$terms_page}' target='_blank'>terms and conditions</a>."),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues("terms_organisation_agreed", FALSE),
        '#return_value' => 'on',
        ];
      $form['section_two']['business_regulated_by_one_authority'] = [
             '#type' => 'radios',
             '#title' => $this->t('Please confirm one of the following'),
             '#options' => [
               2 => 'The organisation has one local authority, which is your local authority',
               1 => 'The organisation has more than one local authority',
               0 => 'The organisation has a local authority that is not yours and the organisation has been informed that this local authority will still be regulating their organisation.',

               ],
                       '#default_value' => $this->getFlowDataHandler()->getDefaultValues('business_regulated_by_one_authority', FALSE),
      ];

    }
    elseif ($applicationType == 'coordinated') {
      $form['section_one']['header'] = [
        '#type' => 'markup',
        '#markup' => $this->t('Please confirm the following'),
        '#prefix' => '<h3 class="heading-medium">',
        '#suffix' => '</h3>',
      ];
      $form['section_one']['coordinator_local_authority_suitable'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('My local authority is suitable for nomination as primary authority partner for the co-ordinator'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('coordinator_local_authority_suitable', FALSE),
        '#return_value' => 'on',
      ];
      $form['section_one']['suitable_nomination'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('My prospective co-ordinator partner is suitable for nomination as a co-ordinator'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('suitable_nomination', FALSE),
        '#return_value' => 'on',
      ];
      $form['section_one']['written_summary_agreed'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('A written summary of partnership arrangements has been agreed with the co-ordinator'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('written_summary_agreed', FALSE),
        '#return_value' => 'on',
      ];
      $form['section_one']['terms_local_authority_agreed'] = [
        '#type' => 'checkbox',
        '#title' => t("My local authority agrees to the Primary Authority <a href='{$terms_page}' target='_blank'>Terms and Conditions</a>."),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues("terms_local_authority_agreed", FALSE),
        '#return_value' => 'on',
      ];
    }
    $this->addCacheableDependency($applicationType);
    return parent::buildForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // Load application type from previous step.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
    $applicationType = $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid);
    if ($applicationType == 'direct') {
      // Section one validation.
      // All items in section needs to be ticked before they can proceed.
      $section_one_form_items_required = [
        'business_eligible_for_partnership' => 'the organisation is eligible',
        'local_authority_suitable_for_nomination' => 'the local authority is suitable for nomination',
        'written_summary_agreed' => 'a written summary has been agreed',
        'terms_organisation_agreed' => 'the Primary Authority Terms and Conditions have been agreed',
      ];
      foreach ($section_one_form_items_required as $form_item => $replacement) {
        if (!$form_state->getValue($form_item)) {
          $this->setElementError(['section_one', $form_item], $form_state, 'Please confirm that @field', $replacement);
        }
      }
      // Section two validation.
      // Check if an empty value is provided.
      if ($form_state->getValue('business_regulated_by_one_authority') === FALSE) {
        $this->setElementError(['section_two','business_regulated_by_one_authority'], $form_state, 'Please select one');
      }
    }
    elseif ($applicationType == 'coordinated') {
      // All items in section needs to be ticked before they can proceed.
      $form_items = [
        'coordinator_local_authority_suitable' => 'the organisation is eligible',
        'suitable_nomination' => 'the coordinator is suitable for nomination',
        'written_summary_agreed' => 'a written summary has been agreed',
        'terms_local_authority_agreed' => 'the local authority agrees to Primary Authority Terms and Conditions',
      ];
      foreach ($form_items as $form_item => $replacement) {
        if (!$form_state->getValue($form_item)) {
          $this->setElementError(['section_one', $form_item], $form_state, 'Please confirm that @field', $replacement);
        }
      }
    }
  }
}
