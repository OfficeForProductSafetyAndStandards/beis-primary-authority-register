<?php

namespace Drupal\par_partnership_amend_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * The form for reviewing any changes before they are made.
 */
class ParReviewForm extends ParBaseForm {

  protected $pageTitle = 'Confirm this amendment';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData() {
    // Nothing to load at the moment.
    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ?ParDataPartnership $par_data_partnership = NULL) {
    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataLegalEntity[] $par_data_legal_entities */

    // Display the authorities.
    $form['partnership'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Partnership'),
      ],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("Please review the proposed amendment to the @partnership.",
          ['@partnership' => $par_data_partnership->label()]
        ),
      ],
    ];

    // Get the list of legal entity names.
    $legal_entity_render_arrays = [];
    foreach ($par_data_legal_entities as $delta => $legal_entity) {
      $view_builder = \Drupal::entityTypeManager()
        ->getViewBuilder($legal_entity->getEntityTypeId());
      $legal_entity_render_arrays[$delta] = $view_builder
        ->view($legal_entity, 'summary');
    }

    // Display the legal entities.
    $form['legal_entities'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Legal Entities'),
      ],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("The following legal entities will be added to the partnership:"),
      ],
      'list' => [
        '#theme' => 'item_list',
        '#items' => $legal_entity_render_arrays,
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
      ],
    ];

    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => 'Please check everything is correct, once you submit this amendment the organisation will be asked to confirm the details.',
      '#wrapper_attributes' => ['class' => ['govuk-!-margin-bottom-4', 'govuk-!-margin-top-4']],
    ];

    // Change the main button title to 'remove'.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Submit amendment');

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   *
   */
  public function createEntities() {
    // Set the data for the legal entities.
    $legal_entity_prefix = ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity';
    $legal_cid = $this->getFlowNegotiator()->getFormKey('legal_entity_add');
    $legal_entities = $this->getFlowDataHandler()->getTempDataValue($legal_entity_prefix, $legal_cid) ?: [];
    $par_data_legal_entities = [];
    // Loop through all stored values and create the legal entity.
    foreach ($legal_entities as $delta => $legal_entity) {
      // Creating the legal entity and using ParDataLegalEntity::lookup() allows
      // information to be retrieved from a registered source like Companies House.
      if ($legal_entity['registry'] == 'ch_as_different_type') {
        $par_data_legal_entities[$delta] = ParDataLegalEntity::create([
          'registry' => $legal_entity['registry'],
          'registered_name' => $legal_entity['ch_as_different_type']['legal_entity_name'],
          'registered_number' => $legal_entity['ch_as_different_type']['legal_entity_number'],
          'legal_entity_type' => 'special_org',
        ]);
      }
      else {
        $par_data_legal_entities[$delta] = ParDataLegalEntity::create([
          'registry' => $this->getFlowDataHandler()->getTempDataValue([$legal_entity_prefix, $delta, 'registry'], $legal_cid),
          'registered_name' => $this->getFlowDataHandler()->getTempDataValue([$legal_entity_prefix, $delta, 'unregistered', 'legal_entity_name'], $legal_cid),
          'registered_number' => $this->getFlowDataHandler()->getTempDataValue([$legal_entity_prefix, $delta, 'registered', 'legal_entity_number'], $legal_cid),
          'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $delta, 'unregistered', 'legal_entity_type'], $legal_cid),
        ]);
      }
      $par_data_legal_entities[$delta]->lookup();

      // Ensure they are ordered correctly.
      ksort($par_data_legal_entities);
    }

    return [
      'par_data_legal_entities' => $par_data_legal_entities,
    ];
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataLegalEntity[] $par_data_legal_entities */

    // Ensure that the amendment has been confirmed.
    if (!$form_state->getValue('confirmation')) {
      $id = $this->getElementId('confirmation', $form);
      $form_state->setErrorByName($this->getElementName(['confirmation']), $this->wrapErrorMessage('Please confirm the amendment to this partnership.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the partnership.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataLegalEntity[] $par_data_legal_entities */

    // Transfer the partnership.
    $partnership_legal_entities = [];
    foreach ($par_data_legal_entities as $delta => $par_data_legal_entity) {
      if ($par_data_partnership instanceof ParDataPartnership && $par_data_legal_entity instanceof ParDataLegalEntity) {
        $partnership_legal_entity = $par_data_partnership->addLegalEntity($par_data_legal_entity);
        try {
          // Only update the status if the partnership legal entity is new.
          $default_status = $partnership_legal_entity->getTypeEntity()->getDefaultStatus();
          if (!$partnership_legal_entity->getRawStatus() ||
            $partnership_legal_entity->getRawStatus() === $default_status) {

            // Setting the status before the partnership legal entity has been
            // saved to the partnership needs to bypass transition checks.
            $partnership_legal_entity->setParStatus('confirmed_authority', TRUE);
            $partnership_legal_entity->save();
            $partnership_legal_entities[$delta] = $partnership_legal_entity;
          }
        }
        catch (ParDataException) {

        }
      }
    }

    if (!empty($partnership_legal_entities)) {
      $par_data_partnership->save();

      // Dispatch a custom event for the whole amendment process,
      // this must be done after they are saved to the partnership.
      $dispatcher = \Drupal::service('event_dispatcher');
      $event = new ParDataEvent($par_data_partnership);
      $action = ParDataEvent::customAction($par_data_partnership->getEntityTypeId(), 'amendment_submitted');
      $dispatcher->dispatch($event, $action);
    }
    // Log an error.
    else {
      $message = $this->t('The partnership amendment for the @partnership could not be saved, @count legal entities could not be added.');
      $replacements = [
        '@partnership' => $par_data_partnership->label(),
        '@count' => count($par_data_legal_entities) - count($partnership_legal_entities),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    $this->getFlowDataHandler()->deleteStore();
  }

}
