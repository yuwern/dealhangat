<?php
if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(dirname(__FILE__))));
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
$app = ROOT . DS . 'app' . DS;
$js = $app . 'webroot' . DS . 'js' . DS;
$css = $app . 'webroot' . DS . 'css' . DS;
$img = $app . 'webroot' . DS . 'img' . DS;
$files = $app . 'webroot' . DS . 'files' . DS;
$tmp = $app . 'tmp' . DS;
$media = $app . 'media' . DS;
include '../config/database.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>GroupDeal - Diagnostic Tool</title>
<style type="text/css">
body{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	color:#333;
}
/** table-list */
table.list,table.list td,table.list th {
	border:1px solid #B5DBDF;
}
table.topic-list td,table.topic-list th {
	border:none;
	border-bottom:1px solid #B5DBDF;
}
table.list, table.list-info {
	border-spacing:0;
	border-collapse:collapse;
	margin:5px 5px;
	width:auto;
}
table.list td,table.list th {
	margin:0;
	padding:4px;
	border-width:1px;
	background-color:#fff;
	vertical-align:middle;
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
}
table.list td{
	text-align: left;
}
table.list th {
	color:#fff;
	background:#00B5C8;
	text-align:center;
}
table.list th a {
	color:#fff;
}
table.list tr.altrow td {
	background-color:#f0f7fe;
}
table.list tr:hover td {
	background-color:#EFFDFF;
}
table.list tr th.dl,table.list tr td.dl {
	text-align:left;
}
table.list tr th.deal-name,table.list tr td.deal-name {
	width:150px;
	white-space:wrap;
}
table.list tr th.dc,table.list tr td.dc {
	text-align:center;
}
table.list tr th.dr,table.list tr td.dr {
	text-align:right;
}
table.list tr.total-block td {
	background:#f0f8fe;
	font-weight:bold;
}
table.list tr td.deal-name,table.list tr th.deal-name {
	width:220px;
}
table.list tr td.green, table.list-info tr td.green {
	background:#52AF0A;
	color: white;
    font-size: 13px;
    font-weight: bold;
}
table.list tr td.red, table.list-info tr td.red {
	background:#DB3D47;
	color: white;
    font-size: 13px;
    font-weight: bold;
}
table.list tr td.yellow, table.list-info tr td.yellow {
	background:#FAFA3D;
	color:#333333;
    font-size: 13px;
    font-weight: bold;
}
table.list tr td.orange, table.list-info tr td.orange  {
	background:#f68e08;
	color: white;
    font-size: 13px;
    font-weight: bold;
}
table.list-info {
	float:right;
}
table.list-info tr td  {
	margin:0;
	padding:0 10px 0 5px;
	border-bottom:2px solid #fff;
	background-color:#fff;
	vertical-align:middle;
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	text-align:left;
}

