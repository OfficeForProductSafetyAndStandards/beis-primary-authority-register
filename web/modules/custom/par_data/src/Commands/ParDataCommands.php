<?php

namespace Drupal\par_data\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\search_api\ConsoleException;
use Drush\Commands\DrushCommands;
use League\Csv\Reader;
use League\Csv\Writer;

/**
 * Drush commands for PAR Data.
 */
class ParDataCommands extends DrushCommands {

  const COMPANIES_HOUSE_IMPORT_FILE = '../data/companies_house_data.csv';
  const CHARITY_COMMISSION_IMPORT_FILE = '../data/charity_commission_data.txt';
  const LEGAL_ENTITY_CONVERSION_WORK = 'legal_entity_conversion_work';
  const LEGAL_ENTITY_CONVERSION_WORK_EXPORT_FILE = '../data/legal_entity_conversion_work.csv';

  /**
   * The par_data.manager service.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The entity_type.manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * ParDataCommands constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par_data.manager service.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entityTypeManager, \Drupal\Core\Database\Connection $database) {
    parent::__construct();
    $this->parDataManager = $par_data_manager;
    $this->entityTypeManager = $entityTypeManager;
    $this->database = $database;
  }

  /**
   * Warm the PAR Data caches.
   *
   * @param string $type
   *   The type of data to be warmed.
   *
   * @validate-module-enabled par_data
   *
   * @command par-data:cache-warm
   * @aliases pcw

   */
  public function cache_warm($type) {
    $count = 0;

    // Can manually choose to warm other caches not listed as defaults.
    if ($type) {
      echo "Warming caches for $type" . PHP_EOL;

      // Warming for selected caches only as it's currently memory intensive.
      foreach ($this->parDataManager->getEntitiesByType($type) as $entity) {
        $count++;
        // By default it's the relationship cache's responsible for entity management we want to warm.
        $entity->getRelationships();

        $unique_function_id = "getRelationships:{$entity->uuid()}:null:null";
        drupal_static_reset($unique_function_id);

        // Assess memory usage.
        if ($count % 100 == 0 && $count > 0) {
          $memory = round(memory_get_usage() / 1024 / 1024, 2);
          $this->output->writeln(dt('@memory MB used in the generation of %count caches', ['@memory' => $memory, '%count' => $count]));
        }
      }

      return "Cache warmed for all $count data entities.";
    }

    return "No caches warmed.";
  }

  /**
   * Check the health of search_api indexes.
   *
   * @param string|null $index
   *   The index id.
   * @param array $options
   *   (optional) An array of options.
   *
   * @throws \Drupal\search_api\ConsoleException
   *   If a batch process could not be created.
   *
   * @validate-module-enabled par_data
   * @validate-module-enabled search_api
   *
   * @command par-data:index-health
   * @option index-health
   *   Whether to check the index health.
   * @aliases pih

   */
  public function index_health($index = NULL, array $options = ['index-health' => NULL]) {
    $include_index_health = $options['index-health'];

    $index_storage = $this->entityTypeManager->getStorage('search_api_index');

    $indexes = $index_storage->loadMultiple([$index]);
    if (!$indexes) {
      return [];
    }

    foreach ($indexes as $index) {
      // Check the health of the server.
      $server_health = $index->isServerEnabled() ? $index->getServerInstance()->hasValidBackend() : FALSE;

      $indexed = $index->getTrackerInstance()->getIndexedItemsCount();
      $total = $index->getTrackerInstance()->getTotalItemsCount();

      $entity_types = $index->getEntityTypes();
      $count = 0;
      foreach ($entity_types as $key => $type) {
        $entity_storage = $this->entityTypeManager->getStorage($type);
        $count += $entity_storage->getQuery()->count()->execute();
      }

      $index_health = (($total == $indexed) && ($indexed == $count));

      if (!$server_health) {
        throw new ConsoleException(dt('Server for index %index is not valid.', ['%index' => $index->id()]));
      }
      if ($include_index_health and !$index_health) {
        throw new ConsoleException(dt('Index %index has only indexed %indexed out of %total items (%count entities).', [
          '%index' => $index->id(),
          '%indexed' => $indexed,
          '%total' => $total,
          '%count' => $count,
        ]));
      }
    }

    return "Index health good.";
  }

  /**
   * The following commands (par-data:legal-entity-convert-*) are all used in process to convert legal entities to
   * use the registered organisations plugins.
   *
   * @todo Remove after conversion complete.
   */

