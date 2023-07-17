<?php

namespace Drupal\Tests\par_data\Kernel\Access;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;
use Drupal\user\Entity\User;
use Drupal\Core\Cache\Cache;

/**
 * Tests for standard actions performed against the entity.
 *
 * @group PAR Data
 */
class ParPartnershipActionsTest extends ParDataTestBase {

  protected $partnership;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setup();
  }

  /**
   * Test to validate an authority entity.
   */
  public function testDeletionRulePendingEnforcements() {
    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);
    $partnership_values = [
      'approved_date' => $now->format('Y-m-d'),
    ];
    // Create a partnership to test.
    $this->partnership = ParDataPartnership::create($partnership_values + $this->getDirectPartnershipValues());
    $this->partnership->save();

    $partnership_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_partnership');
    $this->assertCount(10, $partnership_memberships, t('Partnership memberships are all correct.'));

  }
}
