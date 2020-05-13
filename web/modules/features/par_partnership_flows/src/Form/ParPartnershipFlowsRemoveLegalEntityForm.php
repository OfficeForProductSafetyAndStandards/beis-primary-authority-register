<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The partnership form for removing the legal entity.
 */
class ParPartnershipFlowsRemoveLegalEntityForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Are you sure you want to remove this legal entity?';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    // Set the legal entity if a value was found for this delta.
    if ($par_data_partnership && $par_data_legal_entity) {
      $legal_entities = $par_data_partnership->get('field_legal_entity')->getValue();
      // Note that this will only return the first instance of this legal_entity,
      // although this field should be unique so there shouldn't be more than one.
      $key = array_search($par_data_legal_entity->id(), array_column($legal_entities, 'target_id'));
      if ($key !== FALSE) {
        $this->getFlowDataHandler()->setFormPermValue('field_legal_entity_delta', $key);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $field_legal_entity_delta = NULL) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    $delta = $this->getFlowDataHandler()->getFormPermValue('field_legal_entity_delta');

    // If there is no legal entity skip this step.
    // @TODO Monitor PAR-1592. If a PR is submitted for that it will need
    // applying jto this method call too.
    if ($delta === NULL) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute('cancel'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    // Prohibit removing of the last legal entity.
    if ($par_data_partnership->get('field_legal_entity')->count() <= 1) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute('cancel'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['remove'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Are you sure you want to remove the legal entity @legal_entity from the @partnership?', ['@legal_entity' => $par_data_legal_entity->label(), '@partnership' => $par_data_partnership->label()]),
      '#attributes' => ['class' => ['remove-legal-entity', 'form-group']],
    ];

    $form['delta'] = [
      '#type' => 'hidden',
      '#value' => $delta,
    ];

    // Change the main button title to 'remove'.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Remove');

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');
    $delta = $this->getFlowDataHandler()->getTempDataValue('delta');

    // Remove the field delta.
    try {
      if (isset($delta)) {
        $par_data_partnership->get('field_legal_entity')->removeItem($delta);
      }
      else {
        throw new \InvalidArgumentException('No field delta has been provided.');
      }
    }
    catch (\InvalidArgumentException $e) {

    }

    // Don't save if there are no more legal entities.
    if (!$par_data_partnership->get('field_legal_entity')->isEmpty() && $par_data_partnership->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The %legal_entity could not be removed from the %field on partnership %partnership');
      $replacements = [
        '%legal_entity' => $par_data_legal_entity->label(),
        '%field' => $this->getFlowDataHandler()->getTempDataValue('field_legal_entity'),
        '%partnership' => $par_data_partnership->label(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
