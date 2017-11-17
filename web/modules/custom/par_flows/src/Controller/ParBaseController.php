<?php

namespace Drupal\par_flows\Controller;

use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_flows\ParDisplayTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessResult;

/**
* A controller for all styleguide page output.
*/
class ParBaseController extends ControllerBase implements ParBaseInterface {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use StringTranslationTrait;
  use ParControllerTrait;

  /**
   * The access result
   *
   * @var \Drupal\Core\Access\AccessResult
   */
  protected $accessResult;

  /**
   * The flow entity storage class, for loading flows.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $flowStorage;

  /**
   * A machine safe value representing the current form journey.
   *
   * @var string
   */
  protected $flow;

  /**
   * Page title.
   *
   * @var string
   */
  protected $pageTitle;

  /**
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The current user object.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user object.
   */
  public function __construct(ConfigEntityStorageInterface $flow_storage, ParDataManagerInterface $par_data_manager, AccountInterface $current_user) {
    $this->flowStorage = $flow_storage;
    $this->parDataManager = $par_data_manager;
    $this->setCurrentUser();
    $this->setCurrentUser($current_user);

    // If no flow entity exists throw a build error.
    if (!$this->getFlow()) {
      $this->getLogger($this->getLoggerChannel())->critical('There is no flow %flow for this form.', ['%flow' => $this->getFlowName()]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_flow'),
      $container->get('par_data.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    if (!$this->pageTitle &&
      $default_title = $this->getFlow()->getDefaultTitle()) {
      return $default_title;
    }

    // Do we have a form flow subheader?
    if (!empty($this->getFlow()->getDefaultSectionTitle() &&
      !empty($this->pageTitle))) {
      $this->pageTitle = "{$this->getFlow()->getDefaultSectionTitle()} | {$this->pageTitle}";
    }

    return $this->pageTitle;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user.roles', 'route'];
  }

  /**
   * {@inheritdoc}
   */
  public function build($build) {
    if ($this->getFlow()->hasAction('done')) {
      $build['done'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('done', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
            ->setText('Done')
            ->toString(),
        ]),
      ];
    }
    else {
      if ($this->getFlow()->hasAction('next')) {
        $build['next'] = [
          '#type' => 'markup',
          '#prefix' => '<div class="form-group">',
          '#suffix' => '</div>',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('next', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
              ->setText('Continue')
              ->toString(),
          ]),
        ];
      }

      if ($this->getFlow()->hasAction('cancel')) {
        $build['cancel'] = [
          '#type' => 'markup',
          '#prefix' => '<div>',
          '#suffix' => '</div>',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getPrevLink('cancel')
              ->setText('Cancel')
              ->toString(),
          ]),
        ];
      }
    }

    $cache = [
      '#cache' => [
        'contexts' => $this->getCacheContexts(),
        'tags' => $this->getCacheTags(),
        'max-age' => $this->getCacheMaxAge(),
      ]
    ];

    return $build + $cache;
  }

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
  public function getFlowName() {
    if (empty($this->flow)) {
      throw new ParFlowException('The flow must have a name.');
    }
    return $this->flow;
  }

  /**
   * Get the current flow entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The flow entity.
   */
  public function getFlow() {
    return $this->getFlowStorage()->load($this->getFlowName());
  }

  /**
   * Get the injected Flow Entity Storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The flow storage handler.
   */
  public function getFlowStorage() {
    return $this->flowStorage;
  }

  /**
   * Access callback
   * Useful for custom business logic for access.
   *
   * @see \Drupal\Core\Access\AccessResult
   *   The options for callback.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function accessCallback() {
    return $this->accessResult ? $this->accessResult : AccessResult::allowed();
  }

}
