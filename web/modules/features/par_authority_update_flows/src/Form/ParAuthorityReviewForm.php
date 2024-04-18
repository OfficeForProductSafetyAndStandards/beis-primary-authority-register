<?php

namespace Drupal\par_authority_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_authority_update_flows\ParFlowAccessTrait;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The authority update review form.
 */
class ParAuthorityReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Review authority details';

  /**
   * Load the data for this.
   */
  public function loadData() {
    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataAuthority $par_data_authority */
    /** @var \Drupal\par_data\Entity\ParDataPremises $par_data_premises */

    if (isset($par_data_authority)) {
      $this->getFlowDataHandler()->setParameter('par_data_authority', $par_data_authority);
    }

    if (isset($par_data_authority)) {
      $this->getFlowDataHandler()->setParameter('par_data_premises', $par_data_premises);
    }

    parent::loadData();
  }

  /**
   * Implements createEntities().
   */
  public function createEntities() {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');
    $par_data_premises = $par_data_authority?->getPremises(TRUE);

    // Get the cache IDs for the various forms that needs to be extracted from.
    $authority_name_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_name');
    $authority_type_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_type');
    $authority_address_cid = $this->getFlowNegotiator()->getFormKey('authority_address');
    $ons_code_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_ons');
    $regulatory_functions_cid = $this->getFlowNegotiator()->getFormKey('par_authority_update_regulatory_functions');

    if ($par_data_authority instanceof ParDataAuthority) {
      if ($authority_name = $this->getFlowDataHandler()->getTempDataValue('name', $authority_name_cid)) {
        $par_data_authority->set('authority_name', $authority_name);
      }
      if ($authority_type = $this->getFlowDataHandler()->getTempDataValue('authority_type', $authority_type_cid)) {
        $par_data_authority->set('authority_type', $authority_type);
      }
      if (!empty($this->getFlowDataHandler()->getFormTempData($authority_address_cid))) {
        $par_data_premises = $par_data_authority?->getPremises(TRUE);
        // Set the address values.
        $address = [
          'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code', $authority_address_cid),
          'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1', $authority_address_cid),
          'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2', $authority_address_cid),
          'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city', $authority_address_cid),
          'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county', $authority_address_cid),
          'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode', $authority_address_cid),
        ];
        $par_data_premises->set('address', $address);

        // Set the nation value.
        $nation = $this->getFlowDataHandler()->getTempDataValue('country_code') === 'GB' ?
          $this->getFlowDataHandler()->getTempDataValue('nation') : '';
        $par_data_premises->setNation($nation);
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
      'par_data_premises' => $par_data_premises ?? NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL) {
    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);

    $form['info'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => ['class' => ['govuk-warning-text']],
      'icon' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => '!',
        '#attributes' => [
          'class' => ['govuk-warning-text__icon'],
          'aria-hidden' => 'true',
        ],
      ],
      'strong' => [
        '#type' => 'html_tag',
        '#tag' => 'strong',
        '#value' => $this->t('Please be aware that changing the name of the authority will affect all past and ongoing partnerships. Only do this with the correct legal authorisation.'),
        '#attributes' => ['class' => ['govuk-warning-text__text']],
        'message' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $this->t('Warning'),
          '#attributes' => ['class' => ['govuk-warning-text__assistive']],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataAuthority $par_data_authority */
    /** @var \Drupal\par_data\Entity\ParDataPremises $par_data_premises */

    if ($par_data_authority instanceof ParDataEntityInterface && $par_data_authority->save()) {
      if ($par_data_premises instanceof ParDataEntityInterface) {
        $par_data_premises->save();
      }

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
