<?php

namespace Drupal\par_data\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Provides a form for deleting par_data entities.
 *
 * @ingroup par_data
 */
class ParDataDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function getDeletionMessage() {
    /** @var \Drupal\par_data\Entity\ParDataEntityInterface $entity */
    $entity = $this->getEntity();

    if (!$entity->isDefaultTranslation()) {
      return $this->t('@language translation of the @type @bundle %label has been deleted.', [
        '@language' => $entity->language()->getName(),
        '@type' => $entity->getEntityType()->id(),
        '@bundle' => $entity->getType(),
        '%label' => $entity->label(),
      ]);
    }

    return $this->t('The @type @bundle %title has been deleted.', [
      '@type' => $entity->getEntityType()->id(),
      '@bundle' => $entity->getType(),
      '%title' => $entity->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function logDeletionMessage() {
    /** @var \Drupal\par_data\Entity\ParDataEntityInterface $entity */
    $entity = $this->getEntity();
    $this->logger('content')->notice('@type: deleted %title.', [
      '@type' => $entity->getType(),
      '%title' => $entity->label(),
    ]);
  }

}
