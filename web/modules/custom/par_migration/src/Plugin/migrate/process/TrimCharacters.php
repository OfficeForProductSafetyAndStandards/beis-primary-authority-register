<?php

namespace Drupal\par_migration\Plugin\migrate\process;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Removes the first and last configured characters.
 *
 * @MigrateProcessPlugin(
 *   id = "trim_characters"
 * )
 */
class TrimCharacters extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_string($value)) {
      $characters = isset($this->configuration['characters']) ? $this->configuration['characters'] : " \t\n\r\0\x0B";
      return trim($value, $characters);
    }
    else {
      throw new MigrateException(sprintf('%s is not a string', var_export($value, TRUE)));
    }
  }
}
