<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\companies\Plugin\CompaniesRegister\CompaniesRegisterInterface;
use Drupal\companies\Plugin\CompaniesRegister\CompaniesRegisterApi;

/**
 * Cease a member.
 *
 * @CompaniesRegister(
 *   id = "companies_house",
 *   title = @Translation("UK companies register through Companies House.")
 * )
 */
class CompaniesHouseRegister extends CompaniesRegisterApi {

  public function getClient() {
    $config = \Drupal::config('companies.settings');

    $api_key = $config->get('companies_house_api_key');
    if (empty($api_key)) {
      return;
    }

    try {
      $this->client = new CompaniesHouseClient($api_key);
    }
    catch (\Alphagov\Notifications\Exception\ApiException $e) {
      \Drupal::logger('govuk_notify')->warning("Failed to create Gov Notify Client using API: @message",
        ['@message' => $e->getMessage()]);
    }
    catch (\Exception $e) {
      \Drupal::logger('govuk_notify')->warning("Failed to create Gov Notify Client using API: @message",
        ['@message' => $e->getMessage()]);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getCompany(string $id): array {
    $profile = $this->client->getCompanyProfile($id);
    return $profile;
  }
}
