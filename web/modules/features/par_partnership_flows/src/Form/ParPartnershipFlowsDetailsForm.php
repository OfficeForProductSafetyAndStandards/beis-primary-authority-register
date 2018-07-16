<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

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

    // Display the primary address along with the link to edit it.
    $form['registered_address'] = $this->renderSection('Organisation address', $par_data_organisation, ['field_premises' => 'summary'], ['edit-entity', 'add'], TRUE, TRUE);

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
//        $form['associations']['upload_link'] = [
//          '#type' => 'markup',
//          '#markup' => t('@link', [
//            '@link' => Link::createFromRoute('Upload a Member List (CSV)', 'par_member_upload_flows.member_upload', $this->getRouteParams())->toString(),
//          ]),
//          '#weight' => -100,
//          '#prefix' => '<p>',
//          '#suffix' => '</p>',
//        ];
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
      $operations = ['edit-entity','add'];
    }
    $form['legal_entities'] = $this->renderSection('Legal entities', $par_data_partnership, ['field_legal_entity' => 'summary'], $operations);

    // Display all the trading names along with the links for the allowed
    // operations on these.
    $form['trading_names'] = $this->renderSection('Trading names', $par_data_organisation, ['trading_name' => 'full'], ['edit-field', 'add']);

    // Everything below is for the authority to edit and add to.
    $par_data_authority = $par_data_partnership->getAuthority(TRUE);
    $form['authority'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority ? $par_data_authority->get('authority_name')->getString() : '',
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display details about the partnership for information.
    $form['partnership_since'] = $this->renderSection('In partnership since', $par_data_partnership, ['approved_date' => 'full']);

    // Display details about the partnership for information.
    $form['regulatory_functions'] = $this->renderSection('Partnered for', $par_data_partnership, ['field_regulatory_function' => 'full']);

    // Display details about the partnership for information.
    $form['about_partnership'] = $this->renderSection('About the partnership', $par_data_partnership, ['about_partnership' => 'about'], ['edit-field']);

    $form['inspection_plans'] = [
      '#type' => 'fieldset',
      '#title' => t('Inspection plans'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['inspection_plans']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getNextLink('inspection_plans')
          ->setText('See all Inspection Plans')
          ->toString(),
      ]),
    ];

    $form['advice'] = [
      '#type' => 'fieldset',
      '#title' => t('Advice and Documents'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['advice']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getNextLink('advice')
          ->setText('See all Advice')
          ->toString(),
      ]),
    ];

    // Display the authority contacts for information.
    $form['authority_contacts'] = $this->renderSection('Contacts at the Primary Authority', $par_data_partnership, ['field_authority_person' => 'detailed'], ['edit-entity', 'add']);

    // Display all the legal entities along with the links for the allowed
    // operations on these.
    $form['organisation_contacts'] = $this->renderSection('Contacts at the organisation', $par_data_partnership, ['field_organisation_person' => 'detailed'], ['edit-entity', 'add']);

    $checkbox = $this->getInformationCheckbox();
    if (!$par_data_partnership->getBoolean($checkbox)) {
      $form[$checkbox] = [
        '#type' => 'checkbox',
        '#title' => t("I confirm I have reviewed the information above"),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues($checkbox, FALSE),
        '#disabled' => $this->getFlowDataHandler()->getDefaultValues($checkbox, FALSE),
        '#return_value' => 'on',
      ];
    }

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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Make sure the confirm box is ticked.
    $checkbox = $this->getInformationCheckbox();
    if (!$par_data_partnership->getBoolean($checkbox) && !$form_state->getValue($checkbox)) {
      $this->setElementError($checkbox, $form_state, 'Please confirm you have reviewed the details.');
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
        $par_data_partnership->set('partnership_status',
          ($checkbox === 'partnership_info_agreed_authority') ? 'confirmed_authority' : 'confirmed_business');
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
