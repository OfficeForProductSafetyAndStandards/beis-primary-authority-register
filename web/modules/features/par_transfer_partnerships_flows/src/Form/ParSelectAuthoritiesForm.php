<?php

namespace Drupal\par_transfer_partnerships_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for selecting authorities.
 */
class ParSelectAuthoritiesForm extends ParBaseForm {

  protected $pageTitle = 'Which authority would you like to transfer to?';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData() {

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ?ParDataAuthority $par_data_authority = NULL) {
    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_authority);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state, ?ParDataAuthority $par_data_authority = NULL) {
    parent::validateForm($form, $form_state);

    // Validate that the new authority has the same regulatory functions as the
    // old authority.
    $from = $this->getFlowDataHandler()->getParameter('par_data_authority');
    $authority_id = $this->getFlowDataHandler()->getTempDataValue('authority_id');
    $to = $authority_id ? \Drupal::entityTypeManager()->getStorage('par_data_authority')
      ->load($authority_id) : NULL;

    if ($from instanceof ParDataAuthority && $to instanceof ParDataAuthority) {
      $old_functions = array_values($from->get('field_regulatory_function')->getValue());
      $new_functions = array_values($to->get('field_regulatory_function')->getValue());

      // Sort the array elements.
      sort($old_functions);
      sort($new_functions);

      if ($old_functions !== $new_functions) {
        $id_key = $this->getElementKey('authority');
        $message = $this->t("The regulatory functions do not match those offered by @old_authority.", ['@old_authority' => $from->label()])->render();
        $form_state->setErrorByName('authority', $this->wrapErrorMessage($message, $this->getElementId($id_key, $form)));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
