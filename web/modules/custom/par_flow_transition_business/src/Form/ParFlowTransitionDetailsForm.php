<?php

namespace Drupal\par_flow_transition_business\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionDetailsForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_business';

  public function getFormId() {
    return 'par_flow_transition_business_details';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // We need to get the value of the terms and conditions checkbox.
      $this->loadDataValue("partnership_info_agreed_business", $par_data_partnership->get('partnership_info_agreed_business')->getString());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    $par_data_authority = current($par_data_partnership->getAuthority());

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_builder = $par_data_organisation->getViewBuilder();

    $form['details_intro'] = [
      '#markup' => "Review and confirm the details of your partnership with " . $par_data_authority->authority_name->getString(),
    ];

    $form['business_name'] = [
      '#type' => 'fieldset',
      '#title' => t('Business Name:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['business_name']['name'] = $organisation_builder->view($par_data_organisation, 'title');

    $form['about_business'] = [
      '#type' => 'fieldset',
      '#title' => t('About the business:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $about_organisation = $par_data_organisation ? $organisation_builder->view($par_data_organisation, 'about') : '';
    $form['about_business']['info'] = $this->renderMarkupField($about_organisation);

    $form['about_business']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(5)->setText('edit')->toString()
      ]),
    ];

    // Registered address.
    $par_data_premises = $par_data_organisation->getPremises();
    $registered_premises = array_shift($par_data_premises);
    $premises_view_builder = $registered_premises->getViewBuilder();

    if ($registered_premises) {

      $form['registered_address'] = [
        '#type' => 'fieldset',
        '#title' => t('Registered address'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $registered_address = $premises_view_builder->view($registered_premises, 'full');
      $form['registered_address']['address'] = $this->renderMarkupField($registered_address);

      $form['registered_address']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(6, [
            'par_data_premises' => $registered_premises->id()
          ])->setText('edit')->toString()
        ]),
      ];
    }

    if ($par_data_premises) {
      $form['registered_address']['alternative_premises'] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($par_data_premises as $premises) {
        $person_view_builder = $premises->getViewBuilder();

        $alternative_person = $person_view_builder->view($premises, 'full');
        $form['registered_address']['alternative_premises'][$premises->id()] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['registered_address']['alternative_premises'][$premises->id() . '_edit'] = [
          '#type' => 'markup',
          '#markup' => t('<br>%link', [
            '%link' => $this->getFlow()->getLinkByStep(6, [
              'par_data_premises' => $premises->id()
            ])->setText('edit')->toString()
          ]),
        ];
      }
    }

    // Contacts.
    // Primary contact summary.
    $par_data_contacts = $par_data_partnership->getOrganisationPeople();
    $par_data_primary_person = array_shift($par_data_contacts);
    $primary_person_view_builder = $par_data_primary_person->getViewBuilder();

    if ($par_data_primary_person) {
      $form['primary_contact'] = [
        '#type' => 'fieldset',
        '#title' => t('Main business contact:'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $primary_person = $primary_person_view_builder->view($par_data_primary_person, 'summary');
      $form['primary_contact']['details'] = $this->renderMarkupField($primary_person);

      $form['primary_contact']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(7, [
            'par_data_person' => $par_data_primary_person->id()
          ])->setText('edit')->toString()
        ]),
      ];
    }

    if ($par_data_contacts) {
      $form['primary_contact']['alternative_people'] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($par_data_contacts as $person) {
        $person_view_builder = $person->getViewBuilder();

        $alternative_person = $person_view_builder->view($person, 'summary');
        $form['primary_contact']['alternative_people'][$person->id()] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['primary_contact']['alternative_people'][$person->id() . '_edit'] = [
          '#type' => 'markup',
          '#markup' => t('<br>%link', [
            '%link' => $this->getFlow()->getLinkByStep(7, [
              'par_data_person' => $person->id()
            ])->setText('edit')->toString()
          ]),
        ];
      }
    }

    // Legal Entities.
    $par_data_legal_entities = $par_data_organisation->getLegalEntity();
    $par_data_legal_entity = array_shift($par_data_legal_entities);
    $legal_view_builder = $par_data_legal_entity->getViewBuilder();

    if ($par_data_legal_entity) {

      $form['legal_entity'] = [
        '#type' => 'fieldset',
        '#title' => t('Legal Entities:'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $legal_entity = $legal_view_builder->view($par_data_legal_entity, 'full');
      $form['legal_entity']['entity'] = $this->renderMarkupField($legal_entity);

      $form['legal_entity']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(8, [
            'par_data_legal_entity' => $par_data_legal_entity->id()
          ])->setText('edit')->toString()
        ]),
      ];
    }

    if ($par_data_legal_entities) {
      $form['legal_entity']['alternative'] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($par_data_legal_entities as $legal_entity_item) {
        $legal_view_builder = $legal_entity_item->getViewBuilder();

        $alternative_legal = $legal_view_builder->view($legal_entity_item, 'full');
        $form['legal_entity']['alternative'][$legal_entity_item->id()] = $this->renderMarkupField($alternative_legal);

        // We can get a link to a given form step like so.
        $form['legal_entity']['alternative'][$legal_entity_item->id() . '_edit'] = [
          '#type' => 'markup',
          '#markup' => t('<br>%link', [
            '%link' => $this->getFlow()->getLinkByStep(8, [
              'par_data_legal_entity' => $legal_entity_item->id()
            ])->setText('edit')->toString()
          ]),
        ];
      }
    }

    $form['legal_entity']['alternative']['add'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(10)->setText('add another legal entity')->toString()
      ]),
    ];

    // Trading names.
    $par_data_trading_names = $par_data_organisation->get('trading_name')->getValue();
    if ($par_data_trading_names) {

      foreach ($par_data_trading_names as $key => $trading_name) {
        $form['trading_names'][$key] = [
          '#type' => 'fieldset',
          '#title' => $key === 0 ? t('Trading Names:') : '',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['trading_names'][$key]['entity'] = [
          '#markup' => $trading_name['value'],
        ];

        $form['trading_names'][$key]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('<br>%link', [
            '%link' => $this->getFlow()->getLinkByStep(9, [
              'trading_name_delta' => $key
            ])->setText('edit')->toString()
          ]),
        ];
      }

      $form['trading_names']['add'] = [
        '#type' => 'markup',
        '#markup' => t('<br>%link', [
          '%link' => $this->getFlow()->getLinkByStep(11)->setText('add another trading name')->toString()
          ]),
      ];
    }


    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm that the information above is correct.'),
      '#default_value' => $this->getDefaultValues('partnership_info_agreed_business'),
      '#disabled' => $this->getDefaultValues('partnership_info_agreed_business'),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $partnership = $this->getRouteParam('par_data_partnership');
    $partnership->set('partnership_info_agreed_business', $this->getTempDataValue('confirmation'));
    if ($partnership->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('The %field field could not be saved for %form_id');
      $replacements = [
        '%field' => 'about_partnership',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getPrevRoute(), $this->getRouteParams());
  }
}
