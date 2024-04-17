<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParAddActionForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Add an action to the enforcement notice';

  /**
   * Load the data for this.
   */
  public function loadData() {

    // Load the plugin with a given cardinality, either the value being edited
    // or the next available new cardinality to add to.
    if (!$cardinality = $this->getFlowDataHandler()->getParameter('cardinality')) {
      foreach ($this->getComponents() as $component) {

        // The cardinality we're loading for is the enforcement action plugin.
        if ($component->getPluginId() === 'enforcement_action') {

          // Only need to get the new cardinality of the first plugin,
          // as all plugins on the page share the same value.
          // @todo Consider re-instigating this pattern of one action per page, but not needed now.
          // $this->getFlowDataHandler()->setParameter('cardinality', $component->getNewCardinality());
          break;
        }
      }
    }

    parent::loadData();
  }

}
