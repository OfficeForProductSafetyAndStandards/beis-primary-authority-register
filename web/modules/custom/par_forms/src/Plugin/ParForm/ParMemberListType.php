<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Partnership member display.
 *
 * @ParForm(
 *   id = "member_list_type",
 *   title = @Translation("Choose the type of member list.")
 * )
 */
class ParMemberListType extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface && $par_data_partnership->isCoordinated()) {
      // Set the type of list.
      $this->setDefaultValuesByKey('member_list_type', $index, $par_data_partnership->getMemberDisplay());

      $list_options = $par_data_partnership->getTypeEntity()
        ->getAllowedValues('member_display');
      $this->getFlowDataHandler()->setFormPermValue("list_type_options", $list_options);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // This form should only be displayed for coordinated partnerships.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if (!$par_data_partnership instanceof ParDataEntityInterface || !$par_data_partnership->isCoordinated()) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    // Add descriptions to the option labels.
    $list_options = $this->getFlowDataHandler()->getFormPermValue("list_type_options");
    array_walk($list_options, [$this, 'getOptionLabel']);

    $form['type'] = [
      '#type' => 'radios',
      '#title' => t('Choose which format to display the member list in'),
      '#title_tag' => 'h2',
      '#options' => $list_options,
      '#default_value' => $this->getDefaultValuesByKey('member_list_type', $index, NULL),
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    return $form;
  }

  /**
   * Get option label.
   */
  public function getOptionLabel(&$item, $key) {
    switch ($key) {
      case ParDataPartnership::MEMBER_DISPLAY_INTERNAL:
        $description = "Upload the list securely to the Primary Authority Register.
            <br><br>This list will remain private within the Register and will make
            it easier for Local Authorities to search the partnership without submitting
            requests for information to the co-ordinator.";

        break;

      case ParDataPartnership::MEMBER_DISPLAY_EXTERNAL:
        $description = "This list must be publicly available to enable Local Authorities
          to access the member list without submitting requests for information to
          the co-ordinator.";

        break;

      case ParDataPartnership::MEMBER_DISPLAY_REQUEST:
        $description = "Where the co-ordinator receives a request for a copy of its
          Primary Authority Membership List from its primary authority, an enforcing
          authority, a supporting regulator, or from the Secretary of State, the co-ordinator
          must make the copy available as soon as reasonably practicable and, in any
          event, not later than the third working day after the date of receiving
          the request at no charge.";

        break;

    }

    $label = [
      '#type' => 'container',
      '#attributes' => ['class' => ['list-type-label']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $item,
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $description,
      ],
    ];

    $item = \Drupal::service('renderer')->render($label);
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }

}
