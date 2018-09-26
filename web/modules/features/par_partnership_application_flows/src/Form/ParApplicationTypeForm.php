<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * Partnership Application Form - Type radios page.
 */
class ParApplicationTypeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'What kind of partnership are you applying for?';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');

    $form['application_type'] = [
      '#title' => 'Choose a type of partnership',
      '#description' => '<p>A business can form its own direct partnership. It then receives Primary Authority Advice tailored to its specific needs from its primary authority.</p><p>Alternatively, a business can belong to a trade association (or other type of group) to benefit from a co-ordinated primary authority. In this case, the Primary Authority Advice is still from the primary authority, but provided via the trade association, and tailored to the general needs of its members.</p><p>For more information visit the <a href="https://www.gov.uk/guidance/local-regulation-primary-authority#what-are-the-two-types-of-partnership" target="_blank">Primary Authority Guidance</a></p>',
      '#type' => 'radios',
      '#options' => $partnership_bundle->getAllowedValues('partnership_type'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('application_type'),
      '#attributes' => ['class' => ['form-group']],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('application_type')) {
      $id = $this->getElementId(['application_type_fieldset'], $form);
      $form_state->setErrorByName($this->getElementName('application_type'), $this->wrapErrorMessage('Please select the type of application.', $id));
    }

    parent::validateForm($form, $form_state);
  }

}
