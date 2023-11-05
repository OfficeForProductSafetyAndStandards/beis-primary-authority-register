<?php

namespace Drupal\par_member_list_update_flows\Form;

use CommerceGuys\Intl\Formatter\ParsedPattern;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_log\EventSubscriber\ParData;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Confirm the number of members.
 */
class ParConfirmListDetailsForm extends ParBaseForm {

  /**
   * The confirmation values.
   */
  const CONFIRM = 'yes';
  const UPDATE = 'no';

  /**
   * The revision prefix for identifying when the organisation last updated the list.
   */
  const REVISION_PREFIX = 'PAR_MEMBER_LIST_UPDATE';

  protected $pageTitle = 'Is the list up-to-date?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */

    $par_data_partnership = $this->getFlowDataHandler()
      ->getParameter('par_data_partnership');

    if ($par_data_partnership->isCoordinated()) {
      $this->getFlowDataHandler()
        ->setFormPermValue('member_number', $par_data_partnership->numberOfMembers());
      $this->getFlowDataHandler()
        ->setFormPermValue('member_display', $par_data_partnership->getMemberDisplay());

      $display_label = $par_data_partnership->getTypeEntity()
        ->getAllowedFieldlabel('member_display', $par_data_partnership->getMemberDisplay());
      $this->getFlowDataHandler()
        ->setFormPermValue('display_label', strtolower($display_label));

      // External lists should set the link.
      $link = $par_data_partnership->getMemberLink();
      if ($par_data_partnership->getMemberDisplay() === ParDataPartnership::MEMBER_DISPLAY_EXTERNAL) {
        $this->getFlowDataHandler()->setFormPermValue('member_link', $link);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $member_number = $this->getFlowDataHandler()->getDefaultValues('member_number', 0);
    $member_display = $this->getFlowDataHandler()->getDefaultValues('member_display');
    $display_label = $this->getFlowDataHandler()->getDefaultValues('display_label', $member_display);

    // Confirm the number of members.
    $form['number'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['member-count', 'govuk-form-group']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $this->t('Number of members'),
        '#attributes' => ['class' => ['govuk-heading-m']],
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->formatPlural(
          $member_number,
          'There is <strong>@count</strong> active member in the member list.',
          'There are <strong>@count</strong> active members in the member list.',
          ['@count' => $member_number]
        ),
      ]
    ];

    switch ($member_display) {
      case ParDataPartnership::MEMBER_DISPLAY_INTERNAL:
        $message = $this->t("You will be able to update the member list, and change the number of members, after confirming you want this to be hosted internally within the Primary Authority Register.");
        break;

      case ParDataPartnership::MEMBER_DISPLAY_EXTERNAL:
        $message = $this->t('The list will display as an @type.', ['@type' => $display_label]);
        break;

      case ParDataPartnership::MEMBER_DISPLAY_REQUEST:
        $message = $this->t('The list will display as an @type.<br><br>
            The co-ordinator must make the copy available
            as soon as reasonably practicable and, in any event, not later than the
            third working day after the date of receiving the request at no charge.',
          ['@type' => $display_label]);
        break;

    }

    $form['number']['warning'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $message,
    ];

    // External lists should confirm the link if one has been set.
    if ($member_display === ParDataPartnership::MEMBER_DISPLAY_EXTERNAL
      && $member_link = $this->getFlowDataHandler()->getDefaultValues('member_link', NULL)) {
      $form['link'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['member-link', 'govuk-form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#value' => $this->t('Member link'),
          '#attributes' => ['class' => ['govuk-heading-m']],
        ],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('This member list is publicly accessible on the following link:'),
        ],
        'link' => [
          '#type' => 'link',
          '#title' => $member_link->toString(),
          '#url' => $member_link,
          '#attributes' => ['class' => 'external-link'],
        ]
      ];
    }

    // Confirm the list type.
    $form['confirm'] = [
      '#type' => 'radios',
      '#title' => $this->t('Do these details accurately reflect the member list?'),
      '#title_tag' => 'h2',
      '#options' => [
        self::CONFIRM => 'Yes, these details are correct',
        self::UPDATE => 'No, these details need to be updated'
      ],
      '#default_value' => self::CONFIRM,
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('confirm')) {
      $id = $this->getElementId(['confirm'], $form);
      $form_state->setErrorByName($this->getElementName('confirm'), $this->wrapErrorMessage('Please confirm these details.', $id));
    }

    parent::validateForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $list_type_cid = $this->getFlowNegotiator()->getFormKey('par_update_list_type');
    $member_link_cid = $this->getFlowNegotiator()->getFormKey('par_update_member_link');
    $member_number_cid = $this->getFlowNegotiator()->getFormKey('par_update_member_number');

    if ($par_data_partnership) {
      $allowed_types = $par_data_partnership->getTypeEntity()
        ->getAllowedValues('member_display');
      $type = $this->getFlowDataHandler()->getTempDataValue('type', $list_type_cid);

      // If no list type has been selected return the partnership without changes.
      if (empty($type) || !isset($allowed_types[$type])) {
        return [
          'par_data_partnership' => $par_data_partnership
        ];
      }

      // Set the list type.
      $par_data_partnership->set('member_display', $type);

      // Set the link for external lists.
      // No need to reset the link field if switching from an external list.
      $link = $this->getFlowDataHandler()->getTempDataValue('member_link', $member_link_cid);
      if ($type === ParDataPartnership::MEMBER_DISPLAY_EXTERNAL && !empty($link)) {
        $par_data_partnership->set('member_link', $link);
      }

      // Set the number of members if NOT an internal list.
      // No need to reset the number of members if switching to an internal list.
      $count = $this->getFlowDataHandler()->getTempDataValue('number_members', $member_number_cid);
      if ($type !== ParDataPartnership::MEMBER_DISPLAY_INTERNAL && !empty($count)) {
        $par_data_partnership->set('member_number', $count);
      }
    }

    return [
      'par_data_partnership' => $par_data_partnership ?? NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $confirm = $this->getFlowDataHandler()->getTempDataValue('confirm');

    // If the changes are confirmed.
    if ($confirm === self::CONFIRM) {
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

      // Save a new partnership revision.
      $revision_message = "The membership list has been confirmed.";
      $par_data_partnership->setNewRevision(TRUE, implode(':', [ParDataPartnership::MEMBER_LIST_REVISION_PREFIX, $revision_message]));
      if ($par_data_partnership->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }

      // Internal lists should return to the list overview page.
      if ($par_data_partnership->getMemberDisplay() === ParDataPartnership::MEMBER_DISPLAY_INTERNAL) {
        $confirm_route = 'view.members_list.member_list_coordinator';
        $form_state->setRedirect($confirm_route, ['par_data_partnership' => $par_data_partnership->id()]);
      }
    }

    // Return to the first step.
    if ($confirm === self::UPDATE) {
      // The journey is complete.
      $confirm_route = $this->getFlowNegotiator()->getFlow()->progress('update');
      $form_state->setRedirectUrl($confirm_route);
    }
  }

}
