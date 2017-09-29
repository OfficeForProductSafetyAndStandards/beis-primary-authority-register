<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParRedirectTrait;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsPartnershipSearchForm extends FormBase {

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
  public function buildForm(array $form, FormStateInterface $form_state) {
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

    $partnerships = $this->getPartnerships();
    $advice = $this->getAdvice();
    $inspection_plans = $this->getInspectionPlans();

    $message = $this->t('Transitioning now will remove %partnerships partnerships, %advice advice and %inspections inspection plans from the system', ['%partnerships' => count($partnerships), '%advice' => count($advice), '%inspections' => count($inspection_plans)]);
    $build['actions']['help_text'] = [
      '#type' => 'markup',
      '#markup' => render($message),
    ];

    $build['actions']['transition'] = [
      '#type' => 'submit',
      '#value' => $this->t('Transition partnerships, advice and inspection plans now'),
      '#attributes' => [
        'class' => ['cta-submit']
      ],
    ];

    return $build;
  }

  public function getPartnerships() {
    $conditions = [
      'authority_info' => [
        'OR' => [
          ['partnership_info_agreed_authority', 0],
          ['partnership_info_agreed_authority', NULL, 'IS NULL'],
        ],
      ],
      'authority_terms' => [
        'OR' => [
          ['terms_authority_agreed', 0],
          ['terms_authority_agreed', NULL, 'IS NULL'],
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
      'type' => [
        'AND' => [
          ['status', 1]
        ],
      ],
    ];

    $partnerships = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_partnership', $conditions);

    return $partnerships;
  }

  public function getAdvice() {
    $conditions = [
      'type' => [
        'AND' => [
          ['advice_type', ['business_advice', 'background_information'], 'NOT IN'],
          ['status', 1]
        ],
      ],
    ];

    $advice = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_advice', $conditions);

    return $advice;
  }

  public function getInspectionPlans() {
    $conditions = [
      'type' => [
        'AND' => [
          ['status', 1]
        ],
      ],
    ];

    $inspection_plans = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_inspection_plan', $conditions);

    return $inspection_plans;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Transition Partnerships.
    $partnerships = $this->getPartnerships();

    foreach($partnerships as $p) {
      $p->invalidate();
    }

    // Transition Advice.
    $advice = $this->getAdvice();

    foreach($advice as $a) {
      $a->invalidate();
    }

    $inspection_plans = $this->getInspectionPlans();

    foreach ($inspection_plans as $i) {
      $i->invalidate();
    }

    // Then update all entities that remain transitioned to the correct statuses.
    $conditions = [
      'type' => [
        'AND' => [
          ['status', 1]
        ],
      ],
    ];

    $partnerships = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_partnership', $conditions);

    foreach ($partnerships as $entity) {
      $entity->setParStatus('confirmed_rd');
      $entity->save();
    }
  }

}
