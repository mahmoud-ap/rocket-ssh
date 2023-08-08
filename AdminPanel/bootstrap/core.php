<?php 

/**
 * Core Funcions
 */

/**
 * get specified config
 * 
 * loads all configs and searched through it
 * 
 * @param  mixed args each can be a config key or sub key
 * @return mixed config or null
 */
function getConfig() {
    global $configs;
    $configsTemp = $configs;
    $args = func_get_args();
    $countArgs = count($args);
    if ($countArgs > 0) {
        $i = 0;
        while ($i < $countArgs) {
            if (is_array($configsTemp) && array_key_exists($args[$i], $configsTemp)) {
                $configsTemp = $configsTemp[$args[$i]];
                if ($i == ($countArgs - 1)) { // last arg
                    return $configsTemp;
                }
            } else {
                $i = $countArgs;
            }
            $i++;
        }
    }
    return null;
}

/**
 * Get All Configs
 * 
 * @return array configs
 */
function getAllConfigs() {
    global $configs;
    if (!empty($configs)) {
        return $configs;
    }
    return null;
}

/**
 * Load All Helpers in App/Helpers
 */
function loadAllAppHelpers() {
    $helpersFolderApp = PATH_APP . DS . 'Helpers' . DS;

    foreach (glob($helpersFolderApp . '*_helper.php') as $helper) {
        require_once($helper);
    }
}

/**
 * Load Specified Helpers in App/Helpers
 * 
 * @param mixed args as helper names, can be single, array
 */
function loadAppHelpers($helperNames) {
    $helpersFolder = PATH_APP . DS . 'Helpers' . DS;

    $args = func_get_args();
    $toLoad = array();

    if (count($args) > 1) {
        $toLoad = $args;
    } else {
        if (!is_array($helperNames)) {
            $toLoad[] = $helperNames;
        } else {
            $toLoad = $helperNames;
        }
    }
    
    foreach ($toLoad as $helperName) {
        $helperPath = $helpersFolder . $helperName . '_helper.php';
        if (file_exists($helperPath)) {
            require_once($helperPath);
        }
    }
}

/**
 * Load All Helpers in System/Helpers
 */
function loadAllSysHelpers() {
    $helpersFolder = PATH_BOOTSTRAP . DS . 'Helpers' . DS;

    foreach (glob($helpersFolder . '*_helper.php') as $helper) {
        require_once($helper);
    }
}

/**
 * Load Specified Helpers in System/Helpers
 * 
 * @param mixed args as helper names, can be single, array
 */
function loadSysHelpers($helperNames) {
    $helpersFolder = PATH_BOOTSTRAP . DS . 'Helpers' . DS;

    $args = func_get_args();
    $toLoad = array();

    if (count($args) > 1) {
        $toLoad = $args;
    } else {
        if (!is_array($helperNames)) {
            $toLoad[] = $helperNames;
        } else {
            $toLoad = $helperNames;
        }
    }
    
    foreach ($toLoad as $helperName) {
        $helperPath = $helpersFolder . $helperName . '_helper.php';
        if (file_exists($helperPath)) {
            require_once($helperPath);
        }
    }
}

/**
 * Load All Helpers in App/Helpers & System/Helpers
 */
function loadAllHelpers() {
    // load all app helpers, allow for overriding sys functions
    loadAllAppHelpers();
    // load all system helpers
    loadAllSysHelpers();
}

/**
 * Load Specified Helpers in App/Helpers & System/Helpers
 * 
 * @param mixed args as helper names, can be single, array
 */
function loadHelpers($helperNames) {
    $args = func_get_args();
    $toLoad = array();

    // wildcard load
    if (in_array('*', $args)) {
        loadAllHelpers();
        return;
    }

    if (count($args) > 1) {
        $toLoad = $args;
    } else {
        if (!is_array($helperNames)) {
            $toLoad[] = $helperNames;
        } else {
            $toLoad = $helperNames;
        }
    }

    // load app helpers, allow for overriding sys functions
    loadAppHelpers($toLoad);
    // load system helpers
    loadSysHelpers($toLoad);
}

/**
 * Sets the timezone
 * 
 * @param string php supported timezone
 */
function setTimeZone($timezone) {
    date_default_timezone_set($timezone);
}

/**
 * Enable or Disable PHP Error Reporting
 * 
 * @param bool whether to show errors or not
 */
function setPhpErrorReporting($showErrors = false) {
    if ($showErrors) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}

/**
 * Setting Access Control Headers
 * 
 * @param array access settings defined in configs
 */
function setAccessControl($accessSettings = array()) {
    if (!empty($accessSettings['allow_origin'])) {
        header('Access-Control-Allow-Origin: ' . trim($accessSettings['allow_origin']));
    }
    if (!empty($accessSettings['allow_headers'])) {
        header('Access-Control-Allow-Headers: ' . trim($accessSettings['allow_headers']));
    }
    if (!empty($accessSettings['allow_methods'])) {
        header('Access-Control-Allow-Methods: ' . trim($accessSettings['allow_methods']));
    }
    if (!empty($accessSettings['allow_credentials'])) {
        header('Access-Control-Allow-Credentials: ' . trim($accessSettings['allow_credentials']));
    }
}

/**
 * Get Session Configs and populate Session Options for slim-session middleware
 * 
 * @return array session options for slim-session middleware
 */
function getSessionOptions() {
    $options = array();
	$conf = getConfig('session');
	
	$options['name'] = (!empty($conf['name'])) ? $conf['name'] : 'xpanel';
	if (is_bool($conf['autorefresh'])) {
        $options['autorefresh'] = $conf['autorefresh'];
	}
	if (!empty($conf['lifetime'])) {
        $options['lifetime'] = $conf['lifetime'];
	}
	if (!empty($conf['path'])) {
        $options['path'] = $conf['path'];
	}
	if (!empty($conf['domain'])) {
        $options['domain'] = $conf['domain'];
	}
	if (is_bool($conf['secure'])) {
        $options['secure'] = $conf['secure'];
	}
	if (is_bool($conf['httponly'])) {
        $options['httponly'] = $conf['httponly'];
	}
	if (!empty($conf['ini_settings']) && is_array($conf['ini_settings'])) {
        $options['ini_settings'] = $conf['ini_settings'];
	}
    
    return $options;
}