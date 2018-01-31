<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the premises details.
 */
class ParAddressForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm the primary contact details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_address';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;
    $par_data_premises = $organisation ? $organisation->getPremises(TRUE) : NULL;

    // Override the route parameter so that data loaded will be from this entity.
    $this->getFlowDataHandler()->setParameter('par_data_premises', $par_data_premises);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // We don't want to save for Partnership Application journey.
    // Temporary fix to resolve the saving of addresses when not needed.
    // @TODO When forms are separated then this can be removed.
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_application') {
      return;
    }

    // Create or update an existing PAR Premises record.
    $premises_param = $this->getFlowDataHandler()->getParameter('par_data_premises');
    $par_data_premises = $premises_param ? $premises_param : ParDataPremises::create([
      'type' => 'premises',
      'uid' => $this->getCurrentUser()->id(),
    ]);

    if ($par_data_premises) {
      $address = [
        'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code'),
        'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1'),
        'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2'),
        'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city'),
        'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county'),
        'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode'),
      ];

      $par_data_premises->set('address', $address);
      $par_data_premises->set('nation', $this->getFlowDataHandler()->getTempDataValue('nation'));

      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_organisation = $par_data_partnership ? $par_data_partnership->getOrganisation(TRUE) : NULL;

      // Check we are updating an existing partnership/organisation.
      if ($par_data_partnership && $par_data_premises->save()) {

        // Add premises to organisation if a new PAR Premises record is created.
        if (!$premises_param) {
          // Add to field_premises.
          $par_data_organisation->get('field_premises')
            ->appendItem($par_data_premises->id());
          $par_data_organisation->save();
        }

        $this->getFlowDataHandler()->deleteStore();

      }
      else {
        $message = $this->t('Address %premises could not be saved for %form_id');
        $replacements = [
          '%premises' => print_r($address, 1),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
