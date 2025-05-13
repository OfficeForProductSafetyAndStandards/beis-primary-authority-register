<?php

namespace Drupal\par_data\Plugin\views\area;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\area\AreaPluginBase;

/**
 * Views area par_flow_link handler.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("par_flow_link")
 */
class ParFlowLink extends AreaPluginBase {

  /**
   * Get the route provider service.
   *
   * @return \Drupal\Core\Routing\RouteProviderInterface
   */
  protected function getRouteProvier() {
    return \Drupal::service('router.route_provider');
  }

  /**
   * Get the current route.
   *
   * @return \Drupal\Core\Routing\RouteMatchInterface
   */
  protected function getRouteMatch() {
    return \Drupal::routeMatch();
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
    $options['class'] = ['default' => []];

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
      '#default_value' => $this->options['title'] ?: '',
    ];

    $form['assistive_text'] = [
      '#title' => 'Assistive text',
      '#description' => 'Enter some descriptive text for screenreaders (this will be hidden).',
      '#type' => 'textfield',
      '#default_value' => $this->options['assistive_text'] ?: '',
    ];

    $form['link'] = [
      '#title' => 'Link',
      '#description' => 'Select the link that you wish to display. You can use substitutions in the format `/route/%/pattern`, but they must be available in the current route.',
      '#type' => 'textfield',
      '#default_value' => $this->options['link'] ?: '',
    ];

    $form['class'] = [
      '#title' => 'Link classes',
      '#description' => 'Enter any additional classes to be added to the link, separated by a comma.',
      '#type' => 'textfield',
      '#default_value' => $this->options['class'] ?: '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validate() {
    $errors = parent::validate();

    if (empty($this->options['title']) || empty($this->options['link'])) {
      $errors[] = $this->t('The link must set a title and a url.', []);
      return $errors;
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function render($empty = FALSE) {
    $path = $this->options['link'] ?: '';
    $classes = explode(',', (string) ($this->options['class'] ?: 'flow-link'));
    $title = trim(strip_tags(Html::decodeEntities($this->options['title'] ?: '')));
    $assistive_text = strip_tags(Html::decodeEntities($this->options['assistive_text'] ?: ''));

    $attributes = ['class' => $classes];
    if (!empty($assistive_text)) {
      $attributes['aria-label'] = $assistive_text;
    }
    $options = ['attributes' => $attributes];

    // @todo There is an issue here when rendering the flow link in an
    // admin view which doesn't have any route parameters.
    //
    // Error: Call to a member function isCoordinated() on null in Drupal\par_member_add_flows\Form\ParOrganisationNameForm->accessCallback()
    // (line 37 of /var/www/par/web/modules/features/par_member_add_flows/src/ParFlowAccessTrait.php)
    //
    // Because this is an admin view and doesn't need to render the issue isn't too critical,
    // but we do need to consider how we get parameters into this par flow link,
    // we can't use views tokens because there might be no results in the view.
    //
    // Perhaps rather than passing all current route parameters to the Url::fromRoute
    // we could configure which parameters should be passed.
    // Get the route name from the path match.
    $route_matches = $this->getRouteProvier()->getRoutesByPattern($path);
    if ($route_matches->count() > 0) {
      // Route found.
      $route_name = current(array_keys($route_matches->all()));
      $route_params = $this->getRouteMatch()->getRawParameters()->all();
      $url = !empty($path) ? Url::fromRoute($route_name, $route_params, $options) : NULL;
    }

    $link = $url ? Link::fromTextAndUrl($title, $url)->toRenderable() : NULL;

    // Only display if there is a link.
    if (!empty($link) && $url->access() && $url->isRouted()) {
      $options = NestedArray::mergeDeep(['attributes' => ['class' => ['govuk-form-group']]], $options);
      return [
        '#type' => 'link',
        '#title' => $title,
        '#url' => $url,
        '#options' => $options,
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }
  }

}
