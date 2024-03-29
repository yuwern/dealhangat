<?php
/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Basic defines for timing functions.
 */
define('SECOND', 1);
define('MINUTE', 60);
define('HOUR', 3600);
define('DAY', 86400);
define('WEEK', 604800);
define('MONTH', 2592000);
define('YEAR', 31536000);
/**
 * Loads configuration files. Receives a set of configuration files
 * to load.
 * Example:
 *
 * `config('config1', 'config2');`
 *
 * @return boolean Success
 * @link http://book.cakephp.org/view/1125/config
 */
function config()
{
    $args = func_get_args();
    foreach($args as $arg) {
        if ($arg === 'database' && file_exists(CONFIGS . 'database.php')) {
            include_once (CONFIGS . $arg . '.php');
        } elseif (file_exists(CONFIGS . $arg . '.php')) {
            include_once (CONFIGS . $arg . '.php');
            if (count($args) == 1) {
                return true;
            }
        } else {
            if (count($args) == 1) {
                return false;
            }
        }
    }
    return true;
}
/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a browser-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/1190/Basic-Debugging
 * @link http://book.cakephp.org/view/1128/debug
 */
function debug($var = false, $showHtml = null, $showFrom = true)
{
    if (Configure::read('debug') > 0) {
        $file = '';
        $line = '';
        if ($showFrom) {
            $calledFrom = debug_backtrace();
            $file = substr(str_replace(ROOT, '', $calledFrom[0]['file']) , 1);
            $line = $calledFrom[0]['line'];
        }
        $html = <<<HTML
<strong>%s</strong> (line <strong>%s</strong>)
<pre class="cake-debug">
%s
</pre>
HTML;
        $text = <<<TEXT

%s (line %s)
########## DEBUG ##########
%s
###########################

TEXT;
        $template = $html;
        if (php_sapi_name() == 'cli') {
            $template = $text;
        }
        if ($showHtml === null) {
            $showHtml = true;
        }
        $var = print_r($var, true);
        if ($showHtml) {
            $var = str_replace(array(
                '<',
                '>'
            ) , array(
                '&lt;',
                '&gt;'
            ) , $var);
        }
        printf($template, $file, $line, $var);
    }
}
if (!function_exists('sortByKey')) {
    /**
     * Sorts given $array by key $sortby.
     *
     * @param array $array Array to sort
     * @param string $sortby Sort by this key
     * @param string $order  Sort order asc/desc (ascending or descending).
     * @param integer $type Type of sorting to perform
     * @return mixed Sorted array
     */
    function sortByKey(&$array, $sortby, $order = 'asc', $type = SORT_NUMERIC)
    {
        if (!is_array($array)) {
            return null;
        }
        foreach($array as $key => $val) {
            $sa[$key] = $val[$sortby];
        }
        if ($order == 'asc') {
            asort($sa, $type);
        } else {
            arsort($sa, $type);
        }
        foreach($sa as $key => $val) {
            $out[] = $array[$key];
        }
        return $out;
    }
}
/**
 * Convenience method for htmlspecialchars.
 *
 * @param string $text Text to wrap through htmlspecialchars
 * @param boolean $double Encode existing html entities
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/view/1132/h
 */
function h($text, $double = true, $charset = null)
{
    if (is_array($text)) {
        $texts = array();
        foreach($text as $k => $t) {
            $texts[$k] = h($t, $double, $charset);
        }
        return $texts;
    }
    static $defaultCharset = false;
    if ($defaultCharset === false) {
        $defaultCharset = Configure::read('App.encoding');
        if ($defaultCharset === null) {
            $defaultCharset = 'UTF-8';
        }
    }
    if (is_string($double)) {
        $charset = $double;
    }
    return htmlspecialchars($text, ENT_QUOTES, ($charset) ? $charset : $defaultCharset, $double);
}
/**
 * Splits a dot syntax plugin name into its plugin and classname.
 * If $name does not have a dot, then index 0 will be null.
 *
 * Commonly used like `list($plugin, $name) = pluginSplit($name);`
 *
 * @param string $name The name you want to plugin split.
 * @param boolean $dotAppend Set to true if you want the plugin to have a '.' appended to it.
 * @param string $plugin Optional default plugin to use if no plugin is found. Defaults to null.
 * @return array Array with 2 indexes.  0 => plugin name, 1 => classname
 */
