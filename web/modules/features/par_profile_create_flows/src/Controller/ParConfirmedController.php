<?php

namespace Drupal\par_profile_create_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_profile_create_flows\ParPartnershipFlowsTrait;

/**
 * A controller for displaying the application confirmation.
 */
class ParConfirmedController extends ParBaseController {

  protected $pageTitle = 'You\'re new person has been created';

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
      '#markup' => "This person has now been created.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['login'] = [
      '#type' => 'markup',
      '#markup' => "If you have choosen to invite this person to the Primary Authority Register they will receive an email with instructions on how to create an account.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Display the help contact fo this partnership.
    $build['help_text'] = $this->renderSection('If you have any further questions about how personal information is used within PAR you can contact the primary authority', $par_data_partnership, ['field_authority_person' => 'summary'], [], TRUE, TRUE);

    return parent::build($build);
  }

}
