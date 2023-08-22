<?php

namespace Drupal\par_partnership_amend_nominate_flows\Controller;

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
        '#value' => $this->t("The partnership amendment has been nominated."),
      ],
    ];

    $build['next'] = [
      '#title' => $this->t('What happens next?'),
      '#type' => 'fieldset',
    ];
    $build['next']['notification_organisation'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "The business and the authority will be notified that the partnership has been updated.",
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
