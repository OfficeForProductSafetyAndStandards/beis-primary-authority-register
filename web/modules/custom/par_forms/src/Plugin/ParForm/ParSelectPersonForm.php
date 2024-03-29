<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Contact selection form plugin.
 *
 * @ParForm(
 *   id = "person_select",
 *   title = @Translation("Person selection.")
 * )
 */
class ParSelectPersonForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $user_people = [];

    // Get the person entities that represent a given user.
    /** @var \Drupal\user\Entity\User $account */
    $account = $this->getFlowDataHandler()->getParameter('user');
    if ($account && $account->isAuthenticated() && $people = $this->getParDataManager()->getUserPeople($account)) {
      // Ignore deleted person accounts.
      $people = array_filter($people, function ($person) {
        return !$person->isDeleted();
      });

      $user_people = $this->getParDataManager()->getEntitiesAsOptions($people, $user_people, 'summary');
    }

    // Pre-select a person if they have already been choosen
    // and they are in the list of available choices.
    if ($par_data_person && isset($user_people[$par_data_person->id()])) {
      $this->getFlowDataHandler()->setFormPermValue('user_person', $par_data_person->id());
    }

    $this->getFlowDataHandler()->setFormPermValue('user_people', $user_people);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $user_people = $this->getFlowDataHandler()->getFormPermValue('user_people');

    // If there are no people we should create a new record.
    if (count($user_people) <= 0) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }
    // If there is only one person to choose submit the form automatically and go to the next step.
    elseif (count($user_people) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('user_person', key($user_people));
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "We have found multiple contact records for you, please choose which one you would like to update.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['user_person'] = [
      '#type' => 'radios',
      '#title' => t('Choose which contact record you would like to update'),
      '#title_tag' => 'h2',
      '#options' => $user_people,
      '#default_value' => $this->getDefaultValuesByKey("user_person", $index, NULL),
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $person_id_key = $this->getElementKey('user_person');
    if (empty($form_state->getValue($person_id_key))) {
      $id_key = $this->getElementKey('user_person', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($person_id_key), $this->wrapErrorMessage('You must select a contact record.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
