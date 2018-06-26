<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Revoking a partnership.
 */
class ParRdHelpDeskUnrevokeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return "Help Desk | Partnership restored";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('The following partnership has been restored to an active partnership'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['partnership_info']['partnership_between'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
}
