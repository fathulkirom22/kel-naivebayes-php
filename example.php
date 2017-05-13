<?php 
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

include "lib-NaiveBayes.php";

$data_test = NaiveBayes::json_pacth('data_test.json'); // data test
$data_train = NaiveBayes::json_pacth('data_train.json'); // data training
$variableDependen = array("kelamin","ipk","status","pernikahan"); // variable dependen
$variableIndependen = "keterangan"; // variable independen

/**
*
*@traning
**/
NaiveBayes::train($variableIndependen,$variableDependen,$data_train);

/**
*
*@testing
**/
print_r(NaiveBayes::testing($data_test));
print_r(NaiveBayes::testing1($data_test));
print_r(NaiveBayes::test($data_test));





?>