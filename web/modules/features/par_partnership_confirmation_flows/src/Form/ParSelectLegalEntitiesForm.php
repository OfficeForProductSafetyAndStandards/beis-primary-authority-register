<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Organisation Legal Entities selection form.
 * This generates a list of legal entities stored on the PAR Organisation as
 * checkboxes.
 */
class ParSelectLegalEntitiesForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Choose the legal entities for the partnership';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Set the organisation to get legal entities from.
    $par_data_organisation = $par_data_partnership ? $par_data_partnership->getOrganisation(TRUE) : NULL;
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);

    // Set the legal entities to be used as options for this form.
    $par_data_legal_entities = $par_data_partnership ? $par_data_organisation->getLegalEntity() : [];
    $this->getFlowDataHandler()->setParameter('organisation_legal_entities', $par_data_legal_entities);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state) {
    // If there are no existing legal entities we can skip this step.
    if (!$this->getFlowDataHandler()->getParameter('organisation_legal_entities')) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    return parent::buildForm($form, $form_state);
  }

}
