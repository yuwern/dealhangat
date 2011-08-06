<?php
/* SVN: $Id: config.php 59805 2011-07-11 06:23:05Z aravindan_111act10 $ */
/**
 * Custom configurations
 */
// site actions that needs random attack protection...
if (!defined('DEBUG')) {
	define('DEBUG', 0);
	// permanent cache re1ated settings
	define('PERMANENT_CACHE_CHECK', (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '127.0.0.1') ? true : false);
	// site default language
	define('PERMANENT_CACHE_DEFAULT_LANGUAGE', 'en');
	// cookie variable name for site language
	define('PERMANENT_CACHE_COOKIE', '');
	// sub admin is available in site or not
	define('PERMANENT_CACHE_HAVE_SUB_ADMIN', false);
	define('IS_ENABLE_HASHBANG_URL', false);
	$_is_hashbang_supported_bot = (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false);
	define('IS_HASHBANG_SUPPORTED_BOT', $_is_hashbang_supported_bot);
}
$config['site']['_hashSecuredActions'] = array(
    'edit',
    'delete',
    'update',
    'unsubscribe',
    'barcode',
    'update_status',
    'resend',
    'my_account',
    'view_gift_card'
);
$config['site']['domain'] = 'dealhangat.dyndns.biz';
$config['photo']['file'] = array(
    'allowedMime' => array(
        'image/jpeg',
		'image/jpg',
        'image/gif',
        'image/png'
    ) ,
    'allowedExt' => array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    ) ,
    'allowedSize' => '5',
    'allowedSizeUnits' => 'MB',
	'allowEmpty' => true
);
$config['image']['file'] = array(
    'allowedMime' => array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    ) ,
    'allowedExt' => array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    ) ,
    'allowedSize' => '5',
    'allowedSizeUnits' => 'MB',
    'allowEmpty' => false
);
$config['avatar']['file'] = array(
    'allowedMime' => array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    ) ,
    'allowedExt' => array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    ) ,
    'allowedSize' => '5',
    'allowedSizeUnits' => 'MB',
    'allowEmpty' => true
);
$config['pagelogo']['file'] = array(
    'allowedMime' => array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    ) ,
    'allowedExt' => array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    ) ,
    'allowedSize' => '5',
    'allowedSizeUnits' => 'MB',
    'allowEmpty' => true
);
$config['widget_no_scroll'] = array(1, 2, 3, 4);
$config['site']['search_distance'] = 30000;

$config['cdn']['images'] = null; // 'http://images.localhost/';
$config['cdn']['css'] = null; // 'http://static.localhost/';

$config['site']['is_admin_settings_enabled'] = true;
if ($_SERVER['HTTP_HOST'] == 'groupdeal.dev.agriya.com' && !in_array($_SERVER['REMOTE_ADDR'], array('118.102.143.2', '119.82.115.146', '122.183.135.202', '122.183.136.34','122.183.136.36'))) {
	$config['site']['is_admin_settings_enabled'] = false;
	$config['site']['admin_demomode_updation_not_allowed_array'] = array(
		'cities/admin_delete',
		'cities/admin_update',
		'cities/admin_edit',
		'cities/admin_update_status',
		'countries/admin_update',
		'countries/admin_delete',
		'countries/admin_edit',
		'countries/admin_update_status',
		'states/admin_update',
		'states/admin_delete',
		'states/admin_edit',
		'states/admin_update_status',
		'pages/admin_edit',
		'pages/admin_delete',
		'subscriptions/admin_subscription_customise',
	);
}
$config['action']['cache_duration'] = 86400;
?>
