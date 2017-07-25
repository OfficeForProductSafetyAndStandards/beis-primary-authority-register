<?php

namespace Drupal\par_ckeditor\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "parstylebuttons" plugin.
 *
 * @CKEditorPlugin(
 *   id = "parstylebuttons",
 *   label = @Translation("CKEditor Par Style Buttons")
 * )
 */
class ParStyleButtons extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'par_ckeditor') . '/js/plugins/parstylebuttons/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [
      'drupalLink_dialogTitleAdd' => $this->t('Add Link'),
      'drupalLink_dialogTitleEdit' => $this->t('Edit Link'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = drupal_get_path('module', 'par_ckeditor') . '/js/plugins/parstylebuttons';

    return [
      'Contact' => [
        'label' => $this->t('Contact'),
        'image' => $path . '/icons/contact.png',
      ],
      'InfoNotice' => [
        'label' => $this->t('Information notice'),
        'image' => $path . '/icons/infonotice.png',
      ],
      'HelpNotice' => [
        'label' => $this->t('Help notice'),
        'image' => $path . '/icons/helpnotice.png',
      ],
    ];
  }

}
