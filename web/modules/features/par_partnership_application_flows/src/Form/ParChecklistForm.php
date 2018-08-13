<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The checklist form for partnership applications.
 */
class ParChecklistForm extends ParBaseForm {

  use ParFlowAccessTrait;

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
        'business_eligible_for_partnership' => 'Please confirm that the organisation is eligible',
        'local_authority_suitable_for_nomination' => 'Please confirm that the local authority is suitable for nomination',
        'written_summary_agreed' => 'Please confirm that a written summary has been agreed',
        'terms_organisation_agreed' => 'Please confirm that the Primary Authority Terms and Conditions have been agreed',
      ];

      foreach ($section_one_form_items_required as $form_item => $message) {
        if (!$form_state->getValue($form_item)) {
          $id = $this->getElementId(['section_one', $form_item], $form);
          $form_state->setErrorByName($this->getElementName([$form_item]), $this->wrapErrorMessage($message, $id));
        }
      }

      // Section two validation.

      // Check if an empty value is provided.
      if ($form_state->getValue('business_regulated_by_one_authority') === FALSE) {
        $id = $this->getElementId(['section_two','business_regulated_by_one_authority'], $form);
        $form_state->setErrorByName($this->getElementName(['business_regulated_by_one_authority']), $this->wrapErrorMessage('Please confirm the organisation is regulated by only one local authority.', $id));
      }

      // Warn that business needs to be informed their local authority still regulates.
      if ($form_state->getValue('business_regulated_by_one_authority') == 1 &&
        $form_state->getValue('is_local_authority') == 0 &&
        $form_state->getValue('business_informed_local_authority_still_regulates') == 0) {
        $id = $this->getElementId(['section_two','business_informed_local_authority_still_regulates'], $form);
        $form_state->setErrorByName($this->getElementName(['business_informed_local_authority_still_regulates']), $this->wrapErrorMessage('The organisation needs to be informed about local authority.', $id));
      }
    }
    elseif ($applicationType == 'coordinated') {
      // All items in section needs to be ticked before they can proceed.
      $form_items = [
        'coordinator_local_authority_suitable' => 'Please confirm that the organisation is eligible',
        'suitable_nomination' => 'Please confirm that the coordinator is suitable for nomination',
        'written_summary_agreed' => 'Please confirm that a written summary has been agreed',
        'terms_local_authority_agreed' => 'Please confirm that the local authority agrees to Primary Authority Terms and Conditions',
      ];

      foreach ($form_items as $form_item => $message) {
        if (!$form_state->getValue($form_item)) {
          $id = $this->getElementId(['section_one', $form_item], $form);
          $form_state->setErrorByName($this->getElementName($form_item), $this->wrapErrorMessage($message, $id));

        }
      }
    }
  }

}
