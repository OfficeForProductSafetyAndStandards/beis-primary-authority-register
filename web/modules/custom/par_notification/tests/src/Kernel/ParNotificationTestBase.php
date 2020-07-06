<?php

namespace Drupal\Tests\par_notification\Kernel;

use Drupal\comment\Entity\Comment;
use Drupal\message\Entity\MessageTemplate;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataRegulatoryFunction;
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

  static $modules = ['language', 'content_translation', 'comment', 'trance', 'par_data', 'par_data_config', 'message', 'par_message_config', 'par_notification', 'address', 'datetime', 'datetime_range', 'file_test', 'file', 'file_entity'];

  /**
   * Notification types
   */
  protected $preferences = [
    'new_deviation_request',
    'new_enforcement_notification',
    'new_general_enquiry',
    'new_inspection_feedback',
    'new_partnership_notification',
    'new_deviation_response',
    'new_enquiry_response',
    'new_inspection_feedback_response',
    'new_inspection_plan',
    'partnership_approved_notificatio',
    'partnership_confirmed_notificati',
    'partnership_revocation_notificat',
    'revoke_inspection_plan',
    'reviewed_deviation_request',
    'reviewed_enforcement',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp()
  {
    parent::setUp();

    // Install config for par_notification if required.
    $this->installConfig('message');
    $this->installConfig('par_notification');

    // Create the entity bundles required for testing.
    foreach ($this->preferences as $notification_type) {
      $type = MessageTemplate::create([
        'template' => $notification_type,
        'label' => ucfirst(str_replace('_', ' ', $notification_type)),
      ]);
      $type->save();
    }

    // Install the feature config for all messages
    $this->installConfig('par_message_config');

    // Mock all the par_notification event subscriber services.
    $container = \Drupal::getContainer();

    $this->new_deviation_response_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewDeviationRequestReplySubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_deviation_reply_subscriber', $this->new_deviation_response_subscriber);

    $this->new_deviation_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewDeviationRequestSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_deviation_request_subscriber', $this->new_deviation_subscriber);

    $this->new_enforcement_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewEnforcementSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_enforcement_subscriber', $this->new_enforcement_subscriber);

    $this->new_enquiry_response_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewGeneralEnquiryReplySubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_enquiry_reply_subscriber', $this->new_enquiry_response_subscriber);

    $this->new_enquiry_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewGeneralEnquirySubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_general_enquiry_subscriber', $this->new_enquiry_subscriber);

    $this->new_inspection_feedback_response_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewInspectionFeedbackReplySubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_inspection_feedback_reply_subscriber', $this->new_inspection_feedback_response_subscriber);

    $this->new_inspection_feedback_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewInspectionFeedbackSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_inspection_feedback_subscriber', $this->new_inspection_feedback_subscriber);

    $this->new_partnership_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewPartnershipSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.new_partnership_application_subscriber', $this->new_partnership_subscriber);

    $this->partnership_completed_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\PartnershipApplicationCompletedSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.partnership_application_completed_subscriber', $this->partnership_completed_subscriber);

    $this->partnership_approved_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\PartnershipApprovedSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.partnership_approved_subscriber', $this->partnership_approved_subscriber);

    $this->partnership_revoked_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\PartnershipRevocationSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.partnership_revocation_subscriber', $this->partnership_revoked_subscriber);

    $this->deviation_reviewed_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\ReviewedDeviationRequestSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.reviewed_deviation_request_subscriber', $this->deviation_reviewed_subscriber);

    $this->enforcement_reviewed_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\ReviewedEnforcementSubscriber')
      ->setMethods(['getSubscribedEvents', 'onEvent'])
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('par_notification.reviewed_enforcement_subscriber', $this->enforcement_reviewed_subscriber);

    \Drupal::setContainer($container);
  }

  public function createAuthority($name = NULL)
  {
    if (!$name) {
      $name = $this->randomString(16);
    }

    // We need to create an authority first.
    $authority_values = ['authority_name' => "{$name} Authority"] + $this->getAuthorityValues();

    // We need to create additional people.
    // person_1 is the primary contact for this authority and has already been added above.
    // person_2 & person_3 are secondary contacts for the partnership that will be added to the partnership.
    // person_4 & person_5 are members of the authority only, they have no relationship to the partnership.
    $person_2 = ParDataPerson::create(['email' => "person_2@{$name}.com"] + $this->getPersonValues());
    $person_2->set('field_notification_preferences', $this->preferences);
    $person_3 = ParDataPerson::create(['email' => "person_3@{$name}.com"] + $this->getPersonValues());
    $person_4 = ParDataPerson::create(['email' => "person_4@{$name}.com"] + $this->getPersonValues());
    $person_4->set('field_notification_preferences', $this->preferences);
    $person_5 = ParDataPerson::create(['email' => "person_5@{$name}.com"] + $this->getPersonValues());
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
    // person_6 is the primary contact for this organisation and has already been added above.
    // person_7 & person_8 are secondary contacts for the organisation that will be added to the partnership.
    // person_9 & person_10 are members of the organisation only, they have no relationship to the partnership.
    $person_7 = ParDataPerson::create(['email' => 'person_7@organisation.com'] + $this->getPersonValues());
    $person_7->set('field_notification_preferences', $this->preferences);
    $person_8 = ParDataPerson::create(['email' => 'person_8@organisation.com'] + $this->getPersonValues());
    $person_9 = ParDataPerson::create(['email' => 'person_9@organisation.com'] + $this->getPersonValues());
    $person_9->set('field_notification_preferences', $this->preferences);
    $person_10 = ParDataPerson::create(['email' => 'person_10@organisation.com'] + $this->getPersonValues());
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
    $authority = $this->createAuthority('primary');
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
    $person_6 = $organisation_people[0];
    $person_7 = $organisation_people[1];
    $person_8 = $organisation_people[2];

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

  public function createReferredEnforcement() {
    $enforcement = $this->createEnforcement();

    // Block the original action.
    $primary_action = $enforcement->getEnforcementActions(TRUE);
    if ($primary_action) {
      $primary_action->block('Test block action.');
    }

    // We need to create an additional Enforcement Action.
    $enforcement_action = ParDataEnforcementAction::create($this->getEnforcementActionValues());
    $enforcement_action->approve(FALSE);
    $enforcement_action->save();
    $enforcement->get('field_enforcement_action')->appendItem($enforcement_action);

    // We need to create an additional Enforcement Action.
    $enforcement_action = ParDataEnforcementAction::create($this->getEnforcementActionValues());
    $enforcement_action->refer('Test refer action.', FALSE);
    $enforcement_action->save();
    $enforcement->get('field_enforcement_action')->appendItem($enforcement_action);

  }

  public function createEnforcement() {
    // We need to create an Enforcing Authority first.
    $enforcing_authority = $this->createAuthority('enforcing');
    // We need to get the enforcing officer from the enforcing authority.
    $enforcing_person = $enforcing_authority->get('field_person')->referencedEntities()[4];

    // We need to create a partnership
    $partnership = $this->createPartnership();
    $primary_authority = current($partnership->getAuthority());
    $organisation = current($partnership->getOrganisation());

    $legal_entity = current($organisation->getLegalEntity());

    // We need to create an Enforcement Action first.
    $enforcement_action = ParDataEnforcementAction::create($this->getEnforcementActionValues());
    $enforcement_action->save();

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

  public function createDeviationRequest() {
    // We need to create an Enforcing Authority first.
    $enforcing_authority = $this->createAuthority('enforcing');
    // We need to get the enforcing officer from the enforcing authority.
    $enforcing_person = $enforcing_authority->get('field_person')->referencedEntities()[4];

    // We need to create a partnership
    $partnership = $this->createPartnership();

    $inspection_plans = $partnership->get('field_inspection_plan')->referencedEntities();
    $inspection_plan = current($inspection_plans);

    /** @var \Drupal\file\Entity\File $document */
    $document = $this->createFile();

    $deviation_request_values = [
      'type' => 'deviation_request',
      'request_date' => '2017-10-01',
      'notes' => $this->randomString(1000),
      'primary_authority_status' => 'awaiting',
      'primary_authority_notes' => $this->randomString(1000),
      'document' => [
        $document->id(),
      ],
      'field_enforcing_authority' => [
        $enforcing_authority->id(),
      ],
      'field_partnership' => [
        $partnership->id(),
      ],
      'field_inspection_plan' => [
        $inspection_plan->id(),
      ],
      'field_person' => [
        $enforcing_person->id(),
      ],
    ] + $this->getBaseValues();

    $deviation_request = ParDataDeviationRequest::create($deviation_request_values);

    $deviation_request->save();
    return $deviation_request;
  }

  public function createInspectionFeedback() {
    // We need to create an Enforcing Authority first.
    $enforcing_authority = $this->createAuthority('enforcing');
    // We need to get the enforcing officer from the enforcing authority.
    $enforcing_person = $enforcing_authority->get('field_person')->referencedEntities()[4];

    // We need to create a partnership
    $partnership = $this->createPartnership();

    $inspection_plans = $partnership->get('field_inspection_plan')->referencedEntities();
    $inspection_plan = current($inspection_plans);

    $inspection_feedback_values = [
        'type' => 'inspection_feedback',
        'request_date' => '2017-10-01',
        'notes' => $this->randomString(1000),
        'primary_authority_status' => 'awaiting',
        'primary_authority_notes' => $this->randomString(1000),
        'document' => [
          ''
        ],
        'field_enforcing_authority' => [
          $enforcing_authority->id(),
        ],
        'field_partnership' => [
          $partnership->id(),
        ],
        'field_inspection_plan' => [
          $inspection_plan->id(),
        ],
        'field_person' => [
          $enforcing_person->id(),
        ],
      ] + $this->getBaseValues();

    $inspection_feedback = ParDataInspectionFeedback::create($inspection_feedback_values);

    $inspection_feedback->save();
    return $inspection_feedback;
  }

  public function createGeneralEnquiry() {
    // We need to create an Enforcing Authority first.
    $enforcing_authority = $this->createAuthority('enforcing');
    // We need to get the enforcing officer from the enforcing authority.
    $enforcing_person = $enforcing_authority->get('field_person')->referencedEntities()[4];

    // We need to create a partnership.
    $partnership = $this->createPartnership();
    $primary_authority = current($partnership->getAuthority());

    $general_enquiry_values = [
        'type' => 'general_enquiry',
        'request_date' => '2017-10-01',
        'notes' => $this->randomString(1000),
        'primary_authority_status' => 'awaiting',
        'primary_authority_notes' => $this->randomString(1000),
        'document' => [
          ''
        ],
        'field_enforcing_authority' => [
          $enforcing_authority->id(),
        ],
        'field_partnership' => [
          $partnership->id(),
        ],
        'field_primary_authority' => [
          $primary_authority->id(),
        ],
        'field_person' => [
          $enforcing_person->id(),
        ],
      ] + $this->getBaseValues();

    $general_enquiry = ParDataGeneralEnquiry::create($general_enquiry_values);

    $general_enquiry->save();
    return $general_enquiry;
  }

  public function createGeneralEnquiryComment() {
    $enquiry = $this->createGeneralEnquiry();
    $enforcing_officer_mail = !$enquiry->get('field_person')->isEmpty() ? current($enquiry->get('field_person')->referencedEntities())->getEmail() : '';

    // Login as the enforcing officer.
    $enforcing_officer = $this->createUser(['mail' => $enforcing_officer_mail], $this->permissions);
    \Drupal::currentUser()->setAccount($enforcing_officer);

    $comment = Comment::create([
      'entity_type' => $enquiry->getEntityTypeId(),
      'entity_id'   => $enquiry->id(),
      'field_name'  => 'messages',
      'uid' => $enforcing_officer->id(),
      'comment_type' => 'par_deviation_request_comments',
      'subject' => substr($enquiry->label(), 0, 64),
      'status' => 1,
      'comment_body' => $this->randomString(1000),
    ]);
    $comment->save();

    \Drupal::currentUser()->setAccount($this->account);

    return $comment;
  }
}
