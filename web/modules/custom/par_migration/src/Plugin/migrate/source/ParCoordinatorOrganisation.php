<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Coordinator Organisation.
 *
 * @MigrateSource(
 *   id = "par_migration_coordinator_organisation"
 * )
 */
class ParCoordinatorOrganisation extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_organisations';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'a')
      ->fields('a', [
        'organisation_id',
        'name',
        'par_role',
        'size_category',
        'employees_band',
        'phone',
        'email',
        'nation',
        'first_name',
        'last_name',
        'premises_on_map_ok',
        'comments',
        'coordinator_number_eligible',
        'coordinator_type',
      ])
      ->condition('par_role', 'Co-ordinator');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'organisation_id' => $this->t('Organisation ID'),
      'name' => $this->t('Name'),
      'par_role' => $this->t('PAR role'),
      'size_category' => $this->t('Size category'),
      'employees_band' => $this->t('Employees band'),
      'phone' => $this->t('Phone'),
      'email' => $this->t('Email'),
      'nation' => $this->t('Nation'),
      'first_name' => $this->t('First name'),
      'last_name' => $this->t('Last name'),
      'premises_on_map_ok' => $this->t('Premises on map'),
      'comments' => $this->t('Comments'),
      'coordinator_number_eligible' => $this->t('Coordinator number eligible'),
      'coordinator_type' => $this->t('Coordinator type'),
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
