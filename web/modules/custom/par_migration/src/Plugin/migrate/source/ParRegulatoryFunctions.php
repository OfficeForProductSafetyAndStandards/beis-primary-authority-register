<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Regulatory Functions.
 *
 * @MigrateSource(
 *   id = "par_migration_regulatory_functions"
 * )
 */
class ParRegulatoryFunctions extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_regulatory_functions';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', [
        'regulatory_function_id',
        'name',
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'regulatory_function_id' => $this->t('Regulatory Function ID'),
      'name' => $this->t('Name'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'regulatory_function_id' => array(
        'type' => 'integer',
      ),
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
