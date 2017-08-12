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
class ParInspectionPlan extends SqlBase {

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
        'inspection_plan_id',
        'partnership_id',
        'rd_exec_approved',
        'national_regulator_consulted',
        'status',
        'valid_from_date',
        'valid_to_date',
      ]);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'inspection_plan_id' => $this->t('Inspection plan ID'),
      'partnership_id' => $this->t('Partnership ID'),
      'rd_exec_approved' => $this->t('Approved by an RD executive'),
      'national_regulatory_consulted' => $this->t('The national regulator has been consulted'),
      'status' => $this->t('Inspection plan status'),
      'valid_from_date' => $this->t('Valid from'),
      'valid_to_date' => $this->t('Valid to'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'inspection_plan_id' => [
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
