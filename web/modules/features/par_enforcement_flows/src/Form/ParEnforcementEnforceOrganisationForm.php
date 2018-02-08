<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;

/**
 * The de-duping form.
 */
class ParEnforcementEnforceOrganisationForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforce_organisation';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $this->retrieveEditableValues($par_data_partnership);
    // If the partnership is direct we can set the organisation
    // and move onto the next step.
    if ($par_data_partnership->isDirect()) {
      $organisation = current($par_data_partnership->get('field_organisation')->referencedEntities());
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', $organisation->id());
      return $this->redirect($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
    }
    elseif ($par_data_partnership->isCoordinated()) {
      $members = [];
      foreach ($par_data_partnership->get('field_coordinated_business')->referencedEntities() as $coordinated_member) {
        $coordinated_organisation = $coordinated_member->get('field_organisation')->referencedEntities();
        $members = $this->getParDataManager()->getEntitiesAsOptions($coordinated_organisation, $members);
      }

      if (empty($members)) {
        $form['no_members'] = [
          '#type' => 'markup',
          '#markup' => $this->t('Sorry but there are no members for this organisation.'),
          '#prefix' => '<p><strong>',
          '#suffix' => '</strong><p>',
        ];

        $this->getFlowNegotiator()->getFlow()->disableAction('next');
      }
      else {
        // Initialize pager and get current page.
        $number_of_items = 10;
        $current_page = pager_default_initialize(count($members), $number_of_items);

        // Split the items up into chunks:
        $chunks = array_chunk($members, $number_of_items, TRUE);

        // Add the items for our current page to the fieldset.
        $page_options = [];

        foreach ($chunks[$current_page] as $delta => $item) {
          $page_options[$delta] = $item;
        }

        $form['par_data_organisation_id'] = [
          '#type' => 'radios',
          '#title' => t('Choose the member to enforce'),
          '#options' => $page_options,
          '#default_value' => $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', []),
        ];

        $form['pager'] = [
          '#type' => 'pager',
          '#theme' => 'pagerer',
          '#element' => 0,
          '#config' => [
            'preset' => $this->config('pagerer.settings')
              ->get('core_override_preset'),
          ],
        ];
      }
    }

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

    // If an existing organisation was selected and has an address
    // and contact, skip to the review step, or skip to the contact
    // step if an existing organisation was selected which has an
    // address but no contact.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_organisation_suggestion');
    $organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', '', $cid);
    if ($par_data_organisation = ParDataOrganisation::load($organisation_id)) {
      if (!$par_data_organisation->get('field_person')->isEmpty()) {
        $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getNextRoute('review'), $this->getRouteParams());
      }
      elseif ($par_data_organisation->get('field_person')->isEmpty()
        && !$par_data_organisation->get('field_premises')->isEmpty()) {
        $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getNextRoute('add_contact'), $this->getRouteParams());
      }
    }
  }

}