table.list-info tr td.green, table.list-info tr td.red, table.list-info tr td.yellow, table.list-info tr td.orange, table.list-info tr td.orange {
	background:#52AF0A;
	width:20px;
	padding:0;
}
table.list-info tr td.red {
	background:#DB3D47;
}
table.list-info tr td.yellow {
	background:#FAFA3D;
}
table.list tr td.orange, table.list-info tr td.orange  {
	background:#f68e08;
}
div.list-info-block{
	float:right;
	width:625px;
}
div.top-block-left {
	float:left;
}
div.top-block{
	min-height:90px;
	overflow:hidden;
}
.info-details {
    background: url("img/icon-info-details.png") no-repeat scroll 10px center #F9F9F9;
    border: 1px dashed #DDDDDD;
    margin: 6px 10px;
    padding: 15px 15px 15px 50px;
}
.invalid_mail{
color:#C40000;
}
.green_req_details{
background:#52AF0A;
color:#FFFFFF;
}
.red_req_details{
background:#DB3D47;
color:#FFFFFF;
}
.orange_req_details{
background:#f68e08;
color:#FFFFFF;
}
.yellow_req_details{
background:#FAFA3D;
color:#000000;
}
.pay_gate_req{
width:500px;
background-color:#DB3D47;
color:#FFFFFF;
}
.cred_detail {
background:#FAFA3D;
color:#000000;
text-align:center;
}
</style>
</head>
<?php
$req_not_met = 0;
$req_not_met_pre = 0;
$req_not_met_no_man = 0;
$req_met_unable_check = 0;
$master_db = _db_check('master');
$default_db = _db_check('default');
$_writable_folders = array(
    $tmp,
    $media,
    $css,
    $js,
    $img,
    $files,
    ROOT . DS . 'core' . DS . 'vendors' . DS . 'securimage'
);
$out = '';
foreach($_writable_folders as $folder) {
    if (_is_writable_recursive($folder)) {
        $out.= '<tr><td> ' . $folder . '</td><td class="green">Writable</td></tr>';
    } else {
        $out.= '<tr><td>' . $folder . '</td><td class="red">Not Writable</td></tr>';
        $req_not_met = 1;
    }
}
$_writable_files = array(
    $app . 'vendors' . DS . 'shells' . DS . 'cron.sh',
    $app . 'vendors' . DS . 'shells' . DS . 'cron.php',
    ROOT . DS . 'core' . DS . 'cake' . DS . 'console' . DS . 'cake'
);
foreach($_writable_files as $file) {
    if (is_writable($file)) {
        $out.= '<tr><td> ' . $file . '</td><td class="green">Writable</td></tr>';
    } else {
        $out.= '<tr><td>' . $file . '</td><td class="red">Not Writable</td></tr>';
        $req_not_met = 1;
    }
}
$tmpCacheFileSize = bytes_to_higher(dskspace($tmp . 'cache'));
$tmpLogsFileSize = bytes_to_higher(dskspace($tmp . 'logs'));
$writable = $out;
?>
<?php
//---------------- Default table -------------------------------------------------------
function _db_check($database = 'default')
{
    $db_config_obj = new DATABASE_CONFIG;
    $db_info = array();
    $db_data = $db_config_obj->$database;
    $hostname = $db_data['host'];
    $username = $db_data['login'];
    $password = $db_data['password'];
    $database = $db_data['database'];
    $link = @mysql_connect($hostname, $username, $password);
    if ($link) {
        $db_info['db_connection'] = 'Connected';
        $db_selected = @mysql_select_db($database, $link);
        if ($db_selected) {
            $db_info['db_connect'] = $database;
            //------------------Site Admin Mail------------------------
            $admin_mail = '';
            $result_admin_email = mysql_query("select * from settings where name ='site.contact_email'", $link);
            if (!$result_admin_email) {
                echo $db_info['admin_mail']['error'] = mysql_error();
            } else {
                if ($row_admin_email = mysql_fetch_assoc($result_admin_email)) {
                    $db_info['admin_mail']['value'] = $row_admin_email['value'];
                }
            }
            // ----------payment Gateway info get ------------------------------
            $result = mysql_query("select * from payment_gateways", $link);
            $db_info['payment_gateway']['error'] = 'None';
            if (!$result) {
                echo $db_info['payment_gateway']['error'] = mysql_error();
            } else {
                while ($row = mysql_fetch_assoc($result)) {
                    $db_info['payment_gateway']['gateway'][] = $row;
                }
            }
            // ----------payment Gateway info get ------------------------------
            // ----------payment Gateway setting  info get ------------------------------
            $result = mysql_query("SELECT payment_gateways.display_name, payment_gateways.is_test_mode, payment_gateways.is_active, payment_gateway_settings.id, payment_gateway_settings.payment_gateway_id, payment_gateway_settings.key, payment_gateway_settings.test_mode_value, payment_gateway_settings.live_mode_value  FROM payment_gateways, payment_gateway_settings WHERE payment_gateways.id = payment_gateway_settings.payment_gateway_id", $link);
            $db_info['payment_gateway']['error'] = 'None';
            if (!$result) {
                echo $db_info['payment_gateway']['error'] = mysql_error();
            } else {
                while ($row = mysql_fetch_assoc($result)) {
                    $db_info['payment_gateway']['gateway_settings'][] = $row;
                }
            }
            foreach($db_info['payment_gateway']['gateway'] as $key => &$gateway) {
                foreach($db_info['payment_gateway']['gateway_settings'] as &$settings) {
                    if ($gateway['id'] == $settings['payment_gateway_id']) {
                        if (($settings['key'] != 'is_enable_for_buy_a_deal' && $settings['key'] != 'is_enable_for_gift_card' && $settings['key'] != 'is_enable_for_add_to_wallet')) {
                            $db_info['payment_gateway']['gateway'][$key]['settings'][] = $settings;
                        } else {
                            $db_info['payment_gateway']['gateway'][$key][$settings['key']] = $settings['test_mode_value'];
                        }
                    }
                }
            }
            $result = mysql_query("select * from settings where setting_category_id in (1, 8, 14, 18)", $link);
            $db_info['payment_gateway']['error'] = 'None';
            if (!$result) {
                echo $db_info['payment_gateway']['error'] = mysql_error();
            } else {
                $db_info['settings'][18] = $db_info['settings'][8] = $db_info['settings'][1] = array(
                    'Mandatory' => array() ,
                    'Credentials' => array() ,
                    'Not Mandatory' => array()
                );
                $mandatory = array(
                    'facebook.app_id',
                    'facebook.fb_api_key',
                    'facebook.fb_secrect_key',
                    'twitter.consumer_key',
                    'twitter.consumer_secret',
                    'facebook.fb_access_token',
                    'facebook.fb_user_id',
                    'twitter.site_user_access_key',
                    'twitter.site_user_access_token'
                );
                $site_settings = array(
                    'site.contact_email',
                    'site.is_in_prelaunch_mode',
                    'site.maintenance_mode'
                );
                $site_name = array(
                    'site.name',
                    'site.is_mobile_app'
                );
                $msn_invite_settings = array(
                    'friend.msn_app_id',
                    'friend.msn_secret'
                );
                $yahoo_invite_settings = array(
                    'invite.yahoo_app_id',
                    'invite.yahoo_consumer_key',
                    'invite.yahoo_secret_key'
                );
                while ($row = mysql_fetch_assoc($result)) {
                    if (in_array($row['name'], $mandatory)) {
                        $db_info['settings'][$row['setting_category_id']]['Mandatory'][] = array(
							'name' => $row['name'],
                            'label' => $row['label'],
                            'value' => $row['value']
                        );
                    } elseif (in_array($row['name'], $site_settings)) {
                        $db_info['settings'][$row['setting_category_id']]['site_setting'][] = array(
							'name' => $row['name'],
                            'label' => $row['label'],
                            'value' => $row['value']
                        );
                    } elseif ($row['setting_category_id'] != 1 && $row['setting_category_id'] != 14) {
                        $db_info['settings'][$row['setting_category_id']]['Mandatory'][] = array(
							'name' => $row['name'],
                            'label' => $row['label'],
                            'value' => $row['value']
                        );
                    } elseif (in_array($row['name'], $msn_invite_settings)) {
                        $db_info['settings']['msn']['Mandatory'][] = array(
							'name' => $row['name'],
                            'label' => $row['label'],
                            'value' => $row['value']
                        );
                    } elseif (in_array($row['name'], $yahoo_invite_settings)) {
                        $db_info['settings']['yahoo']['Mandatory'][] = array(
							'name' => $row['name'],
                            'label' => $row['label'],
                            'value' => $row['value']
                        );
                    } elseif (in_array($row['name'], $site_name)) {
                        $db_info['settings']['site'][] = $row['value'];
                    }
                }
            }
            // ----------payment Gateway info get ------------------------------

        } else {
            $db_info['db_connect'] = mysql_error();
        }
        mysql_close($link);
    } else {
        $db_info['db_connection'] = mysql_error();
    }
    return $db_info;
}
//---------------- Default table -------------------------------------------------------
// Reference: http://www.izzycode.com/php/php-function-get-atomic-time.html
function GetAtomicTime()
{
    // Get time server list ...
    $atomic_time_server_content = file_get_contents('http://tf.nist.gov/tf-cgi/servers.cgi');
    preg_match_all('/<table[^>]*>(.*?)<\/table>/si', $atomic_time_server_content, $matches);
    // Only the 3rd table has info...
    $atomic_time_server_table = $matches[0][2];
    preg_match_all('/<td[^>]*>((?:<td.+?<\/td|.)*?)\s*<\/td>/si', $atomic_time_server_table, $matches);
    // take only "Up" server IPs...
    $server_ips = array();
    for ($i = 5, $len = count($matches[1]); $i < $len; $i+= 4) {
        if (strpos($matches[1][$i+2], 'All services available') !== false) {
            $server_ips[] = $matches[1][$i];
        }
    }
    // Look up in any "Up" server, if it fails, try one by one...
    for ($i = 0, $len = count($server_ips) , $is_server_responds = false; !$is_server_responds && ($i < $len); ++$i) {
        $fp = fsockopen($server_ips[$i], 37);
        if ($fp) {
            fputs($fp, "\n");
            $timevalue = fread($fp, 49);
            fclose($fp);
            $is_server_responds = true;
            // debug...
            // echo 'Server IP: ', $server_ips[$i], "\n";

        }
    }
    $atomic_time = (abs(hexdec('7fffffff') -hexdec(bin2hex($timevalue)) -hexdec('7fffffff')) -2208988800);
    return $atomic_time;
}
//---------------------
function _is_writable_recursive($dir)
{
    if (!($folder = @opendir($dir))) {
        return false;
    }
    while ($file = readdir($folder)) {
        if ($file != '.' && $file != '..' && $file != '.svn' && (!is_writable($dir . DS . $file) || (is_dir($dir . DS . $file) && !_is_writable_recursive($dir . DS . $file)))) {
            closedir($folder);
            return false;
        }
    }
    closedir($folder);
    return true;
}
function _dskspace($dir)
{
    $s = @stat($dir);
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
?>

<body>

<?php
////////////Post Facebook//////////////////////
$fb_check = '';
if ($_GET['action'] == 'post' && $_GET['to'] != '' && $_GET['to'] == 'facebook') {
    if (!empty($master_db['settings'][8]['Mandatory'][0]['value']) && !empty($master_db['settings'][8]['Mandatory'][1]['value']) && !empty($master_db['settings'][8]['Credentials'][0]['value']) && !empty($master_db['settings'][8]['Mandatory'][8]['value'])) {
        $site = $master_db['settings']['site'][0];
        $app_id = $master_db['settings'][8]['Mandatory'][0]['value'];
        $secret_key = $master_db['settings'][8]['Mandatory'][1]['value'];
        $fb_access_token = $master_db['settings'][8]['Credentials'][0]['value'];
        $fb_page_id = $master_db['settings'][8]['Mandatory'][8]['value'];
        $message = 'This is the test post from ' . $site . ' - Facebook';
        include_once '../vendors/facebook/facebook.php';
        $facebook = new Facebook(array(
            'appId' => $app_id,
            'secret' => $secret_key,
            'cookie' => true
        ));
        try {
            $url = "http://" . $_SERVER['SERVER_NAME'];
            $facebook->api('/' . $fb_page_id . '/feed', 'POST', array(
                'access_token' => $fb_access_token,
                'message' => $message,
                'picture' => '',
                'icon' => '',
                'link' => $url,
                'caption' => $_SERVER['SERVER_NAME'],
                'description' => $_SERVER['SERVER_NAME']
            ));
            $fb_check = 'Successfully posted on facebook wall';
        }
        catch(Exception $e) {
            $fb_check = 'Post on facebook error - ' . $e;
        }
    } else {
        $fb_check = 'Facebook API values should not be empty';
    }
}
////////////Post Facebook ends////////////////
////////////Post twitter//////////////////////
$twit_check = '';
if ($_GET['action'] == 'post' && $_GET['to'] != '' && $_GET['to'] == 'twitter') {
    if (!empty($master_db['settings'][18]['Mandatory'][2]['value']) && !empty($master_db['settings'][18]['Mandatory'][3]['value']) && !empty($master_db['settings'][18]['Credentials'][0]['value']) && !empty($master_db['settings'][18]['Credentials'][1]['value'])) {
        function post_tweet($tweet_text, $master_db)
        {
            require_once ('diagnose/tmhoauth/tmhOAuth.php');
            // Set the authorization values
            // In keeping with the OAuth tradition of maximum confusion,
            // the names of some of these values are different from the Twitter Dev interface
            // user_token is called Access Token on the Dev site
            // user_secret is called Access Token Secret on the Dev site
            $connection = new tmhOAuth(array(
                'consumer_key' => $master_db['settings'][18]['Mandatory'][2]['value'],
                'consumer_secret' => $master_db['settings'][18]['Mandatory'][3]['value'],
                'user_token' => $master_db['settings'][18]['Credentials'][1]['value'],
                'user_secret' => $master_db['settings'][18]['Credentials'][0]['value'],
            ));
            // Make the API call
            $connection->request('POST', $connection->url('1/statuses/update') , array(
                'status' => $tweet_text
            ));
            return $connection->response['code'];
        }
        $site = $master_db['settings']['site'][0];
        $tweet_text = 'This is the test post from ' . $site . ' - Twitter on ' . date('h:i:s A', time());
        $result = post_tweet($tweet_text, $master_db);
        if ($result == 200) {
            $twit_check = 'Successfully posted on twitter';
        } else {
            $twit_check = 'Post on twitter error - ' . $result;
        }
    } else {
        $twit_check = 'Twitter API values should not be empty';
    }
}
////////////Post twitter ends//////////////////////
////////////Mail work status//////////////////////
$email_check = '';
if ($_GET['action'] == 'email' && $_GET['to'] != '') {
    $site = $master_db['settings']['site'][0];
    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $_GET['to'])) {
        $email_check = "<div class='invalid_mail'>Invalid email</div>";
    } else {
        $to = $_GET['to'];
        $subject = 'Important: ' . $site . ' Test Email';
        $body = 'This is the test email from ' . $site . '. If you receive this email, your server is more likely working fine.';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers.= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        // More headers
        $headers.= 'From: ' . $site . ' <' . $to . '>' . "\r\n";
        if (@mail($to, $subject, $body, $headers)) {
            $email_check = "Staus: Email is working";
        } else {
            $email_check = "Staus: Email is not working";
        }
    }
}
////////////Mail work status ends//////////////////////

