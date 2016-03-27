<?php

namespace extpoint\base;

class NotImplementedException extends Exception {

    public function __construct() {

        $backtrace = debug_backtrace(0, 2);

        if (isset($backtrace[1]['class'])) {
            parent::__construct('Not implemented yet. Called at ' . $backtrace[1]['class'] . '::' . $backtrace[1]['function']);
        }
        else {
            parent::__construct('Not implemented');
        }
    }
}
