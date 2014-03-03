<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function nolan_col_ends_with($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}