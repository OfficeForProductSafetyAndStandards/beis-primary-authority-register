<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Contact merging form plugin.
 *
 * @ParForm(
 *   id = "person_merge",
 *   title = @Translation("Person selection for merging.")
 * )
 */
class ParMergePersonForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $contacts = $this->getFlowDataHandler()->getParameter('contacts');

    if ($contacts && current($contacts) instanceof ParDataEntityInterface) {
      $contact_options = $this->getParDataManager()->getEntitiesAsOptions($contacts, [], 'summary');

      $this->getFlowDataHandler()->setFormPermValue('contact_options', $contact_options);
      $this->setDefaultValuesByKey("contacts", $index, array_keys($contact_options));
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "Combining contact records will update the user details.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['contacts'] = [
      '#type' => 'checkboxes',
      '#title' => t('Choose which contact records you would like to combine'),
      '#title_tag' => 'h2',
      '#options' => $this->getDefaultValuesByKey("contact_options", $index, []),
      '#default_value' => $this->getDefaultValuesByKey("contacts", $index, []),
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $person_id_key = $this->getElementKey('contacts');
    $values = array_filter($form_state->getValue($person_id_key));
    if (count($values) <= 1) {
      $id_key = $this->getElementKey('contacts', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($person_id_key), $this->wrapErrorMessage('You must select at least two contact records to merge.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
