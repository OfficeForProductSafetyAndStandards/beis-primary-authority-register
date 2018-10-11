<?php

namespace Drupal\par_partnership_application_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParPartnershipCompletedController extends ParBaseController {

  protected $pageTitle = 'Partnership application completed';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {

    $build['next_steps'] = [
      '#title' => $this->t('What happens next?'),
      '#type' => 'fieldset',
    ];
    $build['next_steps']['notified'] = [
      '#type' => 'markup',
      '#markup' => $this->t('An email has been sent to %email.', ['%email' => $par_data_person->get('email')->getString()]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['info'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Once they have completed their details the partnership will be eligible for nomination', ['%email' => $par_data_person->get('email')->getString()]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['next_steps']['chase'] = [
      '#type' => 'markup',
      '#markup' => $this->t('If you don\'t receive a notification that the organisation has completed the partnership inormation please get in contact with them.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
