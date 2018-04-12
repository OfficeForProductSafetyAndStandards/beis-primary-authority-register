<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Url;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "authority_select",
 *   title = @Translation("Authority selection.")
 * )
 */
class ParAboutBusinessForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $user_authorities = [];

    // Get the authorities that the current user belongs to.
    if ($this->getFlowDataHandler()->currentUser()->isAuthenticated()) {
      $account = User::Load($this->getFlowDataHandler()->currentUser()->id());
      $authorities = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_authority', TRUE);
      $user_authorities = $this->getParDataManager()->getEntitiesAsOptions($authorities, $user_authorities);
    }

    $this->getFlowDataHandler()->setFormPermValue('user_authorities', $user_authorities);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Get all the allowed authorities.
    $user_authorities = $this->getFlowDataHandler()->getParameter('user_authorities');

    // If no suggestions were found cancel out of the journey.
    // @TODO Provide a selection mechanism for admin/helpdesk users acting on behalf of authority users.
    if (count($user_authorities) <= 0) {
      $message = $this->t('No authority count be found for user %user');
      $replacements = [
        '%user' => $this->getFlowDataHandler()->currentUser()->getDisplayName(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);

      $url = Url::fromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('cancel'), $this->getFlowDataHandler()->getParameters());
      return new RedirectResponse($url);
    }
    // If only one authority submit the form automatically and go to the next step.
    elseif (count($user_authorities) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_authority_id', key($authority_options));
      $url = Url::fromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getFlowDataHandler()->getParameters());
      return new RedirectResponse($url);
    }

    $form['par_data_authority_id'] = [
      '#type' => 'radios',
      '#title' => t('Choose a Primary Authority'),
      '#options' => $user_authorities,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValuesByKey("par_data_authority_id", $cardinality, NULL),
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validateForm(&$form_state, $cardinality = 1) {
    $authority_id_key = $this->getElementKey('par_data_authority_id');
    if (empty($form_state->getValue($authority_id_key))) {
      $form_state->setErrorByName($authority_id_key, $this->t('<a href="#edit-par_data_authority_id">You must select an authority.</a>'));
    }

    parent::validate($form_state, $cardinality);
  }
}
