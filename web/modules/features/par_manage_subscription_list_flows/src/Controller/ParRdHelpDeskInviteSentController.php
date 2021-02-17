<?php

namespace Drupal\par_rd_help_desk_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParRdHelpDeskInviteSentController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'invite_authority_members';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {

    $build['sent_to'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Your invitation has been sent to:<br> %name', ['%name' => $par_data_person->get('email')->getString()]),
      '#prefix' => '<p><strong>',
      '#suffix' => '</strong></p>',
    ];

    $build['call_person'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Why don’t you give %name a call to confirm they have received the invitation and see if they have any questions?', ['%name' => $par_data_person->get('first_name')->getString()]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $build['telephone'] = [
      '#type' => 'markup',
      '#markup' => $par_data_person->get('work_phone')->getString(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
