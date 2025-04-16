<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Partnership member link.
 *
 * @ParForm(
 *   id = "member_link",
 *   title = @Translation("Enter the member list link.")
 * )
 */
class ParMemberListLink extends ParFormPluginBase {

  /**
   * Get the http client.
   */
  public function getHttpClient() {
    return \Drupal::httpClient();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface && $par_data_partnership->isCoordinated()) {
      $link = $par_data_partnership->getMemberLink();
      $this->setDefaultValuesByKey('member_link', $index, $link ? $link->toString() : '');
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // This form should only be displayed for coordinated partnerships.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if (!$par_data_partnership instanceof ParDataEntityInterface || !$par_data_partnership->isCoordinated()) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    $form['member_link'] = [
      '#type' => 'textfield',
      '#description' => 'e.g. https://example.com',
      '#title' => $this->t('Enter the link to the member list'),
      '#default_value' => $this->getDefaultValuesByKey('member_link', $index),
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $member_link_key = $this->getElementKey('member_link');

    try {
      $url = Url::fromUri($form_state->getValue($member_link_key));

      // Also check that this URL can be accessed.
      $request = $this->getHttpClient()->request('GET', $url->toString());
    }
    catch (\InvalidArgumentException) {
      $id_key = $this->getElementKey('member_link', $index, TRUE);
      $message = $this->wrapErrorMessage('Please enter a fully qualified URL for the member list.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($member_link_key), $message);
    }
    catch (GuzzleException) {
      $id_key = $this->getElementKey('member_link', $index, TRUE);
      $message = $this->wrapErrorMessage('The URL is not accessible.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($member_link_key), $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }
}
