<?php

/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */
$toEnqueueArr = array();

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





function enqueueStyle($fileAddr, $inFooter = false)
{
    if (!empty($fileAddr)) {
        global $toEnqueueArr;
        if ($inFooter) {
            $toEnqueueArr['styles']['footer'][] = trim($fileAddr);
        } else {
            $toEnqueueArr['styles']['header'][] = trim($fileAddr);
        }
    }
}

function enqueueStyleHeader($fileAddr)
{
    enqueueStyle($fileAddr, false);
}

function enqueueStyleFooter($fileAddr)
{
    enqueueStyle($fileAddr, true);
}

function enqueueScript($fileAddr, $inFooter = false)
{
    if (!empty($fileAddr)) {
        global $toEnqueueArr;
        if ($inFooter) {
            $toEnqueueArr['scripts']['footer'][] = trim($fileAddr);
        } else {
            $toEnqueueArr['scripts']['header'][] = trim($fileAddr);
        }
    }
}

function enqueueScriptHeader($fileAddr)
{
    enqueueScript($fileAddr, false);
}

function enqueueScriptFooter($fileAddr)
{
    enqueueScript($fileAddr, true);
}

function printStyleTag($fileAddr)
{
    if (!empty($fileAddr)) {
        echo '<link rel="stylesheet" href="' . trim($fileAddr) . '" type="text/css" />' . "\n";
    }
}

function printScriptTag($fileAddr)
{
    if (!empty($fileAddr)) {
        echo '<script type="text/javascript" src="' . trim($fileAddr) . '"></script>' . "\n";
    }
}

function headerFiles()
{
    global $toEnqueueArr;
    if (!empty($toEnqueueArr['styles']['header'])) {
        foreach ($toEnqueueArr['styles']['header'] as $styleFile) {
            printStyleTag($styleFile);
        }
    }
    if (!empty($toEnqueueArr['scripts']['header'])) {
        foreach ($toEnqueueArr['scripts']['header'] as $scriptFile) {
            printScriptTag($scriptFile);
        }
    }
}

function footerFiles()
{
    global $toEnqueueArr;
    if (!empty($toEnqueueArr['styles']['footer'])) {
        foreach ($toEnqueueArr['styles']['footer'] as $styleFile) {
            printStyleTag($styleFile);
        }
    }
    if (!empty($toEnqueueArr['scripts']['footer'])) {
        foreach ($toEnqueueArr['scripts']['footer'] as $scriptFile) {
            printScriptTag($scriptFile);
        }
    }
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


function userIPAddress()
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

function getServerIp()
{
    return !empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "";
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



function getSessionUser()
{
    if (session()->get("userInfo")) {
        return  (array) session()->get('userInfo');
    }

    return false;
}


function viewContentPath($viewFile)
{
    return PATH_APP . DS . "Views" . DS . $viewFile;
}

function loadViewSection($path)
{
    $fullPath = PATH_APP . DS . "Views" . DS . $path;
    if (file_exists($fullPath)) {
        include $fullPath;
    }
}


function inlineIcon($iconName, $customClass = "")
{
    $html = "<i class='far fa-$iconName icon $customClass'></i>";
    return  $html;
}

function getAdminRole()
{
    return  container()->userInfo->role;
}

function getAdminUsername()
{
    return  container()->userInfo->username;
}

function getCurrentDate()
{
    return  jdate()->format("d F Y");
}

function generateNetmodQR($userInfo)
{

    $username   = $userInfo->username;
    $password   = $userInfo->password;
    $serverIp   = getServerIp();
    $port       = getenv("SSH_PORT");

    $str = "chl=ssh://$username:$password@$serverIp:$port";
    $url = 'https://chart.googleapis.com/chart?cht=qr&chs=160x160&chld=L|0&' . $str;
    return $url;
}

function parseSQLFileForTables($sqlContent, $tableNames = [])
{
    $insertValues       = array();
    $lines              = explode("\n", $sqlContent);
    $isInsideInsert     = false;
    $currentTable       = '';
    $currentInsert      = '';

    foreach ($lines as $line) {
        // Check if the line starts with 'INSERT INTO'
        if (strpos(trim($line), 'INSERT INTO') === 0) {
            $isInsideInsert = true;
            $currentTable   = getTableName($line);
            $currentInsert  = '';
        }

        if ($isInsideInsert && in_array($currentTable, $tableNames)) {
            // Append the line to the current insert statement
            $currentInsert .= $line;

            // Check if the current insert statement is complete
            if (substr(trim($line), -1) === ';') {
                $insertValues[$currentTable] = extractValuesFromQuery($currentInsert);
                $isInsideInsert = false;
            }
        }
    }

    return $insertValues;
}

function getTableName($insertStatement)
{
    // Extract the table name from the insert statement
    $pattern = '/INSERT INTO `([^`]*)`/i';
    preg_match($pattern, $insertStatement, $matches);
    return $matches[1];
}


function extractValuesFromQuery($stringQuery)
{
    $pattern = '/\((.*?)\)/';
    preg_match_all($pattern, $stringQuery, $matches);

    $values = $matches[1];
    $values = array_map('trim', $values);

    // Separate each set of values into its own array
    $insertArrays = [];
    foreach ($values as $key => $value) {
        $insertArrays[] = explode(',', str_replace("'", "", $value));
    }

    return $insertArrays;
}


function convertToEnNum($string)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    $num = range(0, 9);
    $convertedPersianNums = str_replace($persian, $num, $string);
    $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);

    return $englishNumbersOnly;
}

