<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Select Legal Entities form.
 *
 * @ParForm(
 *   id = "select_legal_entities",
 *   title = @Translation("Select legal entities for partnership form.")
 * )
 */
class ParSelectLegalEntitiesForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $this->getflowDataHandler()->getParameter('par_data_organisation');

    // Get legal entities on the PAR Organisation.
    $organisation_legal_entities = $par_data_organisation->getLegalEntity();
    // Build options for legal entities forms.
    $radio_options = $this->getParDataManager()->getEntitiesAsOptions($organisation_legal_entities, [], 'summary');
    $this->getFlowDataHandler()->setFormPermValue('legal_entity_options', $radio_options);

    $partnership_legal_entities = $par_data_partnership->getLegalEntity();
    $partnership_ids = [];
    foreach ($partnership_legal_entities as $partnership_legal_entity) {
      $partnership_ids[] = $partnership_legal_entity->id();
    }
    $this->getFlowDataHandler()
      ->setTempDataValue('partnership_legal_entities', $partnership_ids);

    if ($par_data_partnership) {
      $this->getFlowDataHandler()->setFormPermValue('coordinated_partnership', $par_data_partnership->isCoordinated());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $radio_options = $this->getFlowDataHandler()->getFormPermValue('legal_entity_options');

    // Retrieve legal entities on the partnership.
    $partnership_legal_entities = $this->getFlowDataHandler()
      ->getTempDataValue('partnership_legal_entities');

    // Intro text.
    $form['legal_entity_intro'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('What is a legal entity?'),
      ],
      'text' => [
        '#type' => 'markup',
        '#markup' => $this->t('A legal entity is any kind of individual or organisation that has legal standing. This can include a
limited company or partnership, as well as other types of organisations such as trusts and charities.'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ],
    ];
    if ($this->getFlowDataHandler()->getFormPermValue('coordinated_partnership')) {
      $form['legal_entity_intro']['note'] = [
        '#type' => 'markup',
        '#markup' => '<div class="form-group notice">
              <i class="icon icon-important"><span class="visually-hidden">Warning</span></i>
              <strong class="bold-small">Please select the legal entities for the co-ordinator not the members covered by this partnership.</strong>
            </div>',
      ];
    }

    // Checkboxes for legal entities.
    $form['field_legal_entity'] = [
      '#type' => 'checkboxes',
      '#attributes' => ['class' => ['govuk-form-group']],
      '#title' => t('Choose which legal entities this partnership relates to'),
      '#options' => $radio_options,
      // Automatically check all legal entities if no form data is found.
      '#default_value' => $this->getDefaultValuesByKey('field_legal_entity', $index, $partnership_legal_entities),
    ];

    // A note to the user that they can add a new legal entity on the next step.
    $form['legal_entity_add_more_info'] = [
      '#type' => 'container',
      '#title' => $this->t('Additional legal entities'),
      'text' => [
        '#type' => 'markup',
        '#markup' => $this->t("In the next step you can add one or more legal entities to this partnership."),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ],
    ];

    return $form;

  }

}
