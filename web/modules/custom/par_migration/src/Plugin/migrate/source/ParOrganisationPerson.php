<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Organisation Person.
 *
 * @MigrateSource(
 *   id = "par_migration_organisation_person"
 * )
 */
class ParOrganisationPerson extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_people';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'a')
      ->fields('a', [
        'person_id',
        'organisation_id',
        'title',
        'first_name',
        'last_name',
        'job_title',
        'work_phone',
        'mobile_phone',
        'email',
        'work_phone_preferred',
        'mobile_phone_preferred',
        'email_preferred',
        'communication_notes',
      ])
      ->isNotNull('organisation_id');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'person_id' => $this->t('Person ID'),
      'organisation_id' => $this->t('Organisation ID'),
      'title' => $this->t('Salutation'),
      'first_name' => $this->t('First name'),
      'last_name' => $this->t('Last name'),
      'job_title' => $this->t('Job title'),
      'work_phone' => $this->t('Work phone'),
      'mobile_phone' => $this->t('Mobile phone'),
      'email' => $this->t('E-mail'),
      'work_phone_preferred' => $this->t('Work phone preferred'),
      'mobile_phone_preferred' => $this->t('Mobile phone preferred'),
      'email_preferred' => $this->t('E-mail preferred'),
      'communication_notes' => $this->t('Communication notes'),
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
