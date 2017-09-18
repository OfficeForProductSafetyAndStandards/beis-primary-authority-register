<?php

namespace Drupal\par_flows\Controller;

use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* A controller for all styleguide page output.
*/
class ParBaseController extends ControllerBase implements ParBaseInterface {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use StringTranslationTrait;

  /**
   * The flow entity storage class, for loading flows.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $flowStorage;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * A machine safe value representing the current form journey.
   *
   * @var string
   */
  protected $flow;

  /**
   * The account for the current logged in user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $userAccount;

  /**
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The current user object.
   */
  public function __construct(ConfigEntityStorageInterface $flow_storage, ParDataManagerInterface $par_data_manager) {
    $this->flowStorage = $flow_storage;
    $this->parDataManager = $par_data_manager;

    $this->setCurrentUser();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_flow'),
      $container->get('par_data.manager')
    );
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    return $this->t('Primary Authority Register');
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
    $cache = array(
      '#cache' => array(
        'contexts' => $this->getCacheContexts(),
        'tags' => $this->getCacheTags(),
        'max-age' => $this->getCacheMaxAge(),
      )
    );

    return $build + $cache;
  }

  /**
   * Set the current user account.
   */
  public function setCurrentUser() {
    if (\Drupal::currentUser()->isAuthenticated()) {
      $this->userAccount = User::load(\Drupal::currentUser()->id());
    }
  }

  /**
   * Get the current user account.
   */
  public function getUserAccount() {
    return $this->userAccount;
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par_flows';
  }

  /**
   * Returns the PAR data manager.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   *   Get the logger channel to use.
   */
  public function getParDataManager() {
    return $this->parDataManager;
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

}
