<?php

namespace Drupal\par_member_cease_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirming the user is authorised to revoke partnerships.
 */
class ParMemberCeaseConfirmForm extends ParBaseForm {

  /**
   * Sets the page title.
   *
   * @var pageTitle
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
      '#title' => $this->t('What happens next?'),
      '#type' => 'fieldset',
    ];
    $form['next_steps']['info'] = [
      '#type' => 'markup',
      '#markup' => "If you want to remove all information about {$par_data_coordinated_business->label()} from the Primary Authority Register please contact the helpdesk.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Cease');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');

    // We only want to cease members that are currently active.
    if (!$par_data_coordinated_business->isRevoked()) {
      $cid = $this->getFlowNegotiator()->getFormKey('cease_date');
      $date_value = $this->getFlowDataHandler()->getTempDataValue('date_membership_ceased', $cid);

      // The format submitted by this form is expected to be in the format 2019-01-29.
      $date_fragments = explode('-', $date_value);
      $format = strlen($date_fragments[0]) === 2 ? "y-m-d" : "Y-m-d";
      $cease_date = DrupalDateTime::createFromFormat($format, $date_value, NULL, ['validate_format' => FALSE]);

      $par_data_coordinated_business->cease($cease_date);

      $this->getFlowDataHandler()->deleteStore();

    }
  }

}
