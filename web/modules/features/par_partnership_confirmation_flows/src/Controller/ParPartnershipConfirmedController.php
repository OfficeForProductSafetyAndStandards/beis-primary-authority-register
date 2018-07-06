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
      '#markup' => "The partnership has been updated, this information will now be presented to the Office for Product Safety & Standards. Once this partnership has been reviewed the Office will either nominate this partnership or contact you with more information.",
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
