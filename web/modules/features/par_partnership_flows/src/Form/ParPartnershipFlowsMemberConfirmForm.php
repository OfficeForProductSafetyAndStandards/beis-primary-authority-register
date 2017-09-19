<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The advice document upload form.
 */
class ParPartnershipFlowsMemberConfirmForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_member_upload_confirm';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataAdvice $par_data_advice
   *   The advice being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if (isset($par_data_partnership)) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // Test
      $members = $this->getDefaultValues("coordinated_members");
      var_dump(count($members));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // List all the members.
    $form['members'] = [
      '#type' => 'fieldset',
      '#tree' => TRUE,
      '#description' => 'Below is a list of all the members that will be added to this partnership.',
    ];
    foreach ($this->getDefaultValues("coordinated_members") as $i => $member) {
      $form['members'][$i] = [
        '#type' => 'markup',
        '#description' => 'Member: ' . $member[1],
      ];
    }

    $form['next'] = [
      '#type' => 'submit',
      '#name' => 'next',
      '#value' => t('Upload'),
    ];

    // Go back to Advice Documents list.
    $previous_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the advice entity from the URL.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    if ($csv = $this->getTempDataValue('coordinated_members')) {
      $rows = [];

      // Load the submitted files and process the data.
      $files = File::loadMultiple($csv);
      foreach ($files as $file) {
        $rows = $this->getParDataManager()->processCsvFile($file, $rows);
      }

      // Save the data in the User's temp private store for later processing.
      if (!empty($rows)) {
        $this->setTempDataValue('coordinated_members', $rows);
      }
    }
  }

}
