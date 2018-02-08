<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The advice document upload form.
 */
class ParPartnershipFlowsMemberConfirmForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use StringTranslationTrait;

  /**
   * The column mappings in the CSV.
   */
  protected $columns = [
    'par_data_premises' => [
      'address' => [
        'address_line1' => 3,
        'address_line2' => 4,
        'address_line3' => 5,
        'locality' => 6,
        'administrative_area' => 7,
        'postal_code' => 8,
      ],
      'nation' => 9,
    ],
    'par_data_person' => [
      'first_name' => 10,
      'last_name' => 11,
      'work_phone' => 12,
      'mobile_phone' => 13,
      'email' => 14,
    ],
    'par_data_legal_entity' => [
      'registered_name' => 17,
      'legal_entity_type' => 18,
      'registered_number' => 19,
    ],
    'par_data_organisation' => [
      'organisation_name' => 2,
      'nation' => 9,
    ],
    'par_data_coordinated_business' => [
      'date_membership_began' => 15,
      'date_membership_ceased' => 16,
    ]
  ];

  /**
   * Defaultl values with need to be saved.
   */
  protected $defaults = [
    'par_data_premises' => [
      'address' => [
        'country_code' => 'GB',
      ],
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_member_upload_confirm';
  }

  protected function getColumns() {
    return $this->columns;
  }

  public function getColumn($entity_type, $field_name, $property = NULL) {
    $columns = $this->getColumns();

    if ($property) {
      return isset($columns[$entity_type][$field_name][$property]) ? $columns[$entity_type][$field_name][$property] -1 : NULL;
    }
    else {
      return isset($columns[$entity_type][$field_name]) ? $columns[$entity_type][$field_name] -1 : NULL;
    }
  }

  protected function getDefaults() {
    return $this->defaults;
  }

  /**
   * Getting for accessing the values from the CSV row.
   *
   * @param $row
   * @param $column
   *
   * @return string
   *   The value at the given index, a default value, or NULL
   */
  public function getRowValue($row, $column) {
    if (isset($row[$column]) && !empty($row[$column])) {
      return $row[$column];
    }
    else {
      return NULL;
    }
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataAdvice $par_data_advice
   *   The advice being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_member_upload');
    $members = $this->getFlowDataHandler()->getDefaultValues("coordinated_members", [], $cid);
    if (!empty($members)) {
      $count = count($members);
      $form['member_summary'] = [
        '#type' => 'markup',
        '#markup' => $this->formatPlural($count,
          '%count member has been found and is ready to be imported.',
          '%count members have been found and are ready to be imported.',
          ['%count' => $count]
        ),
        '#predix' => '<p>',
        '#suffix' => '</p>',
      ];

      $form['members'] = [
        '#type' => 'fieldset',
        '#tree' => TRUE,
        '#title' => 'Below is a list of members that require your attention.',
      ];

      // Process each row so that we can de-dupe and validate each.
      foreach ($members as $i => $member) {
        $row = $members[$i];
        $name = $this->getRowValue($row, $this->getColumn('par_data_organisation', 'organisation_name'));

        $properties = [];
        if (!empty($name)) {
          $properties = [
            'trading_name' => [
              'trading_name' => $name,
            ],
            'organisation_name' => [
              'organisation_name' => $name,
            ],
          ];
        }

        $existing_options = [];
        foreach ($properties as $group => $conditions) {
          $existing_options += \Drupal::entityManager()
            ->getStorage('par_data_organisation')
            ->loadByProperties($conditions);
        }

        $viewBuilder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

        $radio_options = [];
        foreach($existing_options as $option) {
          $option_view = $viewBuilder->view($option, 'title');

          $radio_options[$option->id()] = $this->renderMarkupField($option_view)['#markup'];
        }

        if (count($radio_options) >= 1) {
          $form['members'][$i]['existing_organisation'] = [
            '#type' => 'radios',
            '#title' => t('A match was found for @name', ['@name' => $name]),
            '#description' => t('Did you mean any of these organisations?'),
            '#options' => $radio_options + ['new' => "Add as a new organisation."],
            '#default_value' => current(array_keys($radio_options)),
            '#required' => TRUE,
          ];
        }
      }
    }
    else {
      $form['members']['summary'] = [
        '#type' => 'markup',
        '#description' => 'No members could be found in your CSV, please check the format is correct or contact the helpdesk for further assistance.',
      ];
    }

    // @TODO Automatically submit the form if there are no members that require attention.

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get all the members which need attention and extra processing.
    $attentions = $this->getFlowDataHandler()->getTempDataValue(["members"]);

    // Get the *full* partnership entity from the URL.
    $route_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_member_upload');
    $members = $this->getFlowDataHandler()->getTempDataValue("coordinated_members", $cid);
    foreach ($members as $i => $member) {
      $requires_attention = isset($attentions[$i]) ? $attentions[$i] : NULL;

      // Process the row.
      $this->processRow($route_partnership->id(), $member, $requires_attention);
    }

  }

  /**
   * Helper to process the raw CSV row data and map to entity values.
   *
   * @param $par_data_partnership_id
   *   The id of the partnership that this member is being attached to.
   * @param $member
   *   The raw CSV row data.
   * @param null $attention
   *   The attention points are raised to make the user aware of possible data manipulations or errors on input.
   */
  public function processRow($par_data_partnership_id, $member, $attention = NULL) {
    $existing = isset($attention['existing_organisation']) && $attention['existing_organisation'] !== 'new' ? $attention['existing_organisation'] : FALSE;

    $data = [];
    foreach ($this->getColumns() as $entity_type => $fields) {
      $data[$entity_type] = [];

      // Ignore the column index values.
      foreach ($fields as $field => $properties) {
        if (is_array($properties)) {
          $data[$entity_type][$field] = [];
          foreach ($properties as $property => $index) {
            $column = $this->getColumn($entity_type, $field, $property);
            $data[$entity_type][$field][$property] = $this->getRowValue($member, $column);
          }
        }
        else {
          $column = $this->getColumn($entity_type, $field);
          $data[$entity_type][$field] = $this->getRowValue($member, $column);
        }
      }
    }

    // Make sure we set the default values
    $data = $data + $this->getDefaults();

    // Send all the data off to the queue for processing.
    $this->addRowToQueue($par_data_partnership_id, $data, $existing);
  }

  /**
   * Helper to add the member to the queue to process intensive operations in the background.
   *
   * @param $par_data_partnership
   *   The partnership that this member is being attached to.
   * @param $member
   *   The raw CSV row data.
   * @param null $existing
   *   Whether the organisation being added exists or needs to be created.
   */
  public function addRowToQueue($par_data_partnership_id, $member, $existing) {
    // Generate the appropriate data array for passing to the queue.
    $data = [
      'partnership' => $par_data_partnership_id,
      'row' => $member,
      'existing' => $existing,
    ];

    try {
      $queue = \Drupal::queue('par_partnership_add_members', TRUE);
      $queue->createQueue();
      $queue->createItem($data);
    } catch (\Exception $e) {
      // @TODO Log this in a way that errors can be reported to the uploader.
    }

    drupal_set_message(t('The members are being processed, please check back shortly to see the membership list updated.'), 'status');
  }

}
