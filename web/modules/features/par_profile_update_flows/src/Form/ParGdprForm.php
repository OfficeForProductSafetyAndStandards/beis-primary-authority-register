<?php

namespace Drupal\par_profile_update_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for the premises details.
 */
class ParGdprForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm acceptance of data policy';

  /**
   * Redirect current user.
   */
  public function redirectCurrentUser() {
    $account = $this->getCurrentUser();
    $url = $this->getUrlGenerator()->generateFromRoute('par_profile_update_flows.gdpr', $this->getRouteParams() + ['user' => $account->id()]);
    return new RedirectResponse($url);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $data_policy = $form_state->getValue('data_policy');
    if ($data_policy !== 'on') {
      $this->setElementError('data_policy', $form_state, 'Please confirm you have read the Privacy Notice and understand how the Office intend to use your personal data. If you would like to opt-out please contact the helpdesk');
    }

    return parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    if ($account = $this->getFlowDataHandler()->getParameter('user')) {
      $account->set('field_gdpr', TRUE);

      if (!$account->save()) {
        $message = $this->t('GDPR agreement could not be saved for %person');
        $replacements = [
          '%person' => $account->getEmail(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
