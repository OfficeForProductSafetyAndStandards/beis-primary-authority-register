<?php

namespace Drupal\par_notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\par_notification\Form\ParInvitationForm;
use Drupal\user\Form\UserLoginForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for handling link redirection requests when the user is not signed in.
 */
class ParAccessDeniedController extends ControllerBase {

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Get form builder service.
   *
   * @return \Drupal\Core\Form\FormBuilderInterface
   */
  public function getFormBuilder() {
    return \Drupal::formBuilder();
  }

  /**
   * Process a received request with help from the link manager.
   */
  public function build(Request $request) {
    $build = [];

    $message = \Drupal::routeMatch()->getParameter('message');

    // Redirect back to the message link if a user account is detected.
    // This could happen if the user account is authenticated in a separate tab.
    $account = $this->currentUser();
    if ($account->isAuthenticated()) {
      $message_link = Url::fromRoute('par_notification.link_manager', ['message' => $message->id()]);
      return new RedirectResponse($message_link->toString());
    }

    // Determine whether to allow invitations.
    $allow_invitations = ($message->hasField('field_to')
      && !$message->get('field_to')->isEmpty()
      && !user_load_by_mail($message->get('field_to')->getString()));

    // Column size is determined by whether invitations should be shown.
    $column_size = $allow_invitations ? 'govuk-grid-column-one-half' : 'govuk-grid-column-full';

    $build['account'] = [
      '#type'   => 'container',
      '#attributes' => ['class' => ['govuk-grid-row']],
    ];

    // Show sign in form.
    $build['account']['signin'] = [
      '#type'   => 'container',
      '#weight' => 1,
      '#attributes' => ['class' => [$column_size]],
    ];
    $build['account']['signin']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#attributes' => ['class' => ['govuk-heading-m']],
      '#value' => $this->t('Please sign in'),
    ];
    $build['account']['signin']['form'] = $this->getFormBuilder()->getForm(UserLoginForm::class);

    // Show invitation form.
    if ($allow_invitations) {
      $build['account']['invitation'] = [
        '#type'   => 'container',
        '#weight' => 2,
        '#attributes' => ['class' => [$column_size]],
      ];
      $build['account']['invitation']['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Or request an invitation'),
      ];
      $build['account']['invitation']['form'] = $this->getFormBuilder()->getForm(ParInvitationForm::class);
    }

    return $build;
  }

}
