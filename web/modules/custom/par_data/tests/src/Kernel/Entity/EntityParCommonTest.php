<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\Entity\ParDataRegulatoryFunction;
use Drupal\par_data\Entity\ParDataSicCode;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests common functionality on all entities.
 *
 * @group PAR Data
 */
class EntityParCommonTest extends ParDataTestBase {

  /**
   * Test to validate an authority entity.
   */
  public function testEntityLabels() {
    // Get the advice label.
    $entity = ParDataAdvice::create($this->getAdviceValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Advice label fits within the required length.');

    // Get the authority label.
    $entity = ParDataAuthority::create($this->getAuthorityValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Authority label fits within the required length.');

    // Get the enforcement notice label.
    $entity = ParDataEnforcementNotice::create($this->getEnforcementNoticeValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Enforcement Notice label fits within the required length.');

    // Get the enforcement action label.
    $entity = ParDataEnforcementAction::create($this->getEnforcementActionValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Enforcement Action label fits within the required length.');

    // Get the inspection plan label.
    $entity = ParDataInspectionPlan::create($this->getInspectionPlanValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Inspection Plan label fits within the required length.');

    // Get the legal entity label.
    $entity = ParDataLegalEntity::create($this->getLegalEntityValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Legal Entity label fits within the required length.');

    // Get the organisation label.
    $entity = ParDataOrganisation::create($this->getOrganisationValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Organisation label fits within the required length.');

    // Get the coordinated business label.
    $entity = ParDataCoordinatedBusiness::create($this->getCoordinatedBusinessValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Coordinated Business label fits within the required length.');

    // Get the partnership label.
    $entity = ParDataPartnership::create($this->getDirectPartnershipValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Partnership label fits within the required length.');

    // Get the person label.
    $entity = ParDataPerson::create($this->getPersonValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Person label fits within the required length.');

    // Get the premises label.
    $entity = ParDataPremises::create($this->getPremisesValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Premises label fits within the required length.');

    // Get the regulatory function label.
    $entity = ParDataRegulatoryFunction::create($this->getRegulatoryFunctionValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'Regulatory Function label fits within the required length.');

    // Get the SIC code label.
    $entity = ParDataSicCode::create($this->getSicCodeValues());
    $label_length = strlen($entity->label());
    $this->assertTrue(($label_length > 5 && $label_length < 500), 'SIC Code label fits within the required length.');
  }
}
