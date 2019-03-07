<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * User details display plugin.
 *
 * @ParForm(
 *   id = "create_account",
 *   title = @Translation("Create a user account.")
 * )
 */
class ParCreateAccount extends ParFormPluginBase {

  const CREATE = ['yes', 'existing'];
  const IGNORE = ['no'];

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    $options = [
      'yes' => "Yes, create an account for this person",
      'no' => "No, don't give this person a user account",
    ];

    if ($account = $this->getFlowDataHandler()->getParameter('user')) {
      $this->getFlowDataHandler()->setFormPermValue("existing_account", TRUE);

      $options = [
        'existing' => "Yes, keep {$account->getEmail()}'s existing user account",
        'no' => "No, don't give this person a user account",
      ];
    }

    $this->getFlowDataHandler()->setFormPermValue("account_options", $options);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $account_options = $this->getFlowDataHandler()->getFormPermValue('account_options');

    // If there is an existing account then for now we won't give the option to remove this.
    if ($this->getFlowDataHandler()->getDefaultValues("existing_account", FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['create_account'] = [
      '#type' => 'radios',
      '#title' => t('Would you like this person to have a user account?'),
      '#options' => $account_options,
      '#default_value' => 'yes',
      '#attributes' => ['class' => ['form-group']],
    ];

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
