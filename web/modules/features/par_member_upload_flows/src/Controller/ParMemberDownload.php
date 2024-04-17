<?php

namespace Drupal\par_member_upload_flows\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\par_data\Entity\ParDataPartnership;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberDownload extends ControllerBase {

  /**
   * The response cache kill switch.
   */
  protected $killSwitch;

  /**
   * Constructs a ParMemberDownload page.
   *
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The page cache kill switch.
   */
  public function __construct(KillSwitch $kill_switch) {
    $this->killSwitch = $kill_switch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('page_cache_kill_switch'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    // This page should not be cacheable.
    $this->killSwitch->trigger();

    $name = strtolower('member_list_for_' . $par_data_partnership->label());
    $name = preg_replace('/[^a-z0-9_]+/', '_', $name);
    $document_name = preg_replace('/_+/', '_', $name);

    if ($file = $this->getCsvHandler()->download($par_data_partnership)) {
      // Make this file response un-cacheable.
      $response = new BinaryFileResponse($file->getFileUri());
      $response->setMaxAge(0)
        ->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        "$document_name.csv"
      );
      return $response;
    }

    return [
      '#cache' => ['max-age' => 0],
      '#markup' => '<p>This file could not be downloaded, please contact the helpdesk if this issue persists.',
    ];
  }

  /**
   * @return \Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

}
