<?php

namespace Drupal\par_member_list_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Update the list type.
 */
class ParUpdateListTypeForm extends ParBaseForm {

  protected $pageTitle = 'How would you like to display the member list?';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()
      ->getParameter('par_data_partnership');

    parent::loadData();
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $list_type = $this->getFlowDataHandler()->getTempDataValue('type');
    switch ($list_type) {
      case ParDataPartnership::MEMBER_DISPLAY_INTERNAL:
        $confirm_route = $this->getFlowNegotiator()->getFlow()->progress('internal');
        $form_state->setRedirectUrl($confirm_route);

        break;

      case ParDataPartnership::MEMBER_DISPLAY_REQUEST:
        $confirm_route = $this->getFlowNegotiator()->getFlow()->progress('request');
        $form_state->setRedirectUrl($confirm_route);

        break;

      case ParDataPartnership::MEMBER_DISPLAY_EXTERNAL:
        $confirm_route = $this->getFlowNegotiator()->getFlow()->progress('external');
        $form_state->setRedirectUrl($confirm_route);

        break;
    }
  }

}
