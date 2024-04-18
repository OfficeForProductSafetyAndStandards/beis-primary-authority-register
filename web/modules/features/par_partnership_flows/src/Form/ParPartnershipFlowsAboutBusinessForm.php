<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the about organisation details.
 */
class ParPartnershipFlowsAboutBusinessForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * Page title.
   *
   * @var ?string
   */
  protected $pageTitle = 'Information about the organisation';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_partnership?->getOrganisation(TRUE));
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the organisation to save the information to.
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');

    // Get the submitted value.
    $about_business = $this->getFlowDataHandler()->getTempDataValue('about_business');

    // Save the value for the about_partnership field.
    $par_data_organisation?->get('comments')->setValue([
      'value' => $this->getFlowDataHandler()->getTempDataValue('about_business'),
      'format' => 'plain_text',
    ]);
    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The %field field could not be saved for %form_id');
      $replacements = [
        '%field' => 'comments',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
