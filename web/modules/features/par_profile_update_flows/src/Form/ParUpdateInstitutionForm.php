<?php

namespace Drupal\par_profile_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for adding users to an institution.
 */
class ParUpdateInstitutionForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Title property.
   */
  protected $pageTitle = 'Update which authorities or organisations this person belongs to';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $account = $this->getCurrentUser();
    if ($update_account = $this->getFlowDataHandler()->getParameter('user')) {
      // Get the person being updated.
      $cid_person_select = $this->getFlowNegotiator()->getFormKey('par_choose_person');
      $person = $this->getFlowDataHandler()->getDefaultValues('user_person', '', $cid_person_select);
      $par_data_person = ParDataPerson::load($person);

      // Get the directly related authorities and organisations.
      $par_relationship_manager = $this->getParDataManager()->getReducedIterator(1);
      $memberships = $par_relationship_manager->getRelatedEntities($par_data_person);
      $user_organisations = array_filter($memberships, function ($membership) {
        return ('par_data_organisation' === $membership->getEntityTypeId());
      });
      $user_authorities = array_filter($memberships, function ($membership) {
        return ('par_data_authority' === $membership->getEntityTypeId());
      });

      // Store the organisation ids.
      $organisation_ids = [];
      foreach ($user_organisations as $user_organisation) {
        $organisation_ids[] = $user_organisation->id();
      }
      $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_id', $organisation_ids);

      // Store the authority ids.
      $authority_ids = [];
      foreach ($user_authorities as $user_authority) {
        $authority_ids[] = $user_authority->id();
      }
      $this->getFlowDataHandler()->setFormPermValue('par_data_authority_id', $authority_ids);
    }

    // The default for this form is for none of these to be required.
    $this->getFlowDataHandler()->setFormPermValue('organisation_required', FALSE);
    $this->getFlowDataHandler()->setFormPermValue('authority_required', FALSE);

    $authorities = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_authority');
    $organisations = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_organisation');

    if (isset($organisations)) {
      $organisation_options = isset($organisations) ? $this->getParDataManager()->getEntitiesAsOptions($organisations, [], 'summary') : [];
      $this->getFlowDataHandler()->setFormPermValue('organisations', $organisation_options);
    }
    if (isset($authorities)) {
      $authority_options = isset($authorities) ? $this->getParDataManager()->getEntitiesAsOptions($authorities, [], 'summary') : [];
      $this->getFlowDataHandler()->setFormPermValue('authorities', $authority_options);
    }

    $this->getFlowDataHandler()->setFormPermValue('allow_multiple', TRUE);

    // We explicitly want to override the default plugin loadData() methods.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $cid_link_account = $this->getFlowNegotiator()->getFormKey('par_profile_update_link');
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $cid_link_account);

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('role', '', $cid_role_select);

    // Skip the invitation process if a user id has already been matched
    // or the user has choosen not to add a user.
    if (!empty($user_id) || !$role) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute(), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // If
  }

}
