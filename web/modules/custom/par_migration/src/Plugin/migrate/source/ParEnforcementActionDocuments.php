<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Enforcemennt Action documents.
 *
 * @MigrateSource(
 *   id = "par_migration_enforcement_action_documents"
 * )
 */
class ParEnforcementActionDocuments extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_documents';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', [
        'document_id',
        'owning_object_type',
        'owning_object_id',
        'role_within_object',
        'document_path',
        'document_name',
        'size_in_bytes',
        'file_type',
        'document_content',
      ])
      ->orderBy('size_in_bytes', 'asc')
      ->condition('owning_object_type', 'Enforcement Action');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'document_id' => $this->t('Document ID'),
      'owning_object_type' => $this->t('Owning Object Type'),
      'owning_object_id' => $this->t('Owning Object ID'),
      'role_within_object' => $this->t('Role within Object'),
      'document_path' => $this->t('Document Path'),
      'document_name' => $this->t('Document Name'),
      'size_in_bytes' => $this->t('Size in Bytes'),
      'file_type' => $this->t('File Type'),
      'document_content' => $this->t('Document Content'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'document_id' => [
        'type' => 'integer',
      ],
      'owning_object_id' => [
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
