<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

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
   * @param ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // Partnership Confirmation.
      $allowed_values = $par_data_partnership->type->entity->getConfigurationByType('partnership_status', 'allowed_values');
      // Set the on and off values so we don't have to do that again.
      $this->loadDataValue('confirmation_set_value', $allowed_values['confirmed_authority']);
      $this->loadDataValue('confirmation_unset_value', $allowed_values['awaiting_review']);
      $partnership_status = $par_data_partnership->getParStatus();
      if ($partnership_status === $allowed_values['confirmed_authority']) {
        $this->loadDataValue('confirmation', TRUE);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // About the Partnership.
    $form['first_section'] = [
      '#type' => 'fieldset',
      '#title' => t('About the Partnership'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $partnership_view_builder = $par_data_partnership->getViewBuilder();

    $form['first_section']['about_partnership'] = $par_data_partnership ? $partnership_view_builder->view($par_data_partnership, 'about_partnership') : '';

    // Go to the second step.
    $form['first_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(5)->setText('edit')->toString()
      ]),
    ];

    // List Authority contacts.
    $authority_people = $par_data_partnership->getAuthorityPeople();
    $authority_primary_person = array_shift($authority_people);
    $person_view_builder = $authority_primary_person ? $authority_primary_person->getViewBuilder() : NULL;

    // List the Primary Authority contact.
    if ($authority_primary_person) {
      $form['authority_contacts'] = [
        '#type' => 'fieldset',
        '#title' => t('Main Primary Authority contacts'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $primary_person = $person_view_builder->view($authority_primary_person, 'summary');
      $form['authority_contacts']['primary_person'] = $this->renderMarkupField($primary_person);

      // We can get a link to a given form step like so.
      $form['authority_contacts']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(6, [
            'par_data_person' => $authority_primary_person->id()
          ])->setText('edit')->toString()
        ]),
      ];
    }

    // List the secondary Primary Authority contacts.
    if ($authority_people) {
      $form['authority_contacts']['alternative_people'] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($authority_people as $person) {
        $person_view_builder = $person->getViewBuilder();

        $alternative_person = $person_view_builder->view($person, 'summary');
        $form['authority_contacts']['alternative_people'][$person->id()] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['authority_contacts']['alternative_people'][$person->id() . '_edit'] = [
          '#type' => 'markup',
          '#markup' => t('<br>%link', [
            '%link' => $this->getFlow()->getLinkByStep(6, [
              'par_data_person' => $person->id()
            ])->setText('edit')->toString()
          ]),
        ];
      }
    }

    // List Organisation contacts.
    $organisation_people = $par_data_partnership->getOrganisationPeople();

    $organisation_primary_person = array_shift($organisation_people);
    $person_view_builder = $organisation_primary_person ? $organisation_primary_person->getViewBuilder() : NULL;

    // List the Primary Organisation contact.
    if ($organisation_primary_person) {
      $par_data_organisation = current($par_data_partnership->get('organisation')->referencedEntities());
      $form['organisation_contacts'] = [
        '#type' => 'fieldset',
        '#title' => t('Main @organisation_type contact', ['@organisation_type' => $par_data_organisation->type->entity->label()]),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $organisation_person = $person_view_builder->view($organisation_primary_person, 'summary');
      $form['organisation_contacts']['primary_person'] = $this->renderMarkupField($organisation_person);

      // We can get a link to a given form step like so.
      $form['organisation_contacts']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(6, [
            'par_data_person' => $organisation_primary_person->id()
          ])->setText('edit')->toString()
        ]),
      ];
    }

    // List the secondary Primary Organisation contacts.
    if ($organisation_people) {
      $form['organisation_contacts']['alternative_people'] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($organisation_people as $person) {
        $person_view_builder = $person->getViewBuilder();

        $person_field = $person_view_builder->view($person, 'summary');

        $form['organisation_contacts']['alternative_people'][$person->id()] = $this->renderMarkupField($person_field);

        // We can get a link to a given form step like so.
        $form['organisation_contacts']['alternative_people'][$person->id() . '_edit'] = [
          '#type' => 'markup',
          '#markup' => t('<br>%link', [
            '%link' => $this->getFlow()->getLinkByStep(6, [
              'par_data_person' => $person->id()
            ])->setText('edit')->toString()
          ]),
        ];
      }
    }

    // Areas of Regulatory Advice.
    $form['fourth_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Areas of Regulatory Advice'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $regulatory_functions = $par_data_partnership->get('regulatory_function')->referencedEntities();
    dump($regulatory_functions);
    $regulatory_function_view_builder = current($regulatory_functions)->getViewBuilder();
    foreach ($par_data_partnership->get('regulatory_function')->referencedEntities() as $regulatory_function) {
      $regulatory_function_field = $regulatory_function_view_builder->view($regulatory_function, 'title');
      dump($regulatory_function_field);
      dump($this->renderMarkupField($regulatory_function_field));
      $form['fourth_section'][] = $this->renderMarkupField($regulatory_function_field);
    }

    // Partnership Confirmation.
    $form['partnership_agreement'] = [
      '#type' => 'checkbox',
      '#title' => t('(NOT YET SAVED) A written summary of partnership agreement, such as Memorandum of Understanding, has been agreed with the Business.'),
    ];

    // Partnership Confirmation.
    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm that the partnership information above is correct.'),
      '#default_value' => $this->getDefaultValues('confirmation', FALSE),
      '#return_value' => $this->getDefaultValues('confirmation_set_value', 0),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];
    // We can get a link to a custom route like so.
    $previous_link = $this->getFlow()->getLinkByStep($this->getFlow()->getPrevStep())->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', ['%link' => $previous_link]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_authority = $this->getRouteParam('par_data_authority');
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $this->retrieveEditableValues($par_data_authority, $par_data_partnership);

    // Save the value for the about_partnership field.
    $partnership_status = $this->decideBooleanValue(
      $this->getTempDataValue('confirmation'),
      $this->getDefaultValues('confirmation_set_value', NULL),
      $this->getDefaultValues('confirmation_unset_value', NULL)
    );

    // Save only if the value is different from the one currently set.
    if ($partnership_status !== $par_data_partnership->get('partnership_status')->getString()) {
      $par_data_partnership->set('partnership_status', $partnership_status);
      if ($par_data_partnership->save()) {
        $this->deleteStore();
      } else {
        $message = $this->t('The %field field could not be saved for %form_id');
        $replacements = [
          '%field' => 'confirmation',
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

    // We're not in kansas any more, after submitting the overview let's go home.
    $form_state->setRedirect($this->getFlow()->getPrevRoute(), $this->getRouteParams());
  }

}
