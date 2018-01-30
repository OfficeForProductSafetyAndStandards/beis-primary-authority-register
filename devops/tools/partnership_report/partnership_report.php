<?php

function createRow($key, $line)
{
    global $legalEntities, $adviceTypes, $assoc, $count;
    
    $count ++;
    echo $count . ") " . $key . PHP_EOL;
    
    $legalEntities = array_unique($legalEntities, SORT_REGULAR);
    $adviceTypes = array_unique($adviceTypes, SORT_REGULAR);
    $assoc[] = [
        'partnership_nominated_date' => $line[0],
        'partnership_revoked_date' => $line[1],
        'partnership_status' => $line[2] == 'confirmed_rd' ? 'Active' : $line[2],
        'partnership_type' => ucfirst($line[3]),
        'primary_authority' => $line[4],
        'organisation_name' => $line[5],
        'trading_name' => $line[18],
        'nation' => $line[11],
        'legal_entities' => implode(PHP_EOL, $legalEntities),
        'legal_entity_count' => count($legalEntities),
        'inspection_plan_status' => $line[7] == 'current' ? 'Yes' : 'No',
        'advice_to_business' => in_array('business_advice', $adviceTypes) ? 'Yes' : 'No',
        'advice_to_authority' => in_array('authority_advice', $adviceTypes) ? 'Yes' : 'No',
        'sector' => $line[9],
        'business_size' => $line[10],
    ];
    $legalEntities = [];
    $adviceTypes = [];
    
}
$lines = file('./partnership_report.csv');

$lines = array_unique($lines, SORT_REGULAR);

$assoc = [];

$legalEntities = [];
$adviceTypes = [];
$lastKey = '';
$lastLine = [];
$count = 0;
foreach ($lines as $line) {
    
    $line = str_getcsv($line);
    
    $key = $line[3] . '_' . $line[4] . '_' . $line[5];

    if ($lastKey != $key && $lastKey != '') {
        createRow($lastKey, $lastLine);
    }
    
    if (!empty($line[6]) && empty($line[16]) && empty($line[17])) {
        $legalEntities[] = $line[6];
    }
    
    if (!empty($line[8]) && empty($line[12]) && empty($line[13])) {         
       $adviceTypes[] = $line[8];
    }
    
    $lastKey = $key;
    $lastLine = $line;
}

createRow($lastKey, $lastLine);

$colours = ['#eee', '#fff'];

$s = '';
$lineCount = 0;

$headers = [
    'Partnership Nominated Date',
    'Partnership Revoked Date',
    'Partnership Status',
    'Partnership Type',
    'Primary Authority',
    'Organisation Name',
    'Trading Name',
    'Nation',
    'Legal Entities',
    'Number of Legal Entities',
    'Inspection Plan published',
    'Advice to business',
    'Advice to PA',
    'Business sector',
    'Business Size (Direct)',
];

$csvReport = arrayToCsv($headers, ',');

foreach ($assoc as $row) {
    $stripeColour = $colours[$lineCount++ % 2];
    $s .= '<tr>';
    foreach ($row as $key => $value) {
        $s .= '<td style="background-color:' . $stripeColour . '">' . str_replace(PHP_EOL, '<br>', $value) . '</td>';
        $value = str_replace(PHP_EOL, ';', $value);
    }
    $s .= '</tr>' . PHP_EOL;
    
    $csvReport .= PHP_EOL . arrayToCsv($row, ',');
}

file_put_contents('partnership_report_export.csv', $csvReport . PHP_EOL);

$o = '<a href="partnership_report_export.csv">Download as CSV</a><br>';

$o .= '<table border=1>';
$o .= '<tr>';
foreach ($headers as $header) {
    $o .= '<th>' . $header . '</th>';
}
$o .= '</tr>';
$o .= $s . '</table>';

file_put_contents('./index.html', $o);

/**
 * Formats a line (passed as a fields  array) as CSV and returns the CSV as a string.
 * Adapted from http://us3.php.net/manual/en/function.fputcsv.php#87120
 */
function arrayToCsv( array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');
    
    $output = array();
    foreach ( $fields as $field ) {
        if ($field === null && $nullToMysqlNull) {
            $output[] = 'NULL';
            continue;
        }
        
        // Enclose fields containing $delimiter, $enclosure or whitespace
        if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
            $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        }
        else {
            $output[] = $field;
        }
}

return implode( $delimiter, $output );
}
