<?php

namespace Drupal\par_data\Plugin\OrganisationRegister;

use Drupal\registered_organisations\OrganisationProfile;
use Drupal\registered_organisations\OrganisationProfileInterface;
use Drupal\registered_organisations\OrganisationRegisterApi;
use Drupal\registered_organisations\OrganisationRegisterInterface;

/**
 * PAR internal organisation register.
 *
 * @OrganisationRegister(
 *   id = "internal",
 *   label = @Translation("Unregistered organisations"),
 *   description = @Translation("Organisations such as sole traders and partnerships.")
 * )
 */
class InternalRegister extends OrganisationRegisterApi {

  /**
   * The organisation type enum.
   */
  const ORGANISATION_TYPE = [
    'partnership' => 'Partnership',
    'sole_trader' => 'Sole trader',
    'unincorporated_association' => 'Unincorporated association',
    'other' => 'Other',
  ];

  /**
   * The organisation type description.
   */
  const ORGANISATION_TYPE_DESC = [
    'partnership' => 'A partnership is a contractual arrangement between two or more people that is set up with a view to profit and to share the profits amongst the partners.',
    'sole_trader' => 'A sole trader is an individual who is registered with HMRC for tax purposes.',
    'unincorporated_association' => 'A simple way for a group of volunteers to run an organisation for a common purpose.',
    'other' => 'An unregistered organisation that does not fit in the categories above.',
  ];

  /**
   * {@inheritDoc}
   */
  public function getOrganisation(string $id): OrganisationProfileInterface|null {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function findOrganisation(string $name): array {
    return [];
  }

}
