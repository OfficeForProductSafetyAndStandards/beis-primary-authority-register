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
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\Role;
use Drupal\user\UserInterface;

/**
 * User details display plugin.
 *
 * @ParForm(
 *   id = "user_institutions",
 *   title = @Translation("User institution display.")
 * )
 */
class ParUserInstitutions extends ParFormPluginBase {

  /**
   * Get the PAR Role manager.
   */
  protected function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $user = $this->getFlowDataHandler()->getParameter('user');
    $cache_tags = [];

    if ($user instanceof UserInterface) {
      $cache_tags[] = "user:{$user->id()}";

      /** @var \Drupal\par_roles\ParRoleManagerInterface $par_role_manager */
      $par_role_manager = \Drupal::service('par_roles.role_manager');

      // Display the user institutions.
      $institutions = iterator_to_array($par_role_manager->getInstitutions($user));
      $this->getFlowDataHandler()->setParameter('institutions', $institutions);
    }

    $this->getFlowDataHandler()->setFormPermValue('cache_tags', $cache_tags);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $cache_tags = $this->getFlowDataHandler()->getDefaultValues('cache_tags', []);

    $user = $this->getFlowDataHandler()->getParameter('user') ?? NULL;
    $institutions = $this->getFlowDataHandler()->getParameter('institutions') ?? [];

    $form['user_institutions'] = [
      '#type' => 'container',
      '#weight' => -1,
      '#attributes' => ['class' => ['govuk-form-group']],
      '#cache' => ['tags' => $cache_tags]
    ];

    $message = count($institutions) >= 1 ?
      $this->t("This user has the following memberships:") :
      $this->t("This user does not belong to any authorities or organisations");
    $form['user_institutions'] += [
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Authorities & Organisations'),
        '#attributes' => ['class' => ['govuk-heading-l']],
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $message,
      ],
    ];

    $form['user_institutions']['list'] = [
      '#theme' => 'item_list',
      '#items' => [],
      '#attributes' => ['class' => ['govuk-list', 'govuk-form-group', 'govuk-list--bullet']],
    ];
    foreach ($institutions as $institution) {
      // Add the label.
      $form['user_institutions']['list']['#items'][$institution->id()] = [
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $institution->label(),
        ],
      ];

      // Add the link to remove the user from this institution.
      try {
        if ($user instanceof UserInterface) {
          $params = [
            'user' => $user?->id(),
            'institution_type' => $institution?->getEntityTypeId(),
            'institution_id' => $institution?->id()
          ];
          $membership_remove_flow = ParFlow::load('person_membership_remove');
          $membership_remove_link = $membership_remove_flow?->getStartLink(1, 'Remove this membership', $params);
          $options = [
            'attributes' => [
              'class' => [
                'remove-institution',
                'govuk-!-margin-left-5'
              ]
            ]
          ];
          $form['user_institutions']['list']['#items'][$institution->id()]['label']['remove'] = [
            '#type' => 'link',
            '#title' => $membership_remove_link?->toString(),
            '#url' => $membership_remove_link?->getUrl(),
            '#options' => $options,
            '#weight' => 100,
          ];
        }
      }
      catch (ParFlowException $e) {

      }
    }

    // Add the link to add the user to a new institution.
    try {
      if ($user instanceof UserInterface) {
        $membership_add_flow = ParFlow::load('person_membership_add');
        $membership_add_link = $membership_add_flow?->getStartLink(1, 'Add a membership', ['user' => $user?->id()]);
        $options = ['attributes' => ['class' => ['add-institution']]];
        $form['user_institutions']['add'] = [
          '#type' => 'link',
          '#title' => $membership_add_link?->toString(),
          '#url' => $membership_add_link?->getUrl(),
          '#options' => $options,
          '#weight' => 100,
        ];
      }
    }
    catch (ParFlowException $e) {

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
