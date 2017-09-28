<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParRedirectTrait;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsPartnershipSearchController extends ControllerBase {

  use ParDisplayTrait;
  use ParRedirectTrait;

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_organisation_suggestion';
  }

  /**
   * {@inheritdoc}
   */
  public function content() {
    $conditions = [
      'authority' => [
        'AND' => [
          ['partnership_info_agreed_authority', 1],
          ['terms_authority_agreed', 1],
        ],
      ],
      'business_info' => [
        'OR' => [
          ['partnership_info_agreed_business', 0],
          ['partnership_info_agreed_business', NULL, 'IS NULL'],
        ],
      ],
      'business_terms' => [
        'OR' => [
          ['terms_organisation_agreed', 0],
          ['terms_organisation_agreed', NULL, 'IS NULL'],
        ],
      ],
    ];

    $entities = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_partnership', $conditions);

    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => t('Partnerships'),
    ];

    foreach($entities as $entity) {
      $route = "entity.{$entity->getEntityTypeId()}.edit_form";
      $route_params = [
        $entity->getEntityTypeId() => $entity->id(),
      ];
      $link = $this->getLinkByRoute($route, $route_params)->setText($entity->label());

      $build['partnerships'][$entity->id()] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group']
      ];

      $build['partnerships'][$entity->id()]['link'] = [
        '#type' => 'markup',
        '#markup' => $link->toString(),
      ];
    }

    return $build;
  }

}
