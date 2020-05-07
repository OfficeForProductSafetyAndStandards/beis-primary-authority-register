<?php

namespace Drupal\par_data\Plugin\DevelGenerate;

use Drupal\comment\CommentManagerInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Drupal\field\Entity\FieldConfig;
use Drupal\par_data\ParDataManagerInterface;
use Drush\Utils\StringUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a ParDataDevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "par_data",
 *   label = @Translation("par_data"),
 *   description = @Translation("Generate a given number of par data entities. Optionally delete current par data."),
 *   url = "par-data",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 50,
 *     "kill" = FALSE,
 *     "title_length" = 4
 *   }
 * )
 */
class ParDataDevelGenerate extends DevelGenerateBase implements ContainerFactoryPluginInterface {

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The node type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $typeStorage;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The par data service.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param array $plugin_definition
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_type_storage
   *   The node type storage.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\comment\CommentManagerInterface $comment_manager
   *   The comment manager service.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\par_data\ParDataManagerInterface
   *   The par data manager service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageInterface $node_storage, EntityStorageInterface $node_type_storage, ModuleHandlerInterface $module_handler, DateFormatterInterface $date_formatter, ParDataManagerInterface $par_data_manager, TimeInterface $time) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->moduleHandler = $module_handler;
    $this->nodeStorage = $node_storage;
    $this->nodeTypeStorage = $node_type_storage;
    $this->dateFormatter = $date_formatter;
    $this->parDataManager = $par_data_manager;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $entity_manager->getStorage('node'),
      $entity_manager->getStorage('node_type'),
      $container->get('module_handler'),
      $container->get('date.formatter'),
      $container->get('par_data.manager'),
      $container->get('datetime.time')
    );
  }

  /**
   * Get time service.
   *
   * @return \Drupal\Component\Datetime\TimeInterface
   */
  public function getTime() {
    if (!isset($this->time)) {
      $this->time = \Drupal::time();
    }

    return $this->time;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $types = $this->parDataManager->getParEntityTypes();

    if (empty($types)) {
      $this->setMessage($this->t('You do not have any par data types something is wrong.'));
      return;
    }

    $options = array();

    foreach ($types as $type) {
      /** @var \Drupal\Core\Entity\ContentEntityType $type */
      $options[$type->id()] = array(
        'type' => array('#markup' => $type->getLabel()),
      );
    }

    $header = array(
      'type' => $this->t('Par Data Type'),
    );

    $form['par_data_types'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
    );

    $form['kill'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('<strong>Delete all par data entities</strong> of these types before generating new entities.'),
      '#default_value' => $this->getSetting('kill'),
    );
    $form['num'] = array(
      '#type' => 'number',
      '#title' => $this->t('How many entities would you like to generate?'),
      '#default_value' => $this->getSetting('num'),
      '#required' => TRUE,
      '#min' => 0,
    );

    $options = array(1 => $this->t('Now'));
    foreach (array(3600, 86400, 604800, 2592000, 31536000) as $interval) {
      $options[$interval] = $this->dateFormatter->formatInterval($interval, 1) . ' ' . $this->t('ago');
    }
    $form['time_range'] = array(
      '#type' => 'select',
      '#title' => $this->t('How far back in time should the entities be dated?'),
      '#description' => $this->t('Entity creation dates will be distributed randomly from the current time, back to the selected time.'),
      '#options' => $options,
      '#default_value' => 604800,
    );

    $form['title_length'] = array(
      '#type' => 'number',
      '#title' => $this->t('Maximum number of words in titles'),
      '#default_value' => $this->getSetting('title_length'),
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 255,
    );

    $form['#redirect'] = FALSE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  function settingsFormValidate(array $form, FormStateInterface $form_state) {
    if (!array_filter($form_state->getValue('par_data_types'))) {
      $form_state->setErrorByName('par_data_types', $this->t('Please select at least one content type'));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function generateElements(array $values) {
    if ($values['num'] <= 50) {
      $this->generateContent($values);
    }
    else {
      $this->generateBatchContent($values);
    }
  }

  /**
   * Method responsible for creating content when
   * the number of elements is less than 50.
   */
  private function generateContent($values) {
    $values['par_data_types'] = array_filter($values['par_data_types']);
    if (!empty($values['kill']) && $values['par_data_types']) {
      $this->contentKill($values);
    }

    if (!empty($values['par_data_types'])) {
      // Generate nodes.
      $this->develGenerateContentPreEntity($values);
      $start = time();
      for ($i = 1; $i <= $values['num']; $i++) {
        $this->develGenerateContentAddEntity($values);
        if ($this->isDrush8() && function_exists('drush_log') && $i % drush_get_option('feedback', 1000) == 0) {
          $now = time();
          drush_log(dt('Completed @feedback entities (@rate nodes/min)', array('@feedback' => drush_get_option('feedback', 1000), '@rate' => (drush_get_option('feedback', 1000) * 60) / ($now - $start))), 'ok');
          $start = $now;
        }
      }
    }
    $this->setMessage($this->formatPlural($values['num'], '1 entity created.', 'Finished creating @count entities'));
  }

  /**
   * Method responsible for creating content when
   * the number of elements is greater than 50.
   */
  private function generateBatchContent($values) {
    // Setup the batch operations and save the variables.
    $operations[] = array('devel_generate_operation', array($this, 'batchContentPreEntity', $values));

    // Add the kill operation.
    if ($values['kill']) {
      $operations[] = array('devel_generate_operation', array($this, 'batchContentKill', $values));
    }

    // Add the operations to create the nodes.
    for ($num = 0; $num < $values['num']; $num ++) {
      $operations[] = array('devel_generate_operation', array($this, 'batchContentAddEntity', $values));
    }

    // Set the batch.
    $batch = array(
      'title' => $this->t('Generating Content'),
      'operations' => $operations,
      'finished' => 'devel_generate_batch_finished',
      'file' => drupal_get_path('module', 'devel_generate') . '/devel_generate.batch.inc',
    );
    batch_set($batch);
  }

  public function batchContentPreEntity($vars, &$context) {
    $context['results'] = $vars;
    $context['results']['num'] = 0;
    $this->develGenerateContentPreEntity($context['results']);
  }

  public function batchContentAddEntity($vars, &$context) {
    $this->develGenerateContentAddEntity($context['results']);
    $context['results']['num']++;
  }

  public function batchContentKill($vars, &$context) {
    $this->contentKill($context['results']);
  }

  /**
   * {@inheritdoc}
   */
  public function validateDrushParams($args, $options = []) {
    $add_language = $this->isDrush8() ? drush_get_option('languages') : $options['languages'];
    if (!empty($add_language)) {
      $add_language = explode(',', str_replace(' ', '', $add_language));
      // Intersect with the enabled languages to make sure the language args
      // passed are actually enabled.
      $values['values']['add_language'] = array_intersect($add_language, array_keys($this->languageManager->getLanguages(LanguageInterface::STATE_ALL)));
    }

    $values['kill'] = $this->isDrush8() ? drush_get_option('kill') : $options['kill'];
    $values['title_length'] = 6;
    $values['num'] = array_shift($args);
    $all_types = array_keys(\Drupal::service('par_data.manager')->getParEntityTypes());
    if ($this->isDrush8()) {
      $selected_types = _convert_csv_to_array(drush_get_option('types', []));
    }
    else {
      $selected_types = StringUtils::csvToArray($options['types'] ?: []);
    }

    if (empty($selected_types)) {
      throw new \Exception(dt('No content types available'));
    }

    $values['par_data_types'] = array_combine($selected_types, $selected_types);
    $par_data_types = array_filter($values['par_data_types']);

    if (!empty($values['kill']) && empty($par_data_types)) {
      throw new \Exception(dt('Please provide par data type (--types) in which you want to delete the content.'));
    }

    // Checks for any missing content types before generating nodes.
    if (array_diff($par_data_types, $all_types)) {
      throw new \Exception(dt('One or more par data types have been entered that don\'t exist on this site'));
    }

    return $values;
  }

  /**
   * Deletes all nodes of given node types.
   *
   * @param array $values
   *   The input values from the settings form.
   */
  protected function contentKill($values) {
    foreach ($values['par_data_types'] as $entity_type_id => $entity_type) {
      $storage = $this->parDataManager->getEntityTypeStorage($entity_type_id);
      $entities = $storage->loadMultiple();
      $storage->delete($entities);
      $this->setMessage($this->t('Deleted all %type entities.', array('%type' => $entity_type_id)));
    }
  }

  /**
   * Return the same array passed as parameter
   * but with an array of uids for the key 'users'.
   */
  protected function develGenerateContentPreEntity(&$results) {
    // Get user id.
    $users = $this->getUsers();
    $results['users'] = $users;
  }

  /**
   * Create one node. Used by both batch and non-batch code branches.
   */
  protected function develGenerateContentAddEntity(&$results) {
    if (!isset($results['time_range'])) {
      $results['time_range'] = 0;
    }
    $users = $results['users'];

    $node_type = array_rand(array_filter($results['par_data_types']));
    $uid = $users[array_rand($users)];

    $node = $this->storage->create(array(
      'nid' => NULL,
      'type' => $node_type,
      'title' => $this->getRandom()->sentences(mt_rand(1, $results['title_length']), TRUE),
      'uid' => $uid,
      'revision' => mt_rand(0, 1),
      'status' => TRUE,
      'promote' => mt_rand(0, 1),
      'created' => $this->getTime()->getRequestTime() - mt_rand(0, $results['time_range']),
      'langcode' => $this->getLangcode($results),
    ));

    // A flag to let hook_node_insert() implementations know that this is a
    // generated node.
    $node->devel_generate = $results;

    // Populate all fields with sample values.
    $this->populateFields($node);

    // See devel_generate_node_insert() for actions that happen before and after
    // this save.
    $node->save();
  }

  /**
   * Determine language based on $results.
   */
  protected function getLangcode($results) {
    if (isset($results['add_language'])) {
      $langcodes = $results['add_language'];
      $langcode = $langcodes[array_rand($langcodes)];
    }
    else {
      $langcode = $this->languageManager->getDefaultLanguage()->getId();
    }
    return $langcode;
  }

  /**
   * Retrieve 50 uids from the database.
   */
  protected function getUsers() {
    $connection = \Drupal::database();
    $users = array();
    $result = $connection->queryRange("SELECT uid FROM {users}", 0, 50);
    foreach ($result as $record) {
      $users[] = $record->uid;
    }
    return $users;
  }

}
