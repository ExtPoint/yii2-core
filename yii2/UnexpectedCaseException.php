<?php

namespace extpoint\yii2;

class UnexpectedCaseException extends Exception {

    public function __construct() {

        $backtrace = debug_backtrace(0, 2);

        if (isset($backtrace[1]['class'])) {
            parent::__construct('Unexpected case in ' . $backtrace[1]['class'] . '::' . $backtrace[1]['function']);
        }
        else {
            parent::__construct('Unexpected case');
        }
    }
}