function pluginSplit($name, $dotAppend = false, $plugin = null)
{
    if (strpos($name, '.') !== false) {
        $parts = explode('.', $name, 2);
        if ($dotAppend) {
            $parts[0].= '.';
        }
        return $parts;
    }
    return array(
        $plugin,
        $name
    );
}
/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see	debug()
 * @param array $var Variable to print out
 * @link http://book.cakephp.org/view/1136/pr
 */
function pr($var)
{
    if (Configure::read('debug') > 0) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 * @link http://book.cakephp.org/view/1124/am
 */
function am()
{
    $r = array();
    $args = func_get_args();
    foreach($args as $a) {
        if (!is_array($a)) {
            $a = array(
                $a
            );
        }
        $r = array_merge($r, $a);
    }
    return $r;
}
/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/view/1130/env
 */
function env($key)
{
    if ($key === 'HTTPS') {
        if (isset($_SERVER['HTTPS'])) {
            return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        }
        return (strpos(env('SCRIPT_URI') , 'https://') === 0);
    }
    if ($key === 'SCRIPT_NAME') {
        if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
            $key = 'SCRIPT_URL';
        }
    }
    $val = null;
    if (isset($_SERVER[$key])) {
        $val = $_SERVER[$key];
    } elseif (isset($_ENV[$key])) {
        $val = $_ENV[$key];
    } elseif (getenv($key) !== false) {
        $val = getenv($key);
    }
    if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
        $addr = env('HTTP_PC_REMOTE_ADDR');
        if ($addr !== null) {
            $val = $addr;
        }
    }
    if ($val !== null) {
        return $val;
    }
    switch ($key) {
        case 'SCRIPT_FILENAME':
            if (defined('SERVER_IIS') && SERVER_IIS === true) {
                return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
            }
            break;

        case 'DOCUMENT_ROOT':
            $name = env('SCRIPT_NAME');
            $filename = env('SCRIPT_FILENAME');
            $offset = 0;
            if (!strpos($name, '.php')) {
                $offset = 4;
            }
            return substr($filename, 0, strlen($filename) -(strlen($name) +$offset));
            break;

        case 'PHP_SELF':
            return str_replace(env('DOCUMENT_ROOT') , '', env('SCRIPT_FILENAME'));
            break;

        case 'CGI_MODE':
            return (PHP_SAPI === 'cgi');
            break;

        case 'HTTP_BASE':
            $host = env('HTTP_HOST');
            if (substr_count($host, '.') !== 1) {
                return preg_replace('/^([^.])*/i', null, env('HTTP_HOST'));
            }
            return '.' . $host;
            break;
    }
    return null;
}
/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path	File path within /tmp to save the file.
 * @param  mixed  $data	The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 * @deprecated Please use Cache::write() instead
 */
function cache($path, $data = null, $expires = '+1 day', $target = 'cache')
{
    if (Configure::read('Cache.disable')) {
        return null;
    }
    $now = time();
    if (!is_numeric($expires)) {
        $expires = strtotime($expires, $now);
    }
    switch (strtolower($target)) {
        case 'cache':
            $filename = CACHE . $path;
            break;

        case 'public':
            $filename = WWW_ROOT . $path;
            break;

        case 'tmp':
            $filename = TMP . $path;
            break;
    }
    $timediff = $expires-$now;
    $filetime = false;
    if (file_exists($filename)) {
        $filetime = @filemtime($filename);
    }
    if ($data === null) {
        if (file_exists($filename) && $filetime !== false) {
            if ($filetime+$timediff < $now) {
                @unlink($filename);
            } else {
                $data = @file_get_contents($filename);
            }
        }
    } elseif (is_writable(dirname($filename))) {
        @file_put_contents($filename, $data);
    }
    return $data;
}
/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in
 *   directory will be deleted. If array, names to be searched for deletion. If clearCache() without params,
 *   all files in app/tmp/cache/views will be deleted
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
function clearCache($params = null, $type = 'views', $ext = '.php')
{
    if (is_string($params) || $params === null) {
        $params = preg_replace('/\/\//', '/', $params);
        $cache = CACHE . $type . DS . $params;
        if (is_file($cache . $ext)) {
            @unlink($cache . $ext);
            return true;
        } elseif (is_dir($cache)) {
            $files = glob($cache . '*');
            if ($files === false) {
                return false;
            }
            foreach($files as $file) {
                if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) -6) {
                    @unlink($file);
                }
            }
            return true;
        } else {
            $cache = array(
                CACHE . $type . DS . '*' . $params . $ext,
                CACHE . $type . DS . '*' . $params . '_*' . $ext
            );
            $files = array();
            while ($search = array_shift($cache)) {
                $results = glob($search);
                if ($results !== false) {
                    $files = array_merge($files, $results);
                }
            }
            if (empty($files)) {
                return false;
            }
            foreach($files as $file) {
                if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) -6) {
                    @unlink($file);
                }
            }
            return true;
        }
    } elseif (is_array($params)) {
        foreach($params as $file) {
            clearCache($file, $type, $ext);
        }
        return true;
    }
    return false;
}
function clearPermanentCache($model = null)
{
	foreach($model as $file) {
		array_map('unlink', glob(CACHE . 'views' . DS . '*_public_*' . $file . '.php'));
		array_map('unlink', glob(CACHE . 'views' . DS . '*_loggedin_*' . $file . '.php'));
		if (!empty($_SESSION['Auth']['User']['user_type_id'])) {
			array_map('unlink', glob(CACHE . 'views' . DS . '*_admin_*' . $file . '.php'));
			array_map('unlink', glob(CACHE . 'views' . DS . '*_usertype_' . $_SESSION['Auth']['User']['user_type_id'] . '_*' . $file . '.php'));
			array_map('unlink', glob(CACHE . 'views' . DS . '*_user_' . $_SESSION['Auth']['User']['id'] . '_*' . $file . '.php'));
		}
		if (!empty($_SESSION['Auth']['User']['user_type_id']) == ConstUserTypes::Admin) {
			array_map('unlink', glob(CACHE . 'views' . DS . '*_usertype_*' . $file . '.php'));
			array_map('unlink', glob(CACHE . 'views' . DS . '*_user_*' . $file . '.php'));
		}
	}
    return true;
}
/**
 * Recursively strips slashes from all values in an array
 *
 * @param array $values Array of values to strip slashes
 * @return mixed What is returned from calling stripslashes
 * @link http://book.cakephp.org/view/1138/stripslashes_deep
 */
