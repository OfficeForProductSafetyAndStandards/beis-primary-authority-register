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
   * {@inheritdoc}
   */
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_partnership_details_overview';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // Partnership Information Confirmation.
      $confirmation_value = $par_data_partnership->retrieveBooleanValue('partnership_info_agreed_authority');
      $this->loadDataValue('confirmation', $confirmation_value);
      // Written Summary Confirmation.
      $partnership_agreement_value = $par_data_partnership->retrieveBooleanValue('written_summary_agreed');
      $this->loadDataValue('partnership_agreement', $partnership_agreement_value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    // September deadline reminder
    $form['deadline_reminder'] = [
      '#type' => 'markup',
      '#markup' => t('<p>Review and confirm your data by 14 September 2017</p>'),
    ];

    // About the Partnership.
    $form['first_section'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => t('About the partnership'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $partnership_view_builder = $this->getParDataManager()->getViewBuilder('par_data_partnership');

    $form['first_section']['about_partnership'] = $par_data_partnership ? $partnership_view_builder->view($par_data_partnership, 'about') : '';

    // Go to the second step.
    $form['first_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getLinkByStep(5)->setText('edit')->toString(),
      ]),
    ];

    // List Authority contacts.
    $authority_people = $par_data_partnership->getAuthorityPeople();
    $authority_primary_person = array_shift($authority_people);
    $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

    // List the Primary Authority contact.
    if ($authority_primary_person) {
      $form['authority_contacts'][$authority_primary_person->id()] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => t('Main primary authority contact'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $primary_person = $person_view_builder->view($authority_primary_person, 'summary');
      $form['authority_contacts'][$authority_primary_person->id()]['primary_person'] = [$this->renderMarkupField($primary_person)];

      // We can get a link to a given form step like so.
      $form['authority_contacts'][$authority_primary_person->id()]['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getLinkByStep(6, [
            'par_data_person' => $authority_primary_person->id(),
          ])->setText('edit')->toString(),
        ]),
      ];
    }

    // List the secondary Primary Authority contacts.
    if ($authority_people) {
      $form['authority_contacts']['authority_alternative_contacts'] = [
        '#type' => 'fieldset',
        '#attributes' => ['id' => 'edit-authority-contacts'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($authority_people as $person) {
        $alternative_person = $person_view_builder->view($person, 'summary');

        $form['authority_contacts']['authority_alternative_contacts'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => [
            'class' => [
              'form-group',
              'authority-alternative-contact',
              'authority-alternative-contact-' . array_search($person, $authority_people)
            ],
          ],
        ];

        $form['authority_contacts']['authority_alternative_contacts'][$person->id()]['name'] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['authority_contacts']['authority_alternative_contacts'][$person->id()]['edit_link'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getLinkByStep(6, [
              'par_data_person' => $person->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
      }
    }

    // List Organisation contacts.
    $organisation_people = $par_data_partnership->getOrganisationPeople();

    $organisation_primary_person = array_shift($organisation_people);

    // List the Primary Organisation contact.
    $par_data_organisation = current($par_data_partnership->retrieveEntityValue('field_organisation'));
    if ($par_data_organisation && $organisation_primary_person) {
      $form['organisation_contacts'] = [
        '#type' => 'fieldset',
        '#attributes' => ['id' => 'edit-organisation-contacts'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['organisation_contacts'][$organisation_primary_person->id()] = [
        '#type' => 'fieldset',
        '#attributes' => [
          'class' => 'form-group',
          'id' => 'organisation_alternative_contacts'
        ],
        '#title' => t('Primary @organisation_type contact', ['@organisation_type' => $par_data_organisation->type->entity->label()]),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $organisation_person = $person_view_builder->view($organisation_primary_person, 'summary');
      $form['organisation_contacts'][$organisation_primary_person->id()]['person'] = $this->renderMarkupField($organisation_person);

      // We can get a link to a given form step like so.
      $form['organisation_contacts'][$organisation_primary_person->id()]['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getLinkByStep(6, [
            'par_data_person' => $organisation_primary_person->id(),
          ])->setText('edit')->toString(),
        ]),
      ];
    }

    // List the secondary Organisation contacts.
    if ($par_data_organisation && $organisation_people) {
      foreach ($organisation_people as $person) {
        $person_field = $person_view_builder->view($person, 'summary');

        $form['organisation_alternative_contacts'] = [
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['organisation_alternative_contacts'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => [
            'class' => [
              'form-group',
              'organisation-alternative-contact',
              'organisation-alternative-contacts-' . array_search($person, $organisation_people)
            ]
          ],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['organisation_alternative_contacts'][$person->id()]['person'] = $this->renderMarkupField($person_field);

        // We can get a link to a given form step like so.
        $form['organisation_alternative_contacts'][$person->id()]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getLinkByStep(6, [
              'par_data_person' => $person->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
      }
    }

    // Areas of Regulatory Advice.
    $form['fourth_section'] = [
      '#type' => 'fieldset',
      '#title' => t('Regulatory functions'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $regulatory_function_view_builder = $this->getParDataManager()->getViewBuilder('par_data_regulatory_function');

    $regulatory_function_list_items = [];
    foreach ($par_data_partnership->retrieveEntityValue('field_regulatory_function') as $regulatory_function) {
      $regulatory_function_field = $regulatory_function_view_builder->view($regulatory_function, 'title');
      $regulatory_function_list_items[] = $this->renderMarkupField($regulatory_function_field);
    }

    $form['fourth_section']['list'] = [
      '#theme' => 'item_list',
      '#items' => $regulatory_function_list_items
    ];

    // "Partnership Arrangements have been agreed" confirmation.
    $form['partnership_agreement'] = [
      '#type' => 'checkbox',
      '#title' => t('A written summary of the partnership arrangements has been agreed with the business.'),
      '#disabled' => $this->getDefaultValues('partnership_agreement'),
      '#checked' => $this->getDefaultValues('partnership_agreement'),
      '#default_value' => $this->getDefaultValues('partnership_agreement'),
      '#return_value' => 'on',
    ];

    // "Partnership Information is correct" confirmation.
    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm that the partnership information above is correct.'),
      '#disabled' => $this->getDefaultValues('confirmation'),
      '#checked' => $this->getDefaultValues('confirmation'),
      '#default_value' => $this->getDefaultValues('confirmation'),
      '#return_value' => 'on',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];
    // We can get a link to a custom route like so.
    $previous_link = $this->getFlow()->getLinkByStep($this->getFlow()->getPrevStep())->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $this->retrieveEditableValues($par_data_partnership);

    // Save the value for the partnership status if it's being confirmed.
    if ($confirmation_value = $this->decideBooleanValue($this->getTempDataValue('confirmation'))) {
      $par_data_partnership->set('partnership_info_agreed_authority', $confirmation_value);
      // Also change the status.
      $par_data_partnership->setParStatus('confirmed_authority');
    }

    // Save the value for the written agreement if it's being confirmed.
    if ($partnership_agreement_value = $this->decideBooleanValue($this->getTempDataValue('partnership_agreement'))) {
      $par_data_partnership->set('written_summary_agreed', $partnership_agreement_value);
    }

    if ($par_data_partnership->save()) {
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

    // We're not in kansas any more, after submitting the overview let's go home.
    $form_state->setRedirect($this->getFlow()->getPrevRoute(), $this->getRouteParams());
  }

}
