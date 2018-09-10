<?php

namespace Drupal\par_enquiry_send_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_enquiry_send_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnquiryConfirmationForm extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Enquiry sent';

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL) {

    // Display the help contact fo this partnership.
    $build['help_text'] = $this->renderSection('If you have any questions you can contact', $par_data_partnership, ['field_authority_person' => 'summary'], [], TRUE, TRUE);

    // In order to redirect to a page outside this flow.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    $build = parent::build($build);

    $build['done']['#markup'] = t('@link', [
      '@link' => $this->getLinkByRoute('par_search_partnership_flows.partnership_page', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
        ->setText('Done')
        ->toString(),
    ]);

    return $build;

  }
}