?>
<?php
if (!empty($fb_check)) { ?><center><h2><?php
    echo $fb_check . '<br><a href="' . $_SERVER['PHP_SELF'] . '#fb">Back</a>'; ?></h2></center><?php
} ?>
<?php
if (!empty($twit_check)) { ?><center><h2><?php
    echo $twit_check . '<br><a href="' . $_SERVER['PHP_SELF'] . '#tweet">Back</a>'; ?></h2></center><?php
} ?>
<?php
if (!empty($email_check)) { ?><center><h2><?php
    echo $email_check . '<br><a href="' . $_SERVER['PHP_SELF'] . '#mail">Back</a>'; ?></h2></center><?php
} ?>
<!-- ///////////////////////////////////////////////////////////////// html content ///////////////////////////////////////////////////////////////////////////////////////////////////// -->
<div class="top-block">
<div class="top-block-left">
<h1>GroupDeal Diagnostic Tool</h1>
<em>This tool will check server and software configuration</em> &nbsp;&nbsp;<a href="#final_verdict">Final Verdict</a>
<h2> Step 1: Checking Server Requirements... </h2>
</div>
<div class="list-info-block">

<table class="list-info">
  <tr>
    <td class="green">&nbsp;</td>
    <td> - Requirement Met!</td>
    <td class="red">&nbsp;</td>
    <td > - Requirement not met. <b>Need to fix!</b></td>
  </tr>
  <tr>
    <td class="orange">&nbsp;</td>
    <td> - Requirement met, but, unable to check exact version.</td>
    <td class="yellow">&nbsp;</td>
    <td> - Requirement not met, but, its not madatory.</td>
  </tr>
