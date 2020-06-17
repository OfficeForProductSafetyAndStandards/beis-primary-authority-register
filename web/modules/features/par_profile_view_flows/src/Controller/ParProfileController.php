<?php

namespace Drupal\par_profile_view_flows\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\invite\InviteConstants;

/**
 * A controller for displaying the application confirmation.
 */
class ParProfileController extends ParBaseController {

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * @return DateFormatterInterface
   */
  protected function getEntityTypeManager() {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $name = $par_data_person ? $par_data_person->getFullName() : NULL;

    if (!empty($name)) {
      $this->pageTitle = ucfirst($name);
    }
    else {
      $this->pageTitle = 'Profile';
    }

    return parent::titleCallback();
  }

  public function loadData() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $user = $par_data_person ? $par_data_person->getUserAccount() : NULL;

    if ($user) {
      $this->getFlowDataHandler()->setParameter('user', $user);
    }

    if ($par_data_person) {
      // See if there are any outstanding invitations.
      $invitations = $this->getEntityTypeManager()
        ->getStorage('invite')
        ->loadByProperties([
          'field_invite_email_address' => $par_data_person->getEmail(),
          'status' => InviteConstants::INVITE_VALID
        ]);

      if (count($invitations) >= 1) {
        $invite = current($invitations);
        if ($invite->expires->value >= time()) {
          $date = $this->getDateFormatter()
            ->format($invite->expires->value, 'gds_date_format');

          $this->getFlowDataHandler()
            ->setFormPermValue('invitation_expiration', $date);
        }
      }
    }

    if ($par_data_person && $people = $par_data_person->getAllRelatedPeople()) {
      $this->getFlowDataHandler()->setParameter('contacts', $people);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'contact_locations_detail', $people);
    }
    else {
      $this->getFlowDataHandler()->setParameter('contacts', [$par_data_person]);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'contact_locations_detail', [$par_data_person]);
    }

    parent::loadData();
  }

  public function build($build = []) {
    // When new contacts are added these can't clear the cache,
    // for now we will keep this page uncached.
    $this->killSwitch->trigger();

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }
    if ($user = $this->getFlowDataHandler()->getParameter('user')) {
      $this->addCacheableDependency($user);
    }
    if ($contacts = $this->getFlowDataHandler()->getParameter('contacts')) {
      foreach ($contacts as $contact) {
        $this->addCacheableDependency($contact);
      }
    }

    // Enable the 'done' action instead of the default.
    $this->getFlowNegotiator()->getFlow()->enableAction('done');

    return parent::build($build);
  }

}
