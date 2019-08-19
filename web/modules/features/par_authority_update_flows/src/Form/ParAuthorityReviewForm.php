<?php

namespace Drupal\par_authority_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_update_flows\ParFlowAccessTrait;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_data\Entity\ParDataAuthority;

/**
 * The authority update review form.
 */
class ParAuthorityReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Review authority details';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataAuthority[] $par_data_authority */

    if (isset($par_data_authority)) {
      $this->getFlowDataHandler()->setParameter('par_data_authority', $par_data_authority);
    }

    parent::loadData();
  }

  public function createEntities() {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $authority_name_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_name');
    $authority_type_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_type');
    $ons_code_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_ons');
    $regulatory_functions_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_regulatory_functions');

    if ($par_data_authority) {
      if ($authority_name = $this->getFlowDataHandler()->getTempDataValue('name', $authority_name_cid)) {
        $par_data_authority->set('authority_name', $authority_name);
      }
      if ($authority_type = $this->getFlowDataHandler()->getTempDataValue('authority_type', $authority_type_cid)) {
        $par_data_authority->set('authority_type', $authority_type);
      }
      if ($ons_code = $this->getFlowDataHandler()->getTempDataValue('ons_code', $ons_code_cid)) {
        $par_data_authority->set('ons_code', $ons_code);
      }

      $regulatory_functions = $this->getFlowDataHandler()->getTempDataValue('regulatory_functions', $regulatory_functions_cid);
      if ($regulatory_functions) {
        $par_data_authority->set('field_regulatory_function', array_keys(array_filter($regulatory_functions)));
      }
    }

    return [
      'par_data_authority' => $par_data_authority ?? NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL) {
    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataAuthority[] $par_data_authority */

    if ($par_data_authority && $par_data_authority->save()) {

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Authority %authority could not be updated');
      $replacements = [
        '%authority' => $par_data_authority->label(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
