<?php

namespace Drupal\par_person_update_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_person_update_flows\ParPartnershipFlowsTrait;

/**
 * A controller for displaying the application confirmation.
 */
class ParConfirmedController extends ParBaseController {

  protected $pageTitle = 'Thank you for updating this person';

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
      '#markup' => "Your profile has been updated and any partnerships or notices of enforcement actions will now show these updated details. Other users may use these details to contact you about partnerships within PAR.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['login'] = [
      '#type' => 'markup',
      '#markup' => "If you have changed your e-mail address you will need to use the new address to log in to PAR in the future.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Display the help contact fo this partnership.
    $build['help_text'] = $this->renderSection('If you have any further questions about how your personal information is used within PAR you can contact the primary authority', $par_data_partnership, ['field_authority_person' => 'summary'], [], TRUE, TRUE);

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
