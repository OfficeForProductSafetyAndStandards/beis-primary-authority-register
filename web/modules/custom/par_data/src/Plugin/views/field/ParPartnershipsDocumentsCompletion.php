<?php

namespace Drupal\par_data\Plugin\views\field;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

use Drupal\Core\Form\FormStateInterface;

/**
 * Field handler to get the PAR Data status.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_partnership_revision_documents_completion_percentage")
 */
class ParPartnershipsDocumentsCompletion extends FieldPluginBase {

  /**
   *
   * @{inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    // Cannot be injected as a dependency.
    // @see https://drupal.stackexchange.com/questions/224247/how-do-i-inject-a-dependency-into-a-fieldtype-plugin#comment273484_224248
    $this->parDataManager = \Drupal::service('par_data.manager');
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

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   *
   * @param \Drupal\views\ResultRow $values
   *
   * @return string $documentation_completion
   */
  #[\Override]
  public function render(ResultRow $values) {
    $entity = $values->_entity;

    if ($entity instanceof ParDataPartnership) {

      foreach ($entity->getAdvice() as $document) {
        $document_completion[] = $document->getCompletionPercentage();

        // @todo is this handy?
        // $this->parDataManager->addCacheableDependency($document);
      }

      $documentation_completion = !empty($document_completion) ? $this->parDataManager->calculateAverage($document_completion) : 0;

      return $documentation_completion . '%';

    }
  }

}
