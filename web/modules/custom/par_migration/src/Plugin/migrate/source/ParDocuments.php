<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 11/07/17
 * Time: 17:33
 */

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Businesses.
 *
 * @MigrateSource(
 *   id = "par_migration_documents"
 * )
 */
class ParDocuments extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_documents';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', ['document_id', 'owning_object_type', 'owning_object_id', 'role_within_object', 'document_path', 'document_name', 'size_in_bytes', 'file_type', 'document_content'])
      ->orderBy('size_in_bytes', 'asc')
      ->range(0, 100);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields['document_id'] = $this->t('Document ID');
    $fields['owning_object_type'] = $this->t('Owning Object Type');
    $fields['owning_object_id'] = $this->t('Owning Object ID');
    $fields['role_within_object'] = $this->t('Role within Object');
    $fields['document_path'] = $this->t('Document Path');
    $fields['document_name'] = $this->t('Document Name');
    $fields['size_in_bytes'] = $this->t('Size in Bytes');
    $fields['file_type'] = $this->t('File Type');
    $fields['document_content'] = $this->t('Document Content');
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'document_id' => array(
        'type' => 'integer',
      ),
      'owning_object_id' => array(
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
