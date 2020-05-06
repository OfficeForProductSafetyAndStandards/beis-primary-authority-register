<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\MatchingRouteNotFoundException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($par_data_partnership) {
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $this->pageTitle = $par_data_organisation->get('organisation_name')->getString();
    }

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      $checkbox = $this->getInformationCheckbox($par_data_partnership);
      $this->getFlowDataHandler()->setFormPermValue($checkbox, $par_data_partnership->getBoolean($checkbox));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // Display all the information that can be modified by the organisation.
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);

    // Only show SIC Codes and Employee number if the partnership is a direct
    // partnership.
//    if ($par_data_partnership->isDirect()) {
//      // Add the SIC Codes with the relevant operational links.
//      $form['sic_codes'] = $this->renderSection('Standard industrial classification (SIC) codes', $par_data_organisation, ['field_sic_code' => 'full'], ['edit-field', 'add']);
//
//      // Add the number of employees with a link to edit the field.
//      $form['employee_no'] = $this->renderSection('Number of Employees', $par_data_organisation, ['employees_band' => 'full'], ['edit-field']);
//    }

    // Display all the legal entities along with the links for the allowed
    // operations on these.
    $operations = [];
    $checkbox = $this->getInformationCheckbox();

    // Helptext.
    $form['help_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Updating this information may change who recieves notifications for this partnership. Please check everything is correct.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Helper function to get the information checkbox required. False if none required.
   */
  public function getInformationCheckbox() {
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
      return 'partnership_info_agreed_authority';
    }
    else {
      return 'partnership_info_agreed_business';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $checkbox = $this->getInformationCheckbox();
    if ($par_data_partnership && !$par_data_partnership->getBoolean($checkbox)) {

      // Save the value for the confirmation field.
      if ($checkbox) {
        $par_data_partnership->set($checkbox, $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue($checkbox)));

        // Set partnership status.
        $status = ($checkbox === 'partnership_info_agreed_authority') ? 'confirmed_authority' : 'confirmed_business';
        try {
          $par_data_partnership->setParStatus($status);
        }
        catch (ParDataException $e) {
          // If the status could not be updated we want to log this but contintue.
          $message = $this->t("This status could not be updated to '%status' for the %label");
          $replacements = [
            '%label' => $par_data_partnership->label(),
            '%status' => $status,
          ];
          $this->getLogger($this->getLoggerChannel())
            ->error($message, $replacements);
        }
      }

      if ($checkbox && $par_data_partnership->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %confirm could not be saved for %form_id');
        $replacements = [
          '%confirm' => $par_data_partnership->get('partnership_info_agreed_authority')->toString(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
