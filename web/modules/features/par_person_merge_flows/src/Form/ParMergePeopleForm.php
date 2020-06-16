<?php

namespace Drupal\par_person_merge_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_person_merge_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * A controller for merging people.
 */
class ParMergePeopleForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Title callback default.
   */
  protected $pageTitle = "Choose which people to combine";

  public function loadData() {
    $person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $people = $person->getAllRelatedPeople();

    if ($people) {
      $this->getFlowDataHandler()->setParameter('contacts', $people);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPerson $par_data_person = NULL, User $user = NULL) {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }

    return parent::buildForm($form, $form_state);
  }

}
