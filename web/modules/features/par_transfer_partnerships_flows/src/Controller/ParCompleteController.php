<?php

namespace Drupal\par_transfer_partnerships_flows\Controller;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * The controller for rendering journey completion.
 */
class ParCompleteController extends ParBaseController {

  protected $pageTitle = 'The transfer has been completed';

  /**
   * Load the data for this form.
   */
  public function content($build = []) {
    $build['intro'] = [
      '#type' => 'container',
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("The partnerships have been transferred to the new authority."),
      ],
    ];

    $build['next'] = [
      '#title' => $this->t('What happens next?'),
      '#type' => 'fieldset',
    ];
    $build['next']['info'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "The Primary Authority for these partnerships will now be changed to the new authority and the name of the old authority will be marked on the partnership along with the date of the transfer.",
    ];
    $build['next']['search'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "The partnership will now only show up in search results when the new authority is searched for.",
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('remove_reason')) {
      $id = $this->getElementId('remove_reason', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('Please enter the reason you are removing this inspection plan.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $delta = $this->getFlowDataHandler()->getTempDataValue('delta');
  }

}
