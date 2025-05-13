<?php

namespace Drupal\par_invite_user_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for adding users to an institution.
 */
class ParChooseInstitutionForm extends ParBaseForm {

  /**
   * Title property.
   */
  protected $pageTitle = 'Update which authorities or organisations this person belongs to';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $authorities = $this->getFlowDataHandler()->getFormPermValue('par_data_authority_original');
    $organisations = $this->getFlowDataHandler()->getFormPermValue('par_data_organisation_original');

    // Skip the membership selection process if the contact record already has memberships.
    if (!empty($authorities) || !empty($organisations)) {
      // Save the default values.
      $this->getFlowDataHandler()->setTempDataValue('par_data_authority_id', $authorities);
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', $organisations);

      // Submit the form.
      $this->submitForm($form, $form_state);

      // Progress to the next page.
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // If there are two types of institution selected throw an error.
    $authorities = $form_state->getValue('par_data_authority_id');
    $organisations = $form_state->getValue('par_data_organisation_id');
    if (!empty($authorities) && !empty($organisations)) {
      foreach (['par_data_authority', 'par_data_organisation'] as $institution) {
        $message = 'The user cannot be invited to both an authority and an organisation.';
        $id = $this->getElementId([$institution], $form);
        $form_state->setErrorByName($this->getElementName($institution), $this->wrapErrorMessage($message, $id));
      }
    }
  }

}
