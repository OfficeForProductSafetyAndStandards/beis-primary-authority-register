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
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataAuthority $par_data_authority
   *   The Authority being retrieved.
   * @param ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      $this->loadDataValue('about_partnership', $par_data_partnership->get('about_partnership')->getString());

      // Get all the people and divide them into primary and alternative contacts.
      $people = $par_data_partnership->get('person')->referencedEntities();
      $primary_person = array_shift($people);
      $this->loadDataValue('people', $people);

      // Primary Contacts.
      $this->loadDataValue('primary_person_id', $primary_person->id());
      $this->loadDataValue("person_{$primary_person->id()}_name", $primary_person->get('person_name')->getString());
      $this->loadDataValue("person_{$primary_person->id()}_phone", $primary_person->get('work_phone')->getString());
      $this->loadDataValue("person_{$primary_person->id()}_email", $primary_person->get('email')->getString());

      // Secondary Contacts.
      foreach ($people as $person) {
        $this->loadDataValue("person_{$person->id()}_name", $person->get('person_name')->getString());
        $this->loadDataValue("person_{$person->id()}_phone", $person->get('work_phone')->getString());
        $this->loadDataValue("person_{$person->id()}_email", $person->get('email')->getString());
      }

      // Regulatory Areas.
      $regulatory_areas = $par_data_partnership->get('regulatory_area')->referencedEntities();
      $areas = [];
      foreach ($regulatory_areas as $regulatory_area) {
        $areas[] = $regulatory_area->get('area_name')->getString();
      }

      $this->loadDataValue('regulatory_areas', $areas);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_authority, $par_data_partnership);

    // About the Partnership.
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
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(2)->setText('edit')->toString()
      ]),
    ];

    // Main Primary Authority contact.
    $form['second_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Main Primary Authority contact'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $primary_person_id = $this->getDefaultValues('primary_person_id');
    $form['second_section']['primary_person'] = [
      '#type' => 'markup',
      '#markup' => t('%name <br>%phone <br>%email', [
        '%name' => $this->getDefaultValues("person_{$primary_person_id}_name", '', $this->getFlow()->getFormIdByStep(2)),
        '%phone' => $this->getDefaultValues("person_{$primary_person_id}_phone", '', $this->getFlow()->getFormIdByStep(2)),
        '%email' => $this->getDefaultValues("person_{$primary_person_id}_email", '', $this->getFlow()->getFormIdByStep(2)),
      ]),
    ];
    // We can get a link to a given form step like so.
    $form['second_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(3, [
          'par_data_person' => $this->getDefaultValues('primary_person_id')
        ])->setText('edit')->toString()
      ]),
    ];

    // Secondary Primary Authority contacts.
    $form['third_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Secondary Primary Authority contacts'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    foreach ($this->getDefaultValues('people', []) as $person) {
      $form['third_section'][$person->id()] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $form['third_section']['alternative_people'][$person->id()] = [
        '#type' => 'markup',
        '#markup' => t('%name <br>%phone <br>%email', [
          '%name' => $this->getDefaultValues("person_{$person->id()}_name", '', $this->getFlow()->getFormIdByStep(2)),
          '%phone' => $this->getDefaultValues("person_{$person->id()}_phone", '', $this->getFlow()->getFormIdByStep(2)),
          '%email' => $this->getDefaultValues("person_{$person->id()}_email", '', $this->getFlow()->getFormIdByStep(2)),
        ]),
      ];

      // We can get a link to a given form step like so.
      $form['third_section']['edit'][$person->id()] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(3, [
            'par_data_person' => $person->id()
          ])->setText('edit')->toString()
        ]),
      ];
    }

    // Areas of Regulatory Advice.
    $form['fourth_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Areas of Regulatory Advice'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    foreach ($this->getDefaultValues('regulatory_areas', []) as $regulatory_area) {
      $form['fourth_section'][] = [
        '#type' => 'markup',
        '#markup' => $this->t('%area', [
          '%area' => $regulatory_area,
        ]),
      ];
    }

    // Partnership Confirmation.
    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm that the partnership information above is correct.'),
      '#prefix' => '<div class="form-group">',
      '#suffix' => '</div>',
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

    // Save the value for the about_partnership field.
    $partnership = $this->getRouteParam('par_data_partnership');
    if ($this->getTempDataValue('confirmation', 0) === 1) {
      // @TODO Get allowed values from configuration.
      $partnership->set('partnership_status', 'Confirmed by Authority');

      if ($partnership->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('The %field field could not be saved for %form_id');
        $replacements = [
          '%field' => 'confirmation',
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

    // We're not in kansas any more, after submitting the overview let's go home.
    $form_state->setRedirect('<front>');
  }

}
