<?php

class YouTrack {

    CONST URL = 'https://envalo.myjetbrains.com/';
    CONST COMMAND_PATH = 'youtrack/rest/issue/';
    CONST SUBMIT_PATH = 'youtrack/rest/issue?';

    /**
     * @var string $_username
     */
    private $_username;

    /**
     * @var string $_password
     */
    private $_password;

    /**
     * YouTrack constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * Establishes a curl connection to YouTrack
     *
     * @param $url
     * @return resource
     */
    public function connect($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_username . ":" . base64_decode($this->_password));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        return $ch;
    }

    /**
     * Imports time entries into YouTrack
     *
     * @param $timeToImport
     * @return array
     */
    public function import($timeToImport) {

        $ch = null;
        $results = array();
        $errors = array();

        foreach ($timeToImport as $time) {
            $url = self::URL . self::COMMAND_PATH . $time['ticket'] . '/execute?';
            $add = "add work " . $time['type'] .' '. $time['date'] .' '. $time['duration'] .' '. $time['description'];
            $command = array(
                'command' => $add
            );
            $command = http_build_query($command);

            $ch = $this->connect($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $command);
            curl_setopt($ch, CURLOPT_POST, 1);

            $result = curl_exec($ch);
            $error = $this->checkResult($result);

            if ($error) {
                $time['error'] = $error;
                $results['failed'][] = $time;
            }else {
                $results['submitted'][] = $time;
            }
        }

        if (isset($ch)) {
            curl_close($ch);
        }

        return $results;
    }

    /**
     * @param $result
     * @return null|SimpleXMLElement
     */
    public function checkResult($result) {

        $xml = null;

        if ($result) {
            $xml = new SimpleXMLElement($result);
            $xml = (array)$xml;
        }

        return $xml[0];
    }
}