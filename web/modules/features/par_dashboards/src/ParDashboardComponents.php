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
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\ParDataManager;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
* Manages all functionality universal to Par Data.
*/
class ParDashboardComponents {

  use StringTranslationTrait;

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
   * @param \Drupal\par_data\ParDataManager $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface
   *   The messenger.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, RendererInterface $renderer, MessengerInterface $messenger) {
    $this->parDataManager = $par_data_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->renderer = $renderer;
    $this->messenger = $messenger;
  }

  /**
   * Lazy loader callback to generate partnership content.
   */
  public function renderPartnerships($build = []) {
    // Your partnerships.
    $partnerships = $this->getParDataManager()->hasMembershipsByType($this->getCurrentUser(), 'par_data_partnership');

//    $can_manage_partnerships = $this->getCurrentUser()->hasPermission('confirm partnership') ||
//      $this->getCurrentUser()->hasPermission('update partnership authority details') ||
//      $this->getCurrentUser()->hasPermission('update partnership organisation details');
//    $can_create_partnerships = $this->getCurrentUser()->hasPermission('apply for partnership');
//    if (($partnerships && $can_manage_partnerships) || $can_create_partnerships) {
      // Cache context needs to be added for users with memberships.
      $build['partnerships'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Your partnerships'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#cache' => ['contexts' => ['user.par_memberships:authority']]
      ];

      // List of partnerships and pending applications links.
      if (($partnerships)) {
        $new_partnerships = count($this->getParDataManager()->hasInProgressMembershipsByType($account, 'par_data_partnership'));

        $manage_partnerships = $this->getLinkByRoute('view.par_user_partnerships.partnerships_page');
        $link_text = $new_partnerships > 0 ?
          $this->t('See your partnerships (@count pending)', ['@count' => $new_partnerships]) :
          $this->t('See your partnerships');
        $manage_link = $manage_partnerships->setText($link_text)->toString();
        $build['partnerships']['see'] = [
          '#type' => 'markup',
          '#markup' => "<p>{$manage_link}</p>",
        ];
      }

      // Partnership application link.
      if (TRUE) {
        $create_partnerships = $this->getLinkByRoute('par_partnership_application_flows.partnership_application_start');
        $apply_link = $create_partnerships->setText('Apply for a new partnership')->toString();
        $build['partnerships']['add'] = [
          '#type' => 'markup',
          '#markup' => "<p>{$apply_link}</p>",
        ];
      }
//    }

    return $build;
  }
}
