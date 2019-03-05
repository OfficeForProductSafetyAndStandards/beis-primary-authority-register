<?php

namespace Drupal\par_person_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

trait ParUserDataTrait {

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $account = $par_data_person->getUserAccount();
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    parent::loadData();
  }
}
