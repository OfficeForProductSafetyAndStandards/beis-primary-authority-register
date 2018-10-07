<?php

/**
 * @file
 * Contains \Drupal\par_roles\Plugin\views\access\MultiplePermissions.
 */

namespace Drupal\par_roles\Plugin\views\access;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Drupal\user\Plugin\views\access\Permission;

/**
 * Access plugin checking if the current user can operate on an entity.
 *
 * Taken from https://www.drupal.org/sandbox/dench0/2640652
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "views_access_multiple_permissions",
 *   title = @Translation("Multiple Permissions"),
 *   help = @Translation("Access will be granted to users with the specified permission strings.")
 * )
 */
class MultiplePermissions extends Permission {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    $access = FALSE; // flag for only one access rule enough
    $access_all = TRUE; // flag for all access rules needed
    foreach ($this->options['mult_perm'] as $permission) {
      if ($account->hasPermission($permission) && !$this->options['mult_perm_all']) {
        $access = TRUE;
        break;
      }
      if (!$account->hasPermission($permission) && $this->options['mult_perm_all']) {
        $access_all = FALSE;
        break;
      }
    }
    return ($this->options['mult_perm_all']) ? $access_all : $access;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRouteDefinition(Route $route) {
    $glue = ($this->options['mult_perm_all']) ? ',' : '+';
    $permission = implode($glue, $this->options['mult_perm']);
    $route->setRequirement('_permission', $permission);
  }

  public function summaryTitle() {
    return ($this->options['mult_perm_all']) ?
      $this->t('Need have all permissions') :
      $this->t('Enough only one permission');
  }


  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['mult_perm'] = ['default' => 'access content'];
    $options['mult_perm_all'] = ['default' => 1];

    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['mult_perm'] = $form['perm'];
    unset($form['perm']);

    $form['mult_perm']['#title'] = $this->t('Permission');
    $form['mult_perm']['#default_value'] = $this->options['mult_perm'];
    $form['mult_perm']['#description'] = $this->t('Only users with the selected permission flags will be able to access this display.');
    $form['mult_perm']['#multiple'] = TRUE;
    $form['mult_perm']['#size'] = 15;

    $form['mult_perm_all'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('All permissions'),
      '#default_value' => $this->options['mult_perm_all'],
      '#description' => $this->t('If checked, then only users with all the selected permission flags will be able to access this display. Otherwise users with at least one selected permission flag will be able to access this display'),
    ];
  }

}

