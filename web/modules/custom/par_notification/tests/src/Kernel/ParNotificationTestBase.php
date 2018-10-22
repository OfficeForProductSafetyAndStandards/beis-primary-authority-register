<?php

namespace Drupal\Tests\par_notification\Kernel;

use Drupal\file\Entity\File;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataAdviceType;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataCoordinatedBusinessType;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataDeviationRequestType;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementActionType;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataEnforcementNoticeType;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataGeneralEnquiryType;
use Drupal\par_data\Entity\ParDataInformationReferral;
use Drupal\par_data\Entity\ParDataInformationReferralType;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataInspectionFeedbackType;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataInspectionPlanType;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataLegalEntityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataOrganisationType;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipType;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\Entity\ParDataPremisesType;
use Drupal\par_data\Entity\ParDataRegulatoryFunction;
use Drupal\par_data\Entity\ParDataRegulatoryFunctionType;
use Drupal\par_data\Entity\ParDataSicCode;
use Drupal\par_data\Entity\ParDataSicCodeType;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests the par notifications, based off the par_data tests.
 *
 * Primarily tests that users receive the correct notifications.
 *
 * @group PAR Data
 */
class ParNotificationTestBase extends ParDataTestBase
{

  static $modules = ['language', 'content_translation', 'comment', 'trance', 'par_data', 'par_data_config', 'message', 'par_notification', 'address', 'datetime', 'datetime_range', 'file_test', 'file', 'file_entity'];

  protected $entityEvent;
  protected $parDataEvent;

