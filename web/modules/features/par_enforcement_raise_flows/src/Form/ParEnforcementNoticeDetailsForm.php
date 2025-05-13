<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The member contact form.
 */
class ParEnforcementNoticeDetailsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['summary', 'par_data_enforcement_notice', 'summary', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter a summary description for this notice of enforcement action.',
    ],
    ],
  ];

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Enforcement details';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData() {
    $cid = $this->getFlowNegotiator()->getFormKey('par_authority_selection');
    $authority_id = $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', NULL, $cid);
    if ($authority_id && $par_data_authority = ParDataAuthority::load($authority_id)) {
      $account_id = $this->getFlowDataHandler()->getCurrentUser()->id();
      $account = User::load($account_id);

      // Get logged in user ParDataPerson(s) related to the primary authority.
      if ($par_data_person = $this->getParDataManager()->getUserPerson($account, $par_data_authority)) {
        // Set the person that's being edited.
        $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ?ParDataPartnership $par_data_partnership = NULL) {

    $enforcement_notice_entity_type = $this->getParDataManager()->getParBundleEntity('par_data_enforcement_notice');
    $notice_type = $enforcement_notice_entity_type->getAllowedValues('notice_type');

    $form['notice_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('This enforcement action is'),
      '#title_tag' => 'h2',
      '#options' => $notice_type,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('notice_type', key($notice_type)),
      '#attributes' => ['class' => ['govuk-form-group']],
      '#required' => TRUE,
      '#weight' => 100,
    ];

    $form['summary'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide a summary of the enforcement notification'),
      '#title_tag' => 'h2',
      '#description' => [
        '#theme' => 'item_list',
        '#items' => [
          'full details of the contravention',
          'which products or services are affected',
          'your proposed text for any statutory notice or draft changes etc',
          'your reasons for proposing the enforcement action',
        ],
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
        '#weight' => 100,
      ],
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('summary'),
      '#weight' => 100,
    ];

    return parent::buildForm($form, $form_state);
  }

}
