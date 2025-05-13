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
  #[\Override]
  public function titleCallback() {
    return "Help Desk | Partnership restored";
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ?ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_info'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('The following partnership has been restored to an active partnership'),
      ],
      '#attributes' => ['class' => 'govuk-form-group'],
    ];

    $form['partnership_info']['partnership_between'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

}
