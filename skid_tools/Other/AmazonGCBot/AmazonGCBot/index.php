<?php

$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$strings = [];
$num_to_gen = 500;
$random_string_length = 14;
for($i=0;$i<$num_to_gen;$i++){
 $string = '';
 $max = strlen($characters) - 1;
 for ($j = 0; $j < $random_string_length; $j++) {
  $string .= $characters[mt_rand(0, $max)];
 }
 $strings[] = $string;
}

Echo '<pre>';

foreach($strings as $str) { echo $str . "\r\n"; }