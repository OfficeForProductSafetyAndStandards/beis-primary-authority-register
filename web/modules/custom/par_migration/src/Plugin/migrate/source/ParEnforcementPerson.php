<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Enforcement Person.
 *
 * @MigrateSource(
 *   id = "par_migration_enforcement_person"
 * )
 */
class ParEnforcementPerson extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_people_v';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'p')
      ->fields('p', [
        'person_id',
        'authority_id',
        'title',
        'first_name',
        'last_name',
        'work_phone',
        'mobile_phone',
        'email',
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'person_id' => $this->t('Person ID'),
      'authority_id' => $this->t('Authority ID'),
      'title' => $this->t('Salutation'),
      'first_name' => $this->t('First name'),
      'last_name' => $this->t('Last name'),
      'work_phone' => $this->t('Work phone'),
      'mobile_phone' => $this->t('Mobile phone'),
      'email' => $this->t('E-mail'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'person_id' => [
        'type' => 'integer',
      ],
      'authority_id' => [
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
