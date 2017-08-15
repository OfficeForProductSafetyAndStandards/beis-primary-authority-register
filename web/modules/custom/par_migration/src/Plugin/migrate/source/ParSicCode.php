<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 SIC Codes.
 *
 * @MigrateSource(
 *   id = "par_migration_sic_code"
 * )
 */
class ParSicCode extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_sic_codes';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', [
        'sic_code_id',
        'sic_code',
        'description',
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'sic_code_id' => $this->t('SIC Code ID'),
      'sic_code' => $this->t('SIC Code'),
      'description' => $this->t('Description'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'sic_code_id' => [
        'type' => 'integer',
      ],
    ];
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
