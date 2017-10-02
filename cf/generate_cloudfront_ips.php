<?php

$json = file_get_contents('https://ip-ranges.amazonaws.com/ip-ranges.json');

$a = json_decode($json);

$s = '';

foreach ($a->prefixes as $b) {
   $s .= "'" . $b->ip_prefix . "',";
}

file_put_contents('cloudfront_ips.txt', $s);



