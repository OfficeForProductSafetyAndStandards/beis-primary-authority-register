<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\MatchingRouteNotFoundException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($par_data_partnership) {
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $this->pageTitle = $par_data_organisation->get('organisation_name')->getString();
    }

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      $checkbox = $this->getInformationCheckbox($par_data_partnership);
      $this->getFlowDataHandler()->setFormPermValue($checkbox, $par_data_partnership->getBoolean($checkbox));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // Display all the information that can be modified by the organisation.
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
    $par_data_authority = $par_data_partnership->getAuthority(TRUE);

    // Partnership Authority Name - component.
    $form['authority_name'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "<span class='heading-secondary'>In partnership with</span>" . $par_data_authority->getName(),
      '#attributes' => ['class' => ['heading-large', 'form-group']],
    ];

    // Partnership Basic Information - component.
    $form['partnership_info'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Display details about the partnership for information.
    $about_partnership_display = $par_data_partnership->about_partnership->view(['label' => 'hidden']);
    $form['partnership_info']['about'] = [
      '#type' => 'fieldset',
      '#title' => 'About the partnership',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'details' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->getRenderer()->render($about_partnership_display),
      ],
    ];

    // Display the regulatory functions and partnership approved date.
    $approved_date_display = $par_data_partnership->approved_date->view('full');
    $regulatory_functions = $par_data_partnership->get('field_regulatory_function')->referencedEntities();
    $form['partnership_info']['details'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'regulatory_functions' => [
        '#type' => 'fieldset',
        '#title' => 'Partnered for',
        '#attributes' => ['class' => 'column-one-half'],
        'value' => [
          '#theme' => 'item_list',
          '#list_type' => 'ul',
          '#items' => $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions),
        ]
      ],
      'approved_date' => [
        '#type' => 'fieldset',
        '#title' => 'In partnership since',
        '#attributes' => ['class' => 'column-one-half'],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $this->getRenderer()->render($approved_date_display),
        ],
      ],
    ];

    // Partnership Organisation Information - component.
    $form['organisation_info'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Information about the organisation",
      '#attributes' => ['class' => 'heading-large'],
    ];
    // Display the primary address along with the link to edit it.
    $form['registered_address'] = $this->renderSection('Address', $par_data_organisation, ['field_premises' => 'summary'], ['edit-entity', 'add'], TRUE, TRUE);

    // View and perform operations on the information about the business.
    $form['about_business'] = $this->renderSection('About the organisation', $par_data_organisation, ['comments' => 'about'], ['edit-field']);

    // Only show SIC Codes and Employee number if the partnership is a direct
    // partnership.
    if ($par_data_partnership->isDirect()) {
      // Add the SIC Codes with the relevant operational links.
      $form['sic_codes'] = $this->renderSection('Standard industrial classification (SIC) codes', $par_data_organisation, ['field_sic_code' => 'full'], ['edit-field', 'add']);

      // Add the number of employees with a link to edit the field.
      $form['employee_no'] = $this->renderSection('Number of Employees', $par_data_organisation, ['employees_band' => 'full'], ['edit-field']);
    }

    // Only show Members for coordinated partnerships.
    if ($par_data_partnership->isCoordinated()) {
      $membership_count = $par_data_partnership->countMembers();

      // If the organisation details, and there are already some members.
      if ($this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated'
        && $membership_count >= 1) {
        $form['members_link'] = [
          '#type' => 'fieldset',
          '#title' => t('Number of members'),
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];
        $form['members_link']['count'] = [
          '#type' => 'markup',
          '#markup' => "<p>{$membership_count}</p>",
        ];
        $form['members_link']['link'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => Link::createFromRoute('Show members list', 'view.members_list.member_list_coordinator', $this->getRouteParams())->toString(),
          ]),
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
      // If the organisation details and there aren't yet any members.
      elseif ($this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated') {
        $form['associations'] = $this->renderSection('Number of members', $par_data_organisation, ['size' => 'full'], ['edit-field']);

        $form['associations']['add_link'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => Link::createFromRoute('Add a member', 'par_member_add_flows.add_organisation_name', $this->getRouteParams())->toString(),
          ]),
          '#weight' => -100,
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
        $form['associations']['upload_link'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => Link::createFromRoute('Upload a Member List (CSV)', 'par_member_upload_flows.member_upload', $this->getRouteParams())->toString(),
          ]),
          '#weight' => -100,
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
      // In all other cases show the inline member summary.
      else {
        // Display all the members in basic form for authority users.
        $form['members'] = $this->renderSection('Members', $par_data_partnership, ['field_coordinated_business' => 'title']);
      }
    }

    // Display all the legal entities along with the links for the allowed
    // operations on these.
    $operations = [];
    $checkbox = $this->getInformationCheckbox();

    if ($checkbox === 'partnership_info_agreed_business' && !$par_data_partnership->getBoolean($checkbox)) {
      // PAR-1354 - active partnerships do not allow for associated legal entities to be altered or new entities to be added.
      if ($par_data_partnership->inProgress()) {
        $operations = ['edit-entity','add'];
      }
    }
    $form['legal_entities'] = $this->renderSection('Legal entities', $par_data_partnership, ['field_legal_entity' => 'summary'], $operations);

    // Display all the trading names along with the links for the allowed
    // operations on these.
    $form['trading_names'] = $this->renderSection('Trading names', $par_data_organisation, ['trading_name' => 'full'], ['edit-field', 'add']);

    // Partnership Documents - component.
    $form['documents'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Documents",
      '#attributes' => ['class' => 'heading-large'],
    ];
    // Inspection plan link.
    $form['inspection_plans'] = [
      '#type' => 'fieldset',
      '#title' => t('Inspection plans'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Add the inspection plan link safely with access checks.
    try {
      if ($link = $this->getFlowNegotiator()->getFlow()->getNextLink('inspection_plans', [], [], TRUE)) {
        $add_inspection_list_link = t('@link', [
          '@link' => $link->setText('See all Inspection Plans')->toString(),
        ]);
        $form['inspection_plans']['link'] = [
          '#type' => 'markup',
          '#markup' => $add_inspection_list_link,
        ];
      }
    } catch (ParFlowException $e) {

    }

    // Add the advice link safely with access checks.
    $form['advice'] = [
      '#type' => 'fieldset',
      '#title' => t('Advice and Documents'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    try {
      if ($link = $this->getFlowNegotiator()->getFlow()->getNextLink('advice', [], [], TRUE)) {
        $add_advice_list_link = t('@link', [
          '@link' => $link->setText('See all Advice')->toString(),
        ]);
        $form['advice']['link'] = [
          '#type' => 'markup',
          '#markup' => $add_advice_list_link,
        ];
      }
    } catch (ParFlowException $e) {

    }


    // Partnership Contacts - component.
    $form['Contacts'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Contact information",
      '#attributes' => ['class' => 'heading-large'],
    ];

    // Display the authority contacts for information.
    $authority_contacts = $par_data_partnership->getAuthorityPeople();

    // Initialize pager and get current page.
    $number_per_page = 5;
    $pager = $this->getUniquePager()->getPager('partnership_manage_authority_contacts');
    $current_page = pager_default_initialize(count($authority_contacts), $number_per_page, $pager);

    // Get update and remove links.
    try {
      $params = $this->getRouteParams() + ['type' => 'authority'];
      if ($link = $this->getLinkByRoute('par_partnership_contact_add_flows.create_contact', $params, [], TRUE)) {
        $add_authority_contact_link = t('@link', [
          '@link' => $link->setText('add another authority contact')->toString(),
        ]);
      }
    } catch (ParFlowException $e) {

    }

    $form['authority_contacts'] = [
      '#type' => 'fieldset',
      '#title' => t('Primary Authority'),
      '#attributes' => ['class' => ['form-group']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'field_authority_person' => [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'items' => [
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ],
        'pager' => [
          '#type' => 'pager',
          '#theme' => 'pagerer',
          '#element' => $pager,
          '#weight' => 100,
          '#config' => [
            'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
          ],
        ],
        'operations' => [
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          'add' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($add_authority_contact_link) ? $add_authority_contact_link : '',
          ],
        ],
      ],
    ];

    // Split the items up into chunks:
    $chunks = array_chunk($authority_contacts, $number_per_page);
    foreach ($chunks[$current_page] as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $entity_view = $entity_view_builder->view($entity, 'detailed');
      $rendered_field = $this->getRenderer()->render($entity_view);

      // Get update and remove links.
      try {
        $params = $this->getRouteParams() + ['type' => 'authority', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_update_flows.create_contact', $params, [], TRUE)) {
          $update_authority_contact_link = t('@link', [
            '@link' => $link->setText('edit ' . strtolower($entity->label()))->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }
      try {
        $params = $this->getRouteParams() + ['type' => 'authority', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_remove_flows.remove', $params, [], TRUE)) {
          $remove_authority_contact_link = t('@link', [
            '@link' => $link->setText('remove ' . strtolower($entity->label()) . ' from this partnership')->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }

      $form['authority_contacts']['field_authority_person']['items'][$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['grid-row', 'form-group', 'contact-details']],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'entity' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $rendered_field ? $rendered_field : '<p>(none)</p>',
          '#attributes' => ['class' => ['column-full']],
        ],
        'operations' => [
          'update' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($update_authority_contact_link) ? $update_authority_contact_link : '',
            '#attributes' => ['class' => ['column-one-third']],
          ],
          'remove' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($remove_authority_contact_link) ? $remove_authority_contact_link : '',
            '#attributes' => ['class' => ['column-two-thirds']],
          ],
        ],
      ];
    }


    // Display all the organisational contacts.
    $organisation_contacts = $par_data_partnership->getOrganisationPeople();

    // Initialize pager and get current page.
    $number_per_page = 5;
    $pager = $this->getUniquePager()->getPager('partnership_manage_organisation_contacts');
    $current_page = pager_default_initialize(count($organisation_contacts), $number_per_page, $pager);

    // Get update and remove links.
    try {
      $params = $this->getRouteParams() + ['type' => 'organisation'];
      if ($link = $this->getLinkByRoute('par_partnership_contact_add_flows.create_contact', $params, [], TRUE)) {
        $add_organisation_contact_link = t('@link', [
          '@link' => $link->setText('add another organisation contact')->toString(),
        ]);
      }
    } catch (ParFlowException $e) {

    }

    $form['organisation_contacts'] = [
      '#type' => 'fieldset',
      '#title' => t('Organisation'),
      '#attributes' => ['class' => ['form-group']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'field_organisation_person' => [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'items' => [
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ],
        'pager' => [
          '#type' => 'pager',
          '#theme' => 'pagerer',
          '#element' => $pager,
          '#weight' => 100,
          '#config' => [
            'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
          ],
        ],
        'operations' => [
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          'add' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($add_organisation_contact_link) ? $add_organisation_contact_link : '',
          ],
        ],
      ],
    ];

    // Split the items up into chunks:
    $chunks = array_chunk($organisation_contacts, $number_per_page);
    foreach ($chunks[$current_page] as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $entity_view = $entity_view_builder->view($entity, 'detailed');
      $rendered_field = $this->getRenderer()->render($entity_view);

      // Get update and remove links.
      try {
        $params = $this->getRouteParams() + ['type' => 'organisation', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_update_flows.create_contact', $params, [], TRUE)) {
          $update_organisation_contact_link = t('@link', [
            '@link' => $link->setText('edit ' . strtolower($entity->label()))->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }
      try {
        $params = $this->getRouteParams() + ['type' => 'organisation', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_remove_flows.remove', $params, [], TRUE)) {
          $remove_organisation_contact_link = t('@link', [
            '@link' => $link->setText('remove ' . strtolower($entity->label()) . ' from this partnership')->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }

      $form['organisation_contacts']['field_organisation_person']['items'][$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['grid-row', 'form-group', 'contact-details']],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'entity' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $rendered_field ? $rendered_field : '<p>(none)</p>',
          '#attributes' => ['class' => ['column-full']],
        ],
        'operations' => [
          'update' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($update_organisation_contact_link) ? $update_organisation_contact_link : '',
            '#attributes' => ['class' => ['column-one-third']],
          ],
          'remove' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($remove_organisation_contact_link) ? $remove_organisation_contact_link : '',
            '#attributes' => ['class' => ['column-two-thirds']],
          ],
        ],
      ];
    }

    // Helptext.
    $form['help_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Updating this information may change who recieves notifications for this partnership. Please check everything is correct.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Helper function to get the information checkbox required. False if none required.
   */
  public function getInformationCheckbox() {
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
      return 'partnership_info_agreed_authority';
    }
    else {
      return 'partnership_info_agreed_business';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $checkbox = $this->getInformationCheckbox();
    if ($par_data_partnership && !$par_data_partnership->getBoolean($checkbox)) {

      // Save the value for the confirmation field.
      if ($checkbox) {
        $par_data_partnership->set($checkbox, $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue($checkbox)));

        // Set partnership status.
        $status = ($checkbox === 'partnership_info_agreed_authority') ? 'confirmed_authority' : 'confirmed_business';
        try {
          $par_data_partnership->setParStatus($status);
        }
        catch (ParDataException $e) {
          // If the status could not be updated we want to log this but contintue.
          $message = $this->t("This status could not be updated to '%status' for the %label");
          $replacements = [
            '%label' => $par_data_partnership->label(),
            '%status' => $status,
          ];
          $this->getLogger($this->getLoggerChannel())
            ->error($message, $replacements);
        }
      }

      if ($checkbox && $par_data_partnership->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %confirm could not be saved for %form_id');
        $replacements = [
          '%confirm' => $par_data_partnership->get('partnership_info_agreed_authority')->toString(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
