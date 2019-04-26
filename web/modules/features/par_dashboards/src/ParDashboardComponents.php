<?php

namespace Drupal\par_dashboards;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\ParDataManager;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
* Manages all functionality universal to Par Data.
*/
class ParDashboardComponents {

  use StringTranslationTrait;
  use ParRedirectTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * The entity manager.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $messenger;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\Core\Session\AccountProxy $current_user
   *   The current user.
   * @param \Drupal\par_data\ParDataManager $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface
   *   The messenger.
   */
  public function __construct(AccountProxy $current_user, ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, RendererInterface $renderer, MessengerInterface $messenger) {
    $this->currentUser = $current_user;
    $this->parDataManager = $par_data_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->renderer = $renderer;
    $this->messenger = $messenger;
  }

  public function getCurrentUser() {
    if ($this->currentUser->isAuthenticated()) {
      return User::load($this->currentUser->id());
    }

    return $this->currentUser;
  }

  public function getParDataManager() {
    return $this->parDataManager;
  }

  public function managePartnershipComponent($count = FALSE) {
    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Your partnerships'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']],
    ];

    // Append a new partnership count to the link.
    if ($count) {
      $new_partnerships = count($this->getParDataManager()->hasInProgressMembershipsByType($this->getCurrentUser(), 'par_data_partnership'));
    }

    // List of partnerships and pending applications links.
    $manage_partnerships = $this->getLinkByRoute('view.par_user_partnerships.partnerships_page');
    $link_text = isset($new_partnerships) && $new_partnerships > 0 ?
      $this->t('See your partnerships (@count pending)', ['@count' => $new_partnerships]) :
      $this->t('See your partnerships');
    $manage_link = $manage_partnerships->setText($link_text)->toString();
    $build['partnerships']['see'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_link}</p>",
    ];

    // Partnership application link.
    $create_partnerships = $this->getLinkByRoute('par_partnership_application_flows.partnership_application_start');
    $apply_link = $create_partnerships->setText('Apply for a new partnership')->toString();
    $build['partnerships']['add'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$apply_link}</p>",
    ];

    return $build;
  }

  public function searchPartnershipComponent() {
    $build['partnerships_find'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Find a partnership'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $search_partnerships = $this->getLinkByRoute('view.partnership_search.enforcment_flow_search_partnerships');
    $search_link = $search_partnerships->setText('Search for a partnership')->toString();

    $build['partnerships_find']['text'] = [
      '#type' => 'markup',
      '#markup' => "<p>Search for active partnerships to check advice and raise notice of enforcement action.</p>",
    ];
    $build['partnerships_find']['link'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$search_link}</p>",
    ];

    return $build;
  }

  public function manageUsersComponent() {
    $build['people'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // User management link.
    $manage_people_link = $this->getLinkByRoute('view.par_people.people')
      ->setText('Manage your colleagues')->toString();
    $build['people']['manage'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_people_link}</p>",
    ];

    return $build;
  }

  public function manageProfileComponent() {
    $build['user'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Your account'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Profile management link.
    $manage_profile = $this->getLinkByRoute('par_profile_update_flows.gdpr', ['user' => $this->getCurrentUser()->id()]);
    $profile_link = $manage_profile->setText('Manage your profile details')->toString();
    $build['user']['profile'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$profile_link}</p>",
    ];

    return $build;
  }

  public function messagesComponent($count = FALSE) {
    $build = [];

    if ($count) {
      $new_enforcements = count($this->getParDataManager()->hasInProgressMembershipsByType($this->getCurrentUser(), 'par_data_enforcement_notice'));
      $new_deviations = count($this->getParDataManager()->hasInProgressMembershipsByType($this->getCurrentUser(), 'par_data_deviation_request'));
      $new_feedback = count($this->getParDataManager()->hasNotCommentedOnMembershipsByType($this->getCurrentUser(), 'par_data_inspection_feedback'));
      $new_enquiries = count($this->getParDataManager()->hasNotCommentedOnMembershipsByType($this->getCurrentUser(), 'par_data_general_enquiry'));
    }

    $build['messages'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Messages'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($this->getCurrentUser()->hasPermission('approve enforcement notice') ||
        $this->getCurrentUser()->hasPermission('view enforcement notice') ||
        $this->getCurrentUser()->hasPermission('send enforcement notice')) {
      $link_text = isset($new_enforcements) && $new_enforcements > 0 ?
        $this->t('See your enforcement notices (@count pending)', ['@count' => $new_enforcements]) :
        $this->t('See your enforcement notices');
      $link = $this->getLinkByRoute('view.par_user_enforcements.enforcement_notices_page')
        ->setText($link_text)
        ->toString();
      $build['messages'][] = [
        '#type' => 'markup',
        '#markup' => "<p>{$link}</p>",
      ];
    }

    if ($this->getCurrentUser()->hasPermission('review deviation request') ||
        $this->getCurrentUser()->hasPermission('view deviation request')) {
      $link_text = isset($new_deviations) && $new_deviations > 0 ?
        $this->t('See your deviation requests (@count pending)', ['@count' => $new_deviations]) :
        $this->t('See your deviation requests');
      $deviation_requests_link = $this->getLinkByRoute('view.par_user_deviation_requests.deviation_requests_page')
        ->setText($link_text)
        ->toString();
      $build['messages'][] = [
        '#type' => 'markup',
        '#markup' => "<p>{$deviation_requests_link}</p>",
      ];
    }

    if ($this->getCurrentUser()->hasPermission('view inspection feedback')) {
      $link_text = isset($new_feedback) && $new_feedback > 0 ?
        $this->t('See your inspection feedback (@count pending)', ['@count' => $new_feedback]) :
        $this->t('See your inspection feedback');
      $inspection_feedback_link = $this->getLinkByRoute('view.par_user_inspection_feedback.inspection_feedback_page')
        ->setText($link_text)
        ->toString();
      $build['messages'][] = [
        '#type' => 'markup',
        '#markup' => "<p>{$inspection_feedback_link}</p>",
      ];
    }

    if ($this->getCurrentUser()->hasPermission('view general enquiry')) {
      $link_text = isset($new_enquiries) && $new_enquiries > 0 ?
        $this->t('See your general enquiries (@count pending)', ['@count' => $new_enquiries]) :
        $this->t('See your general enquiries');
      $general_enquiries_link = $this->getLinkByRoute('view.par_user_general_enquiries.general_enquiries_page')
        ->setText($link_text)
        ->toString();
      $build['messages'][] = [
        '#type' => 'markup',
        '#markup' => "<p>{$general_enquiries_link}</p>",
      ];
    }

    return $build;
  }
}