function stripslashes_deep($values)
{
    if (is_array($values)) {
        foreach($values as $key => $value) {
            $values[$key] = stripslashes_deep($value);
        }
    } else {
        $values = stripslashes($values);
    }
    return $values;
}
/**
 * Wraps ternary operations. If $condition is a non-empty value, $val1 is returned, otherwise $val2.
 * Don't use for isset() conditions, or wrap your variable with @ operator:
 * Example:
 * <code>
 * ife(isset($variable), @$variable, 'default');
 * </code>
 *
 * @param mixed $condition Conditional expression
 * @param mixed $val1 Value to return in case condition matches
 * @param mixed $val2 Value to return if condition doesn't match
 * @return mixed $val1 or $val2, depending on whether $condition evaluates to a non-empty expression.
 * @link http://book.cakephp.org/view/704/ife
 */
function ife($condition, $val1 = null, $val2 = null)
{
    if (!empty($condition)) {
        return $val1;
    }
    return $val2;
}
/**
 * Returns a translated string if one is found; Otherwise, the submitted message.
 *
 * @param string $singular Text to translate
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return mixed translated string
 * @link http://book.cakephp.org/view/1121/__
 */
function __($singular, $args = null)
{
    if (!$singular) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($singular);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 1);
    }
    return vsprintf($translated, $args);
}
/**
 * Returns correct plural form of message identified by $singular and $plural for count $count.
 * Some languages have more than one form for plural messages dependent on the count.
 *
 * @param string $singular Singular text to translate
 * @param string $plural Plural text
 * @param integer $count Count
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return mixed plural form of translated string
 */
function __n($singular, $plural, $count, $args = null)
{
    if (!$singular) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($singular, $plural, null, 6, $count);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 3);
    }
    return vsprintf($translated, $args);
}
/**
 * Allows you to override the current domain for a single message lookup.
 *
 * @param string $domain Domain
 * @param string $msg String to translate
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return translated string
 */
function __d($domain, $msg, $args = null)
{
    if (!$msg) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($msg, null, $domain);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 2);
    }
    return vsprintf($translated, $args);
}
/**
 * Allows you to override the current domain for a single plural message lookup.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return plural form of translated string
 */
function __dn($domain, $singular, $plural, $count, $args = null)
{
    if (!$singular) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($singular, $plural, $domain, 6, $count);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 4);
    }
    return vsprintf($translated, $args);
}
/**
 * Allows you to override the current domain for a single message lookup.
 * It also allows you to specify a category.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 *
 * - LC_ALL       0
 * - LC_COLLATE   1
 * - LC_CTYPE     2
 * - LC_MONETARY  3
 * - LC_NUMERIC   4
 * - LC_TIME      5
 * - LC_MESSAGES  6
 *
 * @param string $domain Domain
 * @param string $msg Message to translate
 * @param integer $category Category
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return translated string
 */
