<?php

/**
 * @file
 * Definition of Drupal\par_data\Plugin\views\field\ParPartnershipsCombinedStatusField
 */

namespace Drupal\par_data\Plugin\views\field;

use Drupal\Component\Utility\Html;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

use Drupal\Core\Form\FormStateInterface;

/**
 * Field handler to get the PAR Partnership Combined Status Fields.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_flow_link")
 */
class ParFlowLink extends FieldPluginBase {

  /*
   * @{inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @{inheritdoc}
   */
  #[\Override]
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['title'] = ['default' => []];
    $options['assistive_text'] = ['default' => []];
    $options['link'] = ['default' => []];
    $options['hidden'] = ['default' => []];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['title'] = [
      '#title' => 'Title',
      '#description' => 'Select the title for this link.',
      '#type' => 'textfield',
      '#default_value' => $this->options['title']  ?: '',
    ];

    $form['assistive_text'] = [
      '#title' => 'Assistive text',
      '#description' => 'Enter some descriptive text for screenreaders (this will be hidden).',
      '#type' => 'textfield',
      '#default_value' => $this->options['assistive_text']  ?: '',
    ];

    $form['link'] = [
      '#title' => 'Link',
      '#description' => 'Select the link that you wish to display.',
      '#type' => 'textfield',
      '#default_value' => $this->options['link']  ?: '',
    ];

    $form['hidden'] = [
      '#title' => 'Hidden',
      '#description' => 'Whether to hide if the link is not accessible or does not exist.',
      '#type' => 'checkbox',
      '#default_value' => $this->options['hidden']  ?: '',
    ];
  }

  /**
   * @{inheritdoc}
   *
   * @param ResultRow $values
   *
   * @return string $documentation_completion
   */
  #[\Override]
  public function render(ResultRow $values) {
    $tokens = $this->getRenderTokens([]);

    $path = strip_tags(Html::decodeEntities($this->viewsTokenReplace($this->options['link'] ?: '', $tokens)));
    $title = strip_tags(Html::decodeEntities($this->viewsTokenReplace($this->options['title'] ?: '', $tokens)));
    $assistive_text = strip_tags(Html::decodeEntities($this->viewsTokenReplace($this->options['assistive_text'] ?: '', $tokens)));

    $text = !empty(trim($title)) ? t($title) : 'Continue';
    $options = !empty($assistive_text) ? ['attributes' => ['aria-label' => $assistive_text]] : [];
    $url = !empty($path) ? Url::fromUserInput($path, $options) : NULL;

    $link = $url ? Link::fromTextAndUrl($text, $url)->toRenderable() : NULL;

    // Hide the link
    $text = empty($this->options['hidden']) ? $text : '';

    return !empty($link) && $url->access() && $url->isRouted() ? \Drupal::service('renderer')->render($link) : $text;
  }
}
