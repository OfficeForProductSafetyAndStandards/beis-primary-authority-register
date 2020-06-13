<?php

namespace Drupal\par_person_merge_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_person_merge_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * A controller for merging people.
 */
class ParMergeConfirmForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * Title callback default.
   */
  protected $pageTitle = "Merge contact records";

  public function loadData() {
    $cid = $this->getFlowNegotiator()->getFormKey('merge');
    $merge_ids = $this->getFlowDataHandler()->getDefaultValues("contacts", [], $cid);

    $merge_people = ParDataPerson::loadMultiple($merge_ids);
    $this->getFlowDataHandler()->setFormPermValue('number_contacts', count($merge_people));

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPerson $par_data_person = NULL, User $user = NULL) {
    // Add a message to explain the action being taken.
    $form['info'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => "You are about to merge {$this->getFlowDataHandler()->getDefaultValues('number_contacts', '0')} contacts, do you want to proceed?",
      '#attributes' => ['class' => 'form-group'],
    ];

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }

    // Change the primary call to action.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Merge');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    $cid = $this->getFlowNegotiator()->getFormKey('merge');
    $merge_ids = $this->getFlowDataHandler()->getDefaultValues("contacts", [], $cid);

    // If the $par_data_person is being merged this must be the primary record.
    // This is important because the $par_data_person is used as a route
    // parameter and should not be deleted.
    $primary_key = $par_data_person ? array_search($par_data_person->id(), $merge_ids, TRUE) : FALSE;
    if ($primary_key !== FALSE) {
      unset($merge_ids[$primary_key]);
      $person = $par_data_person;
      $merges = ParDataPerson::loadMultiple($merge_ids);
    }
    // Otherwise take the first entity to be merged as the primary record.
    else {
      $merges = ParDataPerson::loadMultiple($merge_ids);
      $person = array_shift($merges);
    }

    // There must be at least 1 additional entity or merging will not be possible.
    if ($merges && count($merges) >= 1) {
      foreach ($merges as $merge) {
        if ($person instanceof ParDataEntityInterface) {
          try {
            $person->merge($merge, FALSE);
          }
          catch (ParDataException $e) {
            $replacements = [
              '@merge' => $merge->id(),
              '@person' => $person->id(),
              '@reason' => $e->getMessage()
            ];
            $this->getLogger($this->getLoggerChannel())->error('Failed merging @merge into @person. The reason given was: @reason', $replacements);
          }
        }
      }

      // Save the primary contact record.
      if ($person->save()) {
        $this->getFlowDataHandler()->deleteStore();
        return;
      }
    }

    $message = $this->t('The contact ids could not be merged for %person.');
    $replacements = [
      '%person' => $par_data_person->id(),
    ];
    $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
  }

}
