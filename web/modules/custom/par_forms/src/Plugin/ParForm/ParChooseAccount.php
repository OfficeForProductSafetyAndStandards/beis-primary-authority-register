<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * User details display plugin.
 *
 * @ParForm(
 *   id = "choose_account",
 *   title = @Translation("Create a user account.")
 * )
 */
class ParChooseAccount extends ParFormPluginBase {

  const CREATE = 'new'; // Only for unrecognised users
  const IGNORE = 'none'; // Only for new users
  const DELETE = 'remove'; // Only for existing users

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * A helper method to extract only the user ids from the selections.
   *
   * @param mixed $value
   *   The data value to be checked for.
   *
   * @return mixed
   *   A user account if selected, otherwise null.
   */
  static function getUserAccount($value) {
    foreach ([self::DELETE, self::IGNORE, self::CREATE] as $opt) {
      if ($value === $opt) {
        return NULL;
      }
    }

    $account = $value ? User::load($value) : NULL;

    return $account;
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    $cid_contact_details = $this->getFlowNegotiator()->getFormKey('contact_details');
    $contact_email = $this->getFlowDataHandler()->getDefaultValues('email', NULL, $cid_contact_details);

    $account_options = [];

    // See if we can find a user related to the person being updated.
    if ($par_data_person) {
      $existing_account = $par_data_person->getUserAccount();

      if ($existing_account) {
        $account_options[$existing_account->id()] = 'Keep the existing account: ' . $existing_account->getEmail();
      }
    }

    // See if we can find a user related to the new email address being updated.
    if ($contact_email) {
      $users = $this->getParDataManager()->getEntitiesByProperty('user', 'mail', $contact_email);
      $new_account = !empty($users) ? current($users) : NULL;
    }
    if (isset($new_account) && (!isset($existing_account) || ($existing_account->id() !== $new_account->id()))) {
      $account_options[$new_account->id()] = 'Use the new account: ' . $new_account->getEmail();
    }

    // If the given e-mail address isn't recognised then give the option to invite the user.
    if (empty($new_account)) {
      $account_options[self::CREATE] = "Invite $contact_email to create a new account";
    }

    // If there is an existing user then we should give the option to remove it
    if (!empty($existing_account)) {
      $account_options[self::DELETE] = 'Remove the user account';
    }
    // If there is no existing user then we should give the option not to invite a new user.
    else {
      $account_options[self::IGNORE] = 'Do not create a user account';
    }

    // Allow any of the default options to be ignored.
    foreach ([self::DELETE, self::IGNORE, self::CREATE] as $opt) {
      if ($this->getFlowDataHandler()->getFormPermValue("ignore_account_option_$opt")) {
        unset($account_options[$opt]);
      }
    }

    $this->getFlowDataHandler()->setFormPermValue("account_options", $account_options);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $account_options = $this->getFlowDataHandler()->getFormPermValue('account_options');

    // If there is only one choice select it and go to the next page.
    if (count($account_options) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('account', key($account_options));
    }
    if (empty($account_options)) {
      $this->getFlowDataHandler()->setTempDataValue('account', FALSE);
    }
    // If there isn't a choice go to the next page.
    if (count($account_options) <= 1) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['account'] = [
      '#type' => 'radios',
      '#title' => t('Would you like this person to have a user account?'),
      '#options' => $account_options,
      '#default_value' => key($account_options),
      '#attributes' => ['class' => ['form-group']],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $account_key = $this->getElementKey('account');
    if (!$form_state->getValue($account_key)) {
      $id_key = $this->getElementKey('account', $cardinality, TRUE);
      $message = $this->wrapErrorMessage('You must choose an account option.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($account_key), $message);
    }

    return parent::validate($form, $form_state, $cardinality, $action);
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
