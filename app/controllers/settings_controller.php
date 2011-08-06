<?php
class SettingsController extends AppController
{
    public $components = array(
        'Cookie'
    );
    public function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'SiteLogo.filename',
            'Setting'
        );
        parent::beforeFilter();
    }
    public function admin_index()
    {
        $setting_categories = $this->Setting->SettingCategory->find('all', array(
            'conditions' => array(
                "NOT" => array(
                    "SettingCategory.id" => array(
                        7,
                        27
                    )
                )
            ) , // Images category will not showed
            'name' => array(
                'order ASC'
            ) ,
            'recursive' => - 1
        ));
        $this->set('setting_categories', $setting_categories);
    }
    public function admin_edit($category_id = 1)
    {
        $this->loadModel('Language');
        $this->loadModel('City');
        $this->loadModel('Attachment');
        $this->loadModel('SiteLogo');
        $this->loadModel('Timezone');
        $this->loadModel('CurrencyConversion');
        $save_check_flag = 0;
        $ssl_enable = true;
        $this->disableCache();
        if (!empty($this->request->data)) {
            if (Configure::read('site.is_admin_settings_enabled')) {
                // Save settings
                if (isset($this->request->data['Setting']['delete_thumb_images'])) {
                    $imageSettings = $this->Setting->find('all', array(
                        'conditions' => array(
                            'Setting.setting_category_id' => $this->request->data['Setting']['setting_category_id'],
                            'SettingCategory.name' => 'Images'
                        ) ,
                        'fields' => array(
                            'Setting.id',
                            'Setting.name',
                            'Setting.value'
                        ) ,
                        'recursive' => 0
                    ));
                    foreach($imageSettings as $imageSetting) {
                        if ($this->request->data['Setting'][$imageSetting['Setting']['id']]['name'] != trim($imageSetting['Setting']['value'])) {
                            $thumb_size = explode('.', $imageSetting['Setting']['name']);
                            $dir = WWW_ROOT . 'img' . DS . $thumb_size[1];
                            $this->_traverse_directory($dir, 0);
                        }
                    }
                    unset($this->request->data['Setting']['delete_thumb_images']);
                }
                if (!empty($this->request->data['Setting']['72'])) {
                    $this->Cookie->write('user_language', $this->request->data['Setting']['72']['name'], false);
                }
                $category_id = $this->request->data['Setting']['setting_category_id'];
                unset($this->request->data['Setting']['setting_category_id']);
                if (isset($this->request->data['Setting']['not_allow_beyond_original']) || isset($this->request->data['Setting']['allow_handle_aspect'])) {
                    $settings = $this->Setting->find('all', array(
                        'conditions' => array(
                            'Setting.setting_category_id = ' => $category_id
                        ) ,
                        'recursive' => 0
                    ));
                    foreach($settings as $setting) {
                        $field_name = explode('.', $setting['Setting']['name']);
                        if (isset($field_name[2]) && ($field_name[2] == 'is_not_allow_resize_beyond_original_size' || $field_name[2] == 'is_handle_aspect')) {
                            if ($field_name[2] == 'is_not_allow_resize_beyond_original_size') {
                                $setting_data['Setting']['id'] = $setting['Setting']['id'];
                                $setting_data['Setting']['value'] = in_array($setting['Setting']['id'], $this->request->data['Setting']['not_allow_beyond_original']) ? 1 : 0;
                                $this->Setting->save($setting_data['Setting']);
                                $save_check_flag = 1;
                            } else if ($field_name[2] == 'is_handle_aspect') {
                                $setting_data['Setting']['id'] = $setting['Setting']['id'];
                                $setting_data['Setting']['value'] = in_array($setting['Setting']['id'], $this->request->data['Setting']['allow_handle_aspect']) ? 1 : 0;
                                $this->Setting->save($setting_data['Setting']);
                                $save_check_flag = 1;
                            }
                        }
                    }
                    unset($this->request->data['Setting']['not_allow_beyond_original']);
                    unset($this->request->data['Setting']['allow_handle_aspect']);
                }
                foreach($this->request->data['Setting'] as $id => $value) {
                    $settings['Setting']['id'] = $id;
                    if ($id == '280') { // Writing default city name in cache.
                        Cache::delete('site_paypal_conversion_currency');
                        Cache::delete('site_paypal_conversion_currency_rate');
                        Cache::delete('site_supported_currencies');
                        Cache::delete('site_authorizenet_conversion_currency');
                        Configure::write('site.currency_id', $value['name']);
                    }
                    if ($id == '97') { // Writing default city name in cache.
                        if (($default_city = Cache::read('site.default_city', 'long')) === false) {
                            Cache::write('site.default_city', $value['name'], 'long');
                        } else {
                            Cache::delete('site.default_city', 'long');
                            Cache::write('site.default_city', $value['name'], 'long');
                        }
                    }
                    if ($id == '159') { // Writing city routing url in cache
                        if (($city_url = Cache::read('site.city_url', 'long')) === false) {
                            Cache::write('site.city_url', $value['name'], 'long');
                        } else {
                            Cache::delete('site.city_url', 'long');
                            Cache::write('site.city_url', $value['name'], 'long');
                        }
                    }
                    if ($id == '289') { // Writing conversion currency details in cache
                        Cache::delete('site_paypal_conversion_currency');
                        Cache::delete('site_paypal_conversion_currency_rate');
                        Cache::delete('site_supported_currencies');
                        Cache::delete('site_authorizenet_conversion_currency');
                        Configure::write('site.paypal_currency_converted_id', $value['name']);
                        $this->_cacheWriteCurrency();
                    }
                    if (count($value['name']) == 1) {
                        $settings['Setting']['value'] = $value['name'];
                        $this->Setting->save($settings['Setting']);
                        $save_check_flag = 1;
                    } else {
                        if (!empty($value['name']['name'])) {
                            $this->layout = 'ajax';
                            $this->Setting->SiteLogo->Behaviors->attach('ImageUpload', Configure::read('avatar.file'));
                            $ini_upload_error = 1;
                            if ($value['name']['error'] == 1) {
                                $ini_upload_error = 0;
                            }
                            $this->request->data['SiteLogo']['filename'] = $value['name'];
                            $this->request->data['SiteLogo']['filename']['type'] = get_mime($value['name']['tmp_name']);
                            $this->request->data['SiteLogo']['filename']['name'] = $value['name']['name'];
                            $this->Setting->SiteLogo->set($this->request->data);
                            if ($ini_upload_error && $this->Setting->SiteLogo->validates()) {
                                $settings['Setting']['value'] = $value['name']['name'];
                                $this->Setting->save($settings['Setting']);
                                $attachment = $this->Setting->SiteLogo->find('first', array(
                                    'conditions' => array(
                                        'SiteLogo.foreign_id' => $settings['Setting']['id'],
                                        'SiteLogo.class' => 'Setting'
                                    ) ,
                                    'recursive' => - 1
                                ));
                                if (!empty($attachment['SiteLogo']['id'])) {
                                    $this->request->data['SiteLogo']['id'] = $attachment['SiteLogo']['id'];
                                }
                                $this->Attachment->create();
                                $this->request->data['SiteLogo']['class'] = 'SiteLogo';
                                $this->request->data['SiteLogo']['foreign_id'] = $id;
                                $this->Attachment->save($this->request->data['SiteLogo']);
                                $this->autoRender = false;
                                $save_check_flag = 1;
                            } else {
                                if (!empty($this->Setting->SiteLogo->validationErrors)) {
                                    $this->Setting->validationErrors[$settings['Setting']['id']]['name'] = $this->Setting->SiteLogo->validationErrors['filename'];
                                    $save_check_flag = 0;
                                }
                                if ($value['name']['error'] == 1) {
                                    $this->Setting->validationErrors[$settings['Setting']['id']]['name'] = sprintf(__l('The file uploaded is too big, only files less than %s permitted') , ini_get('upload_max_filesize'));
                                }
                                $this->Session->setFlash(__l('Sitelogo Image is not uploaded. Please try again ') , 'default', null, 'error');
                            }
                        }
                    }
                }
                if (!empty($save_check_flag)) {
                    $this->Session->setFlash(__l('Config settings updated') , 'default', null, 'success');
                }
            } else {
                $this->Session->setFlash(__l('Sorry. You Cannot Update the Settings in Demo Mode') , 'default', null, 'error');
            }
            Cache::delete('setting_key_value_pairs');
        }
        $this->request->data['Setting']['setting_category_id'] = $category_id;
        $settings = $this->Setting->find('all', array(
            'conditions' => array(
                'Setting.setting_category_id = ' => $category_id
            ) ,
            'order' => array(
                'Setting.order' => 'asc'
            ) ,
            'recursive' => 0
        ));
        if ($category_id == 1) {
            $url = "https://" . $_SERVER['SERVER_NAME'];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (curl_exec($ch) === false) {
                $ssl_enable = false;
            }
            // Close handle
            curl_close($ch);
        }
        $this->request->data['Setting']['setting_category_id'] = $category_id;
        $setting_category = $this->Setting->SettingCategory->find('first', array(
            'conditions' => array(
                'SettingCategory.id = ' => $category_id
            ) ,
            'recursive' => - 1
        ));
        $this->set('settings_category', $setting_category);
        if (!empty($settings) && $settings[0]['SettingCategory']['name'] == 'Site') {
            $languageOptions = array();
            $cityOptions = array();
            $languages = $this->Language->find('all', array(
                'conditions' => array(
                    'Language.is_active' => 1
                ) ,
                'fields' => array(
                    'Language.name',
                    'Language.iso2'
                )
            ));
            $timezones = $this->Timezone->find('all', array(
                'fields' => array(
                    'Timezone.name',
                    'Timezone.code',
                    'Timezone.gmt_offset'
                ) ,
                'recursive' => - 1
            ));
            if (!empty($timezones)) {
                foreach($timezones as $timezone) {
                    $timezoneOptions[$timezone['Timezone']['code']] = $timezone['Timezone']['name'];
                }
            }
            $this->set(compact('timezoneOptions', 'timezoneOptions'));
            $cities = $this->City->find('all', array(
                'conditions' => array(
                    'City.is_approved' => 1
                ) ,
                'fields' => array(
                    'City.name',
                    'City.slug',
                )
            ));
            if (!empty($languages)) {
                foreach($languages as $language) {
                    $languageOptions[$language['Language']['iso2']] = $language['Language']['name'];
                }
            }
            if (!empty($cities)) {
                foreach($cities as $city) {
                    $cityOptions[$city['City']['slug']] = $city['City']['name'];
                }
            }
            $this->set(compact('languageOptions', 'cityOptions'));
            $attachment = $this->SiteLogo->find('first', array(
                'conditions' => array(
                    'SiteLogo.Class = ' => 'SiteLogo'
                ) ,
                'fields' => array(
                    'SiteLogo.id',
                    'SiteLogo.dir',
                    'SiteLogo.filename',
                    'SiteLogo.width',
                    'SiteLogo.height',
                ) ,
                'recursive' => - 1
            ));
            $this->set('attachment', $attachment);
        }
        if (!empty($settings) && $settings[0]['SettingCategory']['name'] == 'Barcode') {
            $barcodeSymbologies = array(
                'qr' => 'QR Code',
                'c39' => 'c39',
                'c128a' => 'c128a',
                'c128b' => 'c128b',
                'c128c' => 'c128c',
                'i25' => 'i25'
            );
            $this->set(compact('barcodeSymbologies'));
        }
        $beyondOriginals = array();
        $aspects = array();
        foreach($settings as $setting) {
            $field_name = explode('.', $setting['Setting']['name']);
            if (isset($field_name[2])) {
                if ($field_name[2] == 'is_not_allow_resize_beyond_original_size') {
                    $beyondOriginals[$setting['Setting']['id']] = Inflector::humanize(Inflector::underscore($field_name[1]));
                    $this->request->data['Setting']['not_allow_beyond_original'][] = ($setting['Setting']['value']) ? $setting['Setting']['id'] : '';
                } else if ($field_name[2] == 'is_handle_aspect') {
                    $aspects[$setting['Setting']['id']] = Inflector::humanize(Inflector::underscore($field_name[1]));
                    $this->request->data['Setting']['allow_handle_aspect'][] = ($setting['Setting']['value']) ? $setting['Setting']['id'] : '';
                }
            }
        }
        if (!empty($this->request->params['named']['city'])) {
            $get_current_city = $this->request->params['named']['city'];
        } else {
            $get_current_city = Configure::read('site.city');
        }
        App::import('Vendor', 'facebook/facebook');
        $this->facebook = new Facebook(array(
            'appId' => Configure::read('facebook.app_id') ,
            'secret' => Configure::read('facebook.fb_secrect_key') ,
            'cookie' => true
        ));
        $fb_return_url = Router::url(array(
            'controller' => $get_current_city,
            'action' => 'settings',
            'fb_update',
            'admin' => false
        ) , true);
        $this->Session->write('fb_return_url', $fb_return_url);
        $fb_login_url = $this->facebook->getLoginUrl(array(
            'redirect_uri' => Router::url(array(
                'controller' => 'users',
                'action' => 'oauth_facebook',
                'admin' => false
            ) , true) ,
            'scope' => 'email,offline_access,publish_stream'
        ));
        $this->set('ssl_enable', $ssl_enable);
        $this->set('fb_login_url', $fb_login_url);
        $this->set(compact('settings', 'beyondOriginals', 'aspects'));
    }
    public function _traverse_directory($dir, $dir_count)
    {
        $handle = opendir($dir);
        while (false !== ($readdir = readdir($handle))) {
            if ($readdir != '.' && $readdir != '..') {
                $path = $dir . '/' . $readdir;
                if (is_dir($path)) {
                    @chmod($path, 0777);
                    ++$dir_count;
                    $this->_traverse_directory($path, $dir_count);
                }
                if (is_file($path)) {
                    @chmod($path, 0777);
                    @unlink($path);
                    //so that page wouldn't hang
                    flush();
                }
            }
        }
        closedir($handle);
        @rmdir($dir);
        return true;
    }
    public function fb_update()
    {
        App::import('Vendor', 'facebook/facebook');
        $this->facebook = new Facebook(array(
            'appId' => Configure::read('facebook.app_id') ,
            'secret' => Configure::read('facebook.fb_secrect_key') ,
            'cookie' => true
        ));
        //if ($fb_session = $this->facebook->getSession()) {
        if ($fb_session = $this->Session->read('fbuser')) {
            $settings = $this->Setting->find('all', array(
                'conditions' => array(
                    'Setting.name' => array(
                        'facebook.fb_access_token',
                        'facebook.fb_user_id'
                    )
                ) ,
                'fields' => array(
                    'Setting.id',
                    'Setting.name'
                ) ,
                'recursive' => - 1
            ));
            foreach($settings as $setting) {
                $this->request->data['Setting']['id'] = $setting['Setting']['id'];
                if ($setting['Setting']['name'] == 'facebook.fb_user_id') {
                    $this->request->data['Setting']['value'] = $fb_session['id'];
                } elseif ($setting['Setting']['name'] == 'facebook.fb_access_token') {
                    $this->request->data['Setting']['value'] = $fb_session['access_token'];
                }
                if ($this->Setting->save($this->request->data)) {
                    $this->Session->setFlash(__l('Facebook credentials updated') , 'default', null, 'success');
                } else {
                    $this->Session->setFlash(__l('Facebook credentials could not be updated. Please, try again.') , 'default', null, 'error');
                }
            }
        }
        $this->redirect(array(
            'action' => 'index',
            'admin' => true
        ));
    }
}
?>