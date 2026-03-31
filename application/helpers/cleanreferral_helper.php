<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function cleanReferral($url)
{
    $urlClean = implode('.', array_slice(explode('.', parse_url($url, PHP_URL_HOST)), -2));
    if ($urlClean == null) {
        return $url;
    }
    return $urlClean;
}