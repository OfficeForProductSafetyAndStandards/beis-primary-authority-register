<?php

namespace Drupal\par_member_update_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * The form for the premises details.
 */
class ParAddressForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add member organisation address';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    $this->getFlowDataHandler()->setParameter('par_data_premises', $par_data_organisation->getPremises(TRUE));
    parent::loadData();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_premises = $this->getFlowDataHandler()->getParameter('par_data_premises');

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

      $nation = $this->getFlowDataHandler()->getTempDataValue('country_code') === 'GB' ?
        $this->getFlowDataHandler()->getTempDataValue('nation') : '';
      $par_data_premises->set('nation', $nation);

      // Save the updated premises.
      if ($par_data_premises->save()) {
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
