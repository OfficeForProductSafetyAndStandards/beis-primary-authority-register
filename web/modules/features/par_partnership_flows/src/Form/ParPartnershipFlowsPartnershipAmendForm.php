<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipLegalEntity;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\registered_organisations\OrganisationProfile;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Form to add legal entities to partnerships.
 */
class ParPartnershipFlowsPartnershipAmendForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Add legal entities to the partnership';

    return parent::titleCallback();
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Access according to route and user role.
    switch ($route_match->getRouteName()) {
      case 'par_partnership_flows.authority_amend_select':
        if (!in_array('par_authority', $account->getRoles())) {
          $this->accessResult = AccessResult::forbidden('The user is not allowed to access the authority partnership amendment page.');
        }
        break;
      default:
        $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Conditions and term have been acepted so add the new legal entities to the partnership.
    if ($this->getFormId() == 'par_partnership_partnership_amend_terms') {

      $data = $this->getFlowDataHandler()->getMetaDataValue('legal_entity_select:state');

      // Get the partnership, the organisation and LEs already attached to the organisation.
      /* @var ParDataPartnership $partnership */
      $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $organisation = $partnership->getOrganisation(TRUE);

      // Process the selected LEs.
      /* @var OrganisationProfile $selected_legal_entity */
      foreach ($data['selected_legal_entities'] as $selected_legal_entity) {

        // Is this legal entity already recorded in PAR?
        $legal_entity = ParDataLegalEntity::find($selected_legal_entity->getRegister(),
                                                 $selected_legal_entity->getTypeRaw(),
                                                 $selected_legal_entity->getId(),
                                                 $selected_legal_entity->getName());

        // If not found create the LE.
        $legal_entity = ParDataLegalEntity::create([
          'type' => 'legal_entity',
          'registry' => $selected_legal_entity->getRegister(),
          'name' => $selected_legal_entity->getName(),
          'registered_name' => $selected_legal_entity->getName(),
          'registered_number' => $selected_legal_entity->getId(),
          'legal_entity_type' => $selected_legal_entity->getTypeRaw(),
        ]);
        $legal_entity->save();

        // Add the legal entity to the organisation.
        // If the LE already exists in PAR and is attached to the organisation it will not get added again.
        $organisation->addLegalEntity($legal_entity);

        // Now add the legal entity to the partnership.
        $partnership->addLegalEntity($legal_entity);
      }

      // Commit partnership and organisation changes.
      $partnership->save();
      $organisation->save();
      $this->getFlowDataHandler()->deleteStore();
    }

  }
}
