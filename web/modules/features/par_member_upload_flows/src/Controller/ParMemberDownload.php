<?php

namespace Drupal\par_member_upload_flows\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberDownload extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    $name = strtolower('member_list_for_' . $par_data_partnership->label());
    $name = preg_replace('/[^a-z0-9_]+/', '_', $name);
    $document_name = preg_replace('/_+/', '_', $name);

    if ($file = $this->getCsvHandler()->download($par_data_partnership)) {
      $response = new BinaryFileResponse($file->getFileUri());
      $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        "$document_name.csv"
      );
      return $response;
    }

    return $build = [
      '#markup' => '<p>This file could not be downloaded, please contact the helpdesk if this issue persists.',
    ];
  }

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

}
