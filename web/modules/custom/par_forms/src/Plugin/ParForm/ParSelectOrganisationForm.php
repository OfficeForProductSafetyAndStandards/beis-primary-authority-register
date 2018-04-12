<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Url;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "organisation_select",
 *   title = @Translation("Organisation selection.")
 * )
 */
class ParAboutBusinessForm extends ParFormPluginBase {

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

    $this->getFlowDataHandler()->setFormPermValue('user_organisations', $organisations);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Get all the allowed authorities.
    $user_organisations = $this->getFlowDataHandler()->getParameter('user_organisations');

    // If the partnership is direct or there is only one member go to the next step.
    if (count($user_organisations) === 1) {
      $organisation = current($user_organisations);
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', $organisation->id());
      $url = Url::fromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getFlowDataHandler()->getParameters());
      return new RedirectResponse($url);
    }

    // All remaining partnerships must be coordinated.
    // @TODO Allow this to be optional for partnerships with no listed members.
    if (empty($user_organisations)) {
      $form['no_members'] = [
        '#type' => 'markup',
        '#markup' => $this->t('Sorry but there are no members for this organisation.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];

      $this->getFlowNegotiator()->getFlow()->disableAction('next');
    }
    else {
      // Initialize pager and get current page.
      $number_of_items = 10;
      $current_page = pager_default_initialize(count($user_organisations), $number_of_items);

      // Split the items up into chunks:
      $chunks = array_chunk($user_organisations, $number_of_items, TRUE);

      $form['par_data_organisation_id'] = [
        '#type' => 'radios',
        '#title' => t('Choose the member to enforce'),
        '#options' => $chunks[$current_page],
        '#default_value' => $this->getFlowDataHandler()->getDefaultValuesByKey('par_data_organisation_id', $cardinality, []),
      ];

      $form['pager'] = [
        '#type' => 'pager',
        '#theme' => 'pagerer',
        '#element' => $cardinality,
        '#config' => [
          'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
        ],
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validateForm(&$form_state, $cardinality = 1) {
    $authority_id_key = $this->getElementKey('par_data_organisation_id');
    if (empty($form_state->getValue($authority_id_key))) {
      $form_state->setErrorByName($authority_id_key, $this->t('<a href="#edit-par_data_organisation_id">You must select an organisation.</a>'));
    }

    parent::validate($form_state, $cardinality);
  }
}
