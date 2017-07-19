<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionSecondForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_partnership_details';

  public function getFormId() {
    return 'par_flow_transition_partnership_primary_contact';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $authority = NULL, ParDataPartnership $partnership = NULL) {
    if ($partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$partnership->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      // $this->loadDataValue('about_partnership', $partnership->get('about_partnership')->getValue());
    }



    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Go back to the overview.
    $form_state->setRedirect('par_flow_transition_partnership_details.overview', $this->getRouteParams());
  }
}
