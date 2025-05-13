<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "authority_select",
 *   title = @Translation("Authority selection.")
 * )
 */
class ParSelectAuthorityForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $authority_options = [];

    // Get the authorities that the current user belongs to.
    if ($this->getFlowDataHandler()->getCurrentUser()->isAuthenticated()) {
      $account = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
      $authorities = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_authority', TRUE);
      $authority_options = $this->getParDataManager()->getEntitiesAsOptions($authorities, []);
    }

    $this->getFlowDataHandler()->setFormPermValue('authorities', $authority_options);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $authorities = $this->getFlowDataHandler()->getFormPermValue('authorities');
    $required = $this->getFlowDataHandler()->getDefaultValues('authority_required', TRUE);

    // If no suggestions were found cancel out of the journey.
    if ($required && count($authorities) <= 0) {
      $url = $this->getFlowNegotiator()->getFlow()->progress('cancel');
      return new RedirectResponse($url->toString());
    }

    // If only one authority submit the form automatically and go to the next step.
    elseif ($required && count($authorities) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_authority_id', key($authorities));
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    if ($authorities) {
      // Initialize pager and get current page.
      $number_of_items = 10;
      $pager = $this->getUniquePager()->getPager('par_plugin_authority_select_' . $index);
      $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($authorities), $number_of_items, $pager);

      // Split the items up into chunks:
      $chunks = array_chunk($authorities, $number_of_items, TRUE);
      $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

      $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);
      $default_value = $this->getDefaultValuesByKey("par_data_authority_id", $index, NULL);
      $form['par_data_authority_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => t('Choose an Authority'),
        '#title_tag' => 'h2',
        '#options' => $authorities,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['govuk-form-group']],
      ];

      // @todo Add pager so that any selected checkboxes aren't unselected when a new page is loaded.
      //   $form['pager'] = [
      //        '#type' => 'pager',
      //        '#theme' => 'pagerer',
      //        '#element' => $index,
      //        '#config' => [
      //          'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
      //        ],
      //      ];
    }
    else {
      $form['intro'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-form-group']],
        '#value' => $this->t('There are no authorities to choose from.'),
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $required = $this->getFlowDataHandler()->getDefaultValues('authority_required', TRUE);

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $authority_element_key = $this->getElementKey('par_data_authority_id', $index);
    $authorities_selected = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE) ?
      NestedArray::filter((array) $form_state->getValue($authority_element_key)) :
      $form_state->getValue($authority_element_key);

    if ($required && empty($authorities_selected)) {
      $id_key = $this->getElementKey('par_data_authority_id', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($authority_element_key), $this->wrapErrorMessage('You must select an authority.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
