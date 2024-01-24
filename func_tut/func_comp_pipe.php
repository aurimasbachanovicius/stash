<?php
$pipe = fn(mixed $value, callable ...$functions) => array_reduce($functions, fn(mixed $carry, callable $function) => $function($carry), $value);

$filterActiveUsers = fn(string $data): string => $data . ' - Active Users Filtered';
$sortData = fn(string $data): string => $data . ' - Data Sorted';
$filterBad = fn(string $data): string => $data . ' - Bad Data Filtered';
$prepareReport = fn(string $data): string => $data . ' - Report Prepared';

echo $pipe('testingData', $filterActiveUsers, $sortData, $filterBad, $prepareReport);
echo "\n";
echo $pipe('testingData', $filterActiveUsers, $prepareReport, $sortData, $filterBad);
