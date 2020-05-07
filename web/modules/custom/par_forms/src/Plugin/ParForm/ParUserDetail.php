<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\ParDataException;
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
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $user = $this->getFlowDataHandler()->getParameter('user');
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    $cache_tags = [];

    if ($user instanceof UserInterface) {
      $cache_tags[] = "user:{$user->id()}";

      $this->getFlowDataHandler()->setFormPermValue('user_account', $user->getEmail());
      $last_login_date = $user->getLastLoginTime() ? $this->getDateFormatter()->format($user->getLastLoginTime(), 'gds_date_format') : NULL;
      $this->setDefaultValuesByKey('user_login', $cardinality, $last_login_date);
      $this->setDefaultValuesByKey('user_active', $cardinality, (bool) $user->isActive());

      // Disable blocking of last user in an authority/organisation.
      try {
        $isLastSurvingAuthorityMember = !$this->getParDataManager()
          ->isRoleInAllMemberAuthorities($user, ['par_authority']);
      }
      catch (ParDataException $e) {
        $isLastSurvingAuthorityMember = FALSE;
      }
      try {
        $isLastSurvingOrganisationMember = !$this->getParDataManager()
          ->isRoleInAllMemberOrganisations($user, ['par_organisation']);
      }
      catch (ParDataException $e) {
        $isLastSurvingOrganisationMember = FALSE;
      }
      $this->setDefaultValuesByKey('user_unblockable', $cardinality, (bool) ($isLastSurvingAuthorityMember || $isLastSurvingOrganisationMember));


      $roles = Role::loadMultiple($user->getRoles());
      $user_roles = [];
      foreach ($roles as $user_role) {
        if (in_array($user_role->id(), ['par_authority', 'par_enforcement', 'par_organisation', 'par_helpdesk'])) {
          $user_roles[] = str_replace('PAR ', '', $user_role->label());
        }
      }

      $this->setDefaultValuesByKey("user_roles", $cardinality, implode(', ', $user_roles));
      $this->getFlowDataHandler()->setFormPermValue("user_id", $user->id());
    }
    elseif ($par_data_person instanceof ParDataEntityInterface) {
      $cache_tags[] = "par_data_person:{$par_data_person->id()}";

      $this->getFlowDataHandler()->setFormPermValue("person_id", $par_data_person->id());
    }

    $this->getFlowDataHandler()->setFormPermValue('cache_tags', $cache_tags);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get the current invitation expiry date if one has already been sent.
    $invitation_expiry = $this->getFlowDataHandler()->getDefaultValues('invitation_expiration', FALSE);
    $cache_tags = $this->getFlowDataHandler()->getDefaultValues('cache_tags', []);

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $form['user_account'] = [
      '#type' => 'fieldset',
      '#weight' => -1,
      '#attributes' => ['class' => ['grid-row', 'form-group']],
      '#cache' => ['tags' => $cache_tags]
    ];
    if ($cardinality === 1) {
      $form['user_account'] += [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('User account'),
          '#attributes' => ['class' => ['heading-large', 'column-full']],
        ],
        'info' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => "A user account allows a person to log into the primary authority register.",
          '#attributes' => ['class' => ['column-full']],
        ],
      ];
    }

    if ($user_id = $this->getDefaultValuesByKey('user_id', $cardinality, NULL)) {
      $form['user_account']['email'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>E-mail</strong><br>' . $this->getDefaultValuesByKey('user_account', $cardinality, ''),
        '#attributes' => ['class' => ['column-full']],
      ];
      $form['user_account']['roles'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>Type of account</strong><br>' . $this->getDefaultValuesByKey('user_roles', $cardinality, ''),
        '#attributes' => ['class' => ['column-two-thirds']],
      ];

      // Check whether the user is active.
      $active = $this->getDefaultValuesByKey('user_active', $cardinality, FALSE);
      $unblockable = $this->getDefaultValuesByKey('user_unblockable', $cardinality, FALSE);
      if ($active) {
        $form['user_account']['last_access'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => '<strong>Last sign in</strong><br>' . $this->getDefaultValuesByKey('user_login', $cardinality, 'Never signed in'),
          '#attributes' => ['class' => ['column-one-third']],
        ];
      }
      else {
        $form['user_account']['blocked'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => '<strong>The account is no longer active</strong><br>',
          '#attributes' => ['class' => ['column-one-third']],
        ];
      }

      if ($unblockable) {
        $form['user_account']['blocked'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => 'This user can not be removed because they are the only member of one of their authorities or organisations.<br>',
          '#attributes' => ['class' => ['column-full']],
        ];
      }

      $params = $this->getRouteParams() + ['user' => $user_id];
      // Try to add a block user link.
      try {
        if ($link = $this->getLinkByRoute('par_user_block_flows.block', $params, [], TRUE)) {
          $block_link = t('@link', [
            '@link' => $link->setText('Block user account')->toString(),
          ]);
          $form['user_account']['block'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($block_link) ? $block_link : '',
            '#attributes' => ['class' => ['column-full']],
          ];
        }
      } catch (ParFlowException $e) {

      }

      // Try to add a block user link.
      try {
        if ($link = $this->getLinkByRoute('par_user_block_flows.unblock', $params, [], TRUE)) {
          $unblock_link = t('@link', [
            '@link' => $link->setText('Re-activate user account')->toString(),
          ]);
        }
        $form['user_account']['unblock'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => !empty($unblock_link) ? $unblock_link : '',
          '#attributes' => ['class' => ['column-full']],
        ];
      } catch (ParFlowException $e) {

      }

    }
    elseif ($person_id = $this->getDefaultValuesByKey('person_id', NULL)) {
      $form['contact'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t($invitation_expiry ?
            "An invitation has already been sent, it is due to expire on $invitation_expiry" :
            "No user account could be found"),
        ],
      ];

      // Try to add an invite link.
      try {
        $params = $this->getRouteParams() + ['par_data_person' => $person_id];
        $link = $this->getLinkByRoute('par_invite_user_flows.link_contact', $params, ['attributes' => ['class' => ['column-full']]]);
        if ($link->getUrl()->access()) {
          $form['user_account']['invite'] = [
            '#type' => 'markup',
            '#markup' => t('@link', [
              '@link' => $link->setText($invitation_expiry ?
                'Re-send the invitation' :
                'Invite the user to create an account')->toString(),
            ]),
          ];
        }
      } catch (ParFlowException $e) {

      }
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
