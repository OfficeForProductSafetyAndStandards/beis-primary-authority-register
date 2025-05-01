<?php

namespace Drupal\par_partnership_amend_confirm_flows\Controller;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;

/**
 * The controller for rendering journey completion.
 */
class ParCompleteController extends ParBaseController {

  protected $pageTitle = 'The amendments have been confirmed';

  /**
   * Load the data for this form.
   */
  public function content($build = []) {
    $build['intro'] = [
      '#type' => 'container',
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("The partnership amendment has been submitted to the business."),
      ],
    ];

    $build['next'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What happens next?'),
      '#title_tag' => 'h2',
    ];
    $build['next']['notification_organisation'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "The business will get a notification asking them to confirm the changes to this partnership.",
    ];
    $build['next']['notification_helpdesk'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "Once the business has confirmed these changes the Primary Authority Register team will review the changes and approve the new partnership details.",
    ];
    $build['next']['help'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "Please contact the Primary Authority Register team if you'd like to discuss this amendment.",
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
