<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;

/**
 * The authority selection form.
 */
class ParPartnershipFlowsAuthoritySuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Which authority are you acting on behalf of?';

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Only set the state for the temp cache if we are passing a partnership object via the route.
    if ($par_data_partnership) {
      $this->retrieveEditableValues($par_data_partnership);
    }

    // Get the authorities the current user is a member of.
    $authorities = [];
    if ($this->currentUser()->isAuthenticated()) {
      $account = User::Load($this->currentUser()->id());
      $authorities = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_authority', TRUE);
    }

    $authority_view_builder = $this->getParDataManager()->getViewBuilder('par_data_authority');
    $authority_options = [];
    foreach ($authorities as $authority) {
      $authority_view = $authority_view_builder->view($authority, 'summary');

      $authority_options[$authority->id()] = $this->renderMarkupField($authority_view)['#markup'];
    }

    // If no suggestions were found we want to automatically submit the form.
    if (count($authority_options) <= 0) {
      $message = $this->t('No authority count be found for user %user');
      $replacements = [
        '%user' => $account->id(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      return $this->redirect($this->getFlowNegotiator()->getFlow()->progressRoute('cancel'), $this->getRouteParams());
    }
    elseif (count($authority_options) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_authority_id', key($authority_options));
      return $this->redirect($this->getFlowNegotiator()->getFlow()->progressRoute(), $this->getRouteParams());
    }

    $form['par_data_authority_id'] = [
      '#type' => 'radios',
      '#title' => t('Choose a Primary Authority'),
      '#options' => $authority_options,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("par_data_authority_id", NULL),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($authority_view_builder);
    $this->addCacheableDependency($authorities);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    if (!$form_state->getValue('par_data_authority_id')) {
      $id = $this->getElementId(['par_data_authority_id'], $form);
      $form_state->setErrorByName($this->getElementName('par_data_authority_id'), $this->wrapErrorMessage('You must select an authority.', $id));
    }
  }

}
