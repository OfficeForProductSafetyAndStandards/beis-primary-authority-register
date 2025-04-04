<?php

namespace Drupal\Tests\par_data\Kernel\Access;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Authority entity.
 *
 * @group PAR Data
 */
class AccessParPartnershipsTest extends ParDataTestBase {

  /**
   * PAR Data Manager service.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * Email address.
   *
   * @var string
   */
  protected $email = 'test@example.com';

  /**
   * Membership user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $membershipUser;

  /**
   * List of partnerships.
   *
   * @var array
   */
  protected $partnerships = [];

  /**
   * List of authorities.
   *
   * @var array
   */
  protected $authorities = [];

  /**
   * List of organisations.
   *
   * @var array
   */
  protected $organisations = [];

  /**
   * List of people.
   *
   * @var array
   */
  protected $people = [];

  /**
   * List of premises.
   *
   * @var array
   */
  protected $premises = [];

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function setUp(): void {
    parent::setup();

    $this->parDataManager = \Drupal::service('par_data.manager');
    $this->membershipUser = $this->createUser($this->permissions, NULL, FALSE, ['mail' => $this->email]);
  }

  /**
   * Test to validate an authority entity.
   */
  public function testPartnershipMembership() {
    // Create 20 partnerships.
    for ($i = 0; $i < 20; $i++) {
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
        $this->people[$i] = ParDataPerson::create([
          'name' => "Person $i",
          'email' => $this->email,
          'field_user_account' => [$this->membershipUser->id()],
        ] + $this->getPersonValues()
        );
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
        $partnership_values += [
          'field_organisation' => [$this->organisations[$i]->id()],
          'field_organisation_person' => [$this->people[$i]->id()],
        ];
      }

      $this->partnerships[$i] = ParDataPartnership::create($partnership_values + $this->getDirectPartnershipValues());
      $this->partnerships[$i]->save();

    }

    $partnership_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_partnership');
    $this->assertCount(10, $partnership_memberships, t('Partnership memberships are all correct.'));

    $direct_authority_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_authority', TRUE);
    $this->assertCount(5, $direct_authority_memberships, t('Direct Authority memberships are all correct.'));

    $direct_organisation_memberships = $this->parDataManager->hasMembershipsByType($this->membershipUser, 'par_data_organisation', TRUE);
    $this->assertCount(5, $direct_organisation_memberships, t('Direct Organisation memberships are all correct.'));

    // Check that the correct caches have been created.
    foreach ($this->authorities as $i => $authority) {
      $cache = \Drupal::cache('par_data')->get("relationships:{$authority->uuid()}");
      // Only a select number of authorities have member people.
      if ($i >= 10 && $i % 2 == 0) {
        $this->assertNotFalse(
          $cache,
          "Relationships for authority entity {$authority->id()} have been correctly cached."
        );
      }
    }
    foreach ($this->organisations as $i => $organisation) {
      $cache = \Drupal::cache('par_data')->get("relationships:{$organisation->uuid()}");
      // Only a select number of authorities have member people.
      if ($i >= 10 && $i % 2 != 0) {
        $this->assertNotFalse(
          $cache,
          "Relationships for organisation entity {$organisation->id()} have been correctly cached."
        );
      }
    }
  }

}
