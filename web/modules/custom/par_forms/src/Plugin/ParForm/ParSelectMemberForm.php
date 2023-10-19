<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
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
class ParSelectMemberForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $organisations = [];

    // Set the organisation id for direct partnerships with only one organisation.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($par_data_partnership->isDirect()) {
        $organisations = $par_data_partnership->getOrganisation();
      }
      elseif ($par_data_partnership->isCoordinated()
        && $par_data_partnership->getMemberDisplay() === ParDataPartnership::MEMBER_DISPLAY_INTERNAL) {
        foreach ($par_data_partnership->getCoordinatedMember(FALSE, TRUE) as $coordinated_member) {
          $coordinated_organisations = $coordinated_member->getOrganisation();
          $organisations = $this->getParDataManager()->getEntitiesAsOptions($coordinated_organisations, $organisations);
        }
      }
    }

    $this->getFlowDataHandler()->setFormPermValue('partnership_organisations', $organisations);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $partnership_organisations = $this->getFlowDataHandler()->getFormPermValue('partnership_organisations');

    if (count($partnership_organisations) === 1) {
      $organisation = key($partnership_organisations);
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', $organisation);
    }
    // If the partnership is direct or there are not multiple members proceed to the next step.
    if (count($partnership_organisations) <= 1) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    // Initialize pager and get current page.
    $number_of_items = 10;
    $pager = $this->getUniquePager()->getPager('par_plugin_member_select_'.$index);
    $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($partnership_organisations), $number_of_items, $pager);

    // Split the items up into chunks:
    $chunks = array_chunk($partnership_organisations, $number_of_items, TRUE);
    $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

    $form['par_data_organisation_id'] = [
      '#type' => 'radios',
      '#title' => t('Choose the member to enforce'),
      '#options' => $chunk,
      '#default_value' => $this->getDefaultValuesByKey('par_data_organisation_id', $index, []),
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    $form['pager'] = [
      '#type' => 'pager',
      '#theme' => 'pagerer',
      '#element' => $pager,
      '#config' => [
        'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $organisation_id_key = $this->getElementKey('par_data_organisation_id');
    if (empty($form_state->getValue($organisation_id_key))) {
      $id_key = $this->getElementKey('par_data_organisation_id', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($organisation_id_key), $this->wrapErrorMessage('You must select an organisation.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