  /**
   * Notification preferences
   */
  protected $preferences = [
    'new_deviation_request',
    'new_enforcement_notification',
    'new_general_enquiry',
    'new_inspection_feedback',
    'new_partnership_notification',
    'new_response',
    'partnership_approved_notificatio',
    'partnership_confirmed_notificati',
    'partnership_revocation_notificat',
    'reviewed_deviation_request',
    'reviewed_enforcement',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp()
  {
    // Must change the bytea_output to the format "escape" before running tests.
    // @see https://www.drupal.org/node/2810049
    //db_query("ALTER DATABASE 'par' SET bytea_output = 'escape';")->execute();

    parent::setUp();

    // Set up the entity events.
    $this->entityEvent = $this->getMockBuilder('Drupal\Core\Entity\EntityEvent')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->parDataEvent = $this->getMockBuilder('Drupal\par_data\Event\ParDataEventInterface')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();

    $this->entityEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $this->parDataEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
  }

  public function createAuthority()
  {
    // We need to create an authority first.
    $authority_values = $this->getAuthorityValues();

    // We need to create additional people.
    $person_2 = ParDataPerson::create($this->getPersonValues());
    $person_2->set('field_notification_preferences', $this->preferences);
    $person_3 = ParDataPerson::create($this->getPersonValues());
    $person_4 = ParDataPerson::create($this->getPersonValues());
    $person_4->set('field_notification_preferences', $this->preferences);
    $person_5 = ParDataPerson::create($this->getPersonValues());
    $person_2->save();
    $person_3->save();
    $person_4->save();
    $person_5->save();
    $authority_values['field_person'][] = $person_2->id();
    $authority_values['field_person'][] = $person_3->id();
    $authority_values['field_person'][] = $person_4->id();
    $authority_values['field_person'][] = $person_5->id();

    $authority = ParDataAuthority::create($authority_values);

    $authority->save();
    return $authority;
  }

  public function createOrganisation()
  {
    // We need to create an organisation first.
    $organisation_values = $this->getOrganisationValues();

    // We need to create additional people.
    $person_7 = ParDataPerson::create($this->getPersonValues());
    $person_7->set('field_notification_preferences', $this->preferences);
    $person_8 = ParDataPerson::create($this->getPersonValues());
    $person_9 = ParDataPerson::create($this->getPersonValues());
    $person_9->set('field_notification_preferences', $this->preferences);
    $person_10 = ParDataPerson::create($this->getPersonValues());
    $person_7->save();
    $person_8->save();
    $person_9->save();
    $person_10->save();
    $organisation_values['field_person'][] = $person_7->id();
    $organisation_values['field_person'][] = $person_8->id();
    $organisation_values['field_person'][] = $person_9->id();
    $organisation_values['field_person'][] = $person_10->id();

    $organisation = ParDataOrganisation::create($organisation_values);

    $organisation->save();
    return $organisation;
  }

  public function createPartnership()
  {
    $authority = $this->createAuthority();
    $organisation = $this->createOrganisation();

    // We need to create an Advice first.
    $advice = ParDataAdvice::create($this->getAdviceValues());
    $advice->save();

    // We need to create an Inspection Plan first.
    $inspection_plan = ParDataInspectionPlan::create($this->getInspectionPlanValues());
    $inspection_plan->save();

    // We need to create a Regulatory Function first.
    $regulatory_function = ParDataRegulatoryFunction::create($this->getRegulatoryFunctionValues());
    $regulatory_function->save();

    // Get the authority people that will be listed as contacts for this partnership.
    $authority_people = $authority->get('field_person')->referencedEntities();
    $person_1 = $authority_people[0];
    $person_2 = $authority_people[1];
    $person_3 = $authority_people[2];

    // Get the organisation people that will be listed as contacts for this partnership.
    $organisation_people = $organisation->get('field_person')->referencedEntities();
    $person_6 = $authority_people[0];
    $person_7 = $authority_people[1];
    $person_8 = $authority_people[2];

    $partnership_values = [
      'type' => 'partnership',
      'partnership_type' => 'direct',
      'partnership_status' => 'active',
      'about_partnership' => $this->randomString(1000),
      'approved_date' => '2017-06-01',
      'cost_recovery' => 'Cost recovery from partnership',
      'reject_comment' => $this->randomString(1000),
      'revocation_source' => 'RD Executive',
      'revocation_date' => '2017-07-01',
      'revocation_reason' => $this->randomString(1000),
      'authority_change_comment' => $this->randomString(1000),
      'organisation_change_comment' => $this->randomString(1000),
      'terms_organisation_agreed' => TRUE,
      'terms_authority_agreed' => TRUE,
      'coordinator_suitable' => TRUE,
      'partnership_info_agreed_authority' => TRUE,
      'partnership_info_agreed_business' => TRUE,
      'written_summary_agreed' => TRUE,
      'field_organisation' => [
        $organisation->id(),
      ],
      'field_authority' => [
        $authority->id(),
      ],
      'field_advice' => [
        $advice->id(),
      ],
      'field_inspection_plan' => [
        $inspection_plan->id(),
      ],
      'field_regulatory_function' => [
        $regulatory_function->id(),
      ],
      'field_authority_person' => [
        $person_1->id(),
        $person_2->id(),
        $person_3->id(),
      ],
      'field_organisation_person' => [
        $person_6->id(),
        $person_7->id(),
        $person_8->id(),
      ]
    ] + $this->getBaseValues();

    $partnership = ParDataPartnership::create($partnership_values);

    $partnership->save();
    return $partnership;
  }

  public function createEnforcement() {
    // We need to create an Enforcing Authority first.
    $enforcing_authority = $this->createAuthority();

    // We need to create a partnership
    $partnership = $this->createPartnership();
    $primary_authority = current($partnership->getAuthority());
    $organisation = current($partnership->getOrganisation());

    $legal_entity = current($organisation->getLegalEntity());

    // We need to create an Enforcement Action first.
    $enforcement_action = ParDataEnforcementAction::create($this->getEnforcementActionValues());
    $enforcement_action->save();

    // We need to get the enforcing officer from the enforcing authority.
    $enforcing_person = $enforcing_authority->get('field_person')->referencedEntities()[4];

    $enforcement_values = [
      'type' => 'enforcement_notice',
      'notice_type' => 'proposed',
      'notice_date' => '2017-10-01',
      'legal_entity_name' => 'Unassigned Legal Entity Ltd',
      'summary' => $this->randomString(1000),
      'field_enforcing_authority' => [
        $enforcing_authority->id(),
      ],
      'field_organisation' => [
        $organisation->id(),
      ],
      'field_partnership' => [
        $partnership->id(),
      ],
      'field_primary_authority' => [
        $primary_authority->id(),
      ],
      'field_legal_entity' => [
        $legal_entity->id(),
      ],
      'field_enforcement_action' => [
        $enforcement_action->id(),
      ],
      'field_person' => [
        $enforcing_person->id(),
      ],
    ] + $this->getBaseValues();

    $enforcement_notice = ParDataEnforcementNotice::create($enforcement_values);

    $enforcement_notice->save();
    return $enforcement_notice;
  }
}
