<?php

namespace Drupal\registered_organisations\Plugin\OrganisationRegister;

use Drupal\Core\Http\ClientFactory;
use Drupal\registered_organisations\OrganisationProfile;
use Drupal\registered_organisations\OrganisationProfileInterface;
use Drupal\registered_organisations\OrganisationRegisterApi;
use Drupal\registered_organisations\OrganisationRegisterInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Legacy organisation register.
 *
 * @OrganisationRegister(
 *   id = "legacy",
 *   label = @Translation("Legacy"),
 *   description = @Translation("Organisations that have not been converted."),
 * )
 */
class LegacyRegister extends OrganisationRegisterApi {

  /**
   * {@inheritDoc}
   */
  public function findOrganisation(string $name, bool $active = TRUE): array {

    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function getOrganisation(string $id): OrganisationProfile | NULL {

    return NULL;
  }

}
