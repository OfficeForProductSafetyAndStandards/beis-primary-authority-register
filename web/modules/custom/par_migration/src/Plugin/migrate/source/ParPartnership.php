<?php

namespace Drupal\par_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Partnerships.
 *
 * @MigrateSource(
 *   id = "par_migration_partnership"
 * )
 */
class ParPartnership extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'par_partnerships';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', [
        'partnership_id',
        'organisation_id',
        'authority_id',
        'partnership_type',
        'status',
        'tc_organisation_agreed',
        'tc_authority_agreed',
        'coordinator_suitable',
        'partnership_info_confirmed',
        'written_summary_agreed',
        'about_partnership',
        'approved_date',
        'cost_recovery',
        'reject_comment',
        'revocation_source',
        'revocation_date',
        'revocation_reason',
        'authority_change_comment',
        'organisation_change_comment',
      ])
      ->condition('obsolete', 'N');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'partnership_id' => $this->t('Partnership ID'),
      'organisation_id' => $this->t('Organisation ID'),
      'authority_id' => $this->t('Authority ID'),
      'partnership_type' => $this->t('Partnership type'),
      'status' => $this->t('Partnership status'),
      'tc_organisation_agreed' => $this->t('Authority agreed terms & conditions'),
      'tc_authority_agreed' => $this->t('Business agreed terms & conditions'),
      'coordinator_suitable' => $this->t('Coordinator suitable'),
      'partnership_info_confirmed' => $this->t('Partnership information confirmed'),
      'written_summary_agreed' => $this->t('Written summary agreed'),
      'about_partnership' => $this->t('About partnership'),
      'approved_date' => $this->t('Approved date'),
      'cost_recovery' => $this->t('Cost recovery'),
      'reject_comment' => $this->t('Reject comment'),
      'revocation_source' => $this->t('Revocation source'),
      'revocation_date' => $this->t('Revocation date'),
      'revocation_reason' => $this->t('Revocation reason'),
      'authority_change_comment' => $this->t('Authority change comment'),
      'organisation_change_comment' => $this->t('Organisation change comment'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'partnership_id' => array(
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
