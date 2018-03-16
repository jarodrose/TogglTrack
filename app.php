<?php

require __DIR__.'/src/TogglTrack.php';
require __DIR__.'/src/YouTrack.php';

date_default_timezone_set('America/New_York');

$username = $_POST['username'];
$pasword = base64_encode($_POST['password']);
$apiToken = $_POST['apiToken'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

$morningTimestamp = strtotime('00:00:00');
$midnightTimestamp = strtotime("23:59:59");

if ($startDate) {
    $start = date(DATE_ATOM, strtotime($startDate));
    $end = date(DATE_ATOM, strtotime($_POST['endDate'] . '23:59:59')); //Needs to be a string here
} else {
    $start = date(DATE_ATOM, $morningTimestamp);
    $end = date(DATE_ATOM, $midnightTimestamp);
}

$togglTrack = new TogglTrack($apiToken, $start, $end);
$youtrack = new YouTrack($username, $pasword);
$timeEntries = $togglTrack->getTimeEntries();
$parsedEntries = $togglTrack->parseEntries($timeEntries);

if ($parsedEntries) {
    $result = $youtrack->import($parsedEntries);

    if ($result) {
        echo json_encode($result);
    }
}else {
    echo 'No time found';
}