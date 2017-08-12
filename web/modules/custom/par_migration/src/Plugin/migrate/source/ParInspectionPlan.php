<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Inspection Plan.
 *
 * @MigrateSource(
 *   id = "par_migration_inspection_plan"
 * )
 */
class ParAdvice extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_inspection_plans';

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
    return array(
      'advice_id' => [
        'type' => 'integer',
      ],
      'partnership_id' => [
        'type' => 'integer',
      ],
    );
  }

  /**
   * Attaches "nid" property to a row if row "bid" points to a
   *
   * @param \Drupal\migrate\Row $row
   *
   * @return bool
   * @throws \Exception
   */
  function prepareRow(Row $row) {
    return parent::prepareRow($row);
  }

}
