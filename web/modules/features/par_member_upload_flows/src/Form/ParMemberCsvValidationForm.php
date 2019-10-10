<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandler;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberCsvValidationForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'CSV validation errors';

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

  /**
   * Get unique pager service.
   *
   * @return \Drupal\unique_pager\UniquePagerService
   */
  public static function getUniquePager() {
    return \Drupal::service('unique_pager.unique_pager_service');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    // Load csv data from temporary data storage and display any errors or move on.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    if ($data) {
      $errors = $this->getCsvHandler()->validate($data);
    }

    if (!empty($data) && !isset($errors)) {
      return $this->redirect($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
    }

    $url = Url::fromUri('internal:/member-upload-guidance', ['attributes' => ['target' => '_blank']]);
    $guidance_link = Link::fromTextAndUrl(t('Member Guidance Page'), $url);
    $form['info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('There were some errors with the CSV file.'),
      '#description' => $this->t('You can read more about preparing a CSV file on the %guidance. If you need assistance please contact pa@beis.gov.uk', ['%guidance' => $guidance_link->toString()]),
      '#attributes' => [
        'class' => ['form-group'],
      ]
    ];

    // Add the errors to the table rows.
    $rows = [];
    if (isset($errors)) {
      foreach ($errors as $index => $error) {
        $line_class = Html::cleanCssIdentifier("error-line-{$error->getLine()}");
        $column_class = Html::cleanCssIdentifier("error-column-{$error->getColumn()}");
        $rows[] = [
          'data' => [
            'line' => $error->getLine(),
            'column' => $error->getColumn(),
            'error' => $error->getMessage(),
          ],
          'class' => [$line_class, $column_class],
        ];
      }
    }

    // Initialize pager and get current page.
    $pager = $this->getUniquePager()->getPager('csv_members_validation');
    $current_page = pager_default_initialize(count($rows), 10, $pager);

    // Split the items up into chunks:
    $chunks = array_chunk($rows, 10);

    // Display error message if violations are found in the uploaded csv file.
    $form['error_list'] = [
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'table' => [
        '#theme' => 'table',
        '#attributes' => ['class' => ['form-group']],
        '#title' => 'CSV Errors',
        '#weight' => 0,
        '#header' => [
          'Line',
          'Column',
          'Error',
        ],
        '#empty' => $this->t("No data was found in the CSV file."),
      ],
      'pager' => [
        '#type' => 'pager',
        '#theme' => 'pagerer',
        '#element' => 1,
        '#weight' => 1,
        '#config' => [
          'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
        ],
      ]
    ];

    // Add the items for our current page to the fieldset.
    if (isset($chunks[$current_page])) {
      foreach ($chunks[$current_page] as $delta => $item) {
        $form['error_list']['table']['#rows'][$delta] = $item;
      }
    }

    $form = parent::buildForm($form, $form_state);

    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $form['actions']['done']['#value'] = 'Re-upload';
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->getFlowDataHandler()->deleteStore();
  }

}
