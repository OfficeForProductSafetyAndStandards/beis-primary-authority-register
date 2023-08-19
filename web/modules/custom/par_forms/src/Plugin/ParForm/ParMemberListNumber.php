<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Partnership member count.
 *
 * @ParForm(
 *   id = "member_number",
 *   title = @Translation("Choose the number of members.")
 * )
 */
class ParMemberListNumber extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface && $par_data_partnership->isCoordinated()) {
      $this->setDefaultValuesByKey('number_members', $index, $par_data_partnership->numberOfMembers());
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

    $form['number_members'] = [
      '#type' => 'number',
      '#title' => $this->t('Enter the number of members in the list'),
      '#description' => 'Coordinated member lists support between 1 and 10,000 members.',
      '#default_value' => $this->getDefaultValuesByKey('number_members', $index),
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $number_member_key = $this->getElementKey('number_members');

    $number = $form_state->getValue($number_member_key);
    if ((int) $number < 1 || (int) $number > 100000) {
      $id_key = $this->getElementKey('number_members', $index, TRUE);
      $message = $this->wrapErrorMessage('The number of members needs to be between 1 and 100,000.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($number_member_key), $message);
    }

    parent::validate($form, $form_state, $index, $action);
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
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
