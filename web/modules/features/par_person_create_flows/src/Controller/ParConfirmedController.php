<?php

namespace Drupal\par_person_create_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_person_create_flows\ParPartnershipFlowsTrait;

/**
 * A controller for displaying the application confirmation.
 */
class ParConfirmedController extends ParBaseController {

  protected $pageTitle = 'Your new person has been created';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    $cid_review = $this->getFlowNegotiator()->getFormKey('review');
    $par_data_person_id = $this->getFlowDataHandler()->getTempDataValue('par_data_person_id', $cid_review);
    $par_data_person = $par_data_person_id ? ParDataPerson::load($par_data_person_id) : NULL;
    if ($par_data_person) {
      $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
    }

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

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    // Data must be deleted after the form has been built.
    $build = parent::build($build);
    $this->getFlowDataHandler()->deleteStore();
    // These pages can't be cached.
    $this->killSwitch->trigger();

    return $build;
  }

}
