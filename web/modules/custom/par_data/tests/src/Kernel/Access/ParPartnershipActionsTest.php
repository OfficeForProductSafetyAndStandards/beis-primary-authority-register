<?php

namespace Drupal\Tests\par_data\Kernel\Access;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
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

  /**
   * @var ParDataPartnership
   *   A partnership that is awaiting approval.
   */
  protected $pending_partnership;

  /**
   * @var ParDataPartnership
   *   A partnership that has just been nominated.
   */
  protected $nominated_partnership;

  /**
   * @var ParDataPartnership
   *   A partnership that has been active for some time.
   */
  protected $active_partnership;

  /**
   * @var ParDataPartnership
   *   A partnership that has been recently revoked.
   */
  protected $revoked_partnership;

  /**
   * @var ParDataPartnership
   *   A partnership that has been revoked for some time.
   */
  protected $old_partnership;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setup();

    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);

    // Set a new pending approval partnership.
    $partnership_values = [
      'partnership_status' => 'confirmed_business',
      'approved_date' => NULL,
    ];
    $this->pending_partnership = ParDataPartnership::create($partnership_values + $this->getDirectPartnershipValues());
    $this->pending_partnership->save();

    // Create a new partnership that has been recently nominated.
    $this->nominated_partnership = ParDataPartnership::create($partnership_values + $this->getDirectPartnershipValues());
    $this->nominated_partnership->nominate();

    // Set a partnership to be nominated a week ago.
    $this->active_partnership = ParDataPartnership::create($this->getDirectPartnershipValues());
    $this->active_partnership->nominate(FALSE);
    $this->active_partnership->setApprovedDate($now->modify('-1 week'));
    $this->active_partnership->save();

    // Create a new partnership that has been recently revoked.
    $this->revoked_partnership = ParDataPartnership::create($this->getDirectPartnershipValues());
    $this->revoked_partnership->revoke();

    // Set a partnership that was revoked a week ago.
    $this->old_partnership = ParDataPartnership::create($this->getDirectPartnershipValues());
    $this->old_partnership->revoke(FALSE);
    $this->old_partnership->setRevocationDate($now->modify('-1 week'));
    $this->old_partnership->save();
  }

  /**
   * Test to validate an authority entity.
   */
  public function testDeletionRules() {
    // Common rule 1: Revoked partnerships should not be deletable.
    $this->assertTrue(!$this->old_partnership->isRevocable(), t('Revoked partnerships should not be deletable.'));

    // Partnership rule 1: ending partnership and partnerships nominated in the last day can be deleted.
    $this->assertTrue($this->pending_partnership->isDeletable(), t('Pending partnerships should be deletable.'));
    $this->assertTrue($this->nominated_partnership->isDeletable(), t('Partnerships nominated less than 1 day ago should be deletable.'));

    // Partnership rule 1: Partnerships nominated more than 1 day ago should not be deletable.
    $this->assertTrue(!$this->active_partnership->isDeletable(), t('Partnerships nominated more than 1 day ago should not be deletable.'));
  }

  /**
   * Test to validate Rule 1: There are no pending enforcements against the partnership
   */
  public function testRevocationRules() {
    // Common rule 1: Partnerships that are already revoked should not be revocable.
    $this->assertTrue(!$this->old_partnership->isRevocable(), t('Revoked partnerships should not be revocable.'));

    // Partnership rule 1: All active partnerships should be revocable.
    $this->assertTrue($this->active_partnership->isRevocable(), t('Active partnerships should be revocable.'));
    $this->assertTrue($this->nominated_partnership->isRevocable(), t('Partnerships nominated less than 1 day ago should be revocable.'));

    // Partnership rule 1: Inactive partnerships should not be revocable.
    $this->assertTrue(!$this->pending_partnership->isRevocable(), t('Pending partnerships should not be revocable.'));

    // Create an enforcement notice against this partnership with an unapproved notice.
    $action_values = [
      'primary_authority_status' => 'awaiting_approval',
    ];
    $enforcement_action = ParDataEnforcementAction::create($action_values + $this->getEnforcementActionValues());
    $enforcement_action->save();

    $notice_values = [
      'field_partnership' => [
        $this->active_partnership->id(),
      ],
      'field_legal_entity' => [
        $this->active_partnership->getLegalEntity(TRUE)?->id(),
      ],
      'field_enforcement_action' => [
        $enforcement_action->id(),
      ],
    ];
    $enforcement = ParDataEnforcementNotice::create($notice_values + $this->getEnforcementNoticeValues());
    $enforcement->save();

    // Partnership rule 2: The partnership must not have any pending enforcements to be revoked.
//    $this->assertTrue(!$this->active_partnership->isRevocable(), t('Partnerships with pending enforcements should not be revocable.'));
//    $enforcement_action->save();
//    $this->assertTrue($this->active_partnership->isRevocable(), t('Partnerships with approved enforcements should be revocable.'));
  }

  /**
   * Test to validate an authority entity.
   */
  public function testRestorationRules() {
    // Common rule 1: Revoked partnerships should be restorable.
    $this->assertTrue($this->revoked_partnership->isRestorable(), t('Revoked partnerships should be restorable.'));
    $this->assertTrue(!$this->nominated_partnership->isRestorable(), t('Active partnerships should not be restorable.'));
    $this->assertTrue(!$this->active_partnership->isRestorable(), t('Active partnerships should not be restorable.'));

    // Rule 1: Partnerships revoked more than a day ago should not be restorable.
    $this->assertTrue(!$this->old_partnership->isRestorable(), t('Partnerships revoked more than a day ago should not be restorable.'));
  }
}
