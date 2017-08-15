<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Legal Entities.
 *
 * @MigrateSource(
 *   id = "par_migration_legal_entity"
 * )
 */
class ParLegalEntity extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_legal_entities';

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select($this->table, 'b');
    $query->fields('b', [
        'legal_entity_id',
        'organisation_id',
        'legal_entity_type_id',
        'name',
        'registered_no',
      ]);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'legal_entity_id' => $this->t('Legal Entity ID'),
      'organisation_id' => $this->t('Organisation ID'),
      'legal_entity_type_id' => $this->t('Legal Entity Type ID'),
      'name' => $this->t('Name'),
      'registered_no' => $this->t('Registered number'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'legal_entity_id' => [
        'type' => 'integer',
      ],
      'organisation_id' => [
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
