<?php

namespace Drupal\par_profile_view_flows\Controller;

use Drupal\invite\InviteConstants;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_forms\ParFormPluginInterface;

/**
 * A controller for displaying the application confirmation.
 */
class ParProfileController extends ParBaseController {

  /**
   * Get the DateFormatter service.
   *
   * @return \Drupal\Core\Datetime\DateFormatterInterface
   *   Returns the DateFormatter service.
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * Get the EntityTypeManager service.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   Returns the EntityTypeManager service.
   */
  protected function getEntityTypeManager() {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $name = $par_data_person?->getFullName();

    if (!empty($name)) {
      $this->pageTitle = ucfirst($name);
    }
    else {
      $this->pageTitle = 'Profile';
    }

    return parent::titleCallback();
  }

  /**
   * Load data.
   */
  public function loadData() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $user = $par_data_person?->lookupUserAccount();

    if ($user) {
      $this->getFlowDataHandler()->setParameter('user', $user);
    }

    if ($par_data_person) {
      // See if there are any outstanding invitations.
      $invitations = $this->getEntityTypeManager()
        ->getStorage('invite')
        ->loadByProperties([
          'field_invite_email_address' => $par_data_person->getEmail(),
          'status' => InviteConstants::INVITE_VALID,
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

    if ($par_data_person && $people = $par_data_person->getSimilarPeople()) {
      // We have some legacy data which has caused some people to have over 100
      // contacts. An upper limit needs to be set as this can't all be
      // processed within the same page.
      $this->getFlowDataHandler()->setParameter('contacts', array_slice($people, 0, 100, TRUE));

      // In order to display multiple cardinality the contact_locations_detail
      // plugin needs to know how many instances of data to display, it doesn't
      // use this data other than to know how many instances of data to display.
      // The actual displayed data comes from the contacts parameter set above.
      $contact_locations_detail_component = $this->getComponent('contact_locations_detail');
      if ($contact_locations_detail_component instanceof ParFormPluginInterface) {
        $values = [];
        foreach ($people as $person) {
          $values[] = ['username' => $person->label()];
        }
        $this->getFlowDataHandler()->setPluginTempData($contact_locations_detail_component, $values);
      }
    }
    else if ($par_data_person) {
      $this->getFlowDataHandler()->setParameter('contacts', [$par_data_person]);

      // In order to display multiple cardinality the contact_locations_detail
      // plugin needs to know how many instances of data to display, it doesn't
      // use this data other than to know how many instances of data to display.
      // The actual displayed data comes from the contacts parameter set above.
      $contact_locations_detail_component = $this->getComponent('contact_locations_detail');
      if ($contact_locations_detail_component instanceof ParFormPluginInterface) {
        // Set a single value.
        $values = [ ['username' => $par_data_person->label()] ];
        $this->getFlowDataHandler()->setPluginTempData($contact_locations_detail_component, $values);
      }
    }

    parent::loadData();
  }

  /**
   * Build the user profile page.
   */
  public function buildUserPage($build = []) {

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }

    $build['nav'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => '
        <div class="govuk-tabs govuk-!-padding-bottom-5" data-module="govuk-tabs">
            <h2 class="govuk-tabs__title">
                Profile Navigation
            </h2>
            <ul class="govuk-tabs__list">
                <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '">
                        Profile
                    </a>
                </li>
                <li class="govuk-tabs__list-item">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '/contacts">
                        Contacts
                    </a>
                </li>
                <li class="govuk-tabs__list-item">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '/authorities-organisations">
                        Authorities and Organisations
                    </a>
                </li>
            </ul>
        </div>
        ',
    ];

    // Enable the 'done' action instead of the default.
    $this->getFlowNegotiator()->getFlow()->enableAction('done');

    return parent::build($build);
  }

  /**
   * Build the users contact page.
   */
  public function buildContacts($build = []) {
    // When new contacts are added these can't clear the cache,
    // for now we will keep this page uncached.
    $this->killSwitch->trigger();

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }

    if ($contacts = $this->getFlowDataHandler()->getParameter('contacts')) {
      foreach ($contacts as $contact) {
        $this->addCacheableDependency($contact);
      }
    }

    $build['nav'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => '
        <div class="govuk-tabs govuk-!-padding-bottom-5" data-module="govuk-tabs">
            <h2 class="govuk-tabs__title">
                Profile Navigation
            </h2>
            <ul class="govuk-tabs__list">
                <li class="govuk-tabs__list-item">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '">
                        Profile
                    </a>
                </li>
                <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '/contacts">
                        Contacts
                    </a>
                </li>
                <li class="govuk-tabs__list-item">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '/authorities-organisations">
                        Authorities and Organisations
                    </a>
                </li>
            </ul>
        </div>
        ',
    ];

    // Enable the 'done' action instead of the default.
    $this->getFlowNegotiator()->getFlow()->enableAction('done');

    return parent::build($build);
  }

  /**
   * Build the users authorities & organisations page.
   */
  public function buildAuthoritiesOrganisations($build = []) {
    if ($user = $this->getFlowDataHandler()->getParameter('user')) {
      $this->addCacheableDependency($user);
    }

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->addCacheableDependency($par_data_person);
    }

    $build['nav'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => '
        <div class="govuk-tabs govuk-!-padding-bottom-5" data-module="govuk-tabs">
            <h2 class="govuk-tabs__title">
                Profile Navigation
            </h2>
            <ul class="govuk-tabs__list">
                <li class="govuk-tabs__list-item">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '">
                        Profile
                    </a>
                </li>
                <li class="govuk-tabs__list-item">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '/contacts">
                        Contacts
                    </a>
                </li>
                <li class="govuk-tabs__list-item govuk-tabs__list-item--selected">
                    <a class="govuk-tabs__tab" href="/person/' . $par_data_person->id() . '/authorities-organisations">
                        Authorities and Organisations
                    </a>
                </li>
            </ul>
        </div>
        ',
    ];

    // Enable the 'done' action instead of the default.
    $this->getFlowNegotiator()->getFlow()->enableAction('done');

    return parent::build($build);
  }
}
