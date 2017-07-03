<?php

namespace Drupal\par_pagination\Plugin\views\pager;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\pager\SqlBase;

/**
 * The plugin to handle full pager.
 *
 * @ingroup views_pager_plugins
 *
 * @ViewsPager(
 *   id = "par_pagination_pager",
 *   title = @Translation("Paged output, full GDS styled pager"),
 *   short_title = @Translation("Full"),
 *   help = @Translation("Paged output, full GDS styled pager"),
 *   theme = "pager",
 *   register_theme = FALSE
 * )
 */
class ParPaginationPager extends SqlBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    // Use the same default quantity that core uses by default.
    $options['quantity'] = ['default' => 9];

    $options['tags']['contains']['first'] = ['default' => $this->t('« First')];
    $options['tags']['contains']['last'] = ['default' => $this->t('Last »')];

    unset($options['tags']['contains']);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of pager links visible'),
      '#description' => $this->t('Specify the number of links to pages to display in the pager.'),
      '#default_value' => $this->options['quantity'],
    ];

    $form['tags']['first'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First page link text'),
      '#default_value' => $this->options['tags']['first'],
      '#weight' => -10,
    ];

    $form['tags']['last'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last page link text'),
      '#default_value' => $this->options['tags']['last'],
      '#weight' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function summaryTitle() {
    if (!empty($this->options['offset'])) {
      return $this->formatPlural($this->options['items_per_page'], '@count item, skip @skip', 'Paged, @count items, skip @skip', ['@count' => $this->options['items_per_page'], '@skip' => $this->options['offset']]);
    }
    return $this->formatPlural($this->options['items_per_page'], '@count item', 'Paged, @count items', ['@count' => $this->options['items_per_page']]);
  }

  /**
   * {@inheritdoc}
   */
  public function render($input) {
    // The 0, 1, 3, 4 indexes are correct. See the template_preprocess_pager()
    // documentation.
    $tags = [
      0 => $this->options['tags']['first'],
      1 => $this->options['tags']['previous'],
      3 => $this->options['tags']['next'],
      4 => $this->options['tags']['last'],
    ];
    return [
      '#theme' => $this->themeFunctions(),
      '#tags' => $tags,
      '#element' => $this->options['id'],
      '#parameters' => $input,
      '#quantity' => $this->options['quantity'],
      '#route_name' => !empty($this->view->live_preview) ? '<current>' : '<none>',
    ];
  }

}
