<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsOrganisationSuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_organisation_suggestion';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'New Partnership Application';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues() {
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $properties = [
      'trading_name' => [
        'trading_name' => $this->getDefaultValues('organisation_name', '', 'par_partnership_application_organisation_search'),
      ],
      'organisation_name' => [
        'organisation_name' => $this->getDefaultValues('organisation_name', '', 'par_partnership_application_organisation_search'),
      ],
    ];

    $options = [];
    foreach ($properties as $group => $conditions) {
      $options += \Drupal::entityManager()
        ->getStorage('par_data_organisation')
        ->loadByProperties($conditions);
    }

    $viewBuilder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

    $radio_options = [];

    foreach($options as $option) {
      $option_view = $viewBuilder->view($option, 'summary');

      $radio_options[$option->id()] = $this->renderMarkupField($option_view)['#markup'];
    }

    // If no suggestions were found we want to automatically submit the form.
    if (count($radio_options) <= 0) {
      $this->setTempDataValue('option', 'new');
      $this->submitForm($form, $form_state);
      return $this->redirect($this->getFlow()->getNextRoute('add'), $this->getRouteParams());
    }

    $radio_options['new'] = "No, it's a new partnership.";

    $form['option'] = [
      '#type' => 'radios',
      '#title' => t('Did you mean any of these organisations?'),
      '#options' => $radio_options,
    ];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => t('Continue'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#name' => 'cancel',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::cancelFlow'],
      '#attributes' => [
        'class' => ['btn-link']
      ],
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($viewBuilder);
    $this->addCacheableDependency($options);
    $this->addCacheableDependency($properties);

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
  }

}
