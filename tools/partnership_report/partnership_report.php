<?php

$originalLines = file('./partnership_report.csv');

$totalLines = count($originalLines);

$colours = ['#ddd', '#fff'];

$lines = [];
$lastKey = '';
$legalEntityNames = [];
$lastCsv = null;

foreach ($originalLines as $line) {
    $csv = str_getcsv($line);
    $key = $csv[4] . '_' . $csv[5];
    
    if ($key == $lastKey) {
        $legalEntityNames[] = $csv[6];
    } else {
        if ($lastCsv) {
            $lastCsv[7] = count($legalEntityNames);
            $lastCsv[6] = implode(PHP_EOL, $legalEntityNames);
            $lines[] = $lastCsv;
        }
        
        $lastKey = $key;
        $legalEntityNames = [$csv[6]];
        $lastCsv = $csv;
    }
}

$s = '';
$lineCount = 0;

foreach ($lines as $line) {
    
    $stripeColour = $colours[$lineCount++ % 2];
    $s .= '<tr>';
    foreach ($line as $c) {
        $c = str_replace(PHP_EOL, '<br>', $c);
        $s .= '<td style="background-color:' . $stripeColour . '">' . $c . '</td>';
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
$o .= '<th>No. of legal entities (Co-ord)</th>';
$o .= '<th>Inspection Plan published</th>';
$o .= '<th>Advice published</th>';
$o .= '<th>Business sector (Direct)</th>';
$o .= '<th>Business Size (Direct)</th>';
$o .= '</tr>';
$o .= $s . '</table>';

file_put_contents('./index.html', $o);
