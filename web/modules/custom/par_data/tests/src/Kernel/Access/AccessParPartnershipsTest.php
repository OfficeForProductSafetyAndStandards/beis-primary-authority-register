<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

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
  protected $premises = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setup();

    $this->parDataManager = \Drupal::service('par_data.manager');
    $this->membershipUser = $this->createUser(['mail' => $this->email], $this->permissions);
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

      // Let's store the premises IDs for the last 5 partnerships.
      if ($i >= 15) {
        $this->premises[$i] = ParDataPremises::create(['name' => "Premises $i"] + $this->getPremisesValues());
        $this->premises[$i]->save();
      }

      // Let's store the authority ids for even numbered partnerships.
      // and the organisation ids for odd numbered partnerships.
      if ($i % 2 == 0) {
        $authority_values = [
          'name' => "Authority $i",
          'field_person' => [$this->people[$i]->id()],
        ];
        $this->authorities[$i] = ParDataAuthority::create($authority_values + $this->getAuthorityValues());
        $this->authorities[$i]->save();
        $partnership_values = [
          'field_authority' => [$this->authorities[$i]->id()],
          'field_authority_person' => [$this->people[$i]->id()],
        ];
      }
      else {
        // Let's add the known premises to the last few organisations.
        $organisation_values = [
          'name' => "Organisation $i",
          'field_person' => [$this->people[$i]->id()],
        ];
        if ($i >= 15) {
          $organisation_values['field_premises'] = [$this->premises[$i]->id()];
        }
        $this->organisations[$i] = ParDataOrganisation::create($organisation_values + $this->getOrganisationValues());
        $this->organisations[$i]->save();
        $partnership_values = [
          'field_organisation' => [$this->organisations[$i]->id()],
          'field_organisation_person' => [$this->people[$i]->id()],
        ];
      }

      $this->partnerships[$i] = ParDataPartnership::create($partnership_values + $this->getDirectPartnershipValues());
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

    // Check that the correct caches have been created.
    foreach ($this->authorities as $i => $authority) {
      $entityHashKey = $authority->getEntityTypeId() . ':' . $authority->id();
      $cache = \Drupal::cache('data')->get("par_data_relationships:{$entityHashKey}");
      // Only a select number of authorities have member people.
      if ($i >= 10 && $i % 2 == 0) {
        $this->assertNotFalse($cache, t("Relationships for authority entity {$authority->id()} have been correctly cached."));
      }
    }
    foreach ($this->organisations as $i => $organisation) {
      $entityHashKey = $organisation->getEntityTypeId() . ':' . $organisation->id();
      $cache = \Drupal::cache('data')->get("par_data_relationships:{$entityHashKey}");
      // Only a select number of authorities have member people.
      if ($i >= 10 && $i % 2 != 0) {
        $this->assertNotFalse($cache, t("Relationships for organisation entity {$organisation->id()} have been correctly cached."));
      }
    }
    foreach ($this->premises as $i => $premises) {
      $entityHashKey = $premises->getEntityTypeId() . ':' . $premises->id();
      $cache = \Drupal::cache('data')->get("par_data_relationships:{$entityHashKey}");
      if ($i >= 15 && $i % 2 != 0) {
        $this->assertNotFalse($cache, t("Relationships for premises entity {$premises->id()} have been correctly cached."));
      }
    }

    // If we delete a premises it should clear the cache.
    $entityHashKey = $this->premises[17]->getEntityTypeId() . ':' . $this->premises[17]->id();
    $premises_id = $this->premises[17]->id();
    $this->premises[17]->delete();
    $cache = \Drupal::cache('data')->get("par_data_relationships:{$entityHashKey}");
    $this->assertEmpty($cache, t("Relationships for premises entity $premises_id has been correctly cache busted."));

    // It should also clear the correct cache for the authority.
    $entityHashKey = $this->organisations[17]->getEntityTypeId() . ':' . $this->organisations[17]->id();
    $cache = \Drupal::cache('data')->get("par_data_relationships:{$entityHashKey}");
    $this->assertEmpty($cache, t("Relationships for organisation entity {$this->organisations[17]->id()} has been correctly cache busted."));
  }
}
