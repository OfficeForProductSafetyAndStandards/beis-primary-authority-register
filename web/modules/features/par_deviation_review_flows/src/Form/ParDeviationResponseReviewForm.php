<?php

namespace Drupal\par_deviation_review_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_deviation_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for reviewing a deviation request.
 */
class ParDeviationResponseReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = "Review response";

  /**
   * Load the data for this.
   */
  public function loadData() {
    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataDeviationRequest $par_data_deviation_request */

    if ($par_data_deviation_request && $par_data_partnership = $par_data_deviation_request->getPartnership(TRUE)) {
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    }

    parent::loadData();
  }

  /**
   * Implements createEntities().
   */
  public function createEntities() {
    $par_data_deviation_request = $this->getFlowDataHandler()->getParameter('par_data_deviation_request');

    // Get the cache IDs for the various forms that needs to be extracted from.
    $deviation_review_cid = $this->getFlowNegotiator()->getFormKey('par_deviation_request_respond');

    $status = $this->getFlowDataHandler()->getTempDataValue('primary_authority_status', $deviation_review_cid);
    $notes = $this->getFlowDataHandler()->getTempDataValue('primary_authority_notes', $deviation_review_cid);

    switch ($status) {
      case ParDataDeviationRequest::APPROVED:
        $par_data_deviation_request->approve($notes, FALSE);
        break;

      case ParDataDeviationRequest::BLOCKED:
        $par_data_deviation_request->block($notes, FALSE);
        break;
    }

    return [
      'par_data_deviation_request' => $par_data_deviation_request,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // @todo Validate that any referred actions have a primary authority to refer to.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the correct values for the entities to be saved.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataDeviationRequest $par_data_deviation_request */

    // Save the deviation request.
    if ($par_data_deviation_request->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The deviation request %confirm could not be reviewed');
      $replacements = [
        '%confirm' => $par_data_deviation_request->id(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
