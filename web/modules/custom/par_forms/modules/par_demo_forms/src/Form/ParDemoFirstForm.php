<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Form\ParBaseForm;
use Drupal\Core\Entity\EntityConstraintViolationListInterface;
use Drupal\node\NodeInterface;

/**
 * A demo multi-step form.
 */
class ParDemoFirstForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'example';

  public function getFormId() {
    return 'par_demo_first';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    if ($node) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      // You must remember to set the state in the same way
      // on all forms in any given flow.
      $this->setState("edit:{$node->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      $this->loadDataValue('name', $node->getTitle());
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => $this->getDefaultValues('name'),
      '#required' => TRUE,
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * We should only perform form sanitisation here.
   *
   * The real validation is handled at the entity validation layer.
   * @see https://www.drupal.org/docs/8/api/entity-validation-api/entity-validation-api-overview
   *
   * @example
   * // We can sanitise the form values to correct user input.
   * $name = $form_state->getValue('name');
   * $form_state->setValue('name', ucwords(strtolower($name)));
   *
   * @example
   * // If our node
   * $all_flow_data = $this->getAllTempData();
   * $data = [
   *   'type' => 'article',
   *   'title' => 'Test',
   *   'body' => $all_flow_data['name'],
   *   'field_tags' => '', // Empty value.
   *   'uid' => $this->currentUser->id(),
   * ];
   * $node = \Drupal::entityManager()
   *   ->getStorage('node')
   *   ->create($data);
   *
   * // The benefit of calling validation on the entity and then filtering
   * // to certain fields is we can take account of access conditions.
   * $violations = $node->validate()
   *   ->filterByFieldAccess()
   *   ->getByFields([
   *     'body',
   *     'field_tags',
   *   ]);
   * $this->setFieldViolations('name', $form_state, $violations);
   * $this->setFieldViolations('field_tags', $form_state, $violations);
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // We can perform other logic here to sanitise
    // or validate the data.

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @example
   * // The parent submit handler _must_ run first.
   * parent::submitForm($form, $form_state);
   *
   * $all_flow_data = $this->getAllTempData();
   * $data = [
   *   'type' => 'article',
   *   'title' => $all_flow_data['name'],
   *   'uid' => $this->currentUser->id(),
   * ];
   * $node = \Drupal::entityManager()
   *   ->getStorage('node')
   *   ->create($data);
   *
   * // Make sure to only delete the store if the data was correctly saved.
   * if ($node->save()) {
   *   $this->deleteStore();
   * }
   *
   * @example
   * // The parent submit handler _must_ run first.
   * parent::submitForm($form, $form_state);
   *
   * // We can also redirect to a given path if the route name is unknown.
   * $current_path = \Drupal::service('path.current')->getPath();
   * $url_object = \Drupal::service('path.validator')->getUrlIfValid('redirect-to-given-path');
   * $route_name = $url_object->getRouteName();
   * $route_parameters = $url_object->getrouteParameters();
   *
   * $form_state->setRedirect($route_name, $route_parameters);
   *
   * @example
   * // The parent submit handler _must_ run first.
   * parent::submitForm($form, $form_state);
   *
   * // We can also redirect to a view page if we know the view & view page machine name.
   * $route_name = 'view.VIEW_MACHINE_NAME.PAGE_MACHINENAME';
   * $route_parameters = [
   *   'VIEW_CONTEXTUAL_FILTER_ENTITY_ID' => 1
   * ];
   * $form_state->setRedirect($route_name, $route_parameters);
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // We can perform other logic here to save the data.
    // The base class will store all the data to the
    // temporary store.

    // After each child formo we go back to the overview.
    $form_state->setRedirect('par_demo_forms.overview', $this->getRouteParams());
  }
}
