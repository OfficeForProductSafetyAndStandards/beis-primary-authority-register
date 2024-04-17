<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
      '#attributes' => ['class' => ['govuk-form-group']],
      '#value' => "Enforcement notices can only be removed under specific circumstances and where their removal is agreed with the Secretary of State.",
    ];

    return $form;
  }

}
