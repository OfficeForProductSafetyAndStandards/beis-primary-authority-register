<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The de-duping form.
 */
class ParOrganisationSuggestionForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Are you looking for one of these businesses?';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // If an existing organisation was selected and has an address
    // and contact, skip to the review step, or skip to the contact
    // step if an existing organisation was selected which has an
    // address but no contact.
    $organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', NULL);
    if (isset($organisation_id) && $organisation_id !== 'new') {
      $par_data_organisation = ParDataOrganisation::load($organisation_id);
    }
    if (isset($par_data_organisation)) {
      if (!$par_data_organisation->get('field_person')->isEmpty()) {
        $url = $this->getFlowNegotiator()->getFlow()->progress('review');
        $form_state->setRedirectUrl($url);
      }
      elseif ($par_data_organisation->get('field_person')->isEmpty()
        && !$par_data_organisation->get('field_premises')->isEmpty()) {
        $url = $this->getFlowNegotiator()->getFlow()->progress('add_contact');
        $form_state->setRedirectUrl($url);
      }
    }
  }

}
