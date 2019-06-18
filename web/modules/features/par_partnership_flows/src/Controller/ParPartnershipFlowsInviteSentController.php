<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParPartnershipFlowsInviteSentController extends ParBaseController {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Notification sent';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {

    $build['sent_to'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Further details sent to %email', ['%email' => $par_data_person->get('email')->getString()]),
      '#prefix' => '<p><strong>',
      '#suffix' => '</strong></p>',
    ];

    $build['help_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('%name will receive an email with a link to register/login to the PAR website.',
        ['%name' => $par_data_person->getFullName()]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
