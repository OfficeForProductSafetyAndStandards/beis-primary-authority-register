<?php

namespace Drupal\par_authority_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_add_flows\ParFlowAccessTrait;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_data\Entity\ParDataAuthority;

/**
 * The authority add review form.
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
    /** @var ParDataAuthority $par_data_authority */
    /** @var ParDataPremises $par_data_premises */

    if ($par_data_authority) {
      if ($par_data_authority->hasField('authority_name')) {
        $this->getFlowDataHandler()->setFormPermValue("authority_name", $par_data_authority->get('authority_name')->getString());
      }
      if ($par_data_authority->hasField('ons_code')) {
        $this->getFlowDataHandler()->setFormPermValue("ons_code", $par_data_authority->get('ons_code')->getString());
      }

      if ($par_data_authority->hasField('authority_type')) {
        $this->getFlowDataHandler()->setFormPermValue("authority_type", $par_data_authority->getAuthorityType());
      }

      if ($par_data_premises) {
        $this->getFlowDataHandler()->setFormPermValue("authority_address", $par_data_premises->label());
      }

      if ($par_data_authority->hasField('field_regulatory_function')) {
        $regulatory_functions = $par_data_authority->getRegulatoryFunction();
        $authority_functions = [];

        foreach ($regulatory_functions as $regulatory_function) {
          $authority_functions[] = $regulatory_function->label();
        }

        if (!empty($authority_functions)) {
          $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", implode(', ', $authority_functions));
        }
        else {
          $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", $this->t('This authority does not provide any regulatory functions.'));
        }
      }
    }

    parent::loadData();
  }

  public function createEntities() {
    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $authority_name_cid = $this->getFlowNegotiator()->getFormKey('par_authority_add_name');
    $authority_type_cid = $this->getFlowNegotiator()->getFormKey('par_authority_add_type');
    $authority_address_cid = $this->getFlowNegotiator()->getFormKey('par_authority_add_address');
    $ons_code_cid = $this->getFlowNegotiator()->getFormKey('par_authority_add_ons');
    $regulatory_functions_cid = $this->getFlowNegotiator()->getFormKey('par_authority_add_regulatory_functions');

    $par_data_authority = ParDataAuthority::create([
      'authority_name' => $this->getFlowDataHandler()->getTempDataValue('name', $authority_name_cid),
      'authority_type' => $this->getFlowDataHandler()->getTempDataValue('authority_type', $authority_type_cid),
      'ons_code' => $this->getFlowDataHandler()->getTempDataValue('ons_code', $ons_code_cid),
    ]);

    // Get the nation from the address and set on the authority.
    $nation = $this->getFlowDataHandler()->getDefaultValues('nation','', $authority_address_cid);
    if (!$nation) {
      $nation = $this->getFlowDataHandler()
        ->getDefaultValues('country_code', '', $authority_address_cid);
    }
    $par_data_authority->setNation($nation);

    // Create a new address
    $par_data_premises = ParDataPremises::create([
      'type' => 'premises',
      'uid' => $this->getCurrentUser()->id(),
      'address' => [
        'country_code' => $this->getFlowDataHandler()->getDefaultValues('country_code', '', $authority_address_cid),
        'address_line1' => $this->getFlowDataHandler()->getDefaultValues('address_line1','', $authority_address_cid),
        'address_line2' => $this->getFlowDataHandler()->getDefaultValues('address_line2','', $authority_address_cid),
        'locality' => $this->getFlowDataHandler()->getDefaultValues('town_city','', $authority_address_cid),
        'administrative_area' => $this->getFlowDataHandler()->getDefaultValues('county','', $authority_address_cid),
        'postal_code' => $this->getFlowDataHandler()->getDefaultValues('postcode','', $authority_address_cid),
      ],
    ]);
    $par_data_premises->setNation($nation);

    // Set the regulatory functions.
    $regulatory_functions = $this->getFlowDataHandler()->getTempDataValue('regulatory_functions', $regulatory_functions_cid);
    if ($regulatory_functions) {
      $par_data_authority->set('field_regulatory_function', array_keys(array_filter($regulatory_functions)));
    }

    return [
      'par_data_authority' => $par_data_authority ?? NULL,
      'par_data_premises' => $par_data_premises ?? NULL,
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
    /** @var ParDataAuthority $par_data_authority */
    /** @var ParDataPremises $par_data_premises */

    // Save the address and add to the authority.
    if ($par_data_premises && $par_data_premises->save()) {
      $par_data_authority->set('field_premises', $par_data_premises);
    }

    // Save the authority.
    if ($par_data_authority && $par_data_authority->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Authority %authority could not be added');
      $replacements = [
        '%authority' => $par_data_authority->label(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
