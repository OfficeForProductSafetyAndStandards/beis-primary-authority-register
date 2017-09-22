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
  public function titleCallback() {
    return 'New Partnership Application';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_authority_checklist';
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->retrieveEditableValues();

    // Load application type from previous step.
    $applicationType = $this->getDefaultValues('application_type', '', 'par_partnership_application_type');

    // Get Primary Authority Terms and Conditions URL.
    $terms_page = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/49');

    if ($applicationType == 'direct') {
      $form['section_one']['header'] = [
        '#type' => 'markup',
        '#markup' => $this->t('I confirm that…'),
        '#prefix' => '<h3 class="heading-medium">',
        '#suffix' => '</h3>',
      ];

      $form['section_one']['business_eligible_for_partnership'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('The business is eligible to enter into a partnership'),
        '#default_value' => $this->getDefaultValues('business_eligible_for_partnership', FALSE),
        '#return_value' => 'on',
      ];

      $form['section_one']['local_authority_suitable_for_nomination'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('My local authority is suitable for nomination as primary authority for the business'),
        '#default_value' => $this->getDefaultValues('local_authority_suitable_for_nomination', FALSE),
        '#return_value' => 'on',
      ];

      $form['section_one']['written_summary_agreed'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('A written summary of partnership arrangements has been agreed with the business'),
        '#default_value' => $this->getDefaultValues('written_summary_agreed', FALSE),
        '#return_value' => 'on',
      ];

      $form['section_one']['terms_organisation_agreed'] = [
        '#type' => 'checkbox',
        '#title' => t("My local authority agrees to the Primary Authority <a href='{$terms_page}' target='_blank'>terms and conditions</a>."),
        '#default_value' => $this->getDefaultValues("terms_organisation_agreed", FALSE),
        '#return_value' => 'on',
      ];

      // @todo find out if these are another step or relevant on this page.
      $form['section_two']['header'] = [
        '#type' => 'markup',
        '#markup' => $this->t('Business Questions'),
        '#prefix' => '<h3 class="heading-medium">',
        '#suffix' => '</h3>',
      ];

      $form['section_two']['business_regulated_by_one_authority'] = [
        '#type' => 'radios',
        '#title' => $this->t('I am authorised to submit this application'),
        '#options' => [
          1 => 'Yes',
          0 => 'No',
        ],
        '#default_value' => $this->getDefaultValues('business_regulated_by_one_authority', FALSE),
      ];

      $form['section_two']['is_local_authority'] = [
        '#type' => 'radios',
        '#title' => $this->t('Is this your local authority?'),
        '#options' => [
          1 => 'Yes',
          0 => 'No',
        ],
        '#default_value' => $this->getDefaultValues('is_local_authority', FALSE),
        '#states' => [
          'visible' => [
            'input[name="business_regulated_by_one_authority"]' => ['value' => 1],
          ],
        ],
      ];

      $form['section_two']['business_informed_local_authority_still_regulates'] = [
        '#type' => 'radios',
        '#title' => $this->t('I confirm the business has been informed that the local authority in which it is located will continue to regulate it'),
        '#options' => [
          1 => 'Yes',
          0 => 'No',
        ],
        '#default_value' => $this->getDefaultValues('business_informed_local_authority_still_regulates', FALSE),
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
        '#markup' => $this->t('I confirm that…'),
        '#prefix' => '<h3 class="heading-medium">',
        '#suffix' => '</h3>',
      ];

      $form['section_one']['coordinator_local_authority_suitable'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('My local authority is suitable for nomination as primary authority partner for the co-ordinator'),
        '#default_value' => $this->getDefaultValues('coordinator_local_authority_suitable', FALSE),
        '#return_value' => 'on',
      ];

      $form['section_one']['suitable_nomination'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('My prospective co-ordinator partner is suitable for nomination as a co-ordinator'),
        '#default_value' => $this->getDefaultValues('suitable_nomination', FALSE),
        '#return_value' => 'on',
      ];

      $form['section_one']['written_summary_agreed'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('A written summary of partnership arrangements has been agreed with the co-ordinator'),
        '#default_value' => $this->getDefaultValues('written_summary_agreed', FALSE),
        '#return_value' => 'on',
      ];

      $form['section_one']['terms_local_authority_agreed'] = [
        '#type' => 'checkbox',
        '#title' => t("My local authority agrees to the Primary Authority <a href='{$terms_page}' target='_blank'>Terms and Conditions</a>."),
        '#default_value' => $this->getDefaultValues("terms_local_authority_agreed", FALSE),
        '#return_value' => 'on',
      ];
    }

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => $this->t('Continue'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#name' => 'cancel',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::cancelForm'],
      '#limit_validation_errors' => [],
      '#attributes' => [
        'class' => ['btn-link'],
      ],
    ];

    $this->addCacheableDependency($applicationType);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // Load application type from previous step.
    $applicationType = $this->getDefaultValues('application_type', '', 'par_partnership_application_type');
    if ($applicationType == 'direct') {
      // Section one validation.
      // All items in section needs to be ticked before they can proceed.
      $section_one_form_items_required = [
        'business_eligible_for_partnership',
        'local_authority_suitable_for_nomination',
        'written_summary_agreed',
        'terms_organisation_agreed',
      ];

      foreach ($section_one_form_items_required as $form_item) {
        if (!$form_state->getValue($form_item)) {
          $this->setElementError(['section_one', $form_item], $form_state, 'The @field is required');
        }
      }

      // Section two validation.
      if (!$form_state->getValue('business_regulated_by_one_authority')) {
        $this->setElementError('business_regulated_by_one_authority', $form_state, 'You need to be authorised to submit an application');
      }

      if ($form_state->getValue('business_regulated_by_one_authority') &&
        !$form_state->getValue('is_local_authority') &&
        !$form_state->getValue('business_informed_local_authority_still_regulates')) {
        $this->setElementError('business_informed_local_authority_still_regulates', $form_state, 'The business needs to be informed about local authority.');

      }
    }
    elseif ($applicationType == 'coordinated') {
      // All items in section needs to be ticked before they can proceed.
      $form_items = [
        'coordinator_local_authority_suitable',
        'suitable_nomination',
        'written_summary_agreed',
        'terms_local_authority_agreed',
      ];

      foreach ($form_items as $form_item) {
        if (!$form_state->getValue($form_item)) {
          $this->setElementError(['section_one', $form_item], $form_state, 'The @field is required');
        }
      }
    }
  }

}
