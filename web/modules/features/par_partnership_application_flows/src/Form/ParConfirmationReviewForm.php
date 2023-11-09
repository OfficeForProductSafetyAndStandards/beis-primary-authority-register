<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * The partnership form for the partnership details.
 */
class ParConfirmationReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Check partnership information';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataAuthority $par_data_authority */
    /** @var ParDataPerson $primary_authority_contact */
    /** @var ParDataPerson $organisation_contact */
    /** @var ParDataPremises $par_data_premises */
    /** @var Invite $invite */

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());

    if ($par_data_partnership) {
      // Display details about the partnership for information.
      $form['about_partnership'] = $this->renderSection('About the partnership', $par_data_partnership, ['about_partnership' => 'about']);
      $form['about_partnership']['about_partnership']['operations']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('about', [], ['query' => ['destination' => $return_path]])
            ->setText('Change description of this partnership')
            ->toString(),
        ]),
      ];

      $form['partnership'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group']],
      ];

      // Show the organisation name.
      if ($par_data_organisation) {
        $form['partnership']['organisation'] = [
          '#type' => 'container',
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
        ];

        // Display organisation name and organisation primary address.
        $form['partnership']['organisation']['organisation_name'] = [
          '#type' => 'container',
          '#attributes' => ['class' => 'govuk-form-group'],
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#value' => $this->t('Organisation name'),
            '#attributes' => ['class' => 'govuk-heading-m'],
          ],
          'name' => [
            '#type' => 'markup',
            '#markup' => $par_data_organisation->label(),
            '#prefix' => '<div>',
            '#suffix' => '</div>',
          ],
        ];
        // This link cannot come straight back to the review screen, because
        // changing the organisation requires address and contact to be updated too.
        $form['partnership']['organisation']['organisation_name']['operations'] = [
          'edit' => [
            '#type' => 'markup',
            '#markup' => t('@link', [
              '@link' => $this->getFlowNegotiator()->getFlow()
                ->getLinkByCurrentOperation('organisation_name', [], ['query' => ['destination' => $return_path]])
                ->setText('Change this organisation')
                ->toString(),
            ]),
          ]
        ];
        $form['partnership']['organisation']['organisation_registered_address'] = $this->renderEntities('Organisation address', [$par_data_premises], 'summary', [], TRUE);

        // Display contacts at the organisation.
        $form['partnership']['organisation']['organisation_contact'] =  $this->renderEntities('Contact at the organisation', [$organisation_contact]);
      }

      // Show the primary authority name.
      if ($par_data_authority) {
        $form['partnership']['authority'] = [
          '#type' => 'container',
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
        ];
        $form['partnership']['authority']['authority_name'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['govuk-form-group']],
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#value' => $this->t('Primary authority name'),
            '#attributes' => ['class' => ['govuk-heading-m']],
          ],
          'name' => [
            '#type' => 'markup',
            '#markup' => $par_data_authority->label(),
            '#prefix' => '<div>',
            '#suffix' => '</div>',
          ]
        ];

        // Display the authority contacts for information.
        if ($primary_authority_contact) {
          $form['partnership']['authority']['primary_authority_contact'] = $this->renderEntities('Primary contact', [$primary_authority_contact]);
        }
      }

      $form['confirm'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
      ];

      $url_address = 'https://www.gov.uk/government/publications/primary-authority-terms-and-conditions';
      $url = Url::fromUri($url_address, ['attributes' => ['target' => '_blank']]);
      $terms_link = Link::fromTextAndUrl(t('terms & conditions (opens in a new window)'), $url);
      $form['terms_authority_agreed'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I have read and agree to the @terms.', ['@terms' => $terms_link->toString()]),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues("terms_authority_agreed"),
        '#return_value' => 'on',
        '#wrapper_attributes' => ['class' => ['govuk-!-margin-bottom-8', 'govuk-!-margin-top-8']],
      ];

      $form['help_text'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('You won\'t be able to change these details after you save them. Please check everything is correct.'),
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }
    else {
      $form['help_text'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('The partnership could not be created, please contact the Helpdesk if this problem persists.'),
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box and terms box is ticked.
    if (!$form_state->getValue('terms_authority_agreed')) {
      $message = $this->wrapErrorMessage('Please confirm you have read the terms and conditions.', $this->getElementId('terms_authority_agreed', $form));
      $form_state->setErrorByName($this->getElementName('terms_authority_agreed'), $message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataAuthority $par_data_authority */
    /** @var ParDataPerson $primary_authority_contact */
    /** @var ParDataPerson $organisation_contact */
    /** @var ParDataPremises $par_data_premises */
    /** @var Invite $invite */

    // Set the premises and contact information on organisation and partnership.
    if ($primary_authority_contact && $organisation_contact->save() && $par_data_premises->save()) {
      $par_data_organisation->get('field_person')->appendItem($organisation_contact->id());

      $par_data_organisation->get('field_premises')->appendItem($par_data_premises->id());

      $par_data_partnership->set('field_organisation_person', $organisation_contact->id());
      $par_data_partnership->set('field_authority_person', $primary_authority_contact->id());
    }

    // Set the primary authority and the organisation information on the partnership.
    if ($par_data_authority && $par_data_organisation->save()) {
      $par_data_partnership->set('field_organisation', $par_data_organisation->id());
      $par_data_partnership->set('field_authority', $par_data_authority->id());
    }

    // We need to update the status just before the entity is saved.
    try {
      $par_data_partnership->setParStatus('confirmed_authority');
    }
    catch (ParDataException $e) {
      // If the status could not be updated we want to log this but continue.
      $message = $this->t("This status could not be updated to 'Approved by the Authority' for the %label");
      $replacements = [
        '%label' => $par_data_partnership->label(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

    if ($par_data_partnership && $par_data_authority && $par_data_organisation) {
      $save = $par_data_partnership->save();
      if ($save && $invite instanceof Invite) {
        // @TODO Make sure partnership notification isn't sent for users that don't have accounts.
        $invite->save();
      }
    }
    else {
      $message = $this->t('%partnership could not be saved for %form_id');
      $replacements = [
        '%partnership' => $par_data_partnership->label(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);

      // If the partnership could not be saved the application can't be progressed.
      // @TODO Find a better way to alert the user without redirecting them away from the form.
      $this->messenger()->addMessage('There was an error progressing your partnership, please contact the helpdesk for more information.');
      $form_state->setRedirectUrl($this->getFlowNegotiator()->getFlow()->progress('cancel'));
    }
  }

  public function createEntities() {
    // Load the Authority.
    $cid_authority_select = $this->getFlowNegotiator()->getFormKey('authority_select');
    $acting_authority = $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', '', $cid_authority_select);
    if ($par_data_authority = ParDataAuthority::load($acting_authority)) {
      // Get logged in user ParDataPerson(s) related to the primary authority.
      $primary_authority_contact = $this->getParDataManager()->getUserPerson($this->getCurrentUser(), $par_data_authority);
    }

    // Load an existing address if provided.
    $cid_organisation_select = $this->getFlowNegotiator()->getFormKey('organisation_select');
    $existing_organisation = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id','new', $cid_organisation_select);
    if (isset($existing_organisation) && $existing_organisation !== 'new'
      && $par_data_organisation = ParDataOrganisation::load($existing_organisation)) {

      // Get the address and or contact from the existing organisation.
      $par_data_premises = $par_data_organisation->getPremises(TRUE);
      $organisation_contact = $par_data_organisation->getPerson(TRUE);
    }
    // Create a new organisation but do not save yet.
    else {
      $cid_organisation_name = $this->getFlowNegotiator()->getFormKey('organisation_name');
      $par_data_organisation = ParDataOrganisation::create([
        'type' => 'organisation',
        'organisation_name' => $this->getFlowDataHandler()->getDefaultValues('name','', $cid_organisation_name),
      ]);
    }

    $cid_organisation_address = $this->getFlowNegotiator()->getFormKey('organisation_address');
    $nation = $this->getFlowDataHandler()->getDefaultValues('country','', $cid_organisation_address);
    $par_data_organisation->setNation($nation);
    if (!isset($par_data_premises)) {
      $par_data_premises = ParDataPremises::create([
        'type' => 'premises',
        'uid' => $this->getCurrentUser()->id(),
        'address' => [
          'country_code' => $this->getFlowDataHandler()->getDefaultValues('country_code', '', $cid_organisation_address),
          'address_line1' => $this->getFlowDataHandler()->getDefaultValues('address_line1','', $cid_organisation_address),
          'address_line2' => $this->getFlowDataHandler()->getDefaultValues('address_line2','', $cid_organisation_address),
          'locality' => $this->getFlowDataHandler()->getDefaultValues('town_city','', $cid_organisation_address),
          'administrative_area' => $this->getFlowDataHandler()->getDefaultValues('county','', $cid_organisation_address),
          'postal_code' => $this->getFlowDataHandler()->getDefaultValues('postcode','', $cid_organisation_address),
        ],
      ]);
      $par_data_premises->setNation($nation);
    }

    if (!isset($organisation_contact)) {
      $cid_organisation_contact = $this->getFlowNegotiator()->getFormKey('organisation_contact');
      $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid_organisation_contact)['communication_email'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid_organisation_contact)['communication_email']);
      $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid_organisation_contact)['communication_phone'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid_organisation_contact)['communication_phone']);
      $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid_organisation_contact)['communication_mobile'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid_organisation_contact)['communication_mobile']);

      $organisation_contact = ParDataPerson::create([
        'type' => 'person',
        'salutation' => $this->getFlowDataHandler()->getDefaultValues('salutation', '', $cid_organisation_contact),
        'first_name' => $this->getFlowDataHandler()->getDefaultValues('first_name', '', $cid_organisation_contact),
        'last_name' => $this->getFlowDataHandler()->getDefaultValues('last_name', '', $cid_organisation_contact),
        'work_phone' => $this->getFlowDataHandler()->getDefaultValues('work_phone', '', $cid_organisation_contact),
        'mobile_phone' => $this->getFlowDataHandler()->getDefaultValues('mobile_phone', '', $cid_organisation_contact),
        'email' => $this->getFlowDataHandler()->getDefaultValues('email', '', $cid_organisation_contact),
        'communication_email' => $email_preference_value,
        'communication_phone' => $work_phone_preference_value,
        'communication_mobile' => $mobile_phone_preference_value,
        'communication_notes' => $this->getFlowDataHandler()->getDefaultValues('notes', '', $cid_organisation_contact),
      ]);

    }

    $cid_application_type = $this->getFlowNegotiator()->getFormKey('application_type');
    $cid_about = $this->getFlowNegotiator()->getFormKey('about');
    $par_data_partnership = ParDataPartnership::create([
      'type' => 'partnership',
      'uid' => $this->getCurrentUser()->id(),
      'partnership_type' => $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid_application_type),
      'about_partnership' => $this->getFlowDataHandler()->getDefaultValues('about_partnership', '', $cid_about),
      'terms_authority_agreed' => $this->getFlowDataHandler()->getDefaultValues('terms_authority_agreed', 0),
      'partnership_info_agreed_authority' => 1,
    ]);

    // Create the invitation.
    $cid_invite = $this->getFlowNegotiator()->getFormKey('invite');
    $invitation_type = $this->getFlowDataHandler()->getDefaultValues('invitation_type', FALSE, $cid_invite);
    // Override invite type if there were multiple roles to choose from.
    $roles = $this->getFlowDataHandler()->getDefaultValues('roles', [], $cid_invite);
    $target_role = $this->getFlowDataHandler()->getDefaultValues('target_role', FALSE, $cid_invite);
    if ($roles && count($roles) > 1) {
      switch ($target_role) {
        case 'par_enforcement':
          $invitation_type = 'invite_enforcement_officer';

          break;
        case 'par_authority':
          $invitation_type = 'invite_authority_member';

          break;
        case 'par_organisation':
          $invitation_type = 'invite_organisation_member';

          break;
      }
    }

    if ($invitation_type) {
      $invite = Invite::create([
        'type' => $invitation_type,
        'user_id' => $this->getFlowDataHandler()
          ->getTempDataValue('user_id', $cid_invite),
        'invitee' => $this->getFlowDataHandler()
          ->getTempDataValue('to', $cid_invite),
      ]);
      $invite->set('field_invite_email_address', $this->getFlowDataHandler()->getTempDataValue('to', $cid_invite));
      $invite->set('field_invite_email_subject', $this->getFlowDataHandler()->getTempDataValue('subject', $cid_invite));
      $invite->set('field_invite_email_body', $this->getFlowDataHandler()->getTempDataValue('body', $cid_invite));
      $invite->setPlugin('invite_by_email');
    }

    return [
      'par_data_partnership' => isset($par_data_partnership) ? $par_data_partnership : NULL,
      'par_data_organisation' => isset($par_data_organisation) ? $par_data_organisation : NULL,
      'par_data_authority' => isset($par_data_authority) ? $par_data_authority : NULL,
      'primary_authority_contact' => isset($primary_authority_contact) ? $primary_authority_contact : NULL,
      'organisation_contact' => isset($organisation_contact) ? $organisation_contact : NULL,
      'par_data_premises' => isset($par_data_premises) ? $par_data_premises : NULL,
      'invite' => isset($invite) ? $invite : NULL,
    ];
  }

}
