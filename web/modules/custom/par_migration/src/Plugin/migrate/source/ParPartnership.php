<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migration of PAR2 Partnerships.
 *
 * @MigrateSource(
 *   id = "par_migration_partnership"
 * )
 */
class ParPartnership extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_partnerships';

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
    return $this->select($this->table, 'b')
      ->fields('b', [
        'partnership_id',
        'organisation_id',
        'authority_id',
        'partnership_type',
        'status',
        'tc_organisation_agreed',
        'tc_authority_agreed',
        'coordinator_suitable',
        'authority_info_confirmed',
        'organisation_info_confirmed',
        'written_summary_agreed',
        'about_partnership',
        'approved_date',
        'cost_recovery',
        'reject_comment',
        'revocation_source',
        'revocation_date',
        'revocation_reason',
        'authority_change_comment',
        'organisation_change_comment',
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'partnership_id' => $this->t('Partnership ID'),
      'organisation_id' => $this->t('Organisation ID'),
      'authority_id' => $this->t('Authority ID'),
      'partnership_type' => $this->t('Partnership type'),
      'status' => $this->t('Partnership status'),
      'tc_organisation_agreed' => $this->t('Authority agreed terms & conditions'),
      'tc_authority_agreed' => $this->t('Business agreed terms & conditions'),
      'coordinator_suitable' => $this->t('Coordinator suitable'),
      'authority_info_confirmed' => $this->t('Authority information confirmed'),
      'organisation_info_confirmed' => $this->t('Organisation information confirmed'),
      'written_summary_agreed' => $this->t('Written summary agreed'),
      'about_partnership' => $this->t('About partnership'),
      'approved_date' => $this->t('Approved date'),
      'cost_recovery' => $this->t('Cost recovery'),
      'reject_comment' => $this->t('Reject comment'),
      'revocation_source' => $this->t('Revocation source'),
      'revocation_date' => $this->t('Revocation date'),
      'revocation_reason' => $this->t('Revocation reason'),
      'authority_change_comment' => $this->t('Authority change comment'),
      'organisation_change_comment' => $this->t('Organisation change comment'),
    ];
    return $fields;
  }

  protected function collectRegulatoryFunctions() {
    $result = $this->select('par_partnership_regulatory_functions', 'r')
      ->fields('r', [
        'partnership_regulatory_function_id',
        'partnership_id',
        'regulatory_function_id',
      ])
      ->isNotNull('r.partnership_id')
      ->orderBy('r.partnership_id')
      ->execute();

    while ($row = $result->fetchAssoc()) {
      $this->regulatoryFunctions[$row['partnership_id']][] = [
        'target_id' => (int) $row['regulatory_function_id'],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'partnership_id' => [
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
    $partnership = $row->getSourceProperty('partnership_id');

    $regulatory_functions = array_key_exists($partnership, $this->regulatoryFunctions) ? $this->regulatoryFunctions[$partnership] : [];
    $row->setSourceProperty('regulatory_functions', $regulatory_functions);

    return parent::prepareRow($row);
  }

}
