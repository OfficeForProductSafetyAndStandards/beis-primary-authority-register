<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
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
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface && $par_data_partnership->isCoordinated()) {
      // Whether the member list needs updating.
      $this->getFlowDataHandler()->setFormPermValue("needs_updating", $par_data_partnership->memberListNeedsUpdating());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // This form should only be displayed for coordinated partnerships that need updating.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $update = $this->getDefaultValuesByKey('needs_updating', $cardinality, TRUE);

    if ($par_data_partnership instanceof ParDataEntityInterface
      && $par_data_partnership->isCoordinated()
      && $update) {

      // Confirmation link
      try {
        $member_url = $this->getFlowNegotiator()->getFlow('member_list_update')->start(4);
      } catch (ParFlowException $e) {

      }
      if (isset($member_url) && $member_url instanceof Url) {
        $form['confirm'] = [
          '#type' => 'container',
          '#attributes' => ['class' => 'form-group'],
        ];
        $form['confirm']['info'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('It is a statutory requirement to keep this member list regularly up-to-date.'),
          '#attributes' => ['class' => ['govuk-body', 'govuk-!-font-weight-bold']],
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
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}