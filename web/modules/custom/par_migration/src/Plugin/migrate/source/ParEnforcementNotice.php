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
 *   id = "par_migration_enforcement_notice"
 * )
 */
class ParEnforcementNotice extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_enforcement_notices';

  /**
   * @var array
   *   A cached array of enforcement people keyed by enforcement notice ID.
   */
  protected $people = [];

  /**
   * @var array
   *   A cached array of enforcement actions keyed by enforcement notice ID.
   */
  protected $actions = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);

    $this->collectPeople();
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
    return $this->select($this->table, 'en')
      ->fields('en', [
        'enforcement_notice_id',
        'enforcing_authority_id',
        'partnership_id',
        'legal_entity_id',
        'notice_date',
        'notice_type',
        'summary',
      ]);
  }

  protected function collectPeople() {
    $result = $this->select('par_enforcement_notice_people', 'p')
      ->fields('p', [
        'enforcement_notice_person_id',
        'enforcement_notice_id',
        'person_id',
      ])
      ->execute();

    while ($row = $result->fetchAssoc()) {
      $this->people[$row['enforcement_notice_id']][$row['person_id']] = [
        'target_id' => (int) $row['person_id'],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'enforcement_notice_id' => $this->t('Enforcement Notice ID'),
      'enforcing_authority_id' => $this->t('Enforcing Authority ID'),
      'partnership_id' => $this->t('Partnership ID'),
      'legal_entity_id' => $this->t('Legal Entity ID'),
      'notice_date' => $this->t('Notice Date'),
      'notice_type' => $this->t('Notice Type'),
      'summary' => $this->t('Summary'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'enforcement_notice_id' => [
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
    $enforcement_notice = $row->getSourceProperty('enforcement_notice_id');

    $people = array_key_exists($enforcement_notice, $this->people) ? $this->people[$enforcement_notice] : [];
    $row->setSourceProperty('people', $people);

    return parent::prepareRow($row);
  }

}
