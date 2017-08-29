<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParPartnershipFlowsOrganisationController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_business';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    // Need to get the flow from the parent class???
//    $flow = $par_data_partnership->getFlow();
//    $par_data_authority = current($par_data_partnership->getAuthority());
//    $par_data_primary_person = current($par_data_partnership->getAuthorityPeople());
//    $primary_person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');
//    $primary_person = $par_data_primary_person ? $primary_person_view_builder->view($par_data_primary_person, 'summary') : '';

    $build['intro'] = [
      '#markup' => t('Organisation Controller'),
      '#prefix' => '<p>',
      '#sufffix' => '</p>',
    ];

//    // Configuration for each entity is contained within the bundle.
//    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
//    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
//    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
//    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');
//
//    $par_data_authority = current($par_data_partnership->getAuthority());
//
//    // Organisation summary.
//    $par_data_organisation = current($par_data_partnership->getOrganisation());
//    $organisation_builder = $this->getParDataManager()->getViewBuilder('par_data_organisation');
//
//    $build['details_intro'] = [
//      '#markup' => "Review and confirm the details of your partnership with " . $par_data_authority->authority_name->getString(),
//    ];
//
//    $build['business_name'] = [
//      '#type' => 'fieldset',
//      '#title' => t('Business Name:'),
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//    ];
//
//    $build['business_name']['name'] = $organisation_builder->view($par_data_organisation, 'title');
//
//    $build['about_business'] = [
//      '#type' => 'fieldset',
//      '#title' => t('About the business:'),
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//    ];
//
//    $about_organisation = $par_data_organisation ? $organisation_builder->view($par_data_organisation, 'about') : '';
//    $build['about_business']['info'] = $this->renderMarkupField($about_organisation);
//
//    $build['about_business']['edit'] = [
//      '#type' => 'markup',
//      '#markup' => t('@link', [
//        '@link' => $this->getFlow()->getLinkByStep(5)->setText('edit')->toString(),
//      ]),
//    ];
//
//    // Registered address.
//    $par_data_premises = $par_data_organisation->getPremises();
//    $registered_premises = array_shift($par_data_premises);
//
//    if ($registered_premises) {
//      $premises_view_builder = $this->getParDataManager()->getViewBuilder('par_data_premises');
//
//      $build['registered_address']['primary_address'] = [
//        '#type' => 'fieldset',
//        '#title' => t('Registered address:'),
//        '#attributes' => ['class' => 'form-group'],
//        '#collapsible' => FALSE,
//        '#collapsed' => FALSE,
//      ];
//
//      $registered_address = $premises_view_builder->view($registered_premises, 'full');
//      $build['registered_address']['primary_address']['address'] = $this->renderMarkupField($registered_address);
//
//      $build['registered_address']['primary_address']['edit'] = [
//        '#type' => 'markup',
//        '#markup' => t('@link', [
//          '@link' => $this->getFlow()->getLinkByStep(6, [
//            'par_data_premises' => $registered_premises->id(),
//          ])->setText('edit')->toString(),
//        ]),
//      ];
//    }
//
//    if ($par_data_premises) {
//
//      foreach ($par_data_premises as $premises) {
//        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');
//
//        $build['registered_address'][$premises->id()] = [
//          '#type' => 'fieldset',
//          '#attributes' => ['class' => 'form-group'],
//          '#collapsible' => FALSE,
//          '#collapsed' => FALSE,
//        ];
//
//        $alternative_person = $person_view_builder->view($premises, 'full');
//        $build['registered_address'][$premises->id()]['premises'] = $this->renderMarkupField($alternative_person);
//
//        // We can get a link to a given form step like so.
//        $build['registered_address'][$premises->id()]['edit'] = [
//          '#type' => 'markup',
//          '#markup' => t('@link', [
//            '@link' => $this->getFlow()->getLinkByStep(6, [
//              'par_data_premises' => $premises->id(),
//            ])->setText('edit')->toString(),
//          ]),
//        ];
//      }
//    }
//
//    // Contacts.
//    // Primary contact summary.
//    $par_data_contacts = $par_data_partnership->getOrganisationPeople();
//    $par_data_primary_person = array_shift($par_data_contacts);
//
//    if ($par_data_primary_person) {
//      $build['primary_contact'] = [
//        '#type' => 'fieldset',
//        '#attributes' => ['class' => 'form-group'],
//        '#title' => t('Main business contact:'),
//        '#collapsible' => FALSE,
//        '#collapsed' => FALSE,
//      ];
//
//      $primary_person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');
//      $primary_person = $primary_person_view_builder->view($par_data_primary_person, 'summary');
//      $build['primary_contact']['details'] = $this->renderMarkupField($primary_person);
//
//      $build['primary_contact']['edit'] = [
//        '#type' => 'markup',
//        '#markup' => t('@link', [
//          '@link' => $this->getFlow()->getLinkByStep(7, [
//            'par_data_person' => $par_data_primary_person->id(),
//          ])->setText('edit')->toString(),
//        ]),
//      ];
//    }
//
//    if ($par_data_contacts) {
//      $build['alternative_people'] = [
//        '#type' => 'fieldset',
//        '#attributes' => ['class' => 'form-group'],
//        '#collapsible' => FALSE,
//        '#collapsed' => FALSE,
//      ];
//
//      foreach ($par_data_contacts as $person) {
//        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');
//
//        $alternative_person = $person_view_builder->view($person, 'summary');
//
//        $build['alternative_people'][$person->id()] = [
//          '#type' => 'fieldset',
//          '#attributes' => ['class' => 'form-group'],
//          '#collapsible' => FALSE,
//          '#collapsed' => FALSE,
//        ];
//
//        $build['alternative_people'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);
//
//        // We can get a link to a given form step like so.
//        $build['alternative_people'][$person->id()]['edit'] = [
//          '#type' => 'markup',
//          '#markup' => t('@link', [
//            '@link' => $this->getFlow()->getLinkByStep(7, [
//              'par_data_person' => $person->id(),
//            ])->setText('edit')->toString(),
//          ]),
//        ];
//      }
//    }
//
//    // Legal Entities.
//    $par_data_legal_entities = $par_data_organisation->getLegalEntity();
//    $par_data_legal_entity = array_shift($par_data_legal_entities);
//    $build['legal_entity'] = [
//      '#type' => 'fieldset',
//      '#title' => t('Legal Entities:'),
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//    ];
//
//    if ($par_data_legal_entity) {
//
//      $legal_entity_view_builder = $this->getParDataManager()->getViewBuilder('par_data_legal_entity');
//      $legal_entity = $legal_entity_view_builder->view($par_data_legal_entity, 'full');
//      $build['legal_entity']['entity'] = $this->renderMarkupField($legal_entity);
//
//      $build['legal_entity']['edit'] = [
//        '#type' => 'markup',
//        '#markup' => t('@link', [
//          '@link' => $this->getFlow()->getLinkByStep(8, [
//            'par_data_legal_entity' => $par_data_legal_entity->id(),
//          ])->setText('edit')->toString(),
//        ]),
//      ];
//    }
//
//    if ($par_data_legal_entities) {
//
//      foreach ($par_data_legal_entities as $legal_entity_item) {
//        $build['legal_entity_' . $legal_entity_item->id()] = [
//          '#type' => 'fieldset',
//          '#attributes' => ['class' => 'form-group'],
//          '#collapsible' => FALSE,
//          '#collapsed' => FALSE,
//        ];
//        $alternative_legal = $legal_entity_view_builder->view($legal_entity_item, 'full');
//        $build['legal_entity_' . $legal_entity_item->id()]['item'] = $this->renderMarkupField($alternative_legal);
//
//        // We can get a link to a given form step like so.
//        $build['legal_entity_' . $legal_entity_item->id()][$legal_entity_item->id() . '_edit'] = [
//          '#type' => 'markup',
//          '#markup' => t('@link', [
//            '@link' => $this->getFlow()->getLinkByStep(8, [
//              'par_data_legal_entity' => $legal_entity_item->id(),
//            ])->setText('edit')->toString(),
//          ]),
//        ];
//      }
//    }
//
//    $build['legal_entity_add'] = [
//      '#type' => 'fieldset',
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//    ];
//
//    $build['legal_entity_add']['add'] = [
//      '#type' => 'markup',
//      '#markup' => t('@link', [
//        '@link' => $this->getFlow()->getLinkByStep(10)->setText('add another legal entity')->toString(),
//      ]),
//    ];
//
//    // Trading names.
//    $par_data_trading_names = $par_data_organisation->get('trading_name')->getValue();
//    if ($par_data_trading_names) {
//      $build['trading_names'] = [
//        '#type' => 'fieldset',
//        '#attributes' => ['class' => 'form-group'],
//      ];
//
//      foreach ($par_data_trading_names as $key => $trading_name) {
//        $build['trading_names'][$key] = [
//          '#type' => 'fieldset',
//          '#title' => $key === 0 ? t('Trading Names:') : '',
//          '#attributes' => ['class' => 'form-group'],
//          '#collapsible' => FALSE,
//          '#collapsed' => FALSE,
//        ];
//
//        $build['trading_names'][$key]['entity'] = [
//          '#type' => 'markup',
//          '#markup' => $trading_name['value'],
//          '#prefix' => '<div>',
//          '#suffix' => '</div>',
//        ];
//
//        $build['trading_names'][$key]['edit'] = [
//          '#type' => 'markup',
//          '#markup' => t('@link', [
//            '@link' => $this->getFlow()->getLinkByStep(9, [
//              'trading_name_delta' => $key,
//            ])->setText('edit')->toString(),
//          ]),
//        ];
//      }
//
//      $build['trading_names']['add'] = [
//        '#type' => 'markup',
//        '#markup' => t('@link', [
//          '@link' => $this->getFlow()->getLinkByStep(11)->setText('add another trading name')->toString(),
//        ]),
//      ];
//    }

    $build['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($par_data_authority);

    return parent::build($build);

  }

}
