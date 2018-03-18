<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;

/**
 * The upload CSV success page for importing partnerships.
 */
class ParMemberUploadSuccessForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Member list uploaded';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Upload csv file success message.
    $form['csv_upload_success_message_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What happens next?'),
      'intro' => [
        '#type' => 'markup',
        '#markup' => '<p>' . $this->t('Your member list has been uploaded.<br><br>Please check that all the new members are correct. Please try to re-upload the member list if you find any errors or contact the help desk for further assistance.') . '</p>',
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

}
