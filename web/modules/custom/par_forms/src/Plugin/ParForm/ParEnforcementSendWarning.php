<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Enforcement summary form plugin.
 *
 * @ParForm(
 *   id = "enforcement_send_warning",
 *   title = @Translation("A warning notice to remind enforcement officers to send the enforcement to the business.")
 * )
 */
class ParEnforcementSendWarning extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $this->getFlowDataHandler()
      ->setFormPermValue("enforcement_label", $par_data_enforcement_notice->label());

    // A message to display for all approved and partly approved enforcement notices.
    if ($par_data_enforcement_notice->isApproved()) {
      $this->getFlowDataHandler()
        ->setFormPermValue("enforcement_approved", TRUE);
    }
    // A message to display for all blocked and partly blocked enforcement notices.
    if ($par_data_enforcement_notice->isBlocked()) {
      $this->getFlowDataHandler()
        ->setFormPermValue("enforcement_blocked", TRUE);
    }

    // PAR-1735: Enforcement notices against direct partnerships are automatically
    // sent to the business, all other enforcement notices must be sent by the EO.
    if ($par_data_enforcement_notice && $par_data_partnership = $par_data_enforcement_notice->getPartnership(TRUE)) {
      $sent = ($par_data_partnership && $par_data_partnership->isDirect());
      $this->getFlowDataHandler()->setFormPermValue('enforcement_sent', $sent);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Only display this warning if the enforcement has been approved.
    if ($this->getFlowDataHandler()->getFormPermValue('enforcement_approved')) {
      $schedule_url = Url::fromUri('http://www.legislation.gov.uk/ukpga/2008/13/schedule/4A', ['attributes' => ['target' => '_blank']]);
      $schedule_link = Link::fromTextAndUrl('Schedule 4A of The Regulatory Enforcement Sanctions Act 2008', $schedule_url);

      $message = $this->getFlowDataHandler()->getFormPermValue('enforcement_sent')
        ? "Please note that this enforcement notice has been approved.
          The business has been notified of this through the register."
        : "Please note that this enforcement notice has been approved.
          If you intend to proceed with your proposed action you must now notify the business.
          This notification should be made directly to the business via e-mail.
          If you require contact details of the business, please obtain these from the Primary Authority.
          For further information please refer to @link.";

      $form['approved_message'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
        'warning' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t($message, ['@link' => $schedule_link->toString()]),
          '#attributes' => ['class' => ['govuk-!-font-weight-bold']],
        ],
      ];
    }

    // Show determination message.
    if ($this->getFlowDataHandler()->getFormPermValue('enforcement_blocked')
      || $this->getFlowDataHandler()->getFormPermValue('enforcement_approved')) {
      $label = $this->getFlowDataHandler()->getFormPermValue('enforcement_label');
      $email_link = Link::fromTextAndUrl("pa@businessandtrade.gov.uk", Url::fromUri("mailto:pa@businessandtrade.gov.uk?subject=Determination:%20$label"));

      $form['determination_message'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
        'warning-primary' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => "Please contact the Primary Authority if you have any questions around this decision.",
          '#attributes' => ['class' => ['govuk-!-font-weight-bold']],
        ],
        'warning-opss' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => "If you would like to apply to the Secretary of State for consent to refer the matter for determination please contact Office for Product Safety & Standards at {$email_link->toString()}.",
          '#attributes' => ['class' => ['govuk-!-font-weight-bold']],
        ],
      ];
    }

    return $form;
  }

}
