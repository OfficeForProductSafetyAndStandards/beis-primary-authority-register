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
 *   id = "par_migration_enforcement_action"
 * )
 */
class ParEnforcementNotice extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_enforcement_actions';

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);
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
        'enforcement_action_id',
        'enforcement_notice_id',
        'regulatory_function_id',
        'title',
        'details',
        'ea_status',
        'ea_notes',
        'pa_status',
        'pa_notes',
        'blocked_by_advice_id',
        'referral_notes',
        'referred_from_action_id',
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'enforcement_action_id' => $this->t('Enforcement Action ID'),
      'enforcement_notice_id' => $this->t('Enforcement Notice ID'),
      'regulatory_function_id' => $this->t('Regulatory Function ID'),
      'title' => $this->t('Title'),
      'details' => $this->t('Details'),
      'ea_status' => $this->t('EA Status'),
      'ea_notes' => $this->t('EA Notes'),
      'pa_status' => $this->t('PA Status'),
      'pa_notes' => $this->t('PA Notes'),
      'blocked_by_advice_id' => $this->t('Blocked by Advice ID'),
      'referral_notes' => $this->t('Referral Notes'),
      'referral_from_action_id' => $this->t('Referral from Action ID'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'enforcement_actions_id' => [
        'type' => 'integer',
      ],
    ];
  }

}
