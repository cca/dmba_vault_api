<?php

/**
 * @param array options The parsed query string parameters.
 * @return boolean|array errors Returns False if there are no errors, otherwise returns an array of error message strings.
 */

function validate ($options) {
    $errors = array();

    // EQUELLA API limitation: length cannot be over 50
    if (isset($options['length']) && $options['length'] > 50) {
        $errors[] = '"length" parameter cannot be greater than 50';
    }

    // ensure a valid UUID is being used for "id" query
    if (isset($options['id'])) {
        if (!preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $options['id'])) {
            $errors[] = '"id" parameter is not a valid UUID';
        }
    }

    // ensure a valid semester string is being used
    if (isset($options['semester'])) {
        if (!preg_match('/(Spring|Summer|Fall) [0-9]{4}/', $options['semester'])) {
            $errors[] = '"semester" parameter is not a valid term';
        }
    }

    if (sizeof($errors) > 0) {
        return $errors;
    } else {
        return False;
    }
}
