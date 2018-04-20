<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\par_enforcement_raise_flows\ParFormCancelTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParAddActionForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add an action to the  enforcement notice';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    // Load the plugin with a given cardinality, either the value being edited
    // or the next available new cardinality to add to.
    if (!$cardinality = $this->getFlowDataHandler()->getParameter('cardinality')) {
      foreach ($this->getComponents() as $component) {
        // Only need to get the new cardinality of the first plugin,
        // as all plugins on the page share the same value.
        $this->getFlowDataHandler()->setParameter('cardinality', $component->getNewCardinality());
        break;
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

}
