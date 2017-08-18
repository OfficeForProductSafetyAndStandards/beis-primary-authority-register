<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migration of PAR2 Authority.
 *
 * @MigrateSource(
 *   id = "par_migration_authority"
 * )
 */
class ParAuthority extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_authorities';

  /**
   * @var array
   *   A cached array of regulatory functions keyed by authority ID.
   */
  protected $regulatoryFunctions = [];

  /**
   * @var array
   *   A cached array of allowed regulatory functions keyed by authority ID.
   */
  protected $allowedRegulatoryFunctions = [];

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
    return $this->select($this->table, 'a')
      ->fields('a', [
        'authority_id',
        'name',
        'authority_type',
        'nation',
        'ons_code',
        'comments',
      ])
      ->range(0,1000);
  }

  protected function collectRegulatoryFunctions() {
    $result = $this->select('par_authority_regulatory_functions', 'r')
      ->fields('r', [
        'authority_regulatory_function_id',
        'authority_id',
        'regulatory_function_id',
      ])
      ->isNotNull('r.authority_id')
      ->orderBy('r.authority_id')
      ->execute();

    while ($row = $result->fetchAssoc()) {
      $this->regulatoryFunctions[$row['authority_id']][$row['regulatory_function_id']] = [
        'target_id' => (int) $row['regulatory_function_id'],
      ];
    }
  }

  protected function collectAllowedRegulatoryFunctions() {

  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'authority_id' => $this->t('Authority ID'),
      'name' => $this->t('Authority name'),
      'authority_type' => $this->t('Authority type'),
      'nation' => $this->t('Nation'),
      'ons_code' => $this->t('ONS Code'),
      'comments' => $this->t('Comments'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'authority_id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * Attaches regulatory functions.
   *
   * @param \Drupal\migrate\Row $row
   *
   * @return bool
   * @throws \Exception
   */
  function prepareRow(Row $row) {
    $authority = $row->getSourceProperty('authority_id');

    $regulatory_functions = array_key_exists($authority, $this->regulatoryFunctions) ? $this->regulatoryFunctions[$authority] : [];
    $row->setSourceProperty('regulatory_functions', $regulatory_functions);

    return parent::prepareRow($row);
  }

}
