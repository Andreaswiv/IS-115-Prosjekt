<?php
// functions.php

/**
 * Sanitize a variable by stripping tags, converting to HTML entities, and trimming whitespace.
 *
 * @param string $variable The input variable to sanitize.
 * @return string The sanitized variable.
 */
function sanitize($variable)
{
    $variable = strip_tags($variable);
    $variable = htmlentities($variable);
    $variable = trim($variable);
    return $variable;
}

function errorHandling()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

?>

