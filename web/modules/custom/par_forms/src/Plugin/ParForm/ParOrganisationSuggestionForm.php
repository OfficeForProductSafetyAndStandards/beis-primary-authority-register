<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * About partnership form plugin.
 *
 * @ParForm(
 *   id = "organisation_select",
 *   title = @Translation("De-dupe organisation form.")
 * )
 */
class ParOrganisationSuggestionForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $formItems = [
    'par_data_partnership:organisation' => [
      'comments' => 'about_business',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $cid = $this->getFlowNegotiator()->getFormKey('organisation_select');
    $search_query = $this->getFlowDataHandler()->getDefaultValues('name', '', $cid);
    $this->getFlowDataHandler()->setFormPermValue('organisation_select_search_query', $search_query);

    // Go to previous step if search query is not specified.
    if ($search_query) {
      $conditions = [
        'name' => [
          'OR' => [
            ['organisation_name', $search_query, 'STARTS_WITH'],
            ['trading_name', $search_query, 'STARTS_WITH'],
          ]
        ],
      ];

      $organisations = $this->getParDataManager()->getEntitiesByQuery('par_data_organisation', $conditions, 10);

      $radio_options = [];

      foreach ($organisations as $organisation) {
        // PAR-1172 Do not display organisations in coordinated partnerships.
        if (!$organisation->isCoordinatedMember()) {
          $label = $this->renderSection('Organisation', $organisation, [
            'organisation_name' => 'summary',
            'field_premises' => 'summary',
          ], ['edit-entity'], FALSE, TRUE);
          $radio_options[$organisation->id()] = render($label);
        }
      }

      if (count($radio_options) <= 0) {
        $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', 'new');
      }
      else {
        $this->getFlowDataHandler()->setFormPermValue('organisation_select_options', $radio_options);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Go back to the previous page if there's no search term.
    if (!$this->getFlowDataHandler()->getFormPermValue('organisation_select_search_query')) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getPrevRoute(), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    if ($organisation_selection_options = $this->getFlowDataHandler()->getFormPermValue('organisation_select_options')) {
      $form['par_data_organisation_id'] = [
        '#type' => 'radios',
        '#title' => t('Choose an existing organisation or create a new organisation'),
        '#options' => $organisation_selection_options + ['new' => "no, the organisation is not currently in a partnership with any other primary authority"],
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', 'new'),
      ];
    }
    else {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute(), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    return $form;
  }
}
