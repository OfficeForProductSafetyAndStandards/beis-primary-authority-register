<?php

$lines = file('./partnership_report.csv');

$lines = array_unique($lines, SORT_REGULAR);

$assoc = [];

$legalEntities = [];
$lastKey = '';
foreach ($lines as $line) {
    $line = str_getcsv($line);
    $key = $line[4] . '_' . $line[5];
    
    if ($lastKey != $key && $lastKey != '') {
        $assoc[] = [
            'partnership_nominated_date' => $line[0],
            'partnership_revoked_date' => $line[1],
            'partnership_status' => $line[2],
            'partnership_type' => $line[3],
            'primary_authority' => $line[4],
            'organisation_name' => $line[5],
            'legal_entities' => implode(PHP_EOL, $legalEntities),
            'legal_entity_count' => count($legalEntities),
            'inspection_plan_status' => $line[7],
            'advice_type' => $line[8],
            'sector' => $line[9],
            'business_size' => $line[10],
        ];
        $legalEntities = [];
    } else {
        $legalEntities[] = $line[6];
    }
    $lastKey = $key;
}

$colours = ['#eee', '#fff'];

$s = '';
$lineCount = 0;

foreach ($assoc as $row) {
    $stripeColour = $colours[$lineCount++ % 2];
    $s .= '<tr>';
    foreach ($row as $key => $value) {
        $value = str_replace(PHP_EOL, '<br>', $value);
        $s .= '<td style="background-color:' . $stripeColour . '">' . $value . '</td>';
    }
    $s .= '</tr>' . PHP_EOL;
}

$o = '<a href="partnership_report.csv">Download as CSV</a><br>';

$o .= '<table border=1>';
$o .= '<tr>';
$o .= '<th>Partnership Nominated Date</th>';
$o .= '<th>Partnership Revoked Date</th>';
$o .= '<th>Partnership Status</th>';
$o .= '<th>Partnership Type</th>';
$o .= '<th>Primary Authority</th>';
$o .= '<th>Organisation Name</th>';
$o .= '<th>Legal Entities</th>';
$o .= '<th>Number of Legal Entities</th>';
$o .= '<th>Inspection Plan published</th>';
$o .= '<th>Advice published</th>';
$o .= '<th>Business sector (Direct)</th>';
$o .= '<th>Business Size (Direct)</th>';
$o .= '</tr>';
$o .= $s . '</table>';

file_put_contents('./index.html', $o);
