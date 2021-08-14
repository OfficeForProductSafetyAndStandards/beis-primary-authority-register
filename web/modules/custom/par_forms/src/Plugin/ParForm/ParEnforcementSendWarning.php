<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_actions\Plugin\Factory\BusinessDaysCalculator;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;
use RapidWeb\UkBankHolidays\Factories\UkBankHolidayFactory;

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
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // Only allow this message for approved or partly approved enforcements.
    if ($par_data_enforcement_notice->isApproved()) {
      $this->getFlowDataHandler()
        ->setFormPermValue("enforcement_approved", TRUE);
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Only display this warning if the enforcement has been approved.
    if ($this->getFlowDataHandler()->getFormPermValue('enforcement_approved')) {
      $schedule_url = Url::fromUri('http://www.legislation.gov.uk/ukpga/2008/13/schedule/4A', ['attributes' => ['target' => '_blank']]);
      $schedule_link = Link::fromTextAndUrl('Schedule 4A of The Regulatory Enforcement Sanctions Act 2008', $schedule_url);

      $form['warning'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['form-group']],
        'warning' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t("This enforcement notice has now been reviewed by the Primary Authority."),
          '#attributes' => ['class' => ['govuk-!-font-weight-bold']],
        ],
      ];
    }

    return $form;
  }
}
