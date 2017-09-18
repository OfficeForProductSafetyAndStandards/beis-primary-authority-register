<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsApplicationOrganisationContactForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_organisation_address';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'New Partnership Application';
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $this->retrieveEditableValues();

    $form['info'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Contact page to be added here'),
    ];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => t('Continue'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#name' => 'cancel',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::cancelForm'],
      '#attributes' => [
        'class' => ['btn-link']
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

}