function formatTraffice($traffic)
{
    if ($traffic < 1024) {
        return round($traffic, 2) . " MB";
    }
    return round($traffic / 1024, 2) . " GB";
}


function calcDifferenceDate($startDate, $endDate)
{
    $startDate = new DateTime(date("Y-m-d", $startDate));
    $endDate = new DateTime(date("Y-m-d", $endDate));

    $interval = $startDate->diff($endDate);

    $months = $interval->m;
    $days = $interval->d;
    $years = $interval->y;

    $result = '';

    if ($years > 0) {
        $result .= "$years ";
        if ($days > 0 || $months > 0) {
            $result .= "سال و ";
        } else {
            $result .= "ساله";
        }
    }
    
    if ($months > 0) {
        $result .= "$months ";
        if ($days > 0) {
            $result .= "ماه و ";
        } else {
            $result .= "ماهه";
        }
    }

    if ($days > 0) {
        $result .= "$days ";
        if ($months > 0) {
            $result .= " روز";
        } else {
            $result .= "روزه";
        }
    }

    return trim($result);
}


function getEndOfDate($date)
{
    if (!is_numeric($date)) {
        $date = strtotime($date);
    }
    $date = strtotime("today", $date);
    return strtotime("tomorrow", $date) - 1;
}

function  getStartOfDate($date)
{
    if (!is_numeric($date)) {
        $date = strtotime($date);
    }
    return  strtotime("today", $date);
}


function userStatusLabel($status)
{
    $arrayStats = [
        "active"            => "فعال",
        "deactive"          => "غیر فعال",
        "expiry_traffic"    => "انقضای ترافیک",
        "expiry_date"       => "انقضای تاریخ",
    ];

    if (!empty($arrayStats[$status])) {
        return $arrayStats[$status];
    }

    return $status;
}


function githubLastVersion()
{
    $url = getConfig("versionUrl");
    if ($url) {
        $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );
        $urlContent = @file_get_contents($url, false, $context);
        if (!empty($urlContent)) {
            $urlContent = json_decode($urlContent, true);
            $lastVersion = $urlContent["tag_name"];
            return number_format($lastVersion, '.', 1);
        }
    }
    return false;
}

function getUserConfig($username, $password)
{
    $sshPort      = \App\Models\Settings::getSetting("ssh_port");
    $udpPort      = \App\Models\Settings::getSetting("udp_port");

    return [
        "host"      => getServerIp(),
        "ssh_port"  => $sshPort,
        "udp_port"  => $udpPort,
        "username"  => $username,
        "password"  => $password,
    ];
}


function getLocalOnlienUsers()
{
    $onlinePath = PATH_STORAGE . DS . "online.json";

    $content =  file_get_contents($onlinePath);
    if (!empty($content)) {
        try {
            return json_decode($content, true);
        } catch (\Exception $err) {
        }
    }

    return false;
}
function setLocalOnlienUsers($onlineUsers)
{
    $onlinePath = PATH_STORAGE . DS . "online.json";
    @file_put_contents($onlinePath, json_encode($onlineUsers));
}


function adjustDateTime($dateTimeString)
{
    // Create a DateTime object from the given date and time string
    $dateTime = new DateTime($dateTimeString);

    // Get the year from the DateTime object
    $year = (int)$dateTime->format("Y");

    if ($year > 2025) {
        // Change the year to 2025
        $dateTime->setDate(2025, $dateTime->format("m"), $dateTime->format("d"));
        return $dateTime->format("Y-m-d");
    } else {
        return $dateTime->format("Y-m-d");
    }
}
