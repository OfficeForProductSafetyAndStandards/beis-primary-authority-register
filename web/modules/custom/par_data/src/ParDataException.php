<?php

namespace Drupal\flysystem\Commands;

use Drupal\flysystem\FlysystemFactory;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\CommandFailedException;

/**
 * Flysystem drush commands.
 */
class FlysystemCommands extends DrushCommands {

  /**
   * The flysystem factory.
   *
   * @var \Drupal\flysystem\FlysystemFactory
   */
  protected $flysystemFactory;

  /**
   * FlysystemCommands constructor.
   *
   * @param \Drupal\flysystem\FlysystemFactory $flysystem_factory
   *   The flysystem factory.
   */
  public function __construct(FlysystemFactory $flysystem_factory) {
    parent::__construct();
    $this->flysystemFactory = $flysystem_factory;
  }

  /**
   * Put the resource to the flysystem file wrapper.
   *
   * @param $scheme
   *   Name of the filesystem scheme
   * @param $localPath
   *   Path to the file on the local file system, including filename
   * @param $remotePath
   *   Path to the file on the remote file system, including filename
   *
   * @usage flysystem:put scheme local-path remote-path
   *   Put the resource to the flysystem file wrapper.
   *
   * @command flysystem:put
   * @aliases fsp
   */
  public function put($scheme, $localPath, $remotePath) {
    $flysystem = $this->flysystemFactory->getFilesystem($scheme);

    $resource = fopen($localPath, 'r');
    $flysystem->putStream($remotePath, $resource);
    fclose($resource);

    $this->io()->writeln("$localPath written to $scheme:$remotePath");
  }

  /**
   * Get the resource from the flysystem file wrapper.
   *
   * @param $scheme
   *   Name of the filesystem scheme
   * @param $localPath
   *   Path to the file on the local file system, including filename
   * @param $remotePath
   *   Path to the file on the remote file system, including filename
   *
   * @usage flysystem:get scheme local-path remote-path
   *   Get the resource from the flysystem file wrapper.
   *
   * @command flysystem:get
   * @aliases fsg
   */
  public function get($scheme, $localPath, $remotePath) {
    $flysystem = $this->flysystemFactory->getFilesystem($scheme);

    $stream = $flysystem->readStream($remotePath);
    $contents = stream_get_contents($stream);
    fclose($stream);

    file_put_contents($localPath, $contents);

    $this->io()->writeln("$localPath retrieved from $scheme:$remotePath");
  }

}
