<?php

namespace Drupal\par_partnership_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use CommerceGuys\Intl\Country\CountryRepository;

/**
 * The partnership form for the premises details.
 */
class ParPartnershipFlowsAddressForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * @return \Drupal\address\Repository\CountryRepository
   */
  protected function getCountryRepository() {
    return \Drupal::service('address.country_repository');
  }

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['country_code', 'par_data_premises', 'address', 'country_code', NULL, 0, []],
    ['address_line1', 'par_data_premises', 'address', 'address_line1', NULL, 0, [
      'Street address field is required.' => 'You must enter the first line of your address',
    ]],
    ['address_line2', 'par_data_premises', 'address', 'address_line2', NULL, 0, []],
    ['town_city', 'par_data_premises', 'address', 'locality', NULL, 0, [
      'Post town field is required.' => 'You must enter the town or city for this address'
    ]],
    ['postcode', 'par_data_premises', 'address', 'postal_code', NULL, 0, [
      'Postal code field is required.' => 'You must enter a valid postcode.'
    ]],
    ['nation', 'par_data_premises', 'nation', NULL, NULL, 0, []],
  ];

  /**
   * Get partnership.
   */
  public function getPartnershipParam() {
    return $this->getFlowDataHandler()->getParameter('par_data_partnership');
  }

  /**
   * Get partnership.
   */
  public function getPremisesParam() {
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_direct_application' || $this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated_application') {
      $partnership = $this->getPartnershipParam();
      $organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;
      $premises = $organisation ? $organisation->getPremises() : NULL;
      return !empty($premises) ? current($premises) : NULL;
    }
    else {
      return $this->getFlowDataHandler()->getParameter('par_data_premises');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getPartnershipParam();
    $par_data_premises = $this->getPremisesParam();

    if (!empty($par_data_partnership)) {
      // Are we editing an existing premises entity?
      if ($this->getFlowNegotiator()->getFlowName() === 'partnership_direct_application' || $this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated_application') {
        $verb = 'Confirm';
      }
      else {
        $verb = $par_data_premises ? 'Edit' : 'Add';
      }

      if ($this->getFlowNegotiator()->getFlowName() === 'partnership_direct' || $this->getFlowNegotiator()->getFlowName() === 'partnership_direct_application') {
        $this->pageTitle = "{$verb} the registered address";
      }
      else if ($this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated' || $this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated_application') {
        $this->pageTitle = "{$verb} the member's registered address";
      }
    }
    else {
      $this->pageTitle = 'New organisation information';
    }

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataPremises $par_data_premises
   *   The Premises being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {
    if ($par_data_premises) {
      $address = $par_data_premises->get('address')->first();

      // Address.
      $this->getFlowDataHandler()->setFormPermValue("postcode", $address->get('postal_code')->getString());
      $this->getFlowDataHandler()->setFormPermValue("address_line1", $address->get('address_line1')->getString());
      $this->getFlowDataHandler()->setFormPermValue("address_line2", $address->get('address_line2')->getString());
      $this->getFlowDataHandler()->setFormPermValue("town_city", $address->get('locality')->getString());
      $this->getFlowDataHandler()->setFormPermValue("county", $address->get('administrative_area')->getString());
      $this->getFlowDataHandler()->setFormPermValue("country_code", $address->get('country_code')->getString());
      $this->getFlowDataHandler()->setFormPermValue("nation", $par_data_premises->get('nation')->getString());
      $this->getFlowDataHandler()->setFormPermValue("uprn", $par_data_premises->get('uprn')->getString());
      $this->getFlowDataHandler()->setFormPermValue('premises_id', $par_data_premises->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {
    $par_data_premises = $this->getPremisesParam();
    $this->retrieveEditableValues($par_data_partnership, $par_data_premises);
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $form['premises_id'] = [
      '#type' => 'hidden',
      '#value' => $this->getFlowDataHandler()->getDefaultValues('premises_id', 'new'),
    ];

    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 1'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("address_line1"),
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 2'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("address_line2"),
    ];

    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Town / City'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("town_city"),
    ];

    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter County'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("county"),
    ];

    $form['country_code'] = [
      '#type' => 'select',
      '#options' => $this->getCountryRepository()->getList(NULL),
      '#title' => $this->t('Country'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("country_code", "GB"),
    ];

    $form['nation'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Nation'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("nation"),
      '#states' => [
        'visible' => [
          'select[name="country_code"]' => [
            ['value' => 'GB'],
          ],
        ],
      ],
    ];

    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Postcode'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("postcode"),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_premises);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
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
    $premises = $this->getPremisesParam() ? $this->getPremisesParam() : ParDataPremises::create([
      'type' => 'premises',
      'uid' => $this->getCurrentUser()->id(),
    ]);

    if ($premises) {
      $address = [
        'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code'),
        'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1'),
        'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2'),
        'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city'),
        'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county'),
        'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode'),
      ];

      $premises->set('address', $address);

      $nation = $this->getFlowDataHandler()->getTempDataValue('nation');
      $premises->setNation($nation);

      $par_data_partnership = $this->getPartnershipParam();
      $par_data_organisation = $par_data_partnership ? $par_data_partnership->getOrganisation(TRUE) : NULL;
      if ($par_data_organisation) {
        $par_data_organisation->setNation($nation);
      }

      // Check we are updating an existing partnership/organisation.
      if ($par_data_partnership && $premises->save()) {
        // Add premises to organisation if a new PAR Premises record is created.
        if (!$this->getPremisesParam()) {
          // Add to field_premises.
          $par_data_organisation->get('field_premises')
            ->appendItem($premises->id());
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
