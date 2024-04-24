<?php

namespace Drupal\par_partnership_application_flows\Controller;

use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParPartnershipCompletedController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Partnership application completed';

  /**
   * {@inheritdoc}
   */
  public function content() {
    // Get the appropriate form data.
    $cid_contact = $this->getFlowNegotiator()->getFormKey('organisation_contact');
    $recipient_email = $this->getFlowDataHandler()->getDefaultValues('email', '', $cid_contact);

    $build['next_steps'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What happens next?'),
      '#title_tag' => 'h2',
    ];
    $build['next_steps']['notified'] = [
      '#type' => 'markup',
      '#markup' => $this->t('An email has been sent to %email.', ['%email' => $recipient_email]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['info'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Once they have completed their details the partnership will be eligible for nomination'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['chase'] = [
      '#type' => 'markup',
      '#markup' => $this->t("If you don't receive a notification that the organisation has completed the partnership information please get in contact with them."),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Clear the data once this form is built.
    $this->getFlowDataHandler()->deleteStore();
    // These pages can't be cached.
    $this->killSwitch->trigger();

    // Change the action to done.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
