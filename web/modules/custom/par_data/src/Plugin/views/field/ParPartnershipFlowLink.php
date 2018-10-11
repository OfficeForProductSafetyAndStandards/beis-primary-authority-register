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
 * Field handler to get the PAR Partnership journey links.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_partnership_flow_link")
 */
class ParPartnershipFlowLink extends FieldPluginBase {

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
      '#description' => 'Enter the required title for this link.',
      '#type' => 'textfield',
      '#default_value' => $this->options['title']  ?: '',
    ];

    $form['hidden'] = [
      '#title' => 'Hidden',
      '#description' => 'Whether to hide if the link is not accessible or does not exist.',
      '#type' => 'checkbox',
      '#default_value' => $this->options['hidden']  ?: '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $entity = $values->_entity;

    // This view plugin only works with par entities of type par_data_partnership.
    if ($entity instanceof ParDataEntityInterface && $entity->getEntityTypeId() === 'par_data_partnership') {

      if ($entity->inProgress()) {
        $partnership_journey_path =  "/partnership/{{id}}/organisation-details";
      }
      else {
        $partnership_journey_path =  "/partnership/confirm/{{id}}/checklist";
      }

      $tokens = $this->getRenderTokens([]);

      $path = strip_tags(Html::decodeEntities($this->viewsTokenReplace($partnership_journey_path, $tokens)));
      $title = strip_tags(Html::decodeEntities($this->viewsTokenReplace($this->options['title'], $tokens)));

      $text = !empty($title) ? t($title) : 'Continue';
      $url = !empty($path) ? Url::fromUserInput($path) : NULL;
      $link = $url ? Link::fromTextAndUrl($text, $url)->toRenderable() : NULL;

      // Hide the link
      $text = empty($this->options['hidden']) ? $text : '';

      return !empty($link) && $url->access() && $url->isRouted()  ? render($link) : $text;
    }

    return '';
  }
}
