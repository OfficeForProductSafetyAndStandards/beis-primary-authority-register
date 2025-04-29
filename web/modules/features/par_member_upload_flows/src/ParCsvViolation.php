<?php

namespace Drupal\par_member_upload_flows;

/**
 * PAR csv violation.
 */
class ParCsvViolation {

  /**
   * @var int
   */
  protected int|null $line = null;

  /**
   * @var string
   */
  protected string|null $column = null;

  /**
   * @var string
   */
  protected string $message;

  /**
   * Constructor.
   *
   * @param int $line The line of the violation
   * @param string $column The column of the violation
   * @param string $message The error message
   */
  public function __construct(int|null $line, string|null $column, string $message = '', /**
   * Whether or not this violation should trigger an error.
   */
  protected bool $fatal = TRUE) {
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

  /**
   * @return bool
   */
  public function isFatal(): bool {
    return $this->fatal;
  }
}
