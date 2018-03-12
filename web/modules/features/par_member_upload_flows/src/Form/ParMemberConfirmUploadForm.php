<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;

/**
 * The upload CSV confirmation form for importing partnerships.
 */
class ParMemberConfirmUploadForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Upload csv file confirmation message.
    $form['csv_upload_confirmation_message_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Are you sure you want to upload a new list of'
        . ' members?'),
      'intro' => [
        '#type' => 'markup',
        '#markup' => '<p><b>' . $this->t('This operation will erase any'
          . ' existing list of members. If you are unsure, please click the'
          . ' Cancel link (below) and contact the Help Desk.') . '</b></p>',
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Load csv data from temporary data storage and assign to a variable.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);
    dpm($csv_data);
  }

}
