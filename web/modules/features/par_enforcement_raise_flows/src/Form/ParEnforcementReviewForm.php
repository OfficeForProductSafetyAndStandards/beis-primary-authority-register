<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Symfony\Component\Routing\Route;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Review the enforement notice';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataEnforcementNotice $par_data_enforcement_notice */
    /** @var ParDataEnforcementAction[] $par_data_enforcement_actions */

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());

    // Display a link to change the enforced entity.
    $form['enforced_organisation'] = [
      '#type' => 'fieldset',
      '#weight' => 101,
      '#attributes' => ['class' => 'form-group'],
    ];
    $form['enforced_organisation']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('select_legal', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the enforced legal entity')
          ->toString(),
      ]),
    ];

    // Display the Enforcement Notice details.
    $form['enforcement_type'] = $this->renderSection('Type of enforcement notice', $par_data_enforcement_notice, ['notice_type' => 'full'], [], TRUE, TRUE);
    $form['enforcement_type']['#weight'] = 200;
    $form['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_summary']['#weight'] = 201;
    $form['enforcement_summary']['summary']['operations']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('enforcement_details', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the summary of this enforcement')
          ->toString(),
      ]),
    ];

    // Display the details for each Enforcement Action.
    $form['enforcement_actions'] = $this->renderEntities('Enforcement Actions', $par_data_enforcement_actions, 'summary');
    $form['enforcement_actions']['#weight'] = 202;
    foreach ($par_data_enforcement_actions as $delta => $entity) {
      $index = $delta + 1;
      $params = $this->getRouteParams() + ['cardinality' => $index];
      $form['enforcement_actions'][$delta]['operations']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('enforcement_action', $params, ['query' => ['destination' => $return_path]])
            ->setText("Change action $index")
            ->toString(),
        ]),
      ];
    }


    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $enforcement_notice_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_raise_details');
    $enforcement_actions_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_add_action');
    $enforcement_officer_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_officer_details');
    $enforcing_authority_cid = $this->getFlowNegotiator()->getFormKey('par_authority_selection');
    $organisation_select_cid = $this->getFlowNegotiator()->getFormKey('par_enforce_organisation');
    $legal_entity_select_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_raise');

    $date = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);

    // Create the enforcement notice.
    $par_data_enforcement_notice = ParDataEnforcementNotice::create([
      'notice_type' => $this->getFlowDataHandler()->getTempDataValue('notice_type', $enforcement_notice_cid),
      'summary' => $this->getFlowDataHandler()->getTempDataValue('summary', $enforcement_notice_cid),
      'field_primary_authority' => $par_data_partnership->getAuthority(),
      'field_enforcing_authority' => $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', NULL, $enforcing_authority_cid),
      'field_person' =>  $this->getFlowDataHandler()->getDefaultValues('enforcement_officer_id', NULL, $enforcement_officer_cid),
      'field_partnership' => $par_data_partnership->id(),
      'notice_date' => $date->format('Y-m-d'),
    ]);

    // Save any reference to an enforced organisation.
    if ($organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', NULL, $organisation_select_cid)) {
      $par_data_enforcement_notice->set('field_organisation', $organisation_id);
    }

    // Save the entered legal entity.
    if ($legal_entity_id = $this->getFlowDataHandler()->getDefaultValues('legal_entities_select', NULL, $legal_entity_select_cid)) {
      if ($legal_entity_id === 'add_new') {
        $par_data_enforcement_notice->set('field_legal_entity', $legal_entity_id);
      }
      else {
        $par_data_enforcement_notice->set('legal_entity_name', $this->getFlowDataHandler()->getDefaultValues('alternative_legal_entity', '', $legal_entity_select_cid));
      }
    }

    // Create the enforcement actions.
    $enforcement_actions = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action', $enforcement_actions_cid) ?: [];
    $par_data_enforcement_actions = [];
    foreach ($enforcement_actions as $delta => $enforcement_action) {
      // These ones need to be saved fresh.
      $par_data_enforcement_actions[$delta] = ParDataEnforcementAction::create([
        'title' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action', $delta, 'title'], $enforcement_actions_cid),
        'details' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action', $delta, 'details'], $enforcement_actions_cid),
        'document' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action', $delta, 'files'], $enforcement_actions_cid),
        'field_regulatory_function' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action', $delta, 'regulatory_function'], $enforcement_actions_cid),
      ]);
    }

    return [
      'par_data_partnership' => $par_data_partnership,
      'par_data_enforcement_notice' => $par_data_enforcement_notice,
      'par_data_enforcement_actions' => $par_data_enforcement_actions,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataEnforcementNotice $par_data_enforcement_notice */
    /** @var ParDataEnforcementAction[] $par_data_enforcement_actions */

    foreach ($par_data_enforcement_actions as $enforcement_action) {
      if ($enforcement_action->save()) {
        $par_data_enforcement_notice->get('field_enforcement_action')->appendItem($enforcement_action);
      }
    }

    if (!$par_data_enforcement_notice->get('field_enforcement_action')->isEmpty() && $par_data_enforcement_notice->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('An enforcement could not be saved: %partnership');
      $replacements = [
        '%partnership' => $par_data_partnership->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
