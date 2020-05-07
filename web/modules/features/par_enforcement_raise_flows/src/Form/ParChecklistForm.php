<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_enforcement_raise_flows\ParFormCancelTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The checklist form for starting an enforcement.
 */
class ParChecklistForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Have you discussed this issue with the Primary Authority?';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($primary_contact = $par_data_partnership->getAuthorityPeople(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("primary_contact_name", $primary_contact->label());
        $this->getFlowDataHandler()->setFormPermValue("primary_contact_work_phone", $primary_contact->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("primary_contact_email", $primary_contact->get('email')->getString());
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Organisation members can skip this form.
    if ($this->getFlowDataHandler()->getDefaultValues('organisation_member', FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['help'] = [
      '#type' => 'markup',
      '#markup' => '<p>Before proceeding with the enforcement notification, have you been in dialogue with the Primary Authority? They may be able to help you to achieve a swift resolution.</p>',
    ];

    // Where possible enforcement officers should be given the chance to discuss
    // the issues outside of the enforcement process to ensure a swift resolution.
    $form['contact'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['form-group']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Primary authority contact'),
        '#attributes' => ['class' => 'heading-large'],
      ],
      'details' => $this->getFlowDataHandler()->getDefaultValues('notice_summary', NULL),
    ];
    if ($name = $this->getFlowDataHandler()->getDefaultValues('primary_contact_name', NULL)) {
      $form['contact']['name'] = [
        '#type' => 'markup',
        '#markup' => $name,
      ];
      if ($work_phone = $this->getFlowDataHandler()->getDefaultValues('primary_contact_work_phone', NULL)) {
        $form['contact']['work_phone'] = [
          '#type' => 'markup',
          '#markup' => ', ' . $work_phone,
        ];
      }
      if ($email = $this->getFlowDataHandler()->getDefaultValues('primary_contact_email', NULL)) {
        $form['contact']['email'] = [
          '#type' => 'markup',
          '#markup' => ', ' . $email,
        ];
      }

      $form['enquire'] = [
        '#type' => 'link',
        '#weight' => 10,
        '#title' => $this->t('Discuss this enforcement'),
        '#url' => Url::fromRoute('par_enquiry_send_flows.select_authority', $this->getRouteParams()),
        '#prefix' => '<p class="form-group">',
        '#suffix' => '<br><br><br></p>',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

}
