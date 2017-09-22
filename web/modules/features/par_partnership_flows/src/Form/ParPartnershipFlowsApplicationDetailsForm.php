<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsApplicationDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_details';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'New Partnership Application';
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
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    // @todo this file is stub to add all additional information when the step page has been created.
    $form['example'] = [
      '#type' => 'markup',
      '#markup' => "This is basically the done state. A {$this->getDefaultValues('application_type', '', 'par_partnership_application_type')} partnership."
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

}