function __dc($domain, $msg, $category, $args = null)
{
    if (!$msg) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($msg, null, $domain, $category);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 3);
    }
    return vsprintf($translated, $args);
}
/**
 * Allows you to override the current domain for a single plural message lookup.
 * It also allows you to specify a category.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 *
 * - LC_ALL       0
 * - LC_COLLATE   1
 * - LC_CTYPE     2
 * - LC_MONETARY  3
 * - LC_NUMERIC   4
 * - LC_TIME      5
 * - LC_MESSAGES  6
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param integer $category Category
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return plural form of translated string
 */
function __dcn($domain, $singular, $plural, $count, $category, $args = null)
{
    if (!$singular) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($singular, $plural, $domain, $category, $count);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 5);
    }
    return vsprintf($translated, $args);
}
/**
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 *
 * - LC_ALL       0
 * - LC_COLLATE   1
 * - LC_CTYPE     2
 * - LC_MONETARY  3
 * - LC_NUMERIC   4
 * - LC_TIME      5
 * - LC_MESSAGES  6
 *
 * @param string $msg String to translate
 * @param integer $category Category
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return translated string
 */
function __c($msg, $category, $args = null)
{
    if (!$msg) {
        return;
    }
    if (!class_exists('I18n')) {
        App::import('Core', 'i18n');
    }
    $translated = I18n::translate($msg, null, null, $category);
    if ($args === null) {
        return $translated;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args() , 2);
    }
    return vsprintf($translated, $args);
}
/**
 * Shortcut to Log::write.
 *
 * @param string $message Message to write to log
 */
function LogError($message)
{
    if (!class_exists('CakeLog')) {
        App::import('Core', 'CakeLog');
    }
    $bad = array(
        "\n",
        "\r",
        "\t"
    );
    $good = ' ';
    CakeLog::write('error', str_replace($bad, $good, $message));
}
/**
 * Searches include path for files.
 *
 * @param string $file File to look for
 * @return Full path to file if exists, otherwise false
 * @link http://book.cakephp.org/view/1131/fileExistsInPath
 */
function fileExistsInPath($file)
{
    $paths = explode(PATH_SEPARATOR, ini_get('include_path'));
    foreach($paths as $path) {
        $fullPath = $path . DS . $file;
        if (file_exists($fullPath)) {
            return $fullPath;
        } elseif (file_exists($file)) {
            return $file;
        }
    }
    return false;
}
/**
 * Convert forward slashes to underscores and removes first and last underscores in a string
 *
 * @param string String to convert
 * @return string with underscore remove from start and end of string
 * @link http://book.cakephp.org/view/1126/convertSlash
 */
function convertSlash($string)
{
    $string = trim($string, '/');
    $string = preg_replace('/\/\//', '/', $string);
    $string = str_replace('/', '_', $string);
    return $string;
}
/**
 * New __() implementation
 * @author rajesh_04ag02
 * @since  2008-05-19
 */
