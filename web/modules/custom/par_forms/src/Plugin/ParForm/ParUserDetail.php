<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\Role;
use Drupal\user\UserInterface;

/**
 * User details display plugin.
 *
 * @ParForm(
 *   id = "user_detail",
 *   title = @Translation("User detail display.")
 * )
 */
class ParUserDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $user = $this->getFlowDataHandler()->getParameter('user');
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    $cache_tags = [];

    if ($user instanceof UserInterface) {
      $cache_tags[] = "user:{$user->id()}";

      $this->getFlowDataHandler()->setFormPermValue('user_account', $user->getEmail());
      $last_login_date = $user->getLastLoginTime() ? $this->getDateFormatter()->format($user->getLastLoginTime(), 'gds_date_format') : NULL;
      $this->setDefaultValuesByKey('user_login', $index, $last_login_date);
      $this->setDefaultValuesByKey('user_active', $index, (bool) $user->isActive());

      /** @var \Drupal\par_roles\ParRoleManagerInterface $par_role_manager */
      $par_role_manager = \Drupal::service('par_roles.role_manager');

      // Display the user roles.
      $user_roles = $par_role_manager->displayRoles($user);
      if (!empty($user_roles)) {
        $this->setDefaultValuesByKey("user_roles", $index, implode(', ', $user_roles));
      }

      $this->getFlowDataHandler()->setFormPermValue("user_id", $user->id());
    }
    elseif ($par_data_person instanceof ParDataEntityInterface) {
      $cache_tags[] = "par_data_person:{$par_data_person->id()}";

      $this->getFlowDataHandler()->setFormPermValue("person_id", $par_data_person->id());
    }

    $this->getFlowDataHandler()->setFormPermValue('cache_tags', $cache_tags);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get the current invitation expiry date if one has already been sent.
    $invitation_expiry = $this->getFlowDataHandler()->getDefaultValues('invitation_expiration', FALSE);
    $cache_tags = $this->getFlowDataHandler()->getDefaultValues('cache_tags', []);

    $form['user_account'] = [
      '#type' => 'container',
      '#weight' => -1,
      '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group']],
      '#cache' => ['tags' => $cache_tags]
    ];
    if ($index === 1) {
      $form['user_account'] += [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('User account'),
          '#attributes' => ['class' => ['govuk-heading-l', 'govuk-grid-column-full']],
        ],
        'info' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => "A user account allows a person to log into the primary authority register.",
          '#attributes' => ['class' => ['govuk-grid-column-full']],
        ],
      ];
    }

    if ($user_id = $this->getFlowDataHandler()->getFormPermValue('user_id')) {
      $form['user_account']['email'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#value' => 'E-mail',
          '#attributes' => ['class' => ['govuk-heading-s']],
        ],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('user_account', $index, ''),
        ],
      ];
      $form['user_account']['roles'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#value' => 'Type of account',
          '#attributes' => ['class' => ['govuk-heading-s']],
        ],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('user_roles', $index, 'This user has no roles.'),
        ],
      ];

      // Check whether the user is active.
      $active = $this->getDefaultValuesByKey('user_active', $index, FALSE);
      $unblockable = $this->getDefaultValuesByKey('user_unblockable', $index, FALSE);
      if ($active) {
        $form['user_account']['last_access'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['govuk-grid-column-one-third']],
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h3',
            '#value' => 'Last sign in',
            '#attributes' => ['class' => ['govuk-heading-s']],
          ],
          'value' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $this->getDefaultValuesByKey('user_login', $index, 'Never signed in'),
          ],
        ];
      }
      else {
        $form['user_account']['blocked'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => '<strong>The account is no longer active</strong><br>',
          '#attributes' => ['class' => ['govuk-grid-column-one-third']],
        ];
      }

      if ($unblockable) {
        $form['user_account']['blocked'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => 'This user can not be removed because they are the only member of one of their authorities or organisations.',
          '#attributes' => ['class' => ['govuk-grid-column-full']],
        ];
      }

      $params = ['user' => $user_id];
      // Try to add a role management link.
      try {
        $roles_flow = ParFlow::load('manage_user_roles');
        $roles_link = $roles_flow?->getStartLink(1, 'Manage roles', $params);
        if ($roles_link instanceof Link) {
          $form['user_account']['manage_roles'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $roles_link->toString(),
            '#attributes' => ['class' => ['govuk-grid-column-one-quarter']],
          ];
        }
      }
      catch (ParFlowException $e) {

      }

      // Try to add a block user link.
      try {
        $block_flow = ParFlow::load('block_user');
        $block_link = $block_flow?->getStartLink(1, 'Block user account', $params);
        if ($block_link instanceof Link) {
          $form['user_account']['block'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $block_link->toString(),
            '#attributes' => ['class' => ['govuk-grid-column-one-quarter']],
          ];
        }
      }
      catch (ParFlowException $e) {

      }

      // Try to add an un-block user link.
      try {
        $unblock_flow = ParFlow::load('unblock_user');
        $unblock_link = $unblock_flow?->getStartLink(1, 'Re-activate user account', $params);
        if ($unblock_link instanceof Link) {
          $form['user_account']['unblock'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $unblock_link->toString(),
            '#attributes' => ['class' => ['govuk-grid-column-one-quarter']],
          ];
        }
      }
      catch (ParFlowException $e) {

      }

    }
    elseif ($person_id = $this->getFlowDataHandler()->getFormPermValue('person_id')) {
      $form['contact'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t($invitation_expiry ?
            "An invitation has already been sent, it is due to expire on $invitation_expiry" :
            "No user account could be found"),
        ],
      ];

      // Try to add an invitation link.
      try {
        $params = ['par_data_person' => $person_id];
        $link_options = ['attributes' => ['class' => ['govuk-grid-column-full']]];
        $invite_flow = ParFlow::load('user_invite');
        $link_text = $invitation_expiry ? 'Re-send the invitation' : 'Invite the user to create an account';
        $invite_link = $invite_flow?->getStartLink(1, $link_text, $params, $link_options);
        if ($invite_link instanceof Link) {
          $form['user_account']['invite'] = [
            '#type' => 'markup',
            '#markup' => t('@link', [
              '@link' => $invite_link->toString(),
            ]),
          ];
        }
      }
      catch (ParFlowException $e) {

      }
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }
}
