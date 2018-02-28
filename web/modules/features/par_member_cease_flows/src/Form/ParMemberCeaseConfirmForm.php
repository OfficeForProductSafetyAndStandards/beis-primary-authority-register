<?php

namespace Drupal\par_member_cease_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_member_cease_flows\ParFlowAccessTrait;

/**
 * The confirming the user is authorised to revoke partnerships.
 */
class ParMemberCeaseConfirmForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = "Membership Ceased";

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL) {
    $form['membership_info'] = [
      '#type' => 'markup',
      '#markup' => "{$par_data_coordinated_business->label()}'s membership of {$par_data_partnership->label()} has been ceased and the 'membership until' date has been updated on the members list.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Enter the revokcation reason.
    $form['next_steps'] = [
      '#title' => $this->t('What happens next'),
      '#type' => 'fieldset',
    ];
    $form['next_steps']['info'] = [
      '#type' => 'markup',
      '#markup' => "If you want to remove all information about {$par_data_coordinated_business->label()} from the Primary Authority Register please contact the helpdesk.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

}
