<?php

namespace Drupal\par_profile_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for the premises details.
 */
class ParGdprForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * The timestamp when GDPR came into effect.
   */
  const GDPR_START_TIME = 1527206400;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm acceptance of data policy';

  /**
   * Redirect current user on registration.
   */
  public function registrationRedirectCurrentUser() {
    $account = $this->getCurrentUser();
    $params = $this->getRouteParams() + ['user' => $account->id()];
    $url = ParFlow::load('profile_update')->start(1, $params);
    return new RedirectResponse($url->toString());
  }

  /**
   * Redirect current user on login.
   */
  public function loginRedirectCurrentUser() {
    $account = $this->getCurrentUser();

    // All users after the GDPR start time should confirm acceptance
    // of data policy if they haven't done so already.
    if ($account && $account->getCreatedTime() >= self::GDPR_START_TIME && $account->get('field_gdpr')->getString() !== '1') {
      $params = $this->getRouteParams() + ['user' => $account->id()];
      $url = ParFlow::load('profile_update')->start(1, $params);
      return new RedirectResponse($url->toString());
    }
    else {
      $url = $this->getUrlGenerator()->generateFromRoute('par_dashboards.dashboard', $this->getRouteParams());
      return new RedirectResponse($url);
    }

  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Disabling cancelling out of this form.
    $this->getFlowNegotiator()->getFlow()->disableAction('cancel');
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