  /**
   * Full legal entity export
   *
   * Exports all LEs as CSV file. Allows data to be examined in a spreadsheet.
   *
   * @command par-data:legal-entity-convert-export-full
   * @aliases plecef

   */
  public function legal_entity_convert_export_full() {

    // Create writer.
    $file_name = 'legal_entity_export_full.csv';
    $writer = Writer::createFromPath('../data/' . $file_name, 'w+');

    // Export all LEs.
    $sql = <<<END
    SELECT led.id AS id,
           led.registered_name AS name,
           led.registry AS registry,
           led.legal_entity_type AS type,
           led.registered_number AS number
    FROM {par_legal_entities} AS le
    INNER JOIN {par_legal_entities_field_data} AS led ON le.id = led.id AND le.revision_id = led.revision_id
    ORDER BY led.id;
    END;
    $result = $this->database->query($sql);

    $cnt = 0;
    $writer->insertOne(['id', 'registered_name', 'registry', 'legal_entity_type', 'registered_number']);
    foreach ($result as $record) {
      $writer->insertOne([$record->id, $record->name, $record->registry, $record->type, $record->number]);
      $cnt++;
    }

    $this->output->writeln("Exported details of $cnt legal entities written to file $file_name.");
    return "Done.";
  }

  /**
   * Report numbers of legal entities yet to be converted.
   *
   * LEs that have not been converted have a null registry field. This command reports
   * how many of these there are for each legal_entity_type.
   *
   * @command par-data:legal-entity-convert-summary
   * @aliases plecs

   */
  public function legal_entity_convert_summary() {

    // For unconverted LEs list all values of legal_entity_type with counts.
    $result = $this->database->query("SELECT led.legal_entity_type AS type, count(*) AS count FROM {par_legal_entities} AS le " .
                                           "INNER JOIN {par_legal_entities_field_data} AS led ON le.id = led.id AND le.revision_id = led.revision_id " .
                                           "WHERE led.registry IS NULL " .
                                           "GROUP BY led.legal_entity_type;");

    $this->output->writeln('Type - Count');
    foreach ($result as $record) {
      $this->output->writeln($record->type . ' - ' . $record->count);
    }

    return "Done.";
  }

  /**
   * Get stats from CH download.
   *
   * @command par-data:legal-entity-convert-companies-house-stats
   * @aliases plecchs

   */
  public function legal_entity_convert_companies_house_stats() {

    $company_categories = [];
    $company_statuses = [];
    $dissolution_date_cnt = 0;
    $total_cnt = 0;

    $csv = Reader::createFromPath(self::COMPANIES_HOUSE_IMPORT_FILE)->setHeaderOffset(0);
    foreach ($csv as $record) {

      if (isset($company_categories[$record['CompanyCategory']])) {
        $company_categories[$record['CompanyCategory']]++;
      }
      else {
        $company_categories[$record['CompanyCategory']] = 1;
      }

      if (isset($company_statuses[$record['CompanyStatus']])) {
        $company_statuses[$record['CompanyStatus']]++;
      }
      else {
        $company_statuses[$record['CompanyStatus']] = 1;
      }

      if (!empty($record['DissolutionDate'])) {
        $dissolution_date_cnt++;
      }

      $total_cnt++;
    }

    unset($csv);

    $this->output->writeln("Counts for company categories");
    foreach ($company_categories as $category => $cnt) {
      $this->output->writeln("$category - $cnt");
    }
    $this->output->writeln("Counts for company status");
    foreach ($company_statuses as $status => $cnt) {
      $this->output->writeln("$status - $cnt");
    }
    $this->output->writeln("Number of companies with a dissolution date - $dissolution_date_cnt");
    $this->output->writeln("Total number of companies - $total_cnt");

    return "Done.";
  }

