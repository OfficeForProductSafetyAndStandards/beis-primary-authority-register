<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionPaListOfTasks extends ParBaseController  {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_partnership_details';

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

    // Get referenced organisation for use with Business name/address component.
    $organisation = $par_data_partnership->get('organisation')->referencedEntities();
    $organisation = current($organisation);

    $this->loadDataValue("organisation_name", $organisation->get('name')->getString());

    // Get organisation address.
    $premises = $organisation->get('premises')->referencedEntities();
    $premises = array_shift($premises);

    $this->loadDataValue("organisation_address", $premises->get('address'));

    // Primary Contact.
    $this->loadDataValue('primary_person_id', $primary_person->id());
    $this->loadDataValue("person_{$primary_person->id()}_name", $primary_person->get('person_name')->getString());
    $this->loadDataValue("person_{$primary_person->id()}_phone", $primary_person->get('work_phone')->getString());
    $this->loadDataValue("person_{$primary_person->id()}_email", $primary_person->get('email')->getString());
    $this->loadDataValue("person_{$primary_person->id()}_role", $primary_person->get('role')->getString());

  }

  /**
   * @param ParDataPartnership $partnership_status
   *
   * @return boolean
   */
  public function arePartnershipDetailsConfirmed(ParDataPartnership $partnership) {

    $partnership_status = $partnership->get('partnership_status')->getString();

    return ($partnership_status == 'Approved') ? true : false;

  }

  /**
   * {@inheritdoc}
   */
  public function content(ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {
    //$this->retrieveEditableValues($par_data_authority, $par_data_partnership);

    // About the Partnership.
    $form['first_section'] = [
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['first_section']['about_partnership'] = [
      '#theme' => 'par_components_business_name_address',
      '#name' => $this->getDefaultValues("organisation_name", '', $this->getFlow()->getFormIdByStep(2)),
      '#address' => $this->getDefaultValues("organisation_address", '')
    ];

    $form['second_section'] = [
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // I think it would be awesome if we could do something like.
    // Get all the people and divide them into primary and alternative contacts.
    $partnership_people = $par_data_partnership->get('person')->referencedEntities();
    $partnership_primary_person = array_shift($partnership_people);
    // I'd like to make this getViewBuilder method something we could call on the entity itself.
    $par_data_person_vb = \Drupal::entityTypeManager()->getViewBuilder($partnership_primary_person->getEntityTypeId());
    // This would return the render array.
    $par_data_person_vb->view($partnership_primary_person->getEntityTypeId(), 'full');
    // What we can do in each entity's view builder is control the output so it always comes out as a component like below.
    // I've got lots of examples of manipulating view builders and it's all quite nice and simple really.
    // Then we don't ever need to worry about building up these arrays ourselves :)
    // The thing about building our own render arrays is that we loose a lot of functionality like cache keys,
    // so let's keep to the way entities build them as much as possible.
    $form['second_section']['primary_person'] = [
      '#theme' => 'par_components_business_primary_contact',
      '#name' => $this->getDefaultValues("person_{$primary_person_id}_name", '', $this->getFlow()->getFormIdByStep(2)),
      '#telephone' => $this->getDefaultValues("person_{$primary_person_id}_phone", '', $this->getFlow()->getFormIdByStep(2)),
      '#email' => $this->getDefaultValues("person_{$primary_person_id}_email", '', $this->getFlow()->getFormIdByStep(2)),
      '#role' => $this->getDefaultValues("person_{$primary_person_id}_role", '', $this->getFlow()->getFormIdByStep(2)),
    ];

    // Table headers.
    $header = [];

    // Table data/cells.
    $rows = [
      [$this->getLinkByRoute('par_flow_transition_partnership_details.overview')
        ->setText('Review and confirm your partnership details')
        ->toString(), $this->arePartnershipDetailsConfirmed($par_data_partnership) ? 'Approved' : 'awaiting review'],
      [$this->getLinkByRoute('par_flow_transition_partnership_details.overview')
        ->setText('Invite the business to confirm their details')
        ->toString(), 'awaiting review / confirmed'],
      [$this->getLinkByRoute('par_flow_transition_partnership_details.overview')
        ->setText('Review and confirm your Inspection Plan')
        ->toString(), 'awaiting review / confirmed'],
      // @todo replace @business_name with just organisation/business name.
      [$this->getLinkByRoute('par_flow_transition_partnership_details.overview')
        ->setText($this->t('Review and confirm your documentation for @organisation_name', ['@organisation_name' => $this->getDefaultValues('organisation_name', '', $this->getFlow()->getFormIdByStep(3))]))
        ->toString(), 'awaiting review / confirmed'],
    ];

    // First table.
    // $form['basic_table_title'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Basic data table") . '</h2>'];
    $form['basic_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t("No tasks could be found."),
    ];

    $form['save_and_continue'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getLinkByRoute('par_flow_transition_partnership_details.overview', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Save and continue')
          ->toString()
      ]),
    ];

    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getLinkByRoute('<front>')
          ->setText('Cancel')
          ->toString()
      ]),
    ];

    return $form;

  }

}

