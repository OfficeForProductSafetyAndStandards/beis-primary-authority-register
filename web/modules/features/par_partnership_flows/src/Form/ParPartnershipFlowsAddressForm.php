<?php

namespace Drupal\par_partnership_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use CommerceGuys\Intl\Country\CountryRepository;

/**
 * The partnership form for the premises details.
 */
class ParPartnershipFlowsAddressForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function titleCallback() {
    $par_data_premises = $this->getFlowDataHandler()->getParameter('par_data_premises');

    // Select the action being performed.
    $verb = $par_data_premises ? 'Edit' : 'Add';

    $this->pageTitle = "{$verb} the registered address";

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Create or update an existing PAR Premises record.
    $par_data_premises = $this->getFlowDataHandler()->getParameter('par_data_premises');
    if (!$par_data_premises instanceof ParDataPremises) {
      $par_data_premises = ParDataPremises::create([
        'type' => 'premises',
        'uid' => $this->getCurrentUser()->id(),
      ]);
    }

    if ($par_data_premises instanceof ParDataPremises) {
      $address = [
        'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code'),
        'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1'),
        'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2'),
        'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city'),
        'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county'),
        'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode'),
      ];

      $par_data_premises->set('address', $address);

      $nation = $this->getFlowDataHandler()->getTempDataValue('nation');
      $par_data_premises->setNation($nation);

      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_organisation = $par_data_partnership?->getOrganisation(TRUE);
      if ($par_data_organisation) {
        $par_data_organisation->setNation($nation);
      }

      // Capture whether the address is new.
      $is_new = $par_data_premises->isNew();

      // Check we are updating an existing partnership/organisation.
      if ($par_data_partnership && $par_data_premises->save()) {
        // Add premises to organisation if a new PAR Premises record is created.
        if ($is_new) {
          $par_data_organisation->get('field_premises')->appendItem($par_data_premises->id());
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
