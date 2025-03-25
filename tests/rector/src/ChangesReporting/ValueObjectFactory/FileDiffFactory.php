<?php

// phpcs:ignoreFile

declare (strict_types=1);
namespace Par\Rector\ChangesReporting\ValueObjectFactory;

use Par\Rector\Console\Formatter\ConsoleDiffer;

use Rector\ChangesReporting\ValueObject\RectorWithLineChange;
use Rector\Differ\DefaultDiffer;
use Rector\FileSystem\FilePathHelper;
use Rector\ValueObject\Application\File;
use Rector\ValueObject\Reporting\FileDiff;
final class FileDiffFactory
{
    /**
     * @readonly
     * @var \Rector\Differ\DefaultDiffer
     */
    private $defaultDiffer;
    /**
     * @readonly
   * @var \Par\Rector\Console\Formatter\ConsoleDiffer
     */
    private $consoleDiffer;
    /**
     * @readonly
     * @var \Rector\FileSystem\FilePathHelper
     */
    private $filePathHelper;
    public function __construct(DefaultDiffer $defaultDiffer, ConsoleDiffer $consoleDiffer, FilePathHelper $filePathHelper)
    {
        $this->defaultDiffer = $defaultDiffer;
        $this->consoleDiffer = $consoleDiffer;
        $this->filePathHelper = $filePathHelper;
    }
    /**
     * @param RectorWithLineChange[] $rectorsWithLineChanges
     */
    public function createFileDiffWithLineChanges(File $file, string $oldContent, string $newContent, array $rectorsWithLineChanges) : FileDiff
    {
        $relativeFilePath = $this->filePathHelper->relativePath($file->getFilePath());

    if (method_exists($this->consoleDiffer, 'setFile')) {
      // Inform the console differ of the file being processed.
      $this->consoleDiffer->setFile($relativeFilePath);
    }

        // always keep the most recent diff
        return new FileDiff($relativeFilePath, $this->defaultDiffer->diff($oldContent, $newContent), $this->consoleDiffer->diff($oldContent, $newContent), $rectorsWithLineChanges);
    }
    public function createTempFileDiff(File $file) : FileDiff
    {
        return $this->createFileDiffWithLineChanges($file, '', '', $file->getRectorWithLineChanges());
    }
}
