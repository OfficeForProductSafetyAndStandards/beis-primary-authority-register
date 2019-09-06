<?php

namespace Drupal\par_user_block_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_user_block_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * A controller for blocking user accounts.
 */
class ParBlockUserController extends ParBaseForm {

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
  protected $pageTitle = "Disable this user account";

  public function loadData() {
    $user = $this->getFlowDataHandler()->getParameter('user');
    if (!$user && $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $user = $par_data_person->getUserAccount();
      $this->getFlowDataHandler()->setParameter('user', $user);
    }

    if ($user) {
      $this->getFlowDataHandler()->setFormPermValue('email', $user->getEmail());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPerson $par_data_person = NULL, User $user = NULL) {
    // Add a message to explain the action being taken.
    $form['info'] = [
      '#type' => 'markup',
      '#markup' => "<p>You are about to <strong>block</strong> {$this->getFlowDataHandler()->getDefaultValues('email', 'this user')}. <br><br>They will no longer be able to access the Primary Authority Register, but their details will remain in the system as a record of the activities they carried out on behalf of their authorities or organisations.</p>"
    ];

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }
    if ($user = $this->getFlowDataHandler()->getParameter('user')) {
      $this->addCacheableDependency($user);
    }

    // Change the primary call to action.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Block user');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $user = $this->getFlowDataHandler()->getParameter('user');

    // Block this user.
    if ($user && $user->block() && $user->save()) {
      // Also invalidate the user account cache if there is one.
      if ($user) {
        \Drupal::entityTypeManager()->getStorage('user')->resetCache([$user->id()]);
      }

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('User account could not be blocked for: %account');
      $replacements = [
        '%account' => $user->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
