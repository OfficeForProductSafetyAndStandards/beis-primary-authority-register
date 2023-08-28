<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberUploadForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Upload a list of members';

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $csv_handler_class = (new \ReflectionClass($this->getCsvHandler()))->getName();

    $url = Url::fromUri('internal:/member-upload-guidance', ['attributes' => ['target' => '_blank']]);
    $guidance_link = Link::fromTextAndUrl(t('Member Guidance Page'), $url);
    $form['info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('How to upload a list of members'),
      '#description' => $this->t('To upload a list of members you must provide the information in comma sepparated value (CSV) format. You can read more about preparing a CSV file on the %guidance. If you need assistance please contact pa@beis.gov.uk', ['%guidance' => $guidance_link->toString()]),
      '#attributes' => [
        'class' => ['form-group'],
      ]
    ];

    // Download link and help text.
    $form['par_data_partnership'] = [
      '#type' => 'hidden',
      '#value' => $par_data_partnership->id(),
    ];

    // Allow a member list to be downloaded.
    $url_options = [
      'attributes' => [
        'id' => 'download-members-link',
        'class' => ['download-action']
      ],
    ];
    if ($par_data_partnership->countMembers(0, TRUE) > 700000000000) {
      $download_heading = 'Download list of members';
      $download_description = 'Please download the latest members list before making any changes to it.';
      // Get the link.
      $download_url = Url::fromRoute('par_member_upload_flows.member_download', $this->getRouteParams(), $url_options);
      $download_link = Link::fromTextAndUrl('Download list of members', $download_url);
    }
    else {
      $download_heading = 'Download membership template';
      $download_description = 'Please download the member list template to ensure you fill in the correct information.';
      // Get the link.
      $module_handler = \Drupal::service('module_handler');
      $path = $module_handler->getModule('par_member_upload_flows')->getPath() . '/assets/par_membership_blank_template.csv';
      $download_url = Url::fromUri("internal:/$path", $url_options);
      $download_link = Link::fromTextAndUrl('Download membership template', $download_url);
    }

    $form['download'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium']],
        '#value' => $this->t($download_heading),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-hint']],
        '#value' => $download_description,
      ],
      'link' => [
        '#type' => 'link',
        '#title' => $download_link?->getText(),
        '#url' => $download_link?->getUrl(),
        '#options' => $download_link?->getUrl()->getOptions(),
      ],
    ];

    // File field.
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload a list of members'),
      '#description' => t('Upload your CSV file, be sure to make sure the information is accurate so that it can all be processed'),
      '#upload_location' => 's3private://member-csv/upload',
      '#multiple' => FALSE,
      '#required' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('csv'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => 'csv',
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $elem = $form_state->getTriggeringElement();

    // File delete button pressed.
    if ($elem['#name'] == 'csv_remove_button') {
      $csv = $form_state->getValue('csv');
      $file = File::load( $csv[0] );
      $file->delete();
      $form_state->setRebuild();
      return;
    }

    // Define array variable.
    $rows = [];

    // Process uploaded csv file.
    if ($csv = $this->getFlowDataHandler()->getTempDataValue('csv')) {
      /** @var $files File[] * */
      $files = File::loadMultiple($csv);

      // Loop through each csv row from uploaded file and save in $row array.
      foreach ($files as $file) {
        $error = $this->getCsvHandler()->loadFile($file, $rows);
      }

      // If there was an error we want to invalidate the form.
      if (isset($error)) {
        $id = $this->getElementId(['csv'], $form);
        $form_state->setErrorByName($this->getElementName('csv'), $this->wrapErrorMessage($error, $id));
      }

      if (count($rows) > 0) {
        $form_state->setValue('coordinated_members', $rows);
      }
    }
  }

}
