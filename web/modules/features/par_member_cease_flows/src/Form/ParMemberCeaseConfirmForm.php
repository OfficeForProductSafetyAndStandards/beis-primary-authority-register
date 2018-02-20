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

  use ParDisplayTrait;
  use ParFlowAccessTrait;

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
      '#markup' => "{$par_data_coordinated_business->label()}'s membership has been ceased from your partnership.",
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
      '#markup' => "{$par_data_coordinated_business->label()} will still be listed on your member list alongside the date their membership ceased. If you want to remove all information about {$par_data_coordinated_business->label()} from the Primary Authority Register please contact the helpdesk.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

}