  /**
   * Extract LEs to new legal_entity_convert_work table.
   *
   * @command par-data:legal-entity-convert-extract
   * @aliases plece

   */
  public function legal_entity_convert_extract() {

    // Drop any previous companies house table.
    $this->database->query("DROP TABLE IF EXISTS " . self::LEGAL_ENTITY_CONVERSION_WORK . ";");

    // Create table.
    $this->database->query("CREATE TABLE " . self::LEGAL_ENTITY_CONVERSION_WORK . " (" .
      "id integer NOT NULL," .
      "par_type varchar(32)," .
      "par_number varchar(255)," .
      "par_name char(500)," .
      "clean_number char(8)," .
      "ch_type varchar(100)," .
      "ch_number char(8)," .
      "ch_name varchar(160)," .
      "cc_type varchar(100)," .
      "cc_number integer," .
      "cc_name varchar(160)" .
      ");");

    // Process all legal entities into the table.
    $sql = <<<END
    SELECT led.id AS id,
           led.registered_name AS name,
           led.legal_entity_type AS type,
           led.registered_number AS number
    FROM {par_legal_entities} AS le
    INNER JOIN {par_legal_entities_field_data} AS led ON le.id = led.id AND le.revision_id = led.revision_id
    ORDER BY led.id;
    END;
    $result = $this->database->query($sql);

    $total_cnt = 0;
    foreach ($result as $le) {

      // Clean up the registered number.
      $clean_number = strtoupper($le->number);
      $clean_number = preg_replace("/[^A-Z0-9]/", '', $clean_number);
      if (empty($clean_number)) {
        $clean_number = '';
      }
      elseif (strlen($clean_number) > 8) {
        $clean_number = '';
      }
      elseif (strlen($clean_number) < 8 && is_numeric($clean_number)) {
        $clean_number = str_pad($clean_number, 8, '0', STR_PAD_LEFT);
      }
      elseif (strlen($clean_number) < 8 && ctype_alpha(substr($clean_number, 0, 2))) {
        $prefix = substr($clean_number, 0, 2);
        $rest = substr($clean_number, 2);
        $rest = str_pad($rest, 6, '0', STR_PAD_LEFT);
        $clean_number = $prefix . $rest;
      }

      // Write to database.
      $this->database->insert(self::LEGAL_ENTITY_CONVERSION_WORK)
        ->fields([
          'id' => $le->id,
          'par_type' => trim($le->type),
          'par_number' => trim($le->number),
          'par_name' => trim($le->name),
          'clean_number' => $clean_number,
        ])
        ->execute();

      // Report progress.
      $total_cnt++;
      if (($total_cnt % 100) == 0) {
        $this->output->writeln("Processed $total_cnt legal entities.");
      }
    }

    // Create indexes on the id and clean_number columns.
    $this->database->query("CREATE INDEX id ON " . self::LEGAL_ENTITY_CONVERSION_WORK . " (id);");
    $this->database->query("CREATE INDEX clean_number ON " . self::LEGAL_ENTITY_CONVERSION_WORK . " (clean_number);");

    $this->output->writeln("Total number of legal entities processed - $total_cnt");

    return "Done.";
  }

  /**
   * Merge Companies House data into new legal_entity_convert_work table.
   *
   * @command par-data:legal-entity-convert-companies-house-merge
   * @aliases plecchm

   */
  public function legal_entity_convert_companies_house_merge() {

    // Process the Companies House file.
    $total_cnt = 0;
    $found_cnt = 0;
    $csv = Reader::createFromPath(self::COMPANIES_HOUSE_IMPORT_FILE)->setHeaderOffset(0);
    foreach ($csv as $record) {

      // Lookup this company in the work table.
      $sql = "SELECT id FROM " . self::LEGAL_ENTITY_CONVERSION_WORK . " WHERE clean_number = '" . $record[' CompanyNumber'] . "';";
      $query = $this->database->query($sql);
      $id = $query->fetchField();

      if (!empty($id)) {
        $this->database->update(self::LEGAL_ENTITY_CONVERSION_WORK)
          ->fields([
            'ch_type' => $record['CompanyCategory'],
            'ch_number' => $record[" CompanyNumber"], // Yes, really has a leading space!
            'ch_name' => $record['CompanyName'],
          ])
          ->condition('id', $id)
          ->execute();

        $found_cnt++;
      }
      elseif ($record[" CompanyNumber"][7] == 'R') { // Is aannnnnR

        // Lookup this company in the work table usingnumber without 2 char prefix.
        $sql = "SELECT id FROM " . self::LEGAL_ENTITY_CONVERSION_WORK . " WHERE clean_number = '" . substr($record[' CompanyNumber'], 2) . "';";
        $query = $this->database->query($sql);
        $id = $query->fetchField();

        if (!empty($id)) {
          $this->database->update(self::LEGAL_ENTITY_CONVERSION_WORK)
            ->fields([
              'ch_type' => $record['CompanyCategory'],
              'ch_number' => $record[" CompanyNumber"], // Yes, really has a leading space!
              'ch_name' => $record['CompanyName'],
            ])
            ->condition('id', $id)
            ->execute();

          $found_cnt++;
        }
      }

      // Report progress.
      $total_cnt++;
      if (($total_cnt % 100) == 0) {
        $this->output->writeln("Processed $total_cnt, found $found_cnt.");
      }

    }

    $this->output->writeln("Total processed $total_cnt, total found $found_cnt.");

    return "Done.";
  }

