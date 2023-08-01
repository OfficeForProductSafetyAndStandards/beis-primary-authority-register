<?php

namespace Drupal\par_partnership_amend_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for selecting partnerships.
 */
class ParSelectPartnershipsForm extends ParBaseForm {

  protected $pageTitle = 'Which partnerships would you like to migrate?';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');

    $conditions = [
      [
        'AND' => [
          ['field_authority', $par_data_authority->id()]
        ],
      ],
    ];
    $matching_partnerships = $this->getParDataManager()->getEntitiesByQuery('par_data_partnership', $conditions);
    $this->getFlowDataHandler()->setFormPermValue('partnerships', $matching_partnerships);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL) {
    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_authority);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate that this authority has the same regulatory functions.

    // Validate that there are some partnerships that can be transferred.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $delta = $this->getFlowDataHandler()->getTempDataValue('delta');
  }

}
