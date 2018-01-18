<?php

namespace Drupal\par_flows;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Entity\ParFlowInterface;
use Drupal\user\Entity\User;
use Drupal\user\Plugin\views\argument_default\CurrentUser;

class ParFlowNegotiator implements ParFlowNegotiatorInterface {

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The current route matcher.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The flow storage.
   *
   * @var \Drupal\par_flows\ParFlowStorage
   */
  protected $flow_storage;

  /**
   * The current flow entity.
   *
   * @var \Drupal\par_flows\Entity\ParFlowInterface
   */
  protected $flow;

  /**
   * The flow name.
   *
   * @var string
   */
  protected $flow_name;

  /**
   * The flow name.
   *
   * @var string
   */
  protected $flow_state;

  /**
   * Constructs a ParFlowNegotiator instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route
   *   The entity bundle info service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The entity bundle info service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ParDataManagerInterface $par_data_manager, CurrentRouteMatch $current_route, AccountInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->parDataManager = $par_data_manager;
    $this->route = $current_route;
    $this->account = $current_user->id();

    $this->flow_storage = $entity_type_manager->getStorage('par_flow');
  }

  /**
   * {@inheritdoc}
   */
  public function getRoute() {
    return $this->route;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentUser() {
    return $this->account ? User::Load($this->account->id()) : NULL;
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
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
   * {@inheritdoc}
   */
  public function getFlow() {
    return $this->flow_storage->load($this->getFlowName());
  }

  /**
   * {@inheritdoc}
   */
  public function getFlowKey($step_id = NULL, $state = NULL, $flow_name = NULL) {
    $step_id = !empty($step_id) ? $step_id : $this->getStepId();
    $state = !empty($state) ? $state : $this->getState();
    $flow_name = !empty($flow_name) ? $flow_name : $this->getFlowName();

    $key = implode(':', [$flow_name, $state, $step_id]);
    return $this->normalizeKey($key);
  }

  /**
   * {@inheritdoc}
   */
  public function getFlowName() {
    if ($this->flow_name) {
      return $this->flow_name;
    }

    // Load all flows associated with the current route.
    $flows = $this->flow_storage->loadByRoute($this->getCurrentRoute());
    if (count($flows) === 1) {
      $this->flow_name = key($flows);
    }

    // Logic to determine direct and coordinated routes.
    if ($par_data_partnership = $this->getRoute()->getParameter('par_data_partnership')) {
      // If User has helpdesk permissions && the Route is in the helpdesk flow...
      if (isset($flows['helpdesk']) && $this->getCurrentUser()->hasPermission('bypass par_data access')) {
        $this->flow_name = 'helpdesk';
      }
      // IF Route is in authority flow && User is an authority member...
      elseif (isset($flows['partnership_authority']) && $par_data_partnership->isAuthorityMember($this->getCurrentUser())) {
        return 'partnership_authority';
      }
      // If Route is in direct flow && partnership is direct...
      elseif (isset($flows['partnership_direct']) && $par_data_partnership->isDirect()) {
        $this->flow_name = 'partnership_direct';
      }
      // If Route is in coordinated flow && partnership is coordinated...
      elseif (isset($flows['partnership_coordinated']) && $par_data_partnership->isCoordinated()) {
        $this->flow_name = 'partnership_coordinated';
      }
      // If Route is in direct confirmation flow && partnership is direct...
      elseif (isset($flows['partnership_direct_application']) && $par_data_partnership->isDirect()) {
        $this->flow_name = 'partnership_direct_application';
      }
      // If Route is in coordinated flow && partnership is coordinated...
      elseif (isset($flows['partnership_coordinated_application']) && $par_data_partnership->isCoordinated()) {
        $this->flow_name = 'partnership_coordinated_application';
      }
      elseif (isset($flows['partnership_application'])) {
        $this->flow_name = 'partnership_application';
      }
    }

    // Throw an error if the flow can't be negotiated.
    if (empty($this->flow_name)) {
      if (count($flows) >= 1) {
        throw new ParFlowException('The flow name is ambiguous.');
      }
      else {
        throw new ParFlowException('The flow must have a name.');
      }
    }
    return $this->flow_name;
  }

  /**
   * Get the current route name.
   */
  public function getState() {
    if ($this->flow_state) {
      return $this->flow_state;
    }

    $states = [];

    foreach ($this->getFlow()->getStates() as $key) {
      if ($value = $this->getRoute()->getRawParameter($key)) {
        $states[] = $value;
      }
    }

    $this->flow_state = empty($states) ? 'state_default' : 'state_' . implode('_', $states);

    return $this->flow_state;
  }

  /**
   * {@inheritdoc}
   */
  public function getStepId() {
    return $this->getRoute()->getRouteName();
  }

  /**
   * Normalizes a cache ID in order to comply with key length limitations.
   *
   * @param string $key
   *   The passed in cache ID.
   *
   * @return string
   *   An ASCII-encoded cache ID that is at most 64 characters long.
   */
  public function normalizeKey($key) {
    $key = urlencode($key);
    // Nothing to do if the ID is a US ASCII string of 64 characters or less.
    $key_is_ascii = mb_check_encoding($key, 'ASCII');
    if (strlen($key) <= 64 && $key_is_ascii) {
      return $key;
    }

    // If we have generated a longer key, we shrink it to an
    // acceptable length with a configurable hashing algorithm.
    // Sha1 was selected as the default as it performs
    // quickly with minimal collisions.
    //
    // Return a string that uses as much as possible of the original cache ID
    // with the hash appended.
    $hash = hash('sha1', $key);
    if (!$key_is_ascii) {
      return $hash;
    }
    return substr($key, 0, 64 - strlen($hash)) . $hash;
  }
}
