<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 11/07/17
 * Time: 17:33
 */

namespace Drupal\par_demo_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Migration of PAR2 Businesses.
 *
 * @MigrateSource(
 *   id = "par_demo_migration_businesses"
 * )
 */
class ParBusinesses extends SqlBase {

  /**
   * @var string $table The name of the database table.
   */
  protected $table = 'alpha_par_a_businesses';

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select($this->table, 'b')
      ->fields('b', ['business_id', 'phone', 'comments', 'auth_premises', 'employees_band', 'sic_code', 'company_type', 'name', 'email', 'business_type', 'nation', 'first_name', 'surname', 'trading_name']);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields['business_id'] = $this->t('Bid');
    $fields['phone'] = $this->t('Phone');
    $fields['comments'] = $this->t('Comments');
    $fields['auth_premises'] = $this->t('Authorised premises');
    $fields['employees_band'] = $this->t('Number of employees');
    $fields['sic_code'] = $this->t('Sic code');
    $fields['company_type'] = $this->t('Company type');
    $fields['name'] = $this->t('Name');
    $fields['email'] = $this->t('Email');
    $fields['business_type'] = $this->t('Business type');
    $fields['nation'] = $this->t('Nation');
    $fields['first_name'] = $this->t('First name');
    $fields['surname'] = $this->t('Surname');
    $fields['trading_name'] = $this->t('Trading name');
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'business_id' => array(
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
