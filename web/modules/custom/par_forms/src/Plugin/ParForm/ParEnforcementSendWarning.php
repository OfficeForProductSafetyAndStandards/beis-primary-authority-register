<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
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

    // Determine the date by which this enforcement must be sent to the business.
    if ($par_data_enforcement_notice) {
      $approved_time = $par_data_enforcement_notice->getStatusTime('approved');
      if ($approved_time) {
        $approved_date = DrupalDateTime::createFromTimestamp($approved_time, NULL, ['validate_format' => FALSE]);

        // Find date to process.
        $holidays = array_column(UkBankHolidayFactory::getAll(), 'date', 'date');
        $calculator = new BusinessDaysCalculator(
          $approved_date,
          $holidays,
          [BusinessDaysCalculator::SATURDAY, BusinessDaysCalculator::SUNDAY]
        );
        $calculator->addBusinessDays(10);

        $this->getFlowDataHandler()
          ->setFormPermValue("send_date", $this->getDateFormatter()->format($calculator->getDate()->getTimestamp(), 'gds_date_format'));
      }
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Only display this warning if the enforcement has been approved.
    if ($this->getFlowDataHandler()->getFormPermValue('enforcement_approved')) {
      $send_date = $this->getFlowDataHandler()->getDefaultValues('send_date', '');
      $form['send_warning'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['form-group', 'notice']],
        'warning' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t("Please note that this enforcement has been approved and a copy of the information held within must be sent to the business by $send_date at the latest."),
          '#attributes' => ['class' => 'bold-small'],
          '#prefix' => '<i class="icon icon-important"><span class="visually-hidden">Warning</span></i>'
        ],
      ];
    }

    return $form;
  }
}
