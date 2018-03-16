<?php

require __DIR__.'/../vendor/autoload.php';
use MorningTrain\TogglApi\TogglApi;

/*
 * Using Toggl API and YouTrack API for easy time importing.
 */

class TogglTrack {

    /**
     * @var bool|mixed|object
     */
    private $timeEntries;

    /**
     * TogglTrack constructor.
     * @param $token
     * @param null $startDate
     * @param null $endDate
     */
    public function __construct($token, $startDate = null, $endDate = null) {
        $toggl = new TogglApi($token);
        $this->timeEntries = $toggl->getTimeEntriesInRange($startDate, $endDate);
    }

    /**
     * Getting entries for the current day
     *
     * @param $timeEntries
     * @return array
     */
    public function getTimeToImport($timeEntries) {
        $timeToEnter = array();
        date_default_timezone_set('America/New_York');
        $todaysDate = strtotime(date('Y-m-d', time()));

        foreach ($timeEntries as $timeEntry) {
            $startTime = preg_split('/[T\+]+/', $timeEntry->start);

            if ($todaysDate == $startTime[0]) {
                $timeToEnter[] = $timeEntry;
            }
        }

        return $timeToEnter;
    }

    /**
     * Parses the toggle description into ticket number and description for importing into YouTrack.
     *
     * @param $timeEntries
     * @return array|null
     */
    public function parseEntries($timeEntries) {
        if (empty($timeEntries)) {
            return null;
        }

        $parsedEntries = array();

        foreach($timeEntries as $timeEntry) {
            $description = explode('::', $timeEntry->description);
            $splitDescription = preg_split('/[\s]/', $description[0]);
            $ticket = $splitDescription[0];
            $minutes = floor($timeEntry->duration / 60);
            $date = preg_split('/T/', $timeEntry->start);
            $date = date('Y-m-d', strtotime($date[0]));

            if (!$timeEntry->billable) {
                $type = $description[2];
            }else {
                $type = $timeEntry->billable;
            }

            if (array_key_exists($ticket, $parsedEntries) && (strcmp($parsedEntries[$ticket]['description'], $description[1]) == 0)) {
                $existingEntry = $parsedEntries[$ticket];
                $rawTime = explode('m', $existingEntry['duration']);
                $minutes += $rawTime[0];
                $parsedEntries[$ticket]['duration'] = $minutes.'m';
            }else {
                if (array_key_exists($ticket, $parsedEntries) && (strcmp($parsedEntries[$ticket]['description'], $description[1]) != 0)) {
                    $parsedEntries[] = array(
                        'type' => $type,
                        'date' => $date,
                        'duration' => $minutes . 'm',
                        'description' => $description[1],
                        'ticket' => $ticket
                    );
                }else {
                    $parsedEntries[$ticket] = array(
                        'type' => $type,
                        'date' => $date,
                        'duration' => $minutes . 'm',
                        'description' => $description[1],
                        'ticket' => $ticket
                    );
                }
            }
        }

        if (!empty($separateEntries)) {
            $parsedEntries[] = $separateEntries;
        }

        return $parsedEntries;
    }

    /**
     * @return bool|mixed|object
     */
    public function getTimeEntries() {
        return $this->timeEntries;
    }

    /**
     * @param $token
     */
    public function setApiToken($token) {
        $this->apiToken = $token;
    }

}