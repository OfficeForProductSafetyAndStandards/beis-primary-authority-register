<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;
use Drupal\user\Entity\User;

/**
 * Tests PAR Authority entity.
 *
 * @group PAR Data
 */
class AccessParPartnershipsTest extends ParDataTestBase {

  /** @var  ParDataManagerInterface */
  protected $parDataManager;

  protected $email = 'test@example.com';
  /** @var  User */
  protected $membershipUser;

  protected $partnerships = [];
  protected $authorities = [];
  protected $organisations = [];
  protected $people = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setup();

    $this->parDataManager = \Drupal::service('par_data.manager');
    $this->membershipUser = $this->createUser(['mail' => $this->email]);
  }

  /**
   * Test to validate an authority entity.
   */
  public function testPatnershipMembership() {
    // Create 20 partnerships.
    for ($i=0; $i < 20; $i++) {
      $partnership_values = [
        'name' => "Partnership $i",
      ];

      // The first 10 people should have the default email address.
      // The second 10 people should have the email address to test.
      if ($i < 10) {
        $this->people[$i] = ParDataPerson::create($this->getPersonValues());
        $this->people[$i]->save();
      }
      else {
        $this->people[$i] = ParDataPerson::create(['name' => "Person $i", 'email' => $this->email] + $this->getPersonValues());
        $this->people[$i]->save();
      }

      if ($i % 2 == 0) {
        $this->authorities[$i] = ParDataAuthority::create(['name' => "Authority $i", 'field_person' => [$this->people[$i]->id()]] + $this->getAuthorityValues());
        $this->authorities[$i]->save();
        $partnership_values = [
          'field_authority' => [$this->authorities[$i]->id()],
          'field_authority_person' => [$this->people[$i]->id()],
        ];
      }
      else {
        $this->organisations[$i] = ParDataOrganisation::create(['name' => "Organisation $i", 'field_person' => [$this->people[$i]->id()]] + $this->getOrganisationBusinessValues());
        $this->organisations[$i]->save();
        $partnership_values = [
          'field_organisation' => [$this->organisations[$i]->id()],
          'field_organisation_person' => [$this->people[$i]->id()],
        ];
      }

      $this->partnerships[$i] = ParDataPartnership::create($partnership_values + $this->getPartnershipValues());
      $this->partnerships[$i]->save();
    }

    $partnership_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_partnership');
    $this->assertCount(10, $partnership_memberships, t('Partnership memberships are all correct.'));

    // The user has membership to 10 authorities through it's 10 partnerships.
    $authority_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_authority');
    $this->assertCount(10, $authority_memberships, t('Authority memberships are all correct.'));

    // However only half of these are direct memberships.
    $direct_authority_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_authority', TRUE);
    $this->assertCount(5, $direct_authority_memberships, t('Direct Authority memberships are all correct.'));

    $organisation_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_organisation');
    $this->assertCount(10, $organisation_memberships, t('Organisation memberships are all correct.'));

    $direct_organisation_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_organisation', TRUE);
    $this->assertCount(5, $direct_organisation_memberships, t('Direct Organisation memberships are all correct.'));
  }
}