</table>
</div>
</div>

<table border="2" class="list">

  <tr>
    <th colspan="2"> Settings </th>
    <th> Required Server Settings </th>
    <th> Current  Server Settings </th>
  </tr>
  <tr>
	<th colspan="4">
    	 Mandatory
    </th>
</tr>
<?php
$php_version = PHP_VERSION;
$php_version = explode('.', $php_version);
$class = 'class="red"';
if (5 == $php_version[0] && ((2 == $php_version[1] && $php_version[2] >= 0) || (3 == $php_version[1] && $php_version[2] <= 5))) {
    $class = 'class="green"';
} else {
    $req_not_met = 1;
    $req_not_met_pre = 1;
}
?>
  <tr>
    <td colspan="2"> PHP Version </td>
    <td> 
<p>v1.0b4 and above, 5.2+ (preferably 5.3.5)</p>

		<p>v1.0a1 to v1.0b3, 5.x (preferably 5.2+ and &lt;5.3.0)</p>
		</td>
    <td <?php
echo $class; ?>> <?php
echo PHP_VERSION; ?>	</td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(mysql_get_client_info)) {
    $sql_version = mysql_get_client_info();
    $php_version = explode('.', $sql_version);
    if (5 <= $php_version[0]) {
        $class = 'class="green"';
    } else {
        $requird.= "<li> MySQL Version Should be 5.x </li>";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
} else {
    $sql_version = "-";
    $req_not_met = 1;
    $req_not_met_pre = 1;
}
?>
  <tr>
    <td colspan="2"> MySQL Version </td>
    <td> 5.x </td>
     <td <?php
echo $class; ?>> <?php
echo $sql_version; ?>		</td>
  </tr>
<?php
if (function_exists(get_extension_funcs)) {
    if (get_extension_funcs('gd')) {
        $gd_info = gd_info();
        $gd_version = explode(' ', $gd_info['GD Version']);
        $gd_version = str_replace("(", "", $gd_version[1]);
        $gd_version = explode('.', $gd_version);
        if ($gd_version[0] >= 2) {
            $class = 'class="green"';
        } else {
            $requird.= "<li> PHP Extension GD Version should be need  2.x </li>";
            $req_not_met = 1;
            $req_not_met_pre = 1;
        }
        $gd_version = $gd_info['GD Version'];
    } else {
        $gd_version = " Not Installed";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
}
?>
  <tr>
    <td rowspan="5"> Extensions </td>
    <td> GD </td>
    <td> GD Version - 2.x+ </td>
    <td <?php
echo $class; ?>> <?php
echo $gd_version; ?> </td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(get_extension_funcs)) {
    if (get_extension_funcs('pcre')) {
        $pcre_version = PCRE_VERSION;
        $pcre_versions = explode('.', $pcre_version);
        if (7 <= $pcre_versions[0]) {
            $class = 'class="green"';
        } else {
            $requird.= "<li> PHP Extension PCRE Version should be need  7.x </li>";
            $req_not_met = 1;
            $req_not_met_pre = 1;
        }
    } else {
        $pcre_version = "Not Installed";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
}
?>
  <tr>
    <td> PCRE </td>
    <td>  PCRE Version - 7.x+ </td>
    <td <?php
echo $class; ?>><?php
echo $pcre_version;
?></td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(get_extension_funcs)) {
    if (get_extension_funcs('curl')) {
        $curl_info = curl_version();
        $curl_infos = explode('.', $curl_info['version']);
        if (7 <= $curl_infos[0]) {
            $curl_info = $curl_info['version'];
            $class = 'class="green"';
        } else {
            $requird.= "<li> PHP Extension CURL Version should be need  7.x </li>";
            $req_not_met = 1;
            $req_not_met_pre = 1;
        }
    } else {
        $requird.= "<li> PHP Extension CURL Version should be need  7.x </li>";
        $curl_info = "Not Installed";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
}
?>
  <tr>
    <td> CURL </td>
    <td> CURL version - 7.x+ </td>
    <td <?php
echo $class; ?>><?php
echo $curl_info; ?></td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(get_extension_funcs)) {
    if (get_extension_funcs('json')) {
        $class = 'class="orange"';
        $json_info = "Installed [don't know version]";
        $req_met_unable_check = 1;
    } else {
        $json_info = "Not Installed";
        $requird.= "<li> PHP Extension JSON Version should be need  1.x </li>";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
}
?>
  <tr >
    <td> JSON </td>
    <td> json version - 1.x+ </td>
    <td <?php
echo $class; ?>> <?php
echo $json_info; ?>   </td>
  </tr>
<?php
if (extension_loaded('pdo')) {
    $pdo_info = "Enabled";
    $class = 'class="green"';
} else {
    $pdo_info = "Disabled";
    $class = 'class="red"';
}
?>

  <tr >
    <td> PDO </td>
    <td> <?php
echo $pdo_info; ?> </td>
    <td <?php
echo $class; ?>> <?php
echo $pdo_info; ?>   </td>
  </tr>

<?php
$class = 'class="red"';
if (function_exists(ini_get)) {
    $memory_limit = ini_get('memory_limit');
    $memory_limits = str_replace("M", "", $memory_limit);
    if ($memory_limits >= 32 && $memory_limits < 128) {
        $class = 'class="orange"';
        $req_met_unable_check = 1;
    } elseif ($memory_limits >= 128) {
        $class = 'class="green"';
    } else {
        $requird.= "<li> php.ini settings Memory Limit should be minimum 32M</li>";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
} else {
    $memory_limit = '[don\'t know memory_limit]';
    $req_not_met = 1;
    $req_not_met_pre = 1;
}
?>
  <tr>
  	<td rowspan="3"> php.ini settings </td>
    <td> memory_limit </td>
    <td> 128M </td>
    <td <?php
echo $class; ?>>
    	<?php
echo $memory_limit; ?>
    </td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(ini_get)) {
    if (ini_get('safe_mode')) {
        $safe_mode = "ON";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    } else {
        $class = 'class="green"';
        $safe_mode = "OFF";
    }
} else {
    $class = 'class="orange"';
    $safe_mode = '[don\'t know safe_mode status]';
    $req_met_unable_check = 1;
}
?>
  <tr>
    <td> safe_mode </td>
    <td> OFF </td>
    <td <?php
echo $class; ?>>
    	<?php
echo $safe_mode; ?>
    </td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(ini_get)) {
    if (ini_get('open_basedir')) {
        $open_basedir = ini_get('open_basedir');
        $req_not_met = 1;
        $req_not_met_pre = 1;
    } else {
        $class = 'class="green"';
        $open_basedir = "No Value";
    }
} else {
    $class = 'class="orange"';
    $open_basedir = '[don\'t know open_basedir status]';
    $req_met_unable_check = 1;
}
?>
  <tr>
    <td> open_basedir </td>
    <td> No Value </td>
     <td <?php
echo $class; ?>>
    	<?php
echo $open_basedir; ?>
    </td>
  </tr>
<?php
$class = 'class="red"';
if (function_exists(apache_get_version)) {
    $apace_version = apache_get_version();
    $apace_version_info = explode(" ", $apace_version);
    $apace_version_info = explode('/', $apace_version_info[0]);
    $apace_version_info = explode('.', $apace_version_info[1]);
    if ($apace_version_info[0] >= 2) $class = 'class="green"';
    else if ($apace_version_info[0] == 1) $class = 'class="orange"';
    $req_met_unable_check = 1;
} else {
    $version = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
    $apace_version_info = explode('/', $version[0]);
    $apace_version_info = explode('.', $apace_version_info[1]);
    if ($apace_version_info[0] >= 2) $class = 'class="green"';
    else if ($apace_version_info[0] == 1) $class = 'class="orange"';
    $req_met_unable_check = 1;
    $apace_version = $version[0];
}
?>
  <tr>
    <td colspan="2">Apache  </td>
    <td> 1+ (preferably 2+) </td>
    <td <?php
echo $class; ?>>
    	<?php
echo $apace_version; ?>
    </td>
  </tr>

<?php
if (function_exists(apache_get_modules)) {
    $modules = apache_get_modules();
    $class = 'class="red"';
    if (in_array("mod_rewrite", $modules)) {
        $class = 'class="green"';
        $mod_rewrite = "Loaded";
    } else {
        $mod_rewrite = "Not Loaded";
        $req_not_met = 1;
        $req_not_met_pre = 1;
    }
?>
 <tr>
    <td rowspan="1"> Loaded  Modules </td>
    <td> mod_rewrite </td>
    <td> load </td>
    <td <?php
    echo $class; ?>> <?php
    echo $mod_rewrite; ?> </td>
  </tr>

<?php
}
?>
<tr>
	<th colspan="4">
    	Not Mandatory
    </th>
</tr>
<?php
$class = 'class="green"';
if (function_exists(ini_get)) {
    $max_execution_time = ini_get('max_execution_time');
    if ($max_execution_time < 180) {
        $class = 'class="yellow"';
        $req_not_met_no_man = 1;
    }
} else {
    $max_execution_time = '[don\'t know max_execution_time]';
}
?>
  <tr>
    <td rowspan="2"> php.ini settings </td>
    <td> max_execution_time (not mandatory)</td>
    <td> 180  </td>
    <td <?php
echo $class; ?>>
    	<?php
echo $max_execution_time; ?>
    </td>
  </tr>
<?php
$class = 'class="green"';
if (function_exists(ini_get)) {
    $max_input_time = ini_get('max_input_time');
    if ($max_input_time < 6000) {
        $class = 'class="yellow"';
        $req_not_met_no_man = 1;
    }
} else {
    $max_input_time = '[don\'t know max_input_time]';
}
?>
  <tr>
    <td> max_input_time (not mandatory)</td>
    <td> 6000  </td>
    <td <?php
echo $class; ?>>
    	<?php
echo $max_input_time; ?>
    </td>
  </tr>

<?php
if (function_exists(apache_get_modules)) {
?>

<?php
    $class = 'class="yellow"';
    if (in_array("mod_deflate", $modules)) {
        $class = 'class="green"';
        $mod_deflate = "Loaded";
    } else {
        $class = 'class="yellow"';
        $mod_deflate = "Not Loaded";
        $req_not_met_no_man = 1;
    }
?>
  <tr>
  	<td rowspan="2"> Loaded  Modules </td>
    <td> mod_deflate (not mandatory, but highly recommended for better performance - gzip) </td>
    <td> load </td>
    <td <?php
    echo $class; ?>><?php
    echo $mod_deflate; ?></td>
  </tr>
<?php
    $class = 'class="yellow"';
    if (in_array("mod_rewrite", $modules)) {
        $class = 'class="green"';
        $mod_rewrite = "Loaded";
    } else {
        $mod_rewrite = "Not Loaded";
        $req_not_met_no_man = 1;
    }
?>
  <tr >
    <td> mod_expires (not mandatory, but highly recommended for better performance - browser caching)</td>
    <td> load </td>
    <td <?php
    echo $class; ?>><?php
    echo $mod_rewrite; ?></td>
  </tr>
<?php
}
?>

</table>
<?php
$pre_launch_mode_flag = 0;
if ($req_not_met_pre == 1) {
    $pre_launch_mode_flag = 1;
}
?>
<p class="info-details">
	<span> Note: </span>
    <span> If any of the above settings are displayed 'Red'. It means, your current server settings are not compatable for the site. Contact your service provider at once. </span>
</p>
<h2>Step 2: Checking File Permissions...</h2>
<table border="2" class="list">
<tr>
    <th> Folders</th>
    <th> Permissions</th>
</tr>
<?php
echo $writable; ?>
</table>

<p class="info-details">
	<span> Note: </span>
    <span>
		The above folders showed be writable and give '777' permission for the site to work properly. Use either putty or FTP to set the permission if is not set.
		<br/>For more info: <a href="http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#setting_up_files" target="_blank">http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#setting_up_files</a>
	</span>
</p>

<h2>Step 3: Checking Database Setting...</h2>
<table width="500" border="2" class="list">
  <tr>
    <th>&nbsp;</th>
    <th>Master</th>
    <th>Default</th>
  </tr>
  <tr>
    <td>MySql Connect</td>
    <td class="<?php
echo ($master_db['db_connection'] == 'Connected') ? 'green' : 'red'; ?>"><?php
echo $master_db['db_connection'];
if ($master_db['db_connection'] != 'Connected') {
    $req_not_met = 1;
} ?></td>
    <td class="<?php
echo ($default_db['db_connection'] == 'Connected') ? 'green' : 'red'; ?>"><?php
echo $default_db['db_connection']; ?></td>
  </tr>
  <tr>
    <td>Connect Database</td>
    <td class="<?php
echo ($master_db['db_connection'] == 'Connected') ? 'green' : 'red'; ?>"><?php
echo $master_db['db_connect']; ?></td>
    <td class="<?php
echo ($default_db['db_connection'] == 'Connected') ? 'green' : 'red'; ?>"><?php
echo $default_db['db_connect']; ?></td>
  </tr>
</table>
<p class="info-details">
	<span> Note: </span>
    <span>
		Verify your database connectivity if above showed in 'Red' background color.
		<br/>For more info: <a href="http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#setting_up_database" target="_blank">http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#setting_up_database</a>
	</span>
</p>
<h2>Step 4: Checking Server Configuration...</h2>
<table border="2" class="list">
<tr>
	<th> Settings </th>
    <th> Install? </th>
</tr>
<?php
$ssl_enable = 'Installed';
$url = "https://" . $_SERVER['SERVER_NAME'];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if (curl_exec($ch) === false) {
    $ssl_enable = 'Not Installed';
}
curl_close($ch);
?>
  <tr>
    <td>SSL Certificate</td>
    <td class="<?php
echo ($ssl_enable == 'Installed') ? 'green' : 'yellow'; ?>">
	   <?php
echo $ssl_enable; ?>
    </td>
  </tr>
<?php
$ssl_enable = 'Configured';
$url = "http://m." . $_SERVER['SERVER_NAME'];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if (curl_exec($ch) === false) {
    if ($master_db['settings']['site'][1]) //If mobile layout is enabled in admin section.
    {
        $ssl_enable = 'm.&lt;domain name&gt; points to the "public_html" folder which have the app & core folders.';
    } else {
        $ssl_enable = 'Not Installed';
    }
}
// Close handle
curl_close($ch);
?>
  <tr>
    <td>Mobile Version (Subdomain configured for m.<?php echo $_SERVER['SERVER_NAME']; ?> ?)</td>
    <td class="<?php
echo ($ssl_enable == 'Configured') ? 'green' : 'red'; ?>">
		<?php
echo $ssl_enable; ?>
    </td>
  </tr>
<?php
if (!empty($master_db['settings'][1]['site_setting'][0]['value'])) {
    $site = $master_db['settings']['site'][0];
    $to = $master_db['settings'][1]['site_setting'][0]['value'];
    $subject = 'Important: ' . $site . ' Test Email';
    $body = 'This is the test email from ' . $site . '. If you receive this email, your server is more likely working fine.';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers.= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    // More headers
    $headers.= 'From: ' . $site . ' <' . $to . '>' . "\r\n";
    if (@mail($to, $subject, $body, $headers)) {
        $email = "Working";
    } else {
        $email = "Not Working";
    }
} else {
    $email = "Admin email should not be empty";
}
?>
  <tr>
    <td> Mail System</td>
    <td class="<?php
echo ($email == 'Working') ? 'green' : 'red'; ?>">
		<?php
if ($email == 'Working') {
    $to = $master_db['settings'][1]['site_setting'][0]['value'];
    echo $admin_mail_link = '<a href="' . $_SERVER['PHP_SELF'] . '?action=email&amp;to=' . $to . '" id="mail">Send test mail</a> to ' . $to;
} else {
    echo $email;
} ?>
    </td>
  </tr>
<?php
$username = "";
$password = "";
$url = 'http://' . $_SERVER['HTTP_HOST'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
if ($info['http_code'] == 401) {
    $htaccess_papp_protected = "Protected";
    $class = "red";
} else {
    $htaccess_papp_protected = "Not Protected";
    $class = "green";
}
?>
  <tr>
    <td>htaccess Password Protected</td>
    <td class="<?php
echo $class; ?>">
		<?php
echo $htaccess_papp_protected; ?>
    </td>
  </tr>
</table>
<p class="info-details">
	<span> Note: </span>
    <span>
		If you want to use SSL, make you have purchased SSL Certificate and contact the service provider. If you don't need it, you can ignore the SSL warning showed above.
		<br/>For more info: <a href="http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#manage_site_settings" target="_blank">http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#manage_site_settings</a>
	</span>
</p>
<?php
if ($master_db['payment_gateway']['error'] == 'None') { ?>
<h2>Step 5: Checking Third Party Configuration...</h2>

<table border="2" class="list">
  <tr align="center">
    <th colspan="6"> Payment Gateway</th>
  </tr>
  <tr>
    <th>Gateway</th>
    <th>Active?</th>
    <th>Live mode?</th>
    <th>Enable for Deal Purchase?</th>
    <th>Enable for Gift Card Purchase?</th>
    <th>Enable add to wallet?</th>
  </tr>

  <?php
    $payment_gateway_active = "no";
    $payment_gateway_inactive = "no";
    foreach($master_db['payment_gateway']['gateway'] as $gateway) {
        if ($gateway['is_active']) {
            $payment_gateway_active = "yes";
            if (!$gateway['is_test_mode']) {
                $class = 'green';
            } else {
                $class = 'red';
                $req_not_met = 1;
            }
        } else {
            $payment_gateway_inactive = "yes";
            $class = '';
        }
?>
  <tr>
    <td><?php
        echo $gateway['display_name']; ?></td>
    <td><?php
        echo ($gateway['is_active']) ? 'Yes' : 'No'; ?></td>
    <td class="<?php
        echo $class; ?>"><?php
        echo (!$gateway['is_test_mode']) ? 'Yes' : 'No'; ?></td>
    <td><?php
        echo ($gateway['is_enable_for_buy_a_deal']) ? 'Yes' : 'No'; ?></td>
    <td><?php
        echo ($gateway['is_enable_for_gift_card']) ? 'Yes' : 'No'; ?></td>
    <td><?php
        echo ($gateway['is_enable_for_add_to_wallet']) ? 'Yes' : 'No'; ?></td>
  </tr>
  <?php
    } ?>
</table>
<?php
    if ($payment_gateway_inactive == "yes" && $payment_gateway_active == "no") { ?>
<div class="pay_gate_req"> All payment gateways are disabled. Please select atleast one.
</div>
<?php
    } ?>
<table width="400" border="2" class="list">
    <tr>
        <th colspan="4">Payment Gateway Settings</th>
    </tr>
    <tr>
        <th>Cateway</th>
        <th>Settings</th>
        <th>Live</th>
    </tr>
	<?php
    $setting_labels = array(
        'payee_account' => 'Payee Account',
        'receiver_emails' => 'Receiver Emails',
        'masspay_API_UserName' => 'Masspay API UserName',
        'masspay_API_Password' => 'Masspay API Password',
        'masspay_API_Signature' => 'Masspay API Signature',
        'directpay_API_Signature' => 'Directpay API Signature',
        'directpay_API_Password' => 'Directpay API Password',
        'directpay_API_UserName' => 'Directpay API UserName',
        'authorize_net_api_key' => 'Authorize Net Api Key',
        'authorize_net_trans_key' => 'Authorize Net Trans Key',
        'token' => 'Token',
    );
    foreach($master_db['payment_gateway']['gateway'] as $key => $gateway) {
        $i = 0;
        foreach($gateway['settings'] as $setting) {
            if ($setting['key'] != 'is_enable_wallet') {
                if ($i == 0) {
?>
                    <tr>
                        <td rowspan="<?php
                    echo (count($gateway['settings']) -1); ?>"><?php
                    echo $gateway['display_name']; ?></td>
                        <td><?php
                    echo $setting_labels[$setting['key']]; ?></td>
                        <td class="<?php
                    echo ($setting['live_mode_value']) ? 'green' : 'red'; ?>">
                        <?php
                    if (!$setting['live_mode_value']) {
                        $req_not_met = 1;
                    }
                    if ($setting['key'] != 'masspay_API_Password' && $setting['key'] != 'masspay_API_Signature' && $setting['key'] != 'directpay_API_Signature' && $setting['key'] != 'directpay_API_Password' && $setting['key'] != 'authorize_net_trans_key' && $setting['key'] != 'token') {
                        echo ($setting['live_mode_value']) ? $setting['live_mode_value'] : 'no Value';
                    } else {
                        echo ($setting['live_mode_value']) ? 'yes (Security purpose hide value)' : 'no Value';
                    }
?>
                        <?php
                    if ($setting_labels[$setting['key']] == 'Payee Account' && $key == 0) { ?>
                        <div style="background-color:#FFFFFF; color:#000000;">
                        <br />Your PayPal account has to be in "verified" status. <a href="https://www.paypal.com/verified/pal=<?php
                        echo $setting['live_mode_value']; ?>"
                        target="_blank">Check</a> your status in PayPal.
                        </div>
                        <?php
                    } ?>
                        </td>
                    </tr>
				<?php
                } else {
?>
<tr>
<td><?php
                    echo $setting_labels[$setting['key']]; ?></td>
<td class="<?php
                    echo ($setting['live_mode_value']) ? 'green' : 'red'; ?>">
<?php
                    if (!$setting['live_mode_value']) {
                        $req_not_met = 1;
                    }
                    if ($setting['key'] != 'masspay_API_Password' && $setting['key'] != 'masspay_API_Signature' && $setting['key'] != 'directpay_API_Signature' && $setting['key'] != 'directpay_API_Password' && $setting['key'] != 'authorize_net_trans_key' && $setting['key'] != 'token') {
                        echo ($setting['live_mode_value']) ? $setting['live_mode_value'] : 'No Value';
                    } else echo ($setting['live_mode_value']) ? 'yes (Security purpose hide value)' : 'No Value';
?>
</td>
</tr>
  <?php
                }
                $i++;
            }
        }
    } ?>
</table>

<table class="list">
    <tr>
        <th>Priority</th>
        <th>Name</th>
        <th>Value</th>
    </tr>
	<?php
	
    foreach($master_db['settings'] as $key => $setting) {
        $assigned_keys = array(
            8,
            18,
            1,
            'msn',
            'yahoo'
        );
        if (in_array($key, $assigned_keys)) {
?>
            <tr>
                <th colspan="3">
                <?php
            switch ($key) {
                case 8: // facebook
                    echo 'Facebook - <a href="' . $_SERVER['PHP_SELF'] . '?action=post&amp;to=facebook" id="fb">Test post</a> to wall';
                    break;

                case 18: // Twitter
                    echo 'Twitter - <a href="' . $_SERVER['PHP_SELF'] . '?action=post&amp;to=twitter" id="tweet">Test post</a> to Twitter';
                    break;

                case 'msn': // Twitter
                    echo 'Invite MSN';
                    break;

                case 'yahoo': // Twitter
                    echo 'Invite Yahoo';
                    break;

                case 1: // Site Settings
                    echo "Site Settings";
                    break;
            }
?>
                </th>
            </tr>
	<?php
        } ?>
	<?php
        if (is_array($setting)) {
		if($key == 8) //To display facebook credentials in the last rows
		{
			$setting['Mandatory'][11] = $setting['Mandatory'][7];
			$setting['Mandatory'][12] = $setting['Mandatory'][8];
            unset($setting['Mandatory'][7]);
			unset($setting['Mandatory'][8]);
		}	
		if($key == 18) //To display twitter credentials in the last rows
		{
			$setting['Mandatory'][11] = $setting['Mandatory'][4];
			$setting['Mandatory'][12] = $setting['Mandatory'][5];
            unset($setting['Mandatory'][4]);
			unset($setting['Mandatory'][5]);
		}	
			foreach($setting as $data => $step) {
                $i = 0;
				if($key == 8 || $key == 18)
				{
					$count = count($step)+1;
				} else {
					$count = count($step);
				}
                if (is_array($step)) {
                    foreach($step as $mantry) {
                        $class = 'yellow';
                        if ($data != 'Not Mandatory') {
                            $class = (!empty($mantry['value'])) ? 'green' : 'red';
                        } else {
                            $class = (!empty($mantry['value'])) ? 'green' : 'yellow';
                        }
                        if ($class == 'red') {
                            $req_not_met = 1;
                        }
                        if ($class == 'yellow') {
                            $req_not_met_no_man = 1;
                        }
                        if ($i == 0) {
?>
						<tr>
							<td rowspan="<?php
                            echo $count; ?>"><?php
                            echo ($data == 'site_setting')?'':$data; ?></td>
							<td><?php
                            echo $mantry['label']; ?></td>
							<td class="<?php
                            echo $class; ?> "><?php
                            if ($mantry['name'] == 'site.contact_email') {
                                echo ($mantry['value']) ? $mantry['value'] : 'Contact email should not be empty';
                            } else {
                                echo ($mantry['value']) ? $mantry['value'] : 'No Value';
                            }
?>
							</td>
						</tr>
					<?php
                        } else {
						if($mantry['name'] == 'facebook.fb_access_token')
						{
?>
							<tr>
							<td colspan="2">
                            <div class="cred_detail">
                            The following 2 settings will automatically be updated when clicking "Update Facebook Credentials" link in Facebook tab found under admin settings.
                            </div>
                            </td>
                            </tr>
<?php							
						}

						if($mantry['name'] == 'twitter.site_user_access_key')
						{
?>
							<tr>
							<td colspan="2">
                            <div class="cred_detail">
                            The following 2 settings will automatically be updated when clicking "Update Twitter Credentials" link in Twitter tab found under admin settings.
                            </div>
                            </td>
                            </tr>
<?php							
						}
?>
						<tr>
							<td><?php
                            echo $mantry['label']; ?></td>
							<?php
                            if ($mantry['name'] == 'site.maintenance_mode' || $mantry['name'] == 'site.is_in_prelaunch_mode') {
                                if (!empty($mantry['value'])) {
                                    $class = 'red';
                                } else {
                                    $class = 'green';
                                }
                            }
?>
							<td class="<?php
                            echo $class; ?> "><?php
                            $settings_check_yes_no = array(
                                'twitter.tweets_around_city',
                                'facebook.like_box',
                                'facebook.feeds_code'
                            );
                            $settings_check_one_zero = array(
                                'site.is_in_prelaunch_mode',
                                'site.maintenance_mode',
                                'facebook.enable_facebook_post_open_deal',
                                'twitter.prompt_for_email_after_register',
                                'twitter.enable_twitter_post_open_deal',
                                'twitter.is_twitter_feed_enabled'
                            );
                            if (in_array($mantry['name'], $settings_check_yes_no)) {
                                echo ($mantry['value']) ? 'yes' : 'No Value';
                            } elseif (in_array($mantry['name'], $settings_check_one_zero)) {
                                echo ($mantry['value']) ? 'On' : 'Off';
                            } else {
                                echo ($mantry['value']) ? $mantry['value'] : 'No Value';
                            }
                            if ($mantry['name'] == 'site.is_in_prelaunch_mode') {
                                if ($mantry['value']) {
                                    if ($pre_launch_mode_flag) {
                                        $pre_launch_mode = 'Pre-launch mode is ON. But, can\'t proceed. Please fix the above remarks.';
                                        $setclass_pre_launch = 'red_req_details';
                                    } else {
                                        $pre_launch_mode = 'Success, you should be able access Pre-launch mode';
                                        $setclass_pre_launch = 'green_req_details';
                                    }
                                }
                            }
?>
							</td>
						</tr>
						<?php
                        }
                        $i++;
                    }
                }
            }
        }
    }
?>
</table>
<p class="info-details">
	<span> Note: </span>
    <span>
		Above shows the settings enabled/disabled currently in the site. Login in as Administrator and go to "Settings" to manage the above displayed ones.
		<br/>For more info: <a href="http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#manage_site_settings" target="_blank">http://dev1products.dev.agriya.com/doku.php?id=groupdeal-install#manage_site_settings</a>
    </span>
</p>
<?php
} ?>
<h2>Step 6: Checking Server Time...</h2>
<table border="2" class="list">
    <tr>
        <th> Settings </th>
        <th> Time </th>
    </tr>
    <tr>
        <td>Atomic Time (sec)</td>
        <td class=""><?php
echo GetAtomicTime(); ?></td>
    </tr>
    <tr>
        <td>Server Time (sec)</td>
        <td class=""><?php
echo time(); ?></td>
    </tr>
    <tr>
        <td>Difference (sec)</td>
        <td class=""><?php
echo abs(GetAtomicTime() -time()); ?></td>
    </tr>
    <?php
if (abs(GetAtomicTime() -time()) > 60*5) { ?>
        <tr>
            <td colspan="2" class="red">Difference is greater than 5 mins</td>
        </tr>
    <?php
} else { ?>
        <tr>
            <td colspan="2" class="green">Difference is less than 5 mins</td>
        </tr>
    <?php
} ?>
</table>
<p class="info-details">
	<span> Note: </span>
    <span>
		Timing difference may affect "Signin with Twitter", Facebook, Google, Yahoo!, etc
	</span>
</p>


<?php
$url = "http://" . $_SERVER['SERVER_NAME'] . "/";
if ($req_not_met == 1) {
    $req_sts = 'Sorry, can\'t proceed. Please fix the above remarks.';
    $setclass = 'red_req_details';
} elseif ($req_not_met_no_man == 1) {
    $req_sts = 'Alert: You may able to run <a href="' . $url . '">the site</a>. But, you\'re strongly advised to fix above warnings.';
    $setclass = 'yellow_req_details';
} /*elseif($req_met_unable_check ==1) {
$req_sts = 'Requirement met, but, unable to check exact version.';
$setclass = 'orange_req_details';
}*/
else {
    $req_sts = 'Success, you should be able to run <a href="' . $url . '">the site</a>';
    $setclass = 'green_req_details';
}
?>
<h1>Final Verdict</h1>
<div align="center" class="<?php
echo $setclass; ?>" id="final_verdict"><h1><?php
echo $req_sts; ?></h1></div>
<?php
if (!empty($pre_launch_mode)) { ?>
<div align="center" class="<?php
    echo $setclass_pre_launch; ?>" id="final_verdict"><h1><?php
    echo $pre_launch_mode; ?></h1></div>
<?php
} ?>

</body>
</html>
