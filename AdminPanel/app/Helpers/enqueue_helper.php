<?php


$toEnqueueArr = array();


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
