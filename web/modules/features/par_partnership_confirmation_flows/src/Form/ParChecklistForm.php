<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParFlowAccessTrait;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The checklist form for partnership confirmations.
 */
class ParChecklistForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    // Set page title.
    $this->pageTitle = "Declaration for completion by proxy";

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Load application type from previous step.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $par_data_partnership ? $par_data_partnership->getOrganisation(TRUE) : NULL;

    $current_user = $this->getCurrentUser();
    if ($par_data_organisation && $current_user instanceof UserInterface
      && $this->getParDataManager()->isMember($par_data_organisation, $current_user)) {
      $this->getFlowDataHandler()->setTempDataValue('organisation_member', TRUE);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Organisation members can skip this form.
    if ($this->getFlowDataHandler()->getDefaultValues('organisation_member', FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['confirm'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm I have been given written permission by the organisation.'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('confirm', FALSE),
      '#return_value' => 'on',
    ];

    // There is a legal requirement to mention that the PA is the processor (under GDPR) and as such they must
    // adhere to the Office for Safety and Standards procedures concerning anything that could constitute personal data.
    $form['help'] = [
      '#type' => 'markup',
      '#markup' => '<p>You are required to have obtained written permission from the organisation to fill in the details on their behalf and submit the partnership for nomination.</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('confirm')) {
      $id = $this->getElementId('confirm', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('Please confirm that you have been given permission.', $id));
    }
  }

}
