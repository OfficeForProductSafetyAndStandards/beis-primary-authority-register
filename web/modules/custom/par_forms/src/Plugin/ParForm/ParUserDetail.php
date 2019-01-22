<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
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

    if ($user instanceof UserInterface) {
      $this->getFlowDataHandler()->setFormPermValue('user_account', $user->getEmail());
      $last_login_date = $this->getDateFormatter()->format($user->getLastLoginTime(), 'gds_date_format');
      $this->setDefaultValuesByKey('user_login', $cardinality, $last_login_date);

      $roles = Role::loadMultiple($user->getRoles());
      $user_roles = [];
      foreach ($roles as $user_role) {
        if (in_array($user_role->id(), ['par_authority', 'par_enforcement_officer', 'par_organisation', 'par_helpdesk'])) {
          $user_roles[] = str_replace('PAR ', '', $user_role->label());
        }
      }
      $this->setDefaultValuesByKey("user_roles", $cardinality, implode(', ', $user_roles));
      $this->getFlowDataHandler()->setFormPermValue("user_id", $user->id());
    }
    elseif ($par_data_person instanceof ParDataEntityInterface) {
      $this->getFlowDataHandler()->setFormPermValue("person_id", $par_data_person->id());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $form['user_account'] = [
      '#type' => 'fieldset',
      '#weight' => -1,
      '#attributes' => ['class' => ['grid-row', 'form-group']],
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

    if ($user_id = $this->getFlowDataHandler()->getFormPermValue('user_id', $cardinality, NULL)) {
      $form['user_account']['email'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>E-mail</strong><br>' . $this->getDefaultValuesByKey('user_account', $cardinality, ''),
        '#attributes' => ['class' => ['column-full']],
      ];
      $form['user_account']['last_access'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>Type of account</strong><br>' . $this->getDefaultValuesByKey('user_roles', $cardinality, ''),
        '#attributes' => ['class' => ['column-two-thirds']],
      ];
      $form['user_account']['roles'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>Last sign in</strong><br>' . $this->getDefaultValuesByKey('user_login', $cardinality, ''),
        '#attributes' => ['class' => ['column-one-third']],
      ];

      $params = $this->getRouteParams() + ['user' => $user_id];
      // Try to add an update profile link.
      try {
        $form['user_account']['manage'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getLinkByRoute('par_profile_update_flows.edit_select_person', $params, ['attributes' => ['class' => ['column-full']]])
              ->setText('Update user profile')
              ->toString(),
          ]),
        ];
      } catch (ParFlowException $e) {

      }

      // Try to add a block user link.
      try {
        $link = $this->getLinkByRoute('par_user_block_flows.block', $params, ['attributes' => ['class' => ['column-full']]]);
        if ($link->getUrl()->access()) {
          $form['user_account']['block'] = [
            '#type' => 'markup',
            '#markup' => t('@link', [
              '@link' => $link->setText('Block user account')->toString(),
            ]),
          ];
        }
      } catch (ParFlowException $e) {

      }

      // Try to add a block user link.
      try {
        $link = $this->getLinkByRoute('par_user_block_flows.unblock', $params, ['attributes' => ['class' => ['column-full']]]);
        if ($link->getUrl()->access()) {
          $form['user_account']['unblock'] = [
            '#type' => 'markup',
            '#markup' => t('@link', [
              '@link' => $link->setText('Re-activate user account')->toString(),
            ]),
          ];
        }
      } catch (ParFlowException $e) {

      }
    }
    elseif ($person_id = $this->getFlowDataHandler()->getFormPermValue('person_id', NULL)) {
      $form['contact'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('No user account could be found.'),
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
              '@link' => $link->setText('Invite the user to create an account')->toString(),
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
