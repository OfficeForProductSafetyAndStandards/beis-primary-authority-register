<?php

namespace Drupal\par_member_cease_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_cease_flows\ParFlowAccessTrait;

/**
 * Enter the date the membership began.
 */
class ParMemberCeaseDateForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  public function titleCallback(ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL) {
    $member = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->pageTitle = "Cease membership for {$member->label()}";

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');

    // We only want to cease members that are currently active.
    if (!$par_data_coordinated_business->isRevoked()) {

      $ceased = $par_data_coordinated_business->cease($this->getFlowDataHandler()->getTempDataValue('date_membership_ceased'));

      if ($ceased) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('Cease date could not be saved for %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }

    }
  }

}
