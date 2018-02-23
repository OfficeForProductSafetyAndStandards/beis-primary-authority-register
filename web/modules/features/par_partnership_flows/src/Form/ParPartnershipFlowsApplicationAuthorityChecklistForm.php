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
        '#title' => $this->t('Is the organisation regulated by only one local authority?'),
        '#options' => [
          1 => 'Yes',
          0 => 'No',
        ],
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('business_regulated_by_one_authority', FALSE),
      ];

      $form['section_two']['is_local_authority'] = [
        '#type' => 'radios',
        '#title' => $this->t('Is this your local authority?'),
        '#options' => [
          1 => 'Yes',
          0 => 'No',
        ],
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('is_local_authority', FALSE),
        '#states' => [
          'visible' => [
            'input[name="business_regulated_by_one_authority"]' => ['value' => 1],
          ],
          'disabled' => [
            'input[name="business_regulated_by_one_authority"]' => ['value' => 0],
          ],
        ],
      ];

      $form['section_two']['business_informed_local_authority_still_regulates'] = [
        '#type' => 'radios',
        '#title' => $this->t('I confirm the organisation has been informed that the local authority in which it is located will continue to regulate it'),
        '#options' => [
          1 => 'Yes',
          0 => 'No',
        ],
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('business_informed_local_authority_still_regulates', FALSE),
        '#states' => [
          'visible' => [
            'input[name="business_regulated_by_one_authority"]' => ['value' => 1],
            'input[name="is_local_authority"]' => ['value' => 0],
          ],
          'disabled' => [
            'input[name="business_regulated_by_one_authority"]' => ['value' => 0],
            'input[name="is_local_authority"]' => ['value' => 1],
          ],
        ],
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
        $this->setElementError(['section_two','business_regulated_by_one_authority'], $form_state, 'Please confirm the organisation is regulated by only one local authority.');
      }

      // Warn that business needs to be informed their local authority still regulates.
      if ($form_state->getValue('business_regulated_by_one_authority') == 1 &&
        $form_state->getValue('is_local_authority') == 0 &&
        $form_state->getValue('business_informed_local_authority_still_regulates') == 0) {
        $this->setElementError(['section_two','business_informed_local_authority_still_regulates'], $form_state, 'The organisation needs to be informed about local authority.');
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
