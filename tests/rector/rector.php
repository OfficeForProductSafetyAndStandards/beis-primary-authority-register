<?php

/**
 * @file
 * Rector configuration file.
 */

declare(strict_types=1);

use DrupalFinder\DrupalFinderComposerRuntime;
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal8SetList;
use DrupalRector\Set\Drupal9SetList;
use Par\Rector\Application\ApplicationFileProcessor;
use Par\Rector\Application\FileProcessor;
use Par\Rector\ChangesReporting\ValueObjectFactory\FileDiffFactory;
use Par\Rector\Console\Command\ProcessCommand;
use Par\Rector\Console\Command\WorkerCommand;
use Par\Rector\Console\Formatter\ConsoleDiffer;
use Rector\Application\ApplicationFileProcessor as RectorApplicationFileProcessor;
use Rector\Application\FileProcessor as RectorFileProcessor;
use Rector\ChangesReporting\ValueObjectFactory\FileDiffFactory as RectorFileDiffFactory;
use Rector\Config\RectorConfig;
use Rector\Console\Command\ProcessCommand as RectorProcessCommand;
use Rector\Console\Command\WorkerCommand as RectorWorkerCommand;
use Rector\Console\Formatter\ConsoleDiffer as RectorConsoleDiffer;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
  // Adjust the set lists to be more granular to your Drupal requirements.
  // @todo find out how to only load the relevant rector rules.
  //   Should we try and load \Drupal::VERSION and check?
  $rectorConfig->sets([
    // Check for all the Drupal 8 changes.
    Drupal8SetList::DRUPAL_8,
    // Check for all the Drupal 9 changes.
    Drupal9SetList::DRUPAL_9,
    // Check for all the Drupal 10 changes.
    Drupal10SetList::DRUPAL_10,
  ]);

  // Define sets of rules.
  $rectorConfig->sets([
    LevelSetList::UP_TO_PHP_83,
  ]);

  $drupalFinder = new DrupalFinderComposerRuntime();
  $drupalRoot = $drupalFinder->getDrupalRoot();
  $rectorConfig->autoloadPaths([
    $drupalRoot . '/core',
    $drupalRoot . '/modules',
    $drupalRoot . '/profiles',
    $drupalRoot . '/themes',
  ]);

  $rectorConfig->skip([
    '*/upgrade_status/tests/modules/*',
    // Do not check files brought in by npm.
    '*/node_modules/*',
  ]);

  $rectorConfig->fileExtensions([
    'engine',
    'inc',
    'install',
    'module',
    'php',
    'profile',
    'theme',
  ]);

  // See https://getrector.com/documentation/troubleshooting-parallel for more information.
  // Also https://github.com/rectorphp/rector/discussions/7260 to disable it.
  $rectorConfig->parallel(240, 8, 20);

  $rectorConfig->importNames(TRUE, FALSE);
  $rectorConfig->importShortClasses(FALSE);

  // The following statements enable line numbers in the patch file.
  // Fully customized.
  $rectorConfig->singleton(ConsoleDiffer::class);
  $rectorConfig->alias(ConsoleDiffer::class, RectorConsoleDiffer::class);

  // Patched.
  $rectorConfig->singleton(FileDiffFactory::class);
  $rectorConfig->alias(FileDiffFactory::class, RectorFileDiffFactory::class);

  // Copies with changed namespace and custom classes to support the line numbers in the patch file.
  $rectorConfig->singleton(ApplicationFileProcessor::class);
  $rectorConfig->alias(ApplicationFileProcessor::class, RectorApplicationFileProcessor::class);

  $rectorConfig->singleton(FileProcessor::class);
  $rectorConfig->alias(FileProcessor::class, RectorFileProcessor::class);

  $rectorConfig->singleton(ProcessCommand::class);
  $rectorConfig->alias(ProcessCommand::class, RectorProcessCommand::class);

  $rectorConfig->singleton(WorkerCommand::class);
  $rectorConfig->alias(WorkerCommand::class, RectorWorkerCommand::class);
};
