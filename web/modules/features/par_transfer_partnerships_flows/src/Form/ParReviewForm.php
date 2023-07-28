<?php

namespace Drupal\par_transfer_partnerships_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
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

    // Display the authorities.
    $form['authorities'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium']],
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
    $count = count($partnerships);
    $labels = $this->getParDataManager()->getEntitiesAsOptions($partnerships);
    $form['partnerships'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium']],
        '#value' => $this->t('Partnerships'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->formatPlural($count,
          "The following partnership will be transferred, please check this is correct.",
          "The following @count partnerships will be transferred, please check this is correct.",
          ['@count' => count($partnerships)]),
      ],
      'list' => [
        '#theme' => 'item_list',
        '#items' => $labels,
        '#attributes' => ['class' => ['list', 'list-bullet']],
      ],
    ];

    // Display the authorities.
    $transfer_date = $this->getDateFormatter()->format($transfer_date->getTimestamp(), 'gds_date_format');
    $form['date'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium']],
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

    foreach ($partnerships as $partnership) {
      if ($old_authority instanceof ParDataAuthority && $new_authority instanceof ParDataAuthority) {
        $partnership->transfer($old_authority, $new_authority);
        $partnership->save();
      }
    }
    $this->getFlowDataHandler()->deleteStore();
  }

}
