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
      $form['user_account']['email_heading'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>E-mail</strong>',
        '#attributes' => ['class' => ['column-one-third']],
      ];
      $form['user_account']['last_access_heading'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>Last sign in</strong>',
        '#attributes' => ['class' => ['column-one-third']],
      ];
      $form['user_account']['roles_heading'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => '<strong>Type of account</strong>',
        '#attributes' => ['class' => ['column-one-third']],
      ];
      $form['user_account']['email'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->getDefaultValuesByKey('user_account', $cardinality, ''),
        '#attributes' => ['class' => ['column-one-third']],
      ];
      $form['user_account']['last_access'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->getDefaultValuesByKey('user_login', $cardinality, ''),
        '#attributes' => ['class' => ['column-one-third']],
      ];
      $form['user_account']['roles'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->getDefaultValuesByKey('user_roles', $cardinality, ''),
        '#attributes' => ['class' => ['column-one-third']],
      ];

      $params = $this->getRouteParams() + ['user' => $user_id];
      try {
        $form['user_account']['block'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()
              ->getLinkByCurrentOperation('block', $params, ['query' => ['destination' => $return_path]])
              ->setText('Block user')
              ->toString(),
          ]),
          '#attributes' => ['class' => ['column-full']],
        ];
        $form['user_account']['unblock'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()
              ->getLinkByCurrentOperation('unblock', $params, ['query' => ['destination' => $return_path]])
              ->setText('Unblock user')
              ->toString(),
          ]),
          '#attributes' => ['class' => ['column-full']],
        ];
      } catch (ParFlowException $e) {

      }

      try {
        $form['user_account']['manage'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getLinkByRoute('par_profile_update_flows.select_person', $params, ['attributes' => ['class' => ['column-full']]])
              ->setText('Update user profile')
              ->toString(),
          ]),
        ];
      } catch (ParFlowException $e) {

      }

      try {
        $form['user_account']['invite'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getLinkByRoute('par_profile_update_flows.select_person', $params, ['attributes' => ['class' => ['column-full']]])
              ->setText('Invite the user to create an account')
              ->toString(),
          ]),
        ];
      } catch (ParFlowException $e) {

      }

      try {
        $form['user_account']['block'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getLinkByRoute('par_profile_update_flows.select_person', $params, ['attributes' => ['class' => ['column-full']]])
              ->setText('Block the user')
              ->toString(),
          ]),
        ];
      } catch (ParFlowException $e) {

      }

      try {
        $form['user_account']['role'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getLinkByRoute('par_profile_update_flows.select_person', $params, ['attributes' => ['class' => ['column-full']]])
              ->setText('Change role')
              ->toString(),
          ]),
        ];
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

      try {
        $params = $this->getRouteParams() + ['par_data_person' => $person_id];
        $form['user_account']['invite'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()
              ->getLinkByCurrentOperation('invite_user', $params, ['query' => ['destination' => $return_path]])
              ->setText('Invite the user to create an account')
              ->toString(),
          ]),
        ];
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