  /**
   * Merge Charity Commission data into new legal_entity_convert_work table.
   *
   * @command par-data:legal-entity-convert-charity-commission-merge
   * @aliases plecccm

   */
  public function legal_entity_convert_charity_commission_merge() {

    // Process the Charity Commission file.
    $total_cnt = 0;
    $found_cnt = 0;
    $csv = Reader::createFromPath(self::CHARITY_COMMISSION_IMPORT_FILE);
    $csv->setDelimiter("\t");
    $csv->setHeaderOffset(0);
    foreach ($csv as $record) {

      // We only want the main charities.
      if (!empty($record['linked_charity_number'])) {
        continue;
      }

      $charity_number = str_pad($record['registered_charity_number'], 8, '0', STR_PAD_LEFT);

      // Lookup this charity in the work table.
      $sql = "SELECT id FROM " . self::LEGAL_ENTITY_CONVERSION_WORK . " WHERE clean_number = '" . $charity_number . "' AND par_type = 'registered_charity';";
      $query = $this->database->query($sql);
      $id = $query->fetchField();

      if (!empty($id)) {
        $this->database->update(self::LEGAL_ENTITY_CONVERSION_WORK)
          ->fields([
            'cc_type' => $record['charity_type'],
            'cc_number' => $record["registered_charity_number"],
            'cc_name' => $record['charity_name'],
          ])
          ->condition('id', $id)
          ->execute();

        $found_cnt++;
      }

      // Report progress.
      $total_cnt++;
      if (($total_cnt % 100) == 0) {
        $this->output->writeln("Processed $total_cnt, found $found_cnt.");
      }
    }

    $this->output->writeln("Total processed $total_cnt, total found $found_cnt.");

    return "Done.";
  }

  /**
   * Export the legal_entity_conversion_work table to CSV.
   *
   * @command par-data:legal-entity-convert-work-export
   * @aliases plecwe
   */
  public function legal_entity_convert_work_export() {

    // Create writer.
    $writer = Writer::createFromPath(self::LEGAL_ENTITY_CONVERSION_WORK_EXPORT_FILE, 'w+');

    // Column names.
    $col_names = [
      'id',
      'par_type', 'par_number', 'par_name',
      'clean_number',
      'ch_type', 'ch_number', 'ch_name',
      'cc_type', 'cc_number', 'cc_name',
    ];

    // Export all records.
    $sql = "SELECT " . implode(', ', $col_names) . " " .
           "FROM " . self::LEGAL_ENTITY_CONVERSION_WORK . " " .
           "ORDER BY id;";
    $result = $this->database->query($sql);

    $cnt = 0;
    $writer->insertOne($col_names);
    while ($record = $result->fetchAssoc()) {
      $writer->insertOne(array_values($record));
      $cnt++;
    }

    $this->output->writeln("Exported details of $cnt rows written to file " . self::LEGAL_ENTITY_CONVERSION_WORK_EXPORT_FILE . ".");
    return "Done.";
  }

