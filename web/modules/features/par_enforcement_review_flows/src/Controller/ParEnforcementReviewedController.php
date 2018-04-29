<?php

namespace Drupal\par_enforcement_review_flows\Controller;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnforcementReviewedController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Enforcement notice reviewed';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    $reason = NULL;

    // Organisation summary.
    $enforced_organisation = current($par_data_enforcement_notice->getEnforcedOrganisation());
    $enforced_legal_entity = current($par_data_enforcement_notice->getLegalEntity());
    $enforcing_authority = current($par_data_enforcement_notice->getEnforcingAuthority());
    $enforcing_officer = current($par_data_enforcement_notice->getEnforcingPerson());

    // Load all enforcement actions for the current enforcement notification.
    $enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();

    $build['authority'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['authority']['authority_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Response to notification of enforcement action received from'),
    ];

    $build['authority']['authority_name'] = [
      '#type' => 'markup',
      '#markup' => !empty($enforcing_authority) ? $enforcing_authority->get('authority_name')->getString() : NULL,
      '#prefix' => '<div><h2>',
      '#suffix' => '</h2></div>',
    ];

    $build['organisation'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['organisation']['organisation_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('In partnership with'),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    $build['organisation']['organisation_name'] = [
      '#type' => 'markup',
      '#markup' => !empty($enforced_legal_entity) ? $enforced_legal_entity->get('registered_name')->getString() : NULL,
    ];

    // Only display an address if we have an organisation.
    if (!empty($enforced_organisation)) {
      // Display the primary address.
      $build['registered_address']['address'] = $this->renderSection('Registered address', $enforced_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);
    }
    // To account for enforcement notification data created before enforcement officer release.
    if (!empty($enforcing_officer)) {
      $build['enforcement_officer_name'] = $this->renderSection('Enforcing officer name', $enforcing_officer, ['first_name' => 'summary', 'last_name' => 'summary'], [], TRUE, TRUE);
      $build['enforcement_officer_telephone'] = $this->renderSection('Enforcing officer telephone number', $enforcing_officer, ['work_phone' => 'summary'], [], TRUE, TRUE);
      $build['enforcement_officer_email'] = $this->renderSection('Enforcing officer email address', $enforcing_officer, ['email' => 'summary'], [], TRUE, TRUE);
    }
    $build['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'summary'], [], TRUE, TRUE);

    // Display all enforcement actions assigned to this enforcement action.
    foreach ($enforcement_actions as $enforcement_action) {
      $action_state = $enforcement_action->get('primary_authority_status')->getString();

      if ($action_state === ParDataEnforcementAction::REFERRED) {
        $reason =  $this->renderSection('Reason', $enforcement_action, ['referral_notes' => 'summary'], [], TRUE, TRUE);
      }
      if ($action_state === ParDataEnforcementAction::BLOCKED) {
        $reason =  $this->renderSection('Reason', $enforcement_action, ['primary_authority_notes' => 'summary'], [], TRUE, TRUE);
      }

      $build[$enforcement_action->id()]['action_id'] = $this->renderSection('Proposed enforcement action reference', $enforcement_action, ['id' => 'id'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['action_title'] = $this->renderSection('Title of action', $enforcement_action, ['title' => 'title'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['decision'] = $this->renderSection('Decision', $enforcement_action, ['primary_authority_status' => 'summary'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['reason'] = $reason ? $reason : NULL;
      $build[$enforcement_action->id()]['action_regulatory_function'] = $this->renderSection('Regulatory function', $enforcement_action, ['field_regulatory_function' => 'summary'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['action_details'] = $this->renderSection('Details', $enforcement_action, ['details' => 'summary'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['action_attachments'] =  $this->renderSection('Attachments',$enforcement_action, ['document' => 'par_attachments'], [], TRUE);
    }
    return parent::build($build);
  }
}
