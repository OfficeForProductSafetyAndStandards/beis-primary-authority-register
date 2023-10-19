<?php

namespace Drupal\par_transfer_partnerships_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * The form for reviewing any changes before they are made.
 */
class ParReviewForm extends ParBaseForm {

  protected $pageTitle = 'Confirm this transfer';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL) {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership[] $partnerships */
    /** @var ParDataAuthority $old_authority */
    /** @var ParDataAuthority $new_authority */

    // Get all the contacts for each of the partnerships.
    $primary_authority_contacts = [];
    foreach ($partnerships as $partnership) {
      foreach ($partnership->getAuthorityPeople() as $person) {
        $primary_authority_contacts[$person->id()] = $person;
      }
    }

    // Get all the enforcements and deviation requests awaiting approval.
    $pending_enforcements = [];
    $pending_deviation_requests = [];
    foreach ($partnerships as $partnership) {
      // Only return inactive enforcements.
      $enforcement_notices = $partnership->getEnforcements();
      $enforcement_notices = array_filter($enforcement_notices, function ($enforcement) {
        return $enforcement->inProgress();
      });
      $pending_enforcements = array_merge($pending_enforcements, $enforcement_notices);

      // Only return inactive deviation requests.
      $deviation_requests = $partnership->getDeviationRequests();
      $deviation_requests = array_filter($deviation_requests, function ($deviation) {
        return $deviation->inProgress();
      });
      $pending_deviation_requests = array_merge($pending_deviation_requests, $deviation_requests);
    }

    // Display the authorities.
    $form['authorities'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Authorities'),
      ],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("The partnerships will be transferred from @old to @new",
            ['@old' => $old_authority->label(), '@new' => $new_authority->label()]
          ),
      ],
    ];

    // Display the partnerships that will be transferred.
    $partnership_count = count($partnerships);
    $partnership_labels = $this->getParDataManager()->getEntitiesAsOptions($partnerships);
    $form['partnerships'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Partnerships'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->formatPlural($partnership_count,
          "The following partnership will be transferred, please check this is correct.",
          "The following @count partnerships will be transferred, please check this is correct.",
          ['@count' => $partnership_count]),
      ],
      'list' => [
        '#theme' => 'item_list',
        '#items' => $partnership_labels,
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
      ],
    ];

    // Display the partnerships that will be transferred.
    $contact_count = count($primary_authority_contacts);
    $contact_records = $this->getParDataManager()->getEntitiesAsOptions($primary_authority_contacts, [], 'summary');
    $form['contacts'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Primary Authority Contacts'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->formatPlural($contact_count,
          "This primary authority contact will be added to the new authority.",
          "The following @count primary authority contacts will be transferred to the new authority.",
          ['@count' => $contact_count]),
      ],
      'list' => [
        '#theme' => 'item_list',
        '#items' => $contact_records,
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
      ],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("If there are any primary authority contacts that you don't want transferred to the new authority you must update the partnership with the new records before you transfer the partnership."),
      ],
    ];

    // Display the partnerships that will be transferred.
    $pending_enquiry_count = count($pending_enforcements + $pending_deviation_requests);
    $enquiries = array_merge($pending_enforcements, $pending_deviation_requests);
    $enquiry_labels = $this->getParDataManager()->getEntitiesAsOptions($enquiries);
    $form['enquiries'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Enforcements & Enquiries'),
      ],
    ];
    if ($pending_enquiry_count > 0) {
      $form['enquiries']['pending'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->formatPlural($pending_enquiry_count,
          "There is @count pending notice of enforcement action or deviation request, this will be transferred to the new authority.",
          "There are @count pending notices of enforcement action or deviation requests, these will be transferred to the new authority.",
          ['@count' => $pending_enquiry_count]),
      ];
    }
    $form['enquiries']['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $pending_enquiry_count > 0 ?
        $this->t("All other notices of enforcement action and deviation requests that have been approved, along with all inspection plan feedback and all other enquiries will remain with the existing authority and will not be transferred.") :
        $this->t("All notices of enforcement action, deviation requests, inspection plan feedback and all other enquiries will remain with the existing authority and will not be transferred."),
    ];

    // Display the date of change.
    $transfer_date = $this->getDateFormatter()->format($transfer_date->getTimestamp(), 'gds_date_format');
    $form['date'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Date'),
      ],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("This transfer will be effective from @date, this information will be shown on the partnership.", ['@date' => $transfer_date]),
      ],
    ];

    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => 'Please check everything is correct, once you confirm these details the partnerships will be transferred.',
      '#wrapper_attributes' => ['class' => 'govuk-!-margin-top-8'],
    ];

    // Change the main button title to 'remove'.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Transfer');

    // Make sure to add the authority cacheability data to this form.
    $this->addCacheableDependency($par_data_authority);

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    // Get the old authority.
    $old_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');

    // Get the new authority.
    $authority_cid = $this->getFlowNegotiator()->getFormKey('authority');
    $authority_id = $this->getFlowDataHandler()->getTempDataValue('authority_id', $authority_cid);
    $new_authority = $authority_id ? \Drupal::entityTypeManager()->getStorage('par_data_authority')
      ->load($authority_id) : NULL;

    // Get the transfer date.
    $date_cid = $this->getFlowNegotiator()->getFormKey('date');
    $date_value = $this->getFlowDataHandler()->getTempDataValue('date', $date_cid);
    $date = $date_value ? DrupalDateTime::createFromFormat('Y-m-d', $date_value, ['validate_format' => FALSE]) : NULL;

    // Get the partnerships to transfer.
    $partnerships_cid = $this->getFlowNegotiator()->getFormKey('partnerships');
    $partnership_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_partnership_id', $partnerships_cid);
    $partnerships = $partnership_ids ? \Drupal::entityTypeManager()->getStorage('par_data_partnership')
      ->loadMultiple($partnership_ids) : [];

    return [
      'partnerships' => $partnerships,
      'old_authority' => $old_authority,
      'new_authority' => $new_authority,
      'transfer_date' => $date,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership[] $partnerships */
    /** @var ParDataAuthority $old_authority */
    /** @var ParDataAuthority $new_authority */

    // Ensure that the transfer has been confirmed.
    if (!$form_state->getValue('confirmation')) {
      $id = $this->getElementId('confirmation', $form);
      $form_state->setErrorByName($this->getElementName(['confirmation']), $this->wrapErrorMessage('Please confirm the transfer of partnerships.', $id));
    }

    // Validate that the new authority has the same regulatory functions as the
    // old authority.
    if ($old_authority instanceof ParDataAuthority && $new_authority instanceof ParDataAuthority) {
      $old_functions = array_values($old_authority->get('field_regulatory_function')->getValue());
      $new_functions = array_values($new_authority->get('field_regulatory_function')->getValue());

      // Sort the array elements
      sort($old_functions);
      sort($new_functions);

      if ($old_functions !== $new_functions) {
        $id_key = $this->getElementKey('authority', 1, TRUE);
        $message = $this->t("The regulatory functions do not match those offered by @old_authority.", ['@old_authority' => $old_authority->label()])->render();
        $form_state->setErrorByName('authorities', $this->wrapErrorMessage($message, $this->getElementId($id_key, $form)));
      }
    }
    // Validate that there are two valid authorities to transfer between.
    else {
      $id_key = $this->getElementKey('authority', 1, TRUE);
      $form_state->setErrorByName('authorities', $this->wrapErrorMessage('This transfer cannot be made at this time.', $this->getElementId($id_key, $form)));
    }

    // Validate that there are some partnerships to transfer.
    if (empty($partnerships)) {
      $form_state->setErrorByName('partnerships', $this->wrapErrorMessage($message, $this->getElementId($id_key, $form)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership[] $partnerships */
    /** @var ParDataAuthority $old_authority */
    /** @var ParDataAuthority $new_authority */
    /** @var DrupalDateTime $transfer_date */

    // Transfer the partnership.
    foreach ($partnerships as $partnership) {
      if ($old_authority instanceof ParDataAuthority && $new_authority instanceof ParDataAuthority) {
        $partnership->transfer($old_authority, $new_authority, $transfer_date);
        $partnership->save();
      }
    }

    $this->getFlowDataHandler()->deleteStore();
  }

}