  /**
   * Import changes from the legal_entity_convert_work table.
   *
   * @command par-data:legal-entity-convert-update
   *
   * @aliases plecu
   *
   */
  public function legal_entity_convert_update() {

    // Mapping of existing PAR legal entity types to internal registry types.
    $par_types_internal = [
      'partnership' => 'partnership',
      'sole_trader' => 'sole_trader',
      'Partnership' => 'partnership',
      'Sole Trader' => 'sole_trader',
      'other' => 'other',
    ];

    // Maps the types in the CH download to the values used in the API.
    // I expect these are different for 'legacy' reasons, but it is annoying.
    $types = [
      'PRI/LTD BY GUAR/NSC (Private, limited by guarantee, no share capital)' => 'private-limited-guarant-nsc-limited-exemption',
      'Private Limited Company' => 'ltd',
      'Other company type' => 'other',
      'Charitable Incorporated Organisation' => 'charitable-incorporated-organisation',
      'Community Interest Company' => 'other',
      'Registered Society' => 'registered-society-non-jurisdictional',
      'Limited Partnership' => 'limited-partnership',
      'Limited Liability Partnership' => 'llp',
      'Royal Charter Company' => 'royal-charter',
      'PRI/LBG/NSC (Private, Limited by guarantee, no share capital, use of \'Limited\' exemption)' => 'private-limited-guarant-nsc-limited-exemption',
      'Public Limited Company' => 'plc',
      'Scottish Charitable Incorporated Organisation' => 'scottish-charitable-incorporated-organisation',
      'Overseas Entity' => 'registered-overseas-entity',
      'Private Unlimited Company' => 'private-unlimited-nsc',
      'Old Public Company' => 'old-public-company',
      'Private Unlimited' => 'private-unlimited',
      'Scottish Partnership' => 'scottish-partnership',
      'Investment Company with Variable Capital (Securities)' => 'icvc-securities',
      'Investment Company with Variable Capital' => 'icvc-warrant',
      'Industrial and Provident Society' => 'industrial-and-provident-society',
      'United Kingdom Economic Interest Grouping' => 'ukeig',
      'Investment Company with Variable Capital(Umbrella)' => 'icvc-umbrella',
      'PRIV LTD SECT. 30 (Private limited company, section 30 of the Companies Act)' => 'private-limited-shares-section-30-exemption',
      'Converted/Closed' => 'converted-or-closed',
      'United Kingdom Societas' => 'united-kingdom-societas',
      'Protected Cell Company' => 'protected-cell-company',
      'Other Company Type' => 'other',
      'Further Education and Sixth Form College Corps' => 'further-education-or-sixth-form-college-corporation',
    ];

    // Read all rows from the conversion work table.
    $total_cnt = 0;
    $already_processed_cnt = 0;
    $empty_ignored_cnt = 0;
    $matched_cc_cnt = 0;
    $matched_ch_cnt = 0;
    $set_to_internal_cnt = 0;
    $set_to_legacy_cnt = 0;
    $sql = "SELECT * FROM " . self::LEGAL_ENTITY_CONVERSION_WORK . " ORDER BY id";
    $result = $this->database->query($sql);
    foreach ($result as $row) {

      $total_cnt++;
      if (($total_cnt % 100) == 0) {
        $this->output->writeln("Processed $total_cnt.");
      }

      /* @var \Drupal\par_data\Entity\ParDataLegalEntity $legalEntity */
      $legalEntity = $this->entityTypeManager->getStorage('par_data_legal_entity')->load($row->id);

      // This LE has already been processed by a previous run, so we ignore it.
      // Allows us to do multiple passes. Clear the registry field of an LE to have it reprocessed.
      if (!empty($legalEntity->getRegistry())) {
        $already_processed_cnt++;
        continue;
      }

      // Just ignore empty LEs. These need to be manually removed from PAR.
      if (empty($row->par_type) && empty($row->par_number) && empty($row->par_name)) {
        $empty_ignored_cnt++;
        continue;
      }

      // Matched to charity commission download data.
      if (!empty($row->cc_type)) {
        $legalEntity->set('registry', 'charity_commission');
        $legalEntity->set('registered_name', $row->cc_name);
        $legalEntity->set('legal_entity_type', $row->cc_type);
        $legalEntity->set('registered_number', $row->cc_number);
        $matched_cc_cnt++;
      }

      // Matched to companies house download data.
      elseif (!empty($row->ch_type) && isset($types[$row->ch_type])) {
        $legalEntity->set('registry', 'companies_house');
        $legalEntity->set('registered_name', $row->ch_name);
        $legalEntity->set('legal_entity_type', $types[$row->ch_type]);
        $legalEntity->set('registered_number', $row->ch_number);
        $matched_ch_cnt++;
      }

      // LEs that are internal; unregistered partnership, sole traders etc.
      elseif (isset($par_types_internal[$row->par_type])) {
        $legalEntity->set('registry', 'internal');
        $legalEntity->set('legal_entity_type', $par_types_internal[$row->par_type]);
        $set_to_internal_cnt++;
      }

      // Everything else is marked as legacy.
      else {
        $legalEntity->set('registry', 'legacy');
        $set_to_legacy_cnt++;
      }

      $legalEntity->save();
    }

    $this->output->writeln("Total number of legal entities processed from work table - $total_cnt");
    $this->output->writeln("Legal entities ignored as already processed - $already_processed_cnt");
    $this->output->writeln("Legal entities ignored as being completly blank - $empty_ignored_cnt");
    $this->output->writeln("Legal entities matched to Charity Commission - $matched_cc_cnt");
    $this->output->writeln("Legal entities matched to Companies House - $matched_ch_cnt");
    $this->output->writeln("Legal entities set as internal - $set_to_internal_cnt");
    $this->output->writeln("Legal entities set as legacy - $set_to_legacy_cnt");

    return "Done.";
  }

  /**
   * End of par-data:legal-entity-convert-* commands.
   */
}
