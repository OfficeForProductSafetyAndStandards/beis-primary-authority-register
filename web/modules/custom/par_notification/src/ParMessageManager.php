<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_notification\Entity\ParMessageInterface;
use Drupal\token\TokenInterface;
use Drupal\user\UserInterface;

/**
 * Provides a PAR Message manager.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ParMessageManager implements ParMessageManagerInterface {

  use LoggerChannelTrait;
  use StringTranslationTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The par data manager.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The message entity storage class.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeStorage;

  /**
   * The token replacement service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /*
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\token\Token $token
   *   The token service.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entity_type_manager, TokenInterface $token) {
    $this->parDataManager = $par_data_manager;
    $this->entityTypeStorage = $entity_type_manager;
    $this->token = $token;
  }

  public function getMessageStorage() {
    return $this->entityTypeStorage->getStorage('par_message');
  }

  /**
   * {@inheritdoc}
   */
  public function build($message_id, $recipient, $sender = NULL, $entity = NULL) {
    $bubbleable_metadata = new BubbleableMetadata();

    /** @var ParMessageInterface $message */
    $message = $this->getMessageStorage()->load($message_id);

    $replacement_tokens = [
      'recipient' => $recipient,
      'sender' => $sender,
      'entity' => $entity,
    ];

    // Tokenize the subject and message body only.
    $message->setSubject($this->token->replace($message->getSubject(), $replacement_tokens, ['clear' => TRUE], $bubbleable_metadata));
    $message->setMessage($this->token->replace($message->getMessage(), $replacement_tokens, ['clear' => TRUE], $bubbleable_metadata));

    if (!$message) {
      throw new ParNotificationException($this->t("The message %message could not be found.", ['%message' => $message_id]));
    }

    return $message;
  }

}
