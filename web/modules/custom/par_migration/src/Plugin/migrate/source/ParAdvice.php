<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migration of PAR2 Advice.
 *
 * @MigrateSource(
 *   id = "par_migration_advice"
 * )
 */
class ParAdvice extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_advice';

  /**
   * @var array
   *   A cached array of regulatory functions keyed by authority ID.
   */
  protected $regulatoryFunctions = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);

    $this->collectRegulatoryFunctions();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select($this->table, 'a');
    $query->fields('a', [
        'advice_id',
        'partnership_id',
        'advice_type',
        'authority_visible',
        'coordinator_visible',
        'business_visible',
        'obsolete',
        'notes',
      ]);

    return $query;
  }

  protected function collectRegulatoryFunctions() {
    $result = $this->select('par_advice_regulatory_functions', 'r')
      ->fields('r', [
        'advice_regulatory_function_id',
        'advice_id',
        'regulatory_function_id',
      ])
      ->isNotNull('r.advice_id')
      ->orderBy('r.advice_id')
      ->execute();

    while ($row = $result->fetchAssoc()) {
      $this->regulatoryFunctions[$row['advice_id']][] = [
        'target_id' => (int) $row['regulatory_function_id'],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'advice_id' => $this->t('Advice ID'),
      'partnership_id' => $this->t('Partnership ID'),
      'advice_type' => $this->t('Advice type'),
      'authority_visible' => $this->t('Authority visibility'),
      'coordinator_visible' => $this->t('Coordinatory visibility'),
      'business_visible' => $this->t('Business visibility'),
      'obsolete' => $this->t('Is obsolete'),
      'notes' => $this->t('Adice notes'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'advice_id' => [
        'type' => 'integer',
      ],
      'partnership_id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * Adds the lookuped regulatory functions.
   *
   * @param \Drupal\migrate\Row $row
   *
   * @return bool
   * @throws \Exception
   */
  function prepareRow(Row $row) {
    $advice = $row->getSourceProperty('advice_id');

    $regulatory_functions = array_key_exists($advice, $this->regulatoryFunctions) ? $this->regulatoryFunctions[$advice] : [];
    $row->setSourceProperty('regulatory_functions', $regulatory_functions);

    return parent::prepareRow($row);
  }

}
