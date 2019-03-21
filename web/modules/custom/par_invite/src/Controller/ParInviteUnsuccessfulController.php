<?php

namespace Drupal\par_invite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\invite\InviteConstants;

/**
 * A controller for all styleguide page output.
 */
class ParInviteUnsuccessfulController extends ControllerBase {

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * The main index page for the styleguide.
   */
  public function content($invite = NULL) {

    $invite = \Drupal::routeMatch()->getParameter('invite');
    if ($invite) {
      $invite_email = $invite->get('field_invite_email_address')->getString();
      $people = $this->getParDataManager()->getEntitiesByProperty('par_data_person', 'email', $invite_email);

      switch ($invite->getStatus()) {
        case InviteConstants::INVITE_WITHDRAWN:

          $message = "We're sorry but your invitation has been withdrawn.";

          if (empty($people)) {
            $message .= "<br><br>The contact details have either been removed or the email address has been changed.";
          }

          $message .= "<br><br>Please contact {$invite->getOwner()->getEmail()} to request the invitation be re-sent.";

          break;

        case InviteConstants::INVITE_EXPIRED:

          $message = "We're sorry but your invitation link has expired.";
          $message .= "<br><br>Please contact {$invite->getOwner()->getEmail()} to request the invitation be re-sent.";

          break;

        case InviteConstants::INVITE_USED:

          $message = "We're sorry but your invitation link has already been used once.";
          $message .= "<br><br>Please contact {$invite->getOwner()->getEmail()} to request the invitation be re-sent.";

          break;
      }
    }

    $build['intro'] = [
      '#markup' => t("<p>$message</p>"),
    ];

    return $build;
  }

}
