<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * The Overview form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionOverviewForm extends ParBaseForm {

  /**
   *  {@inheritdoc}
   */
  protected $flow = 'transition_partnership_details';

  public function getFormId() {
    return 'par_flow_transition_partnership_details_overview';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      $this->loadDataValue('about_partnership', $par_data_partnership->get('about_partnership')->getString());
    }

    // Section 1.
    $form['first_section'] = [
      '#type' => 'fieldset',
      '#title' => t('About the Partnership'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['first_section']['about_partnership'] = [
      '#type' => 'markup',
      '#markup' => $this->t('%about', ['%about' => $this->getDefaultValues('about_partnership', '', $this->getFlow()->getFormIdByStep(2))]),
    ];
    // Go to the second step.
    $form['first_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<br>%link', ['%link' => $this->getFlow()->getLinkByStep(2)->setText('edit')->toString()]),
    ];

    // Section 2.
    $form['second_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Main Primary Authority contact'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['second_section']['primary_person'] = [
      '#type' => 'markup',
      '#markup' => t('%name <br>%phone <br>%email <br>%hours', [
        '%name' => '',
        '%phone' => '',
        '%email' => '',
        '%hours' => '',
      ]),
    ];
    // We can get a link to a given form step like so.
    $form['second_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', ['%link' => $this->getFlow()->getLinkByStep(3)->setText('edit')->toString()]),
    ];

    // Section 3.
    $form['third_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Secondary Primary Authority contact'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['third_section']['alternative_people'] = [
      '#type' => 'markup',
      '#markup' => t('%name <br>%phone <br>%email <br>%hours', [
        '%name' => '',
        '%phone' => '',
        '%email' => '',
        '%hours' => '',
      ]),
    ];
    // We can get a link to a given form step like so.
    $form['third_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', ['%link' => $this->getFlow()->getLinkByStep(4)->setText('edit')->toString()]),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];
    // We can get a link to a custom route like so.
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', ['%link' => $this->getLinkByRoute('<front>')->setText('Cancel')->toString()]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // We're not in kansas any more, after submitting the overview let's go home.
    $form_state->setRedirect('<front>');
  }

}
