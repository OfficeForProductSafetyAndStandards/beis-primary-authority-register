<?php

namespace Drupal\par_notification\Permissions;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ParNotificationPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $parDataManager;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   */
  public function __construct(ParDataManagerInterface $par_data_manager) {
    $this->parDataManager = $par_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_data.manager'));
  }

  /**
   * Get permissions for PAR Notifications.
   *
   * @return array
   *   Permissions array.
   */
  public function permissions() {
    $permissions = [];
    $message_templates = \Drupal::service('entity_type.manager')->getStorage('message_template')->loadMultiple();

    foreach ($message_templates as $message_template) {
      $id = $message_template->id();
      $notification = strtolower($message_template->label());

      // Receive notifications.
      $permissions += [
        "receive {$id} notification" => [
          'title' => $this->t('Receive %notification notifications', ['%notification' => $notification]),
        ]
      ];
    }

    return $permissions;
  }

}
