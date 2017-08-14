<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Organisation Premises.
 *
 * @MigrateSource(
 *   id = "par_migration_organisation_premises"
 * )
 */
class ParOrganisationPremises extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_premises';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', [
        'premises_id',
        'organisation_id',
        'line_1',
        'line_2',
        'line_3',
        'town',
        'county',
        'postcode',
        'country',
        'uprn',
      ])
      ->isNotNull('organisation_id');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'premises_id' => $this->t('Premises ID'),
      'organisation_id' => $this->t('Organisation ID'),
      'line_1' => $this->t('Line 1'),
      'line_2' => $this->t('Line 2'),
      'line_3' => $this->t('Line 3'),
      'town' => $this->t('Town'),
      'county' => $this->t('County'),
      'postcode' => $this->t('Postcode'),
      'country' => $this->t('Country'),
      'uprn' => $this->t('UPRN'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'premises_id' => [
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
