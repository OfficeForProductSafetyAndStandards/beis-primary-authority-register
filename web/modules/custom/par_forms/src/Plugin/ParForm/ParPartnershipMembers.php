<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Partnership member display.
 *
 * @ParForm(
 *   id = "partnership_members",
 *   title = @Translation("Partnership documents display.")
 * )
 */
class ParPartnershipMembers extends ParFormPluginBase {

  /**
   * Available display formats.
   */
  const MEMBER_FORMAT_INLINE = 'member_list';
  const MEMBER_FORMAT_LINK = 'member_link_view';

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface && $par_data_partnership->isCoordinated()) {
      // If there is a members list uploaded already.
      $membershipCount = $par_data_partnership->countMembers();
      if ($membershipCount > 0) {
        // Display only active members.
        $this->getFlowDataHandler()->setFormPermValue("members", $par_data_partnership->getCoordinatedMember(FALSE, TRUE));
        $this->getFlowDataHandler()->setFormPermValue("number_of_members", $membershipCount);
      }
      elseif ($numberOfMembers = $par_data_partnership->numberOfMembers()) {
        $this->getFlowDataHandler()->setFormPermValue("number_of_members", $numberOfMembers);
      }
    }

    // Set display configuration options.
    $available_formats = [self::MEMBER_FORMAT_INLINE, self::MEMBER_FORMAT_LINK];
    $format = isset($this->getConfiguration()['format']) && array_search($this->getConfiguration()['format'], $available_formats) !== FALSE
      ? $this->getConfiguration()['format'] : self::MEMBER_FORMAT_INLINE;
    $this->getFlowDataHandler()->setFormPermValue("member_format", $format);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // This form should only be displayed for coordinated partnerships.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if (!$par_data_partnership instanceof ParDataEntityInterface || !$par_data_partnership->isCoordinated()) {
      return $form;
    }

    $form['members'] = [
      '#type' => 'fieldset',
      '#title' => t('Number of members'),
      '#attributes' => ['class' => 'form-group'],
    ];

    // Show the members count.
    $form['members']['count'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t("There are <strong>@count</strong> active members covered by this partnership.", ['@count' => $this->getDefaultValuesByKey('number_of_members', $cardinality, NULL)]),
      '#attributes' => ['class' => ['form-group', 'number-of-members']],
      '#weight' => -5,
    ];

    // Display the members if this partnership has any.
    $members = $this->getDefaultValuesByKey('members', $cardinality, NULL);
    if (!empty($members)) {
      // Show the link to view the full membership list.
      if ($this->getFlowDataHandler()->getFormPermValue("member_format") === self::MEMBER_FORMAT_LINK) {
        try {
          $member_link = $this->getLinkByRoute('view.members_list.member_list_coordinator', [], [], TRUE);
        } catch (ParFlowException $e) {

        }
        if (isset($member_link) && $member_link instanceof Link) {
          $form['members']['list'] = [
            '#type' => 'fieldset',
            '#title' => t('Members'),
            '#attributes' => ['class' => 'form-group'],
            'link' => [
              '#type' => 'html_tag',
              '#tag' => 'p',
              '#value' => $member_link->setText('Show members list')->toString(),
              '#attributes' => ['class' => ['member-list', 'member-list-link']],
            ],
          ];
        }
      }
      // Show the member list inline.
      elseif ($this->getFlowDataHandler()->getFormPermValue("member_format") === self::MEMBER_FORMAT_INLINE) {
        // Initialize pager and get current page.
        $number_per_page = 5;
        $pager = $this->getUniquePager()->getPager('partnership_manage_organisation_contacts');
        $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($members), $number_per_page, $pager);

        $form['members']['list'] = [
          '#type' => 'fieldset',
          '#title' => t('Members'),
          '#attributes' => ['class' => ['member-list', 'member-list-inline']],
          'items' => [
            '#type' => 'container',
          ],
          'pager' => [
            '#type' => 'pager',
            '#theme' => 'pagerer',
            '#element' => $pager,
            '#weight' => 100,
            '#config' => [
              'preset' => $this->config('pagerer.settings')
                ->get('core_override_preset'),
            ],
          ],
        ];

        // Split the members up into chunks:
        $chunks = array_chunk($members, $number_per_page);
        foreach ($chunks[$current_pager->getCurrentPage()] as $delta => $entity) {
          if (!$entity instanceof ParDataEntityInterface) {
            continue;
          }

          // Display the member.
          $form['members']['list']['items'][$delta] = [
            '#type' => 'fieldset',
            '#attributes' => ['class' => ['grid-row', 'form-group', 'coordinated-member']],
            '#collapsible' => FALSE,
            '#collapsed' => FALSE,
            'entity' => [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => $entity->label(),
              '#attributes' => ['class' => ['column-full']],
            ],
//            'operations' => [
//              'update' => [
//                '#type' => 'html_tag',
//                '#tag' => 'p',
//                '#value' => !empty($update_authority_contact_link) ? $update_authority_contact_link : '',
//                '#attributes' => ['class' => ['column-one-third']],
//              ],
//              'remove' => [
//                '#type' => 'html_tag',
//                '#tag' => 'p',
//                '#value' => !empty($remove_authority_contact_link) ? $remove_authority_contact_link : '',
//                '#attributes' => ['class' => ['column-two-thirds']],
//              ],
//            ],
          ];
        }
      }
    }

    // Add link to add a new member.
    try {
      $add_member_link = $this->getLinkByRoute('par_member_add_flows.add_organisation_name', [], [], TRUE);
    } catch (ParFlowException $e) {

    }
    if (isset($add_member_link) && $add_member_link instanceof Link) {
      $link_label = $members && !empty($members) && count($members) >= 1 ? "add another member" : "add a member";
      $form['members']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $add_member_link->setText($link_label)->toString(),
        '#attributes' => ['class' => ['add-member']],
      ];
    }

    // Add link to upload a new csv member list.
    try {
      $upload_member_link = $this->getLinkByRoute('par_member_upload_flows.member_upload', [], [], TRUE);
    } catch (ParFlowException $e) {

    }
    if (isset($upload_member_link) && $upload_member_link instanceof Link) {
      $form['members']['upload'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $upload_member_link->setText('upload a member list (csv)')->toString(),
        '#attributes' => ['class' => ['upload-member']],
      ];
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