function __l($text, $lang_code = false)
{
    if (empty($lang_code)) {
        $config_lang_code = Configure::read('lang_code');
        if (!empty($config_lang_code)) {
            $lang_code = Configure::read('lang_code');
        } else if (!empty($_COOKIE['CakeCookie']['user_language'])) {
            $lang_code = $_COOKIE['CakeCookie']['user_language'];
        } else if (!empty($_COOKIE['CakeCookie']['city_language'])) {
            $lang_code = $_COOKIE['CakeCookie']['city_language'];
        }
    }
    if (!empty($GLOBALS['_langs'][$lang_code][$text])) {
        return $GLOBALS['_langs'][$lang_code][$text];
    } else {
        return $text;
    }
}
// http://in.php.net/base64_encode#82506
//rajesh_04ag02 // 2008-12-05
function base64_url_encode($input)
{
    return strtr(base64_encode($input) , '+/=', '-_,');
}
function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_,', '+/='));
}
// http://in.php.net/mime_content_type#82855 but heavily modified with getimagesize()
// rajesh_04ag02 // 2009-04-08
function get_mime($filename)
{
    $mime = false;
    if ($img_size_arr = getimagesize($filename)) {
        if (isset($img_size_arr['mime'])) {
            $mime = $img_size_arr['mime'];
        }
    }
    if (!$mime) {
        if (function_exists('mime_content_type')) { // if mime_content_type exists use it.
            $mime = mime_content_type($filename);
        } else if (function_exists('finfo_open')) { // if Pecl installed use it
            $finfo = finfo_open(FILEINFO_MIME);
            $mime = finfo_file($finfo, $filename);
            finfo_close($finfo);
        } else { // if nothing left try shell
            if (stripos(PHP_OS, 'WIN') !== false) { // Nothing to do on windows
                $mime = false;
            } else if (stripos(PHP_OS, 'mac') !== false) { // Correct output on macs
                $mime = trim(exec('file -b --mime ' . escapeshellarg($filename)));
            } else { // Regular unix systems
                $mime = trim(exec('file -bi ' . escapeshellarg($filename)));
            }
        }
    }
    return $mime;
}
//http://www.php.net/manual/en/function.disk-total-space.php#34100
//On Windows, return negative. @todo find workaround
// rajesh_04ag02 // 2009-04-21
function _dskspace($dir)
{
    $s = stat($dir);
    $space = $s['blocks']*512;
    if ($space < 0) { //Windows?
        $space = filesize($dir);
    }
    if (is_dir($dir)) {
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' and $file != '..') {
                $space+= _dskspace($dir . '/' . $file);
            }
        }
        closedir($dh);
    }
    return $space;
}
// Tried to use faster du version...
// rajesh_059at09 // 2011-03-04
function dskspace($dir)
{
    // http://www.php.net/manual/en/function.disk-total-space.php#72324
    if ($output = exec('du -sk ' . $dir)) {
        preg_match('/\d+/', $output, $size_in_kb);
        return $size_in_kb[0]*1024;
    } else {
        return _dskspace($dir);
    }
}
// Bytes to highest unit conversion
function bytes_to_higher($bytes)
{
    $symbols = array(
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB'
    );
    $exp = floor(log($bytes) /log(1024));
    return sprintf('%.2f ' . $symbols[$exp], ($bytes ? ($bytes/pow(1024, floor($exp))) : 0));
}
// Higher unit to bytes conversion
function higher_to_bytes($value, $symbol)
{
    $symbols = array(
        'B' => '0',
        'KB' => '1',
        'MB' => '2',
        'GB' => '3',
        'TB' => '4',
        'PB' => '5',
        'EB' => '6',
        'ZB' => '7',
        'YB' => '8'
    );
    return ($value) ? $value*pow(1024, $symbols[$symbol]) : 0;
}
function seconds_to_higher($sec)
{
    if (($sec/3600) >= 1) {
        return sprintf('%d:%02d:%02d', ($sec/3600) , (($sec/60) %60) , ($sec%60));
    } else {
        return sprintf('%d:%02d', (($sec/60) %60) , ($sec%60));
    }
}
function _formatDate($format, $value = null, $query = null)
{
    if (!empty($query)) {
        // for query condition, it will change the user timezone value to GMT 0 value
        return gmdate($format, strtotime($value));
    } else {
        // for display date and time, it will change the GMT 0 value to user timezone value
        return date($format, strtotime(date('Y-m-d H:i:s', $value) . ' GMT'));
    }
}
function urlencode_rfc3986($string, $enable_urlencode = true)
{
    $entities = array(
        '%21',
        '%2A',
        '%27',
        '%28',
        '%29',
        '%3B',
        '%3A',
        '%40',
        '%26',
        '%3D',
        '%2B',
        '%24',
        '%2C',
        '%2F',
        '%3F',
        '%25',
        '%23',
        '%5B',
        '%5D'
    );
    $replacements = array(
        '!',
        '*',
        "'",
        "(",
        ")",
        ";",
        ":",
        "@",
        "&",
        "=",
        "+",
        "$",
        ",",
        "/",
        "?",
        "%",
        "#",
        "[",
        "]"
    );
	if($enable_urlencode){
		$string = urlencode($string);
	}
    return str_replace($entities, $replacements, $string);
}
        function router_url_city($url, $named = null) 
        {
            $user_preferred_city = (!empty($_COOKIE['CakeCookie']['city_slug'])) ? $_COOKIE['CakeCookie']['city_slug'] : Cache::read('site.default_city', 'long');
            if ($city_url = (!empty($url['city']) ? $url['city'] : (!empty($named['city']) ? $named['city'] : $user_preferred_city))) {
                if ($city_url == '/') $city_url = $user_preferred_city;
                if (is_array($url)) {
                    $url['city'] = htmlentities($city_url, ENT_QUOTES);
                } else if ($url == '/') {
                    $url = array(
                        'city' => htmlentities($city_url, ENT_QUOTES)
                    );
                }
            }
            return $url;
        }