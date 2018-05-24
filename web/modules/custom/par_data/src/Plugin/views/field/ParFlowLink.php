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
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['title'] = ['default' => []];
    $options['link'] = ['default' => []];
    $options['hidden'] = ['default' => []];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['title'] = [
      '#title' => 'Title',
      '#description' => 'Select the title for this link.',
      '#type' => 'textfield',
      '#default_value' => $this->options['title']  ?: '',
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
  public function render(ResultRow $values) {
    $entity = $values->_entity;

    if ($entity instanceof ParDataEntityInterface) {
      $tokens = $this->getRenderTokens([]);

      $path = strip_tags(Html::decodeEntities($this->viewsTokenReplace($this->options['link'], $tokens)));
      $title = strip_tags(Html::decodeEntities($this->viewsTokenReplace($this->options['title'], $tokens)));

      $text = !empty($title) ? t($title) : 'Continue';
      $url = !empty($path) ? Url::fromUserInput($path) : NULL;

      $link = $url ? Link::fromTextAndUrl($text, $url)->toRenderable() : NULL;

      // Hide the link
      $text = empty($this->options['hidden']) ? $text : '';

      return !empty($link) && $url->access() && $url->isRouted() ? render($link) : $text;
    }

    return '';
  }
}
