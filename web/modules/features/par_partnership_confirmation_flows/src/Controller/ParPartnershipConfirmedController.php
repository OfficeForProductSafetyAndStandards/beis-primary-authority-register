<?php

namespace Drupal\par_partnership_confirmation_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * A controller for displaying the application confirmation.
 */
class ParPartnershipConfirmedController extends ParBaseController {

  protected $pageTitle = 'New partnership application | Thank you for completing the application';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    // Information about the next steps.
    $build['next_steps'] = [
      '#title' => $this->t('What happens next?'),
      '#type' => 'fieldset',
    ];
    $build['next_steps']['info'] = [
      '#type' => 'markup',
      '#markup' => "Thank you for submitting your application to the Primary Authority Processing Team. We are currently undertaking mandatory checks and will be back in contact in due course.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['notice'] = [
      '#type' => 'markup',
      '#markup' => "A member of our team may make contact with you to confirm details about the application if necessary, please endeavour to return any information requested as quickly as possible to ensure your application is not delayed.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Display the help contact fo this partnership.
    $build['help_text'] = $this->renderSection('If you have any questions you can contact the primary authority', $par_data_partnership, ['field_authority_person' => 'summary'], [], TRUE, TRUE);

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
