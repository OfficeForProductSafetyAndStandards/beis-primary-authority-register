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
