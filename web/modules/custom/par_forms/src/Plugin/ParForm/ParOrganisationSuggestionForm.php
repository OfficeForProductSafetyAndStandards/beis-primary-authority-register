<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * About partnership form plugin.
 *
 * @ParForm(
 *   id = "organisation_suggestion",
 *   title = @Translation("De-dupe organisation form.")
 * )
 */
class ParOrganisationSuggestionForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $cid = $this->getFlowNegotiator()->getFormKey('organisation_select');
    $search_query = $this->getFlowDataHandler()->getDefaultValues('name', '', $cid);
    $this->getFlowDataHandler()->setFormPermValue('organisation_select_search_query', $search_query);

    // Go to previous step if search query is not specified.
    if ($search_query) {
      $conditions = [
        'name' => [
          'OR' => [
            ['organisation_name', ' ' . $search_query, 'CONTAINS'],
            ['organisation_name', $search_query . ' ', 'CONTAINS'],
            ['organisation_name', $search_query, 'STARTS_WITH'],
            ['trading_name', ' ' . $search_query, 'CONTAINS'],
            ['trading_name', $search_query . ' ', 'CONTAINS'],
            ['trading_name', $search_query, 'STARTS_WITH'],
          ],
        ],
      ];

      $organisations = $this->getParDataManager()->getEntitiesByQuery('par_data_organisation', $conditions, 10, 'id');

      if (count($organisations) <= 0) {
        $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', 'new');
      }
      else {
        $radio_options = [];
        foreach ($organisations as $organisation) {
          // As per PAR-1348 coordinated organisations can now be referenced.
          $radio_options = $this->getParDataManager()->getEntitiesAsOptions([$organisation], $radio_options, 'summary');
        }
        $this->getFlowDataHandler()->setFormPermValue('organisation_options', $radio_options);
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Go back to the previous page if there's no search term.
    if (!$this->getFlowDataHandler()->getFormPermValue('organisation_select_search_query')) {
      $url = $this->getFlowNegotiator()->getFlow()->progress(ParFlow::BACK_STEP);
      return new RedirectResponse($url->toString());
    }

    if ($radio_options = $this->getFlowDataHandler()->getFormPermValue('organisation_options')) {
      $form['par_data_organisation_id'] = [
        '#type' => 'radios',
        '#title' => t('Choose an existing organisation or create a new organisation'),
        '#title_tag' => 'h2',
        '#options' => $radio_options + ['new' => "no, the organisation is not currently in a partnership with any other primary authority"],
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', 'new'),
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }
    else {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    return $form;
  }

}
