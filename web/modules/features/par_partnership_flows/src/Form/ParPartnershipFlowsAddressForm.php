<?php

namespace Drupal\par_partnership_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the premises details.
 */
class ParPartnershipFlowsAddressForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  protected $formItems = [
    'par_data_premises:premises' => [
      'address' => [
        'country_code' => 'country_code',
        'address_line1' => 'address_line1',
        'address_line2' => 'address_line2',
        'locality' => 'town_city',
        'postal_code' => 'postcode',
      ],
      'nation' => 'country',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_address';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $par_data_premises = $this->getRouteParam('par_data_premises');

    if (!empty($par_data_partnership)) {
      // Are we editing an existing premises entity?
      $verb = $this->t($par_data_premises ? 'Edit' : 'Add');

      if ($this->getFlowName() === 'partnership_direct') {
        $this->pageTitle = "{$verb} your business address";
      }
      else if ($this->getFlowName() === 'partnership_coordinated') {
        $this->pageTitle = "{$verb} your member business address";
      }
    }
    else {
      $this->pageTitle = 'New business information';
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
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

    if ($par_data_premises) {
      $address = $par_data_premises->get('address')->first();

      // Address.
      $this->loadDataValue("postcode", $address->get('postal_code')->getString());
      $this->loadDataValue("address_line1", $address->get('address_line1')->getString());
      $this->loadDataValue("address_line2", $address->get('address_line2')->getString());
      $this->loadDataValue("town_city", $address->get('locality')->getString());
      $this->loadDataValue("county", $address->get('administrative_area')->getString());
      $this->loadDataValue("country", $par_data_premises->get('nation')->getString());
      $this->loadDataValue("uprn", $par_data_premises->get('uprn')->getString());
      $this->loadDataValue('premises_id', $par_data_premises->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_premises);
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $form['premises_id'] = [
      '#type' => 'hidden',
      '#value' => $this->getDefaultValues('premises_id', 'new'),
    ];

    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 1'),
      '#default_value' => $this->getDefaultValues("address_line1"),
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 2'),
      '#default_value' => $this->getDefaultValues("address_line2"),
    ];

    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Town / City'),
      '#default_value' => $this->getDefaultValues("town_city"),
    ];

    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter County'),
      '#default_value' => $this->getDefaultValues("county"),
    ];

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Nation'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getDefaultValues("country"),
    ];

    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postcode'),
      '#default_value' => $this->getDefaultValues("postcode"),
    ];

    $form['country_code'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Country'),
      '#default_value' => 'GB',
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
    if ($this->getFlowName() === 'partnership_application') {
      return;
    }

    // Create or update an existing PAR Premises record.
    $premises = $this->getRouteParam('par_data_premises') ? $this->getRouteParam('par_data_premises') : ParDataPremises::create([
      'type' => 'premises',
      'uid' => $this->getCurrentUser()->id(),
    ]);

    if ($premises) {
      $address = [
        'country_code' => $this->getTempDataValue('country_code'),
        'address_line1' => $this->getTempDataValue('address_line1'),
        'address_line2' => $this->getTempDataValue('address_line2'),
        'locality' => $this->getTempDataValue('town_city'),
        'administrative_area' => $this->getTempDataValue('county'),
        'postal_code' => $this->getTempDataValue('postcode'),
      ];

      $premises->set('address', $address);
      $premises->set('nation', $this->getTempDataValue('country'));

      $par_data_partnership = $this->getRouteParam('par_data_partnership');
      $par_data_organisation = $par_data_partnership ? $par_data_partnership->getOrganisation(TRUE) : NULL;

      // Check we are updating an existing partnership/organisation.
      if ($par_data_partnership && $premises->save()) {

        // Add premises to organisation if a new PAR Premises record is created.
        if (!$this->getRouteParam('par_data_premises')) {
          // Add to field_premises.
          $par_data_organisation->get('field_premises')
            ->appendItem($premises->id());
          $par_data_organisation->save();
        }

        $this->deleteStore();

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
