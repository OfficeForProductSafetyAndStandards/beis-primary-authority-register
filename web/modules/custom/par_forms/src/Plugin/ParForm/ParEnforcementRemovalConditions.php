<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Removal conditions form plugin.
 *
 * @ParForm(
 *   id = "enforcement_removal_conditions",
 *   title = @Translation("The conditions under which an enforcement can be removed.")
 * )
 */
class ParEnforcementRemovalConditions extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['notifications'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => ['class' => ['form-group']],
      '#value' => "Enforcement notices can only be removed under specific circumstances and where their removal is agreed with the Secretary of State.",
    ];

    return $form;
  }
}
