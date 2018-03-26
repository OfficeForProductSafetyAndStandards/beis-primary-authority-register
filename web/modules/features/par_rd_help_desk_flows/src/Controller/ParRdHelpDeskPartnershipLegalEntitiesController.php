<?php

namespace Drupal\par_rd_help_desk_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * A controller for rendering a list of partnerships where the legal entities differ from those on the organisation.
 */
class ParRdHelpDeskPartnershipLegalEntitiesController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'migrate_legal_entities';

  /**
   * {@inheritdoc}
   */
  public function content() {
    $build = [];

    $build['legal_entity_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
      '#title' => 'Legal Entity List',
      '#header' => [
        'Partnership name',
        'Legal Entities on Partnership',
        'Legal Entities on Organisation',
        'Partnership status',
        'Partnership created',
      ],
      '#rows' => [],
      '#empty' => $this->t("All partnerships are up-to-date."),
    ];

    $par_data_partnerships = $this->getParDataManager()->getEntitiesByType('par_data_partnership');
    foreach ($par_data_partnerships as $key => $partnership) {
      if (!$partnership->isLiving()) {
        continue;
      }
      $organisation = $partnership->getOrganisation(TRUE);
      $organisation_legal_entities = $organisation ? $organisation->getLegalEntity() : NULL;
      $partnership_legal_entities = $partnership->getLegalEntity();

      if ($partnership_legal_entities !== $organisation_legal_entities) {
        $view_builder = $this->getParDataManager()->getViewBuilder('par_data_legal_entity');
        $renderer = \Drupal::service('renderer');
        $partnership_legal_entities_build = !empty($partnership_legal_entities) ? $view_builder->viewMultiple($partnership_legal_entities, 'summary') : NULL;
        $organisation_legal_entities_build = !empty($organisation_legal_entities) ? $view_builder->viewMultiple($organisation_legal_entities, 'summary') : NULL;

        $build['legal_entity_list']['#rows'][] = [
          $partnership->label() . " ({$partnership->id()})",
          $partnership_legal_entities_build ? @$renderer->render($partnership_legal_entities_build) : 'There are none',
          $organisation_legal_entities_build ? @$renderer->render($organisation_legal_entities_build) : 'There are none',
          $partnership->getParStatus(),
          DrupalDateTime::createFromTimestamp((int) $partnership->get('created')->getString(), NULL, ['validate_format' => FALSE]),
        ];
      }
    }
    return parent::build($build);
  }

}
