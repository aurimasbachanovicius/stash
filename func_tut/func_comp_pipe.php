<?php
$pipe = fn($value, ...$functions) => array_reduce($functions, fn($carry, $function) => $function($carry), $value);

$filterActiveUsers = fn($data) => $data . ' - Active Users Filtered';
$sortData = fn($data) => $data . ' - Data Sorted';
$filterBad = fn($data) => $data . ' - Bad Data Filtered';
$prepareReport = fn($data) => $data . ' - Report Prepared';

$result = $pipe('testingData',
    $filterActiveUsers,
    $sortData,
    $filterBad,
    $prepareReport,
);

echo $result;