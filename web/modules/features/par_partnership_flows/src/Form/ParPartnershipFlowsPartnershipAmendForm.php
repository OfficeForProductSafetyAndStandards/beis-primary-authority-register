<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormPluginInterface;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Form to add legal entities to partnerships.
 */
class ParPartnershipFlowsPartnershipAmendForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /*
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $negotiation
   *   The flow negotiator.
   * @param \Drupal\par_flows\ParFlowDataHandlerInterface $data_handler
   *   The flow data handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   The par form builder.
   */
  /*public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager, UrlGeneratorInterface $url_generator) {
    parent::__construct($negotiator, $data_handler, $par_data_manager, $plugin_manager, $url_generator);
    $flow = $this->getFlowNegotiator()->getFlow();
    $actions = $flow->getActions();
    if (($key = array_search('save', $actions)) !== false) {
      unset($actions[$key]);
      $flow->setActions($actions);
    }
  }*/
  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Add legal entities to the partnership';

    return parent::titleCallback();
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Access according to route and user role.
    switch ($route_match->getRouteName()) {
      case 'par_partnership_flows.authority_amend_select':
      case 'par_partnership_flows.authority_amend_declaration':
      case 'par_partnership_flows.authority_amend_terms':
      case 'par_partnership_flows.authority_amend_complete':
        if (!in_array('par_authority', $account->getRoles())) {
          $this->accessResult = AccessResult::forbidden('The user is not allowed to access the authority partnership amendment journey.');
        }
        break;
      case 'par_partnership_flows.organisation_amend_display':
      case 'par_partnership_flows.organisation_amend_terms':
        if (!in_array('par_organisation', $account->getRoles())) {
          $this->accessResult = AccessResult::forbidden('The user is not allowed to access the organisation partnership amendment approval journey.');
        }
        elseif (empty($par_data_partnership->getPartnershipLegalEntities(FALSE, 'confirmed_authority'))) {
          $this->accessResult = AccessResult::forbidden('There are no partnership amendments to approve.');
        }
        break;
      case 'par_partnership_flows.organisation_amend_complete':
        if (!in_array('par_organisation', $account->getRoles())) {
          $this->accessResult = AccessResult::forbidden('The user is not allowed to access the organisation partnership amendment approval journey.');
        }
        break;
      default:
        $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Configure the components on the current form.
    $this->getComponents(); // Instantiates components.
    $form_id = $this->getFormId();
    switch ($form_id) {
      case 'par_partnership_authority_amend_select':
        $comp = $this->getComponent('legal_entities_amend');
        $config = $comp->getConfiguration();
        $config['edit'] = TRUE;
        $config['ple_status'] = 'awaiting_review';
        $comp->setConfiguration($config);
        break;
      case 'par_partnership_authority_amend_declaration':
        break;
      case 'par_partnership_authority_amend_terms':
        $comp = $this->getComponent('terms_and_conditions');
        $config = $comp->getConfiguration();
        $config['help_paras'] = [
          'You won\'t be able to change the legal entities being added to the partnership after ' .
            'you press \'Save\'. Are you sure that everything is correct?',
          ' Press \'Back\' to return to the list and make further changes.',
          ];
        $comp->setConfiguration($config);
        break;
      case 'par_partnership_authority_amend_complete':
        $comp = $this->getComponent('journey_complete');
        $config = $comp->getConfiguration();
        $config['panel_title'] = 'Partnership amendment created';
        $config['panel_body'] = 'Now awaiting approval by the business organisation';
        $config['info_paras'] = [
          'We have saved the partnership amendments you have made.',
        ];
        $config['what_happens_next_paras'] = [
          'The business organisation has been informed of the amendments and asked for their approval.',
          'Once the business approves the amendments OSU will complete the nomination.',
        ];
        $comp->setConfiguration($config);
        break;
      case 'par_partnership_organisation_amend_display':
        $comp = $this->getComponent('legal_entities_amend');
        $config = $comp->getConfiguration();
        $config['edit'] = FALSE;
        $config['ple_status'] = 'confirmed_authority';
        $comp->setConfiguration($config);
        break;
      case 'par_partnership_organisation_amend_terms':
        $comp = $this->getComponent('terms_and_conditions');
        $config = $comp->getConfiguration();
        $config['help_paras'] = [
          'To confirm your acceptance of the legal entity amendment(s) press \'Save\'.',
        ];
        $comp->setConfiguration($config);
        break;
      case 'par_partnership_organisation_amend_complete':
        $comp = $this->getComponent('journey_complete');
        $config = $comp->getConfiguration();
        $config['panel_title'] = 'Approval complete';
        $config['panel_body'] = 'Now awaiting nomination by the representative of the Secretary of State';
        $config['info_paras'] = [
          'Your approval of the partnership amendments has been noted.'
        ];
        $config['what_happens_next_paras'] = [
          'The representative of the Secretary of State will approve the nomination of the partnership.',
        ];
        $comp->setConfiguration($config);
        break;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Depending on the form being processed we update the status of the partnership legal entities.
    $map = [
      'par_partnership_authority_amend_terms' => ['from' => 'awaiting_review', 'to' => 'confirmed_authority'],
      'par_partnership_organisation_amend_terms' => ['from' => 'confirmed_authority', 'to' => 'confirmed_business'],
      'par_partnership_osu_amend_terms' => ['from' => 'confirmed_business', 'to' => 'confirmed_rd'],
    ];
    if (isset($map[$this->getFormId()])) {
      /* @var ParDataPartnership $partnership */
      $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $amend_partnership_legal_entities = $partnership->getPartnershipLegalEntities(FALSE, $map[$this->getFormId()]['from']);
      foreach ($amend_partnership_legal_entities as $amend_partnership_legal_entity) {
        $amend_partnership_legal_entity->setPartnershipLegalEntityStatus($map[$this->getFormId()]['to']);
        $amend_partnership_legal_entity->save();
      }
    }

    parent::submitForm($form, $form_state);
  }
}
