<?php

declare (strict_types=1);

namespace Par\Rector\Console\Formatter;

use Rector\Console\Formatter\ColorConsoleDiffFormatter;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;

/**
 * Console Differ Class.
 *
 * Produces diff output for the console.
 */
final class ConsoleDiffer {
  /**
   * Placeholder for the original file name in the diff output.
   *
   * @var string
   */
  const A_FILE = '[[[####A_FILE####]]]';

  /**
   * Placeholder for the changed file name in the diff output.
   *
   * @var string
   */
  const B_FILE = '[[[####B_FILE####]]]';

  /**
   * Service for producing coloured diff output for the console.
   *
   * @var \Rector\Console\Formatter\ColorConsoleDiffFormatter
   * @readonly
   */
  private $colorConsoleDiffFormatter;

  /**
   * Service to provide diff information.
   *
   * @var \SebastianBergmann\Diff\Differ
   * @readonly
   */
  private $differ;

  /**
   * Name of the file.
   *
   * @var string
   */
  private $file;

  /**
   * Class constructor.
   *
   * @param \Rector\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter
   *   Formatter service.
   */
  public function __construct(ColorConsoleDiffFormatter $colorConsoleDiffFormatter) {
    $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    // @see https://github.com/sebastianbergmann/diff#strictunifieddiffoutputbuilder
    // @see https://github.com/sebastianbergmann/diff/compare/4.0.4...5.0.0#diff-251edf56a6344c03fa264a4926b06c2cee43c25f66192d5f39ebee912b7442dc for upgrade
    $builder = new StrictUnifiedDiffOutputBuilder([
      // Ranges of length one are rendered with the trailing `,1` .
      'collapseRanges'      => TRUE,
      // Number of same lines before ending a new hunk and creating a new one (if needed) .
      'commonLineThreshold' => 6,
      // Like `diff:  -u, -U NUM, --unified[=NUM]`, for patch/git apply compatibility best to keep at least @ 3 .
      'contextLines'        => 3,
      // The file name is not available, hence use a fixed string so that it can be replaced later.
      'fromFile'            => self::A_FILE,
      'fromFileDate'        => NULL,
      // The file name is not available, hence use a fixed string so that it can be replaced later.
      'toFile'              => self::B_FILE,
      'toFileDate'          => NULL,
    ]);
    $this->differ = new Differ($builder);
    $this->setFile('unknown');
  }

  /**
   * Take the old and new versions and return the formatted output.
   *
   * @param string $old
   *   Old version of the code.
   * @param string $new
   *   New version of the code.
   *
   * @return string
   *   Formatted version of the differences.
   */
  public function diff(string $old, string $new) : string {
    $diff = $this->differ->diff($old, $new);
    $diff = str_replace([self::A_FILE, self::B_FILE], ['a/' . $this->file, 'b/' . $this->file], $diff);
    return $this->colorConsoleDiffFormatter->format($diff);
  }

  /**
   * Set the file name.
   *
   * @param string $file
   *   Name of the file.
   */
  public function setFile(string $file) {
    $this->file = $file;
  }

}
