<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The member contact form.
 */
class ParEnforcementRaiseNoticeDetailsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership) {
      $cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_raise');
      $choosen_legal_entity = $this->getFlowDataHandler()->getDefaultValues('legal_entities_select', '', $cid);

      if ($choosen_legal_entity == 'add_new') {
        $enforced_entity_name = $this->getFlowDataHandler()->getDefaultValues('alternative_legal_entity', '', $cid);
      }
      else {
        $legal_entity = ParDataLegalEntity::load($choosen_legal_entity);
        $enforced_entity_name = $legal_entity ? $legal_entity->label() : '';
      }

      if (!empty($enforced_entity_name)) {
        $this->pageTitle = 'Proposed enforcement notification regarding | '. $enforced_entity_name;
      }
    }

    return parent::titleCallback();
  }

}
