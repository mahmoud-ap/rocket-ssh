<?php

function container()
{
    global $app;
    return $app->getContainer();
}

function db($table = null)
{

    $db = container()->db;
    if (!empty($table)) {
        return $db->table($table);
    }
    return $db;
}

function validator()
{
    return container()->validator;
}


function session()
{
    return container()->session;
}


function baseUrl($uri = '', $protocol = NULL)
{
    $base_url = slashItem('url');
    if (isset($protocol)) {
        if ($protocol === '') {
            $base_url = substr($base_url, strpos($base_url, '//'));
        } else {
            $base_url = $protocol . substr($base_url, strpos($base_url, '://'));
        }
    }
    return $base_url . uriString($uri);
}


function assets($path = '')
{
    return baseUrl("assets/$path");
}


function slashItem($item)
{
    $configValue = getConfig($item);
    if (!$configValue) {
        return NULL;
    } elseif (trim($configValue) === '') {
        return '';
    }

    return rtrim($configValue, '/') . '/';
}

function uriString($uri)
{

    if (is_array($uri)) {
        return http_build_query($uri);
    }

    is_array($uri) && $uri = implode('/', $uri);
    return ltrim($uri, '/');
}


function getArrayValue($array, $key, $defaultVal = "")
{

    if (is_object($array)) {
        $array = (array)$array;
    }
    if (!empty($array) && is_array($array)) {
        if (isset($array[$key])) {
            return $array[$key];
        }
    }
    return $defaultVal;
}

function trimArrayValues($array)
{
    foreach ($array as $key => $value) {
        if (!is_array($value) && !is_object($value)) {
            $$value = trim($value);
        }
        $array[$key] = $value;
    }
    return $array;
}

function trafficToGB($traffic)
{
    return round($traffic / 1024, 3);
}


function getIPAddress()
{
    $ip = "";
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function convertToPrettyUnit($value, $unit = "")
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $base = 1024;
    $index = 0;

    // Find the appropriate unit to use
    while ($value >= $base && $index < count($units) - 1) {
        $value /= $base;
        $index++;
    }

    // Construct the pretty unit format
    if (empty($unit)) {
        $prettyUnit = $units[$index];
    } else {
        $prettyUnit = $unit . ' ' . $units[$index];
    }

    // Return the formatted value with the appropriate unit
    return number_format($value, 2) . ' ' . $prettyUnit;
}

function getUsageColor($usagePercent)
{
    // Convert the percentage to a numeric value without the percentage sign
    $usageValue = floatval(str_replace('%', '', $usagePercent));

    // Define color ranges and their corresponding usage percentage
    $colorRanges = array(
        10 => '#00FF00',   // Green - Less than 10% usage
        30 => '#FFFF00',   // Yellow - Less than 30% usage
        70 => '#FFA500',   // Orange - Less than 70% usage
        100 => '#FF0000',  // Red - 100% usage or more
    );

    // Find the appropriate color for the usage percentage
    foreach ($colorRanges as $range => $color) {
        if ($usageValue <= $range) {
            return $color;
        }
    }

    // Return the last color if the usage percentage is higher than the highest range
    return end($colorRanges);
}

function getContrastTextColor($hexcolor)
{
    list($red, $green, $blue) = sscanf($hexcolor, "#%02x%02x%02x");
    $luma = ($red + $green + $blue) / 3;

    if ($luma < 128) {
        $textcolour = "white";
    } else {
        $textcolour = "black";
    }
    return $textcolour;
}

function truncateStr($string, $maxLength = 20)
{
    if (strlen($string) <= $maxLength) {
        return $string;
    } else {
        return substr($string, 0, $maxLength) . '...';
    }
}



