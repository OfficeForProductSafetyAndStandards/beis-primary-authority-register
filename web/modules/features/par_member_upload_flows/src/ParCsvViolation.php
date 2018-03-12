<?php

namespace Drupal\par_member_upload_flows;

/**
 * PAR csv violation.
 */
class ParCsvViolation {

  /**
   * @var int
   */
  protected $line;

  /**
   * @var string
   */
  protected $column;

  /**
   * @var string
   */
  protected $message;

  /**
   * Constructor.
   *
   * @param int $line The line of the violation
   * @param string $column The column of the violation
   * @param string $message The error message
   */
  public function __construct($line, $column, string $message) {
    $this->setLine($line)
      ->setColumn($column)
      ->setMessage($message);
  }

  /**
   * @param int $line
   *
   * @return ParCsvViolation
   */
  public function setLine($line) {
    $this->line = (int) $line;

    return $this;
  }

  /**
   * @return int $line
   */
  public function getLine() {
    return $this->line;
  }

  /**
   * @param string $column
   *
   * @return ParCsvViolation
   */
  public function setColumn($column) {
    if ($column !== null) {
      $column = (string) $column;
    }

    $this->column = $column;

    return $this;
  }

  /**
   * @return string $column
   */
  public function getColumn() {
    return $this->column;
  }

  /**
   * @param string $message
   *
   * @return ParCsvViolation
   */
  public function setMessage(string $message) {
    $this->message = $message;

    return $this;
  }

  /**
   * @return string
   */
  public function getMessage() {
    return $this->message;
  }
}
