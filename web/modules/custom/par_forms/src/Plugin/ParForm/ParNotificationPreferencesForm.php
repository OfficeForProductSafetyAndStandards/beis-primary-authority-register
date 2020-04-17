<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\Xss;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Contact details form plugin.
 *
 * @ParForm(
 *   id = "notification_preferences",
 *   title = @Translation("Notification preferences form.")
 * )
 */
class ParNotificationPreferencesForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['notes', 'par_data_person', 'communication_notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter any communication notes that are relevant to this contact.'
    ]],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    // Get all message types.
    $message_templates = \Drupal::service('entity_type.manager')->getStorage('message_template')->loadMultiple();
    $account = $this->getFlowDataHandler()->getParameter('user');

    $notification_options = [];
    foreach ($message_templates as $message_template) {
      $description = Xss::filter($message_template->getDescription());
      if ($account && $account->hasPermission("receive {$message_template->id()} notification")) {
        $notification_options[$message_template->id()] = "<span>{$message_template->label()}</span><p class='form-hint'>{$description}</p>";
      }
    }

    $this->getFlowDataHandler()->setFormPermValue('notification_option', $notification_options);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $notification_options = $this->getFlowDataHandler()->getFormPermValue('notification_option');
    if (count($notification_options) <= 0) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['help'] = [
      '#markup' => '<p>As a primary contact you will always receive transactional notifications that are relevant to your partnerships, authorities or organisations.</p><p class="form-group">However, you can also choose to receive these notifications as a secondary contact for the partnership or as a member of the authority or organisation it relates to.</p>',
    ];

    $form['preferred_contact'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Choose which additional notifications to receive'),
      '#description' => '<p>Primary contacts will always be sent these notifications.</p>',
      '#options' => $notification_options,
      '#default_value' => $this->getDefaultValuesByKey('notification_preferences', $cardinality, []),
      '#return_value' => 'on',
    ];

    return $form;
  }
}
