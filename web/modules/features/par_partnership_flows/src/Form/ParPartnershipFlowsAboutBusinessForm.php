<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the about organisation details.
 */
class ParPartnershipFlowsAboutBusinessForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  protected $formItems = [];

  protected $pageTitle = 'Information about the business';

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we want to use values already saved we have to tell
      // the form about them.
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $this->getFlowDataHandler()->setFormPermValue('about_business', $par_data_organisation->get('comments')->getString());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $bundle = $par_data_organisation->bundle();
    $this->formItems = [
      'par_data_organisation:' . $bundle => [
        'comments' => 'about_business',
      ],
    ];

    $this->retrieveEditableValues($par_data_partnership);

    $form['about_business_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Provide information about the business'),
    ];

    // Business details.
    $form['about_business_fieldset']['about_business'] = [
      '#type' => 'textarea',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('about_business'),
      '#description' => '<p>Use this section to give a brief overview of the business.</p><p>Include any information you feel may be useful to enforcing authorities.</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = current($partnership->getOrganisation());
    $par_data_organisation->set('comments', $this->getFlowDataHandler()->getTempDataValue('about_business'));
    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The %field field could not be saved for %form_id');
      $replacements = [
        '%field' => 'comments',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
