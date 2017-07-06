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
 *   title = @Translation("GDS Paged output with result output"),
 *   short_title = @Translation("GDS Pager"),
 *   help = @Translation("Paged output with result count"),
 *   theme = "par_advanced_pager"
 * )
 */
class ParAdvancedPager extends SqlBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    // Use the same default quantity that core uses by default.
    $options['quantity'] = array('default' => 9);

    $options['tags']['contains']['first'] = array('default' => $this->t('« First'));
    $options['tags']['contains']['last'] = array('default' => $this->t('Last »'));

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['quantity'] = array(
      '#type' => 'number',
      '#title' => $this->t('Number of pager links visible'),
      '#description' => $this->t('Specify the number of links to pages to display in the pager.'),
      '#default_value' => $this->options['quantity'],
    );

    $form['tags']['first'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('First page link text'),
      '#default_value' => $this->options['tags']['first'],
      '#weight' => -10,
    );

    $form['tags']['last'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Last page link text'),
      '#default_value' => $this->options['tags']['last'],
      '#weight' => 10,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function summaryTitle() {
    if (!empty($this->options['offset'])) {
      return $this->formatPlural($this->options['items_per_page'], '@count item, skip @skip', 'Paged, @count items, skip @skip', array('@count' => $this->options['items_per_page'], '@skip' => $this->options['offset']));
    }
    return $this->formatPlural($this->options['items_per_page'], '@count item', 'Paged, @count items', array('@count' => $this->options['items_per_page']));
  }

  /**
   * {@inheritdoc}
   */
  public function render($input) {
    // The 0, 1, 3, 4 indexes are correct. See the template_preprocess_pager()
    // documentation.
    $tags = array(
      0 => $this->options['tags']['first'],
      1 => $this->options['tags']['previous'],
      3 => $this->options['tags']['next'],
      4 => $this->options['tags']['last'],
    );
    return array(
      '#theme' => $this->themeFunctions(),
      '#tags' => $tags,
      '#element' => $this->options['id'],
      '#parameters' => $input,
      '#quantity' => $this->options['quantity'],
      '#route_name' => !empty($this->view->live_preview) ? '<current>' : '<none>',
    );
  }

}
