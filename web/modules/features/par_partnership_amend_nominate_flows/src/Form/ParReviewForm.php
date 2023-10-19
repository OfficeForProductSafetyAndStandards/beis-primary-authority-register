<?php

namespace Drupal\par_partnership_amend_nominate_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipLegalEntity;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
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
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $partnership_legal_entities = $par_data_partnership?->getPartnershipLegalEntities();
    // Get only the partnership legal entities that are awaiting confirmation.
    $partnership_legal_entities = array_filter($partnership_legal_entities, function ($partnership_legal_entity) {
      return $partnership_legal_entity->getRawStatus() === 'confirmed_business';
    });

    $this->getFlowDataHandler()->setParameter('partnership_legal_entities', $partnership_legal_entities);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    // Get the legal entities to update.
    $partnership_legal_entities = $this->getFlowDataHandler()->getParameter('partnership_legal_entities') ?? [];

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
        '#value' => $this->t("Please confirm the proposed amendment to the @partnership.",
          ['@partnership' => $par_data_partnership->label()]
        ),
      ],
    ];

    // Get the list of legal entity names.
    $legal_entity_render_arrays = [];
    foreach ($partnership_legal_entities as $delta => $legal_entity) {
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
      '#title' => 'Please confirm this partnership amendment is suitable for nomination.',
      '#wrapper_attributes' => ['class' => 'govuk-!-margin-top-8'],
    ];

    // Change the main button title to 'remove'.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Confirm amendment');

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Ensure that the amendment has been confirmed.
    if (!$form_state->getValue('confirmation')) {
      $id = $this->getElementId('confirmation', $form);
      $form_state->setErrorByName($this->getElementName(['confirmation']), $this->wrapErrorMessage('Please confirm the amendments to this partnership.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Get the legal entities to update.
    $partnership_legal_entities = $this->getFlowDataHandler()->getParameter('partnership_legal_entities') ?? [];

    // Transfer the partnership.
    foreach ($partnership_legal_entities as $delta => $partnership_legal_entity) {
      if ($partnership_legal_entity instanceof ParDataPartnershipLegalEntity) {
        // Change the status of the legal entity.
        $saved = $partnership_legal_entity->nominate();

        // Unset the legal entity if it couldn't be saved.
        if (!$saved) {
          unset($partnership_legal_entities[$delta]);
        }
      }
    }

    if (!empty($partnership_legal_entities)) {
      // Dispatch a custom event for the whole amendment process,
      // this must be done after all the legal entities are nominated.
      $dispatcher = \Drupal::service('event_dispatcher');
      $event = new ParDataEvent($par_data_partnership);
      $action = ParDataEvent::customAction($par_data_partnership->getEntityTypeId(), 'amendment_nominated');
      $dispatcher->dispatch($event, $action);
    }
    else {
      $message = $this->t('The partnership amendments for the @partnership could not be nominated, there were no legal entities to be updated.');
      $replacements = [
        '@partnership' => $par_data_partnership->label(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    $this->getFlowDataHandler()->deleteStore();
  }

}
