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
 *   id = "link_contact",
 *   title = @Translation("Link contact to user.")
 * )
 */
class ParLinkContact extends ParFormPluginBase {

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
    $cid_contact_details = $this->getFlowNegotiator()->getFormKey('contact_details');
    $contact_email = $this->getFlowDataHandler()->getDefaultValues('email', NULL, $cid_contact_details);

    $account_options = [];

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $existing_account = $par_data_person->getUserAccount();

      if ($existing_account) {
        // If an account can be found that matches by e-mail address then we should use this.
        $account_options[$existing_account->id()] = 'Keep the existing account: ' . $existing_account->getEmail();
      }
    }

    if ($contact_email) {
      $users = $this->getParDataManager()->getEntitiesByProperty('user', 'mail', $contact_email);
      $new_account = !empty($users) ? current($users) : NULL;
    }
    if (isset($new_account) && (!isset($existing_account) || ($existing_account->id() !== $new_account->id()))) {
      // If an account can be found that matches by e-mail address then we should use this.
      $account_options[$new_account->id()] = 'Update to: ' . $new_account->getEmail();


    }
    if (!isset($new_account) && isset($existing_account) && $contact_email !== $existing_account->getEmail()) {
      // Add an option to allow the user account to be removed.
      // This can only be done if the new email address doesn't
      // match an account and there is an account already.
      $account_options[''] = "<i>Invite {$contact_email} to create a new account, {$existing_account->getEmail()} will no longer be able to access this person's authorities and organisations</i>";
    }

    // If an account can be found that matches by e-mail address then we should use this.
    if (!empty($email) && $user = current($this->getParDataManager()->getEntitiesByProperty('user', 'mail', $email))) {
      $account_options[$user->id()] = $user->getEmail();
    }

    $this->getFlowDataHandler()->setFormPermValue("user_accounts", $account_options);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $user_accounts = $this->getFlowDataHandler()->getDefaultValues('user_accounts', []);

    // If there is only one choice select it and go to the next page.
    if (count($user_accounts) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('user_id', key($user_accounts));
    }
    if (empty($user_accounts)) {
      $this->getFlowDataHandler()->setTempDataValue('user_id', []);
    }
    // If there isn't a choice go to the next page.
    if (count($user_accounts) <= 1) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['user_id'] = [
      '#type' => 'radios',
      '#title' => t('Choose a user account'),
      '#options' => $user_accounts,
      '#default_value' => $this->getDefaultValuesByKey("user_id", $cardinality, key($user_accounts)),
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
