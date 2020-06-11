<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the about partnership details.
 */
class ParPartnershipFlowsAboutForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['about_partnership', 'par_data_partnership', 'about_partnership', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter some information about this partnership.'
    ]],
  ];

  protected $pageTitle = 'Information about the partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $this->pageTitle = 'Information about the new partnership';

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
      // PAR-1618 ensure that partnerships which have been set with the basic_html formatter via the admin
      // don't output HTML tags on edit forms. This is resulting in the tags being saved with content updates when users
      // edit the about text.
      $value = strip_tags($par_data_partnership->get('about_partnership')->getString());
      $value = str_replace(', basic_html', '', $value);
      $this->getFlowDataHandler()->setFormPermValue('about_partnership', $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // Business details.
    $form['about_partnership'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide information about the Partnership'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('about_partnership'),
      '#description' => '<p>Use this section to give a brief overview of the partnership. Include any information you feel may be useful to enforcing authorities.</p>',
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership) {
      // Save the value for the about_partnership field.
      $par_data_partnership->set('about_partnership', $this->getFlowDataHandler()->getTempDataValue('about_partnership'));
      if ($par_data_partnership->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('The %field field could not be saved for %form_id');
        $replacements = [
          '%field' => 'comments',
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
