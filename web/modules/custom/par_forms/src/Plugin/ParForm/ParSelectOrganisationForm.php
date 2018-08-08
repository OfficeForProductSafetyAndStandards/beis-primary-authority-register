<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "member_select",
 *   title = @Translation("Member organisation selection.")
 * )
 */
class ParSelectOrganisationForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $organisations = [];

    // Set the organisation id for direct partnerships with only one organisation.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($par_data_partnership->isDirect()) {
        $organisations = $par_data_partnership->getOrganisation();
      }
      elseif ($par_data_partnership->isCoordinated()) {
        foreach ($par_data_partnership->getCoordinatedMember() as $coordinated_member) {
          $coordinated_organisations = $coordinated_member->getOrganisation();
          $organisations = $this->getParDataManager()->getEntitiesAsOptions($coordinated_organisations, $organisations);
        }
      }
    }

    $this->getFlowDataHandler()->setFormPermValue('partnership_organisations', $organisations);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get all the allowed authorities.
    $partnership_organisations = $this->getFlowDataHandler()->getFormPermValue('partnership_organisations');

    // If the partnership is direct or there are not multiple members proceed to the next step.
    if (count($partnership_organisations) <= 1) {
      if (!empty($partnership_organisations)) {
        $organisation = current($partnership_organisations);
        $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', $organisation->id());
      }
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    // Initialize pager and get current page.
    $number_of_items = 10;
    $current_page = pager_default_initialize(count($partnership_organisations), $number_of_items);

    // Split the items up into chunks:
    $chunks = array_chunk($partnership_organisations, $number_of_items, TRUE);

    $form['par_data_organisation_id'] = [
      '#type' => 'radios',
      '#title' => t('Choose the member to enforce'),
      '#options' => $chunks[$current_page],
      '#default_value' => $this->getDefaultValuesByKey('par_data_organisation_id', $cardinality, []),
    ];

    $form['pager'] = [
      '#type' => 'pager',
      '#theme' => 'pagerer',
      '#element' => $cardinality,
      '#config' => [
        'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $organisation_id_key = $this->getElementKey('par_data_organisation_id');
    if (empty($form_state->getValue($organisation_id_key))) {
      $form_state->setErrorByName($organisation_id_key, $this->t('<a href="#edit-par_data_organisation_id">You must select an organisation.</a>'));
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
