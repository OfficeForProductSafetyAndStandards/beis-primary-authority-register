<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Partnership member confirmation link.
 *
 * @ParForm(
 *   id = "confirm_partnership_members",
 *   title = @Translation("Confirm the parntership members journey.")
 * )
 */
class ParConfirmMembers extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface && $par_data_partnership->isCoordinated()) {
      // Whether the member list needs updating.
      $this->getFlowDataHandler()->setFormPermValue("needs_updating", $par_data_partnership->memberListNeedsUpdating());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // This form should only be displayed for coordinated partnerships that need updating.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $update = $this->getDefaultValuesByKey('needs_updating', $index, TRUE);

    if ($par_data_partnership instanceof ParDataEntityInterface
      && $par_data_partnership->isCoordinated()
      && $update) {

      // Confirmation link.
      try {
        $member_url = $this->getFlowNegotiator()->getFlow('member_list_update')->start(4);
      }
      catch (ParFlowException $e) {

      }
      if (isset($member_url) && $member_url instanceof Url) {
        $form['confirm'] = [
          '#type' => 'container',
          '#attributes' => ['class' => 'govuk-form-group'],
        ];
        $form['confirm']['warning'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => ['class' => ['govuk-warning-text']],
          'icon' => [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => '!',
            '#attributes' => [
              'class' => ['govuk-warning-text__icon'],
              'aria-hidden' => 'true',
            ],
          ],
          'strong' => [
            '#type' => 'html_tag',
            '#tag' => 'strong',
            '#value' => $this->t('It is a statutory requirement to keep this member list regularly up-to-date.'),
            '#attributes' => ['class' => ['govuk-warning-text__text']],
            'message' => [
              '#type' => 'html_tag',
              '#tag' => 'span',
              '#value' => $this->t('Warning'),
              '#attributes' => ['class' => ['govuk-warning-text__assistive']],
            ],
          ],
        ];
        $form['confirm']['link'] = [
          '#type' => 'link',
          '#title' => 'please confirm the member list is accurate',
          '#url' => $member_url,
          '#attributes' => ['class' => ['confirm-member-list-link']],
        ];
      }
    }

    return $form;
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
