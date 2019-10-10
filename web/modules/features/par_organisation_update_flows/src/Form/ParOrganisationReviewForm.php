<?php

namespace Drupal\par_organisation_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataSicCode;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_organisation_update_flows\ParFlowAccessTrait;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_data\Entity\ParDataOrganisation;

/**
 * The organisation update review form.
 */
class ParOrganisationReviewForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Review organisation details';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataOrganisation[] $par_data_organisation */

    if (isset($par_data_organisation)) {
      $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    }

    parent::loadData();
  }

  public function createEntities() {
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $organisation_name_cid = $this->getFlowNegotiator()->getFormKey('par_organisation_update_name');
    $organisation_about_cid = $this->getFlowNegotiator()->getFormKey('par_organisation_update_about_organisation');
    $trading_names_cid = $this->getFlowNegotiator()->getFormKey('par_organisation_update_trading_names');
    $sic_codes_cid = $this->getFlowNegotiator()->getFormKey('par_organisation_update_sic_codes');

    if ($par_data_organisation) {
      if ($organisation_name = $this->getFlowDataHandler()->getTempDataValue('name', $organisation_name_cid)) {
        $par_data_organisation->set('organisation_name', $organisation_name);
      }
      if ($organisation_about = $this->getFlowDataHandler()->getTempDataValue('about_business', $organisation_about_cid)) {
        $par_data_organisation->set('comments', $organisation_about);
      }

      // There can be multiple trading names.
      $trading_names = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'trading_name', $trading_names_cid) ?: [];
      $organisation_names = [];
      foreach ($trading_names as $delta => $trading_name) {
        $key = [ParFormBuilder::PAR_COMPONENT_PREFIX . 'trading_name', $delta, 'trading_name'];
        $organisation_names[] = $this->getFlowDataHandler()->getTempDataValue($key, $trading_names_cid);
      }
      if ($organisation_names) {
        $par_data_organisation->set('trading_name', $organisation_names);
      }

      // There can be multiple sic codes.
      $sic_codes = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'sic_code', $sic_codes_cid) ?: [];
      $organisation_codes = [];
      foreach ($sic_codes as $delta => $sic_code) {
        $key = [ParFormBuilder::PAR_COMPONENT_PREFIX . 'sic_code', $delta, 'sic_code'];
        $organisation_codes[] = $this->getFlowDataHandler()->getTempDataValue($key, $sic_codes_cid);
      }

      if ($organisation_codes) {
        $par_data_organisation->set('field_sic_code', $organisation_codes);
      }
    }

    return [
      'par_data_organisation' => $par_data_organisation ?? NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataOrganisation $par_data_organisation = NULL) {
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
    /** @var ParDataOrganisation[] $par_data_organisation */

    if ($par_data_organisation && $par_data_organisation->save()) {

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Organisation %organisation could not be updated');
      $replacements = [
        '%organisation' => $par_data_organisation->label(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
