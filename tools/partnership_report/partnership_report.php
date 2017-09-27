<?php

$lines = file('./partnership_report.csv');

$totalLines = count($lines);

$colours = ['#ddd', '#fff'];

$s = '';
$lineCount = 0;
foreach ($lines as $line) {
    $stripeColour = $colours[$lineCount++ % 2];
    $csv = str_getcsv($line);
    $s .= '<tr>';
    foreach ($csv as $c) {
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
$o .= '</tr>';
$o .= $s . '</table>';

file_put_contents('./index.html', $o);
