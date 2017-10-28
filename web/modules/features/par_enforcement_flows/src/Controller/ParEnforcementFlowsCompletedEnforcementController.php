<?php

namespace Drupal\par_enforcement_flows\Controller;

use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\Core\Access\AccessResult;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnforcementFlowsCompletedEnforcementController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    $allowed_actions = [
      ParDataEnforcementAction::APPROVED,
      ParDataEnforcementAction::BLOCKED,
      ParDataEnforcementAction::REFERRED,
      ParDataEnforcementAction::AWAITING,
    ];

    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      // If this enforcement notice has any actions that have not yet been reviewed deny access.
      if (in_array($action->getRawStatus(), $allowed_actions) && $action->getRawStatus() === ParDataEnforcementAction::AWAITING) {
        $this->accessResult = AccessResult::forbidden('This enforcement notification has not been fully reviewed yet.');
      }
    }
    return parent::accessCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return;
  }

  /**
   * {@inheritdoc}
   */
  public function content(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    // Organisation summary.
    $partnership = current($par_data_enforcement_notice->getPartnership());
    $par_data_organisation = current($partnership->getOrganisation());
    $par_data_authority = current($partnership->getAuthority());

    // Load all enforcement actions for the current enforcement notification.
    $enforcement_actions = $par_data_enforcement_notice->getEnforcementAction();

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
      '#markup' => $par_data_authority->get('authority_name')->getString(),
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
      '#markup' => $par_data_organisation->get('organisation_name')->getString(),
    ];

    $build['registered_address']['registered_address_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Business address'),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    // Display the primary address.
    $build['registered_address']['address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);
    $build['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'summary'], [], TRUE, TRUE);

    $doc_title =[
      '#type' => 'markup',
      '#markup' => $this->t('Attached files'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3>',
    ];
    // Display all enforcement actions assigned to this enforcement action.
    foreach ($enforcement_actions as $enforcement_action) {
      $build[$enforcement_action->id()]['action_title'] = $this->renderSection('Proposed enforcement action', $enforcement_action, ['title' => 'title'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['action_regulatory_function'] = $this->renderSection('Regulatory function', $enforcement_action, ['field_regulatory_function' => 'summary'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['action_details'] = $this->renderSection('Details', $enforcement_action, ['details' => 'summary'], [], TRUE, TRUE);
      $build[$enforcement_action->id()]['action_attach_title'] = $doc_title;
      $build[$enforcement_action->id()]['action_attach'] = $this->renderMarkupField($enforcement_action->get('document')->view('full'));
    }
    return parent::build($build);
  }
}
