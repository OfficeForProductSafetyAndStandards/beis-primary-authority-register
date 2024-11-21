<?php

namespace Drupal\par_govuk_cookies\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\par_govuk_cookies\Form\CookieConsentForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Cookie Consent' form block.
 *
 * @Block(
 *   id = "cookie_policy",
 *   admin_label = @Translation("Cookie Consent"),
 *   category = @Translation("Forms"),
 * )
 */
class CookieConsentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * CookieConsentBlock Constructor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected FormBuilderInterface $formBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->formBuilder->getForm(CookieConsentForm::class);
  }
}
