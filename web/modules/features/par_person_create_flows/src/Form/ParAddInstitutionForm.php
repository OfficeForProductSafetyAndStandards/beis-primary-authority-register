<?php

namespace Drupal\par_person_create_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_person_create_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for adding users to an institution.
 */
class ParAddInstitutionForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Title callback default.
   */
  public function titleCallback() {
    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('role', '', $cid_role_select);

    switch ($role) {
      case 'par_organisation':
        $this->pageTitle = 'Add this person to an organisation';

        break;

      case 'par_authority':
      case 'par_enforcement':
        $this->pageTitle = 'Add this person to an authority';

        break;

      default:
        $this->pageTitle = 'Add this person to an authority or organisation';
    }

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $account = $this->getCurrentUser();

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('role', '', $cid_role_select);

    // The default for this form is for non of these to be required.
    $this->getFlowDataHandler()->setFormPermValue('organisation_required', FALSE);
    $this->getFlowDataHandler()->setFormPermValue('authority_required', FALSE);

    switch ($role) {
      case 'par_organisation':
        $organisations = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_organisation');
        // Adding an organisation is required for organisation members.
        $this->getFlowDataHandler()->setFormPermValue('organisation_required', TRUE);

        break;

      case 'par_authority':
      case 'par_enforcement':
        $authorities = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_authority');
        // Adding an authority is required for authority members.
        $this->getFlowDataHandler()->setFormPermValue('authority_required', TRUE);

        break;
      case 'par_helpdesk':
        // Helpdesk users don't belong to any single authority or organisation.
        $this->getFlowDataHandler()->setFormPermValue('skip', TRUE);

        break;
      default:
        $authorities = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_authority');
        $organisations = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_organisation');
        // If no user is being created it is still possible to add this person to
        // an authority or organisation, but if there are none this should be skipped.
        if (empty($authorities) && empty($organisations)) {
          $this->getFlowDataHandler()->setFormPermValue('skip', TRUE);
        }
    }

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
    // If the user has choosen not
    if ($this->getFlowDataHandler()->getDefaultValues('skip', FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
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
