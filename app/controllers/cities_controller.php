<?php
class CitiesController extends AppController
{
    public $name = 'Cities';
    public $permanentCacheAction = array(
        'index' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'Attachment',
            'City.facebook_page_id',
            'City.id',
            'City.slug'
        );
        parent::beforeFilter();
    }
    public function index()
    {
        $this->paginate = array(
            'conditions' => array(
                'City.is_approved' => 1
            ) ,
            'fields' => array(
                'City.name',
                'City.slug',
                'City.active_deal_count'
            ) ,
            'order' => array(
                'City.name' => 'asc'
            ) ,
            'limit' => 200,
            'recursive' => - 1
        );
        // <-- For iPhone App code
        if ($this->RequestHandler->prefers('json')) {
            $this->view = 'Json';
            $this->set('json', (empty($this->viewVars['iphone_response'])) ? $this->paginate() : $this->viewVars['iphone_response']);
        } else {
            $this->set('cities', $this->paginate());
        }
        // For iPhone App code -->
        
    }
    public function admin_index()
    {
        $this->_redirectGET2Named(array(
            'q',
            'filter_id',
        ));
        $this->disableCache();
        $this->pageTitle = __l('Cities');
        $conditions = array();
        if (!empty($this->request->data['City']['filter_id'])) {
            $this->request->params['named']['filter_id'] = $this->request->data['City']['filter_id'];
        }
        $this->City->validate = array();
        if (!empty($this->request->params['named']['filter_id'])) {
            if ($this->request->params['named']['filter_id'] == ConstMoreAction::Active) {
                $this->pageTitle.= __l(' - Approved');
                $conditions[$this->modelClass . '.is_approved'] = 1;
            } else if ($this->request->params['named']['filter_id'] == ConstMoreAction::Inactive) {
                $this->pageTitle.= __l(' - Disapproved');
                $conditions[$this->modelClass . '.is_approved'] = 0;
            }
        }
        if (empty($this->request->data['City']['q']) && !empty($this->request->params['named']['q'])) {
            $this->request->data['City']['q'] = $this->request->params['named']['q'];
        }
        if (isset($this->request->data['City']['q']) && !empty($this->request->data['City']['q'])) {
            $this->request->params['named']['q'] = $this->request->data['City']['q'];
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->data['City']['q']);
        }
        $this->City->recursive = 0;
        $this->paginate = array(
            'conditions' => $conditions,
            'fields' => array(
                'City.id',
                'City.name',
                'City.latitude',
                'City.longitude',
                'City.county',
                'City.code',
                'City.slug',
                'City.is_approved',
                'State.name',
                'Country.name',
                'Language.name',
                'City.active_deal_count',
                'Attachment.id',
                'Attachment.filename',
                'Attachment.dir',
                'Attachment.width',
                'Attachment.height'
            ) ,
            'contain' => array(
                'Attachment',
                'Language',
                'Country',
                'State'
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        );
        if (isset($this->request->data['City']['q'])) {
            $this->paginate = array_merge($this->paginate, array(
                'search' => $this->request->params['named']['q']
            ));
        }
        $this->set('cities', $this->paginate());
        $this->set('pending', $this->City->find('count', array(
            'conditions' => array(
                'City.is_approved = ' => 0
            )
        )));
        $this->set('approved', $this->City->find('count', array(
            'conditions' => array(
                'City.is_approved = ' => 1
            )
        )));
        $filters = $this->City->isFilterOptions;
        $moreActions = $this->City->moreActions;
        $this->set(compact('filters', 'moreActions'));
        $this->set('pageTitle', $this->pageTitle);
    }
    public function admin_update_twitter($id = null)
    {
        $this->pageTitle = __l('Update Twitter');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        // Twitter Login //
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'OauthConsumer');
        $this->OauthConsumer = new OauthConsumerComponent($collection);
        $requestToken = $this->OauthConsumer->getRequestToken('Twitter', 'http://twitter.com/oauth/request_token');
        $this->Session->write('requestToken', $requestToken);
        $this->Session->write('twitter_city_id', $id);
        $this->redirect('http://twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit City');
        $this->loadModel('Attachment');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $defaultCity = $this->City->find('first', array(
            'conditions' => array(
                'City.slug' => Configure::read('site.city')
            ) ,
            'fields' => array(
                'City.id'
            ) ,
            'recursive' => - 1
        ));
        if (!empty($defaultCity) && $id == $defaultCity['City']['id']) {
            $this->set('id_default_city', true);
        }
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Attachment']['filename']['name'])) {
                $this->City->Attachment->Behaviors->attach('ImageUpload', Configure::read('image.file'));
            }
            if (!empty($this->request->data['OldAttachment']['id'])) {
                $this->City->Attachment->delete($this->request->data['Attachment']['id']);
            }
            if (!empty($this->request->data['Attachment']['filename']['name'])) {
                $this->request->data['Attachment']['filename']['type'] = get_mime($this->request->data['Attachment']['filename']['tmp_name']);
            }
            if (!empty($this->request->data['Attachment']['filename']['name']) || (!Configure::read('image.file.allowEmpty') && empty($this->request->data['Attachment']['id']))) {
                $this->request->data['Attachment']['class'] = 'City';
                $this->City->Attachment->create();
                $this->City->Attachment->set($this->request->data);
            }
            $this->City->set($this->request->data);
            $ini_upload_error = 1;
            if ($this->request->data['Attachment']['filename']['error'] == 1) {
                $ini_upload_error = 0;
            }
            if ($this->City->validates() && (empty($this->request->data['Attachment']['filename']['name']) || $this->City->Attachment->validates()) && $ini_upload_error) {
                $this->City->save($this->request->data);
                $id = $foreign_id = $this->request->data['City']['id'];
                $attach = $this->City->Attachment->find('first', array(
                    'conditions' => array(
                        'Attachment.foreign_id = ' => $foreign_id,
                        'Attachment.class = ' => 'City'
                    ) ,
                    'fields' => array(
                        'Attachment.id'
                    ) ,
                    'recursive' => - 1,
                ));
                if (!(empty($this->request->data['Attachment']['filename']['name']))) {
                    $this->request->data['Attachment']['filename'] = $this->request->data['Attachment']['filename'];
                    $this->request->data['Attachment']['class'] = $this->modelClass;
                    $this->request->data['Attachment']['description'] = 'City Image';
                    $this->request->data['Attachment']['id'] = $attach['Attachment']['id'];
                    $this->request->data['Attachment']['foreign_id'] = $this->request->data['City']['id'];
                    $data['Attachment']['filename'] = $this->request->data['Attachment']['filename'];
                    $this->City->Attachment->Behaviors->attach('ImageUpload', Configure::read('image.file'));
                    $this->City->Attachment->set($data);
                    if ($this->City->Attachment->validates()) {
                        $this->City->Attachment->save($this->request->data['Attachment']);
                    }
                }
                // delete view more cities cache files
                $this->City->deleteAllCache();
                Cache::delete('site_city_detail_' . $this->request->data['City']['slug']);
                $this->Session->setFlash(__l('City has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('City could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->City->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $attach = $this->City->Attachment->find('first', array(
            'conditions' => array(
                'Attachment.foreign_id = ' => $id,
                'Attachment.class = ' => 'City'
            ) ,
            'fields' => array(
                'Attachment.id'
            ) ,
            'recursive' => - 1,
        ));
        $image_options = array(
            'dimension' => 'city_background_thumb',
            'class' => '',
            'alt' => $this->request->data['City']['name'],
            'title' => $this->request->data['City']['name'],
            'type' => 'jpg'
        );
        $custom_thumb_url = $this->City->getImageUrl('City', $attach['Attachment'], $image_options);
        @unlink($custom_thumb_url);
        $image_options['dimension'] = 'original';
        $original_thumb_url = $this->City->getImageUrl('City', $attach['Attachment'], $image_options);
        @unlink($original_thumb_url);
        $image_options['dimension'] = 'medium_thumb';
        @unlink($this->City->getImageUrl('City', $attach['Attachment'], $image_options));
        $this->set('original_thumb_url', $original_thumb_url);
        $this->set('custom_thumb_url', $custom_thumb_url);
        $this->pageTitle.= ' - ' . $this->request->data['City']['name'];
        $countries = $this->City->Country->find('list');
        $states = $this->City->State->find('list', array(
            'conditions' => array(
                'State.is_approved' => 1
            )
        ));
        //get languages
        $this->loadModel('Translation');
        $this->Translation = new Translation();
        $languageLists = $this->City->Language->Translation->find('all', array(
            'conditions' => array(
                'Language.id !=' => 0
            ) ,
            'fields' => array(
                'DISTINCT(Translation.language_id)',
                'Language.name',
                'Language.id'
            ) ,
            'order' => array(
                'Language.name' => 'ASC'
            )
        ));
        $languages = array();
        if (!empty($languageLists)) {
            foreach($languageLists as $languageList) {
                $languages[$languageList['Language']['id']] = $languageList['Language']['name'];
            }
        }
        //end
        $this->request->data['City']['bgcolor'] = $this->request->data['City']['bgcolor'];
        $this->set(compact('countries', 'states', 'languages'));
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add City');
        $this->loadModel('Attachment');
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Attachment']['filename']['name'])) {
                $this->request->data['Attachment']['filename']['type'] = get_mime($this->request->data['Attachment']['filename']['tmp_name']);
                $this->City->Attachment->Behaviors->attach('ImageUpload', Configure::read('image.file'));
            }
            if (!empty($this->request->data['Attachment']['filename']['name']) || (!Configure::read('image.file.allowEmpty') && empty($this->request->data['Attachment']['id']))) {
                $this->request->data['Attachment']['class'] = 'City';
                $this->City->Attachment->set($this->request->data);
            }
            $this->City->set($this->request->data);
            $ini_upload_error = 1;
            if ($this->request->data['Attachment']['filename']['error'] == 1) {
                $ini_upload_error = 0;
            }
            if ($this->City->validates() && (empty($this->request->data['Attachment']['filename']['name']) || $this->City->Attachment->validates()) && $ini_upload_error) {
                $this->request->data['City']['is_approved'] = 1;
                $this->City->create();
                if ($this->City->save($this->request->data)) {
                    $this->request->data['Attachment']['filename'] = $this->request->data['Attachment']['filename'];
                    $this->request->data['Attachment']['class'] = $this->modelClass;
                    $this->request->data['Attachment']['description'] = 'City Image';
                    $this->request->data['Attachment']['id'] = $attach['Attachment']['id'];
                    $this->request->data['Attachment']['foreign_id'] = $this->City->id;
                    $data['Attachment']['filename'] = $this->request->data['Attachment']['filename'];
                    $this->City->Attachment->Behaviors->attach('ImageUpload', Configure::read('image.file'));
                    $this->City->Attachment->set($data);
                    if ($this->City->Attachment->validates()) {
                        $this->City->Attachment->save($this->request->data['Attachment']);
                    }
                    // delete view more cities cache files
                    $this->City->deleteAllCache();
                    $this->Session->setFlash(__l(' City has been added') , 'default', null, 'success');
                    $this->redirect(array(
                        'action' => 'index'
                    ));
                }
            } else {
                $this->Session->setFlash(__l(' City could not be added. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data['City']['is_approved'] = 1;
        }
        $countries = $this->City->Country->find('list', array(
            'order' => array(
                'Country.name' => 'ASC'
            )
        ));
        $states = $this->City->State->find('list', array(
            'conditions' => array(
                'State.is_approved =' => 1
            ) ,
            'order' => array(
                'State.name'
            )
        ));
        //get languages
        $this->loadModel('Translation');
        $this->Translation = new Translation();
        $languages = $this->Translation->get_languages();
        //end
        $this->request->data['City']['bgcolor'] = '00B5C8';
        $this->set(compact('countries', 'states', 'languages'));
    }
    // To change approve/disapprove status by admin
    public function admin_update_status($id = null, $status = null)
    {
        if (is_null($id) || is_null($status)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->request->data['City']['id'] = $id;
        if ($status == 'disapprove') {
            $this->request->data['City']['is_approved'] = 0;
        }
        if ($status == 'approve') {
            $this->request->data['City']['is_approved'] = 1;
        }
        // delete view more cities cache files
        $this->City->deleteAllCache();
        $this->City->save($this->request->data);
        $this->redirect(array(
            'action' => 'index'
        ));
    }
    public function admin_update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data['City'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['redirect_url']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $cityIds = array();
            foreach($this->request->data['City'] as $city_id => $is_checked) {
                if ($is_checked['id']) {
                    $cityIds[] = $city_id;
                }
            }
            $defaultCity = $this->City->find('first', array(
                'conditions' => array(
                    'City.slug' => Configure::read('site.city')
                ) ,
                'fields' => array(
                    'City.id'
                ) ,
                'recursive' => - 1
            ));
            if ($actionid && !empty($cityIds)) {
                if ($actionid == ConstMoreAction::Inactive) {
                    $this->City->updateAll(array(
                        'City.is_approved' => 0
                    ) , array(
                        'City.id' => $cityIds,
                        'City.slug !=' => Configure::read('site.city')
                    ));
                    $msg = __l('Selected cities has been disapproved');
                    if (!empty($defaultCity) && in_array($defaultCity['City']['id'], $cityIds)) {
                        if (count($cityIds) == 1) {
                            $this->Session->setFlash(__l('You cannot disapprove the default city. Please update default city from settings and try again.') , 'default', null, 'error');
                            $msg = '';
                        } else {
                            $msg.= ' ' . __l('except the default city. Please update default city from settings and try again.');
                        }
                    }
                    if (!empty($msg)) $this->Session->setFlash($msg, 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::Active) {
                    $this->City->updateAll(array(
                        'City.is_approved' => 1
                    ) , array(
                        'City.id' => $cityIds
                    ));
                    $this->Session->setFlash(__l('Selected cities has been activated') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::Delete) {
                    $this->City->deleteAll(array(
                        'City.id' => $cityIds,
                        'City.slug !=' => Configure::read('site.city')
                    ));
                    $msg = __l('Selected cities has been deleted');
                    if (!empty($defaultCity) && in_array($defaultCity['City']['id'], $cityIds)) {
                        if (count($cityIds) == 1) {
                            $this->Session->setFlash(__l('You can not delete the default city. Please update default city from settings and try again.') , 'default', null, 'error');
                            $msg = '';
                        } else {
                            $msg.= ' ' . __l('except the default city. Please update default city from settings and try again.');
                        }
                    }
                    if (!empty($msg)) $this->Session->setFlash($msg, 'default', null, 'success');
                }
                // delete view more cities cache files
                $this->City->deleteAllCache();
            }
        }
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(Router::url('/', true) . $r);
        } else {
            $this->redirect($r);
        }
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $defaultCity = $this->City->find('first', array(
            'conditions' => array(
                'City.slug' => Configure::read('site.city')
            ) ,
            'fields' => array(
                'City.id'
            ) ,
            'recursive' => - 1
        ));
        if (!empty($defaultCity) && $id == $defaultCity['City']['id']) {
            $this->Session->setFlash(__l('You can not delete the default city. Please update default city from settings and try again.') , 'default', null, 'error');
            $this->redirect(array(
                'action' => 'index'
            ));
        }
        if ($this->City->delete($id)) {
            $this->Session->setFlash(__l('City deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function fb_update()
    {
        if ($fb_session = $this->Session->read('fbuser')) {
            $city = $this->City->find('first', array(
                'conditions' => array(
                    'City.slug' => $this->request->params['named']['city']
                ) ,
                'fields' => array(
                    'City.id'
                ) ,
                'recursive' => - 1
            ));
            if (!empty($city)) {
                $this->request->data['City']['id'] = $city['City']['id'];
                $this->request->data['City']['fb_user_id'] = $fb_session['id'];
                $this->request->data['City']['fb_access_token'] = $fb_session['access_token'];
                if ($this->City->save($this->request->data)) {
                    $this->Session->setFlash(__l('Facebook credentials updated for selected city') , 'default', null, 'success');
                } else {
                    $this->Session->setFlash(__l('Facebook credentials could not be updated for selected city. Please, try again.') , 'default', null, 'error');
                }
            }
        }
        $this->redirect(array(
            'action' => 'index'
        ));
    }
    public function check_city()
    {
        $this->autoRender = false;
        $longitude = !empty($this->request->params['named']['longitude']) ? $this->request->params['named']['longitude'] : '';
        $latitude = !empty($this->request->params['named']['latitude']) ? $this->request->params['named']['latitude'] : '';
        if (!empty($this->request->params['named']['type']) && (($this->request->params['named']['type'] == 'getcity') || ($this->request->params['named']['type'] == 'getcitydetail'))) {
            $curl_uri = 'https://freegeoip.appspot.com/json/119.82.115.146';
            //$curl_uri = 'https://freegeoip.appspot.com/json/'.$this->RequestHandler->getClientIP();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $curl_uri);
            //curl_setopt($ch, CURLOPT_HEADER, 0);
            //curl_setopt($ch, CURLOPT_GET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_exec($ch);
            $response = json_decode(curl_exec($ch) , true);
            if (!empty($response) && $response['status']) {
                $longitude = $response['longitude'];
                $latitude = $response['latitude'];
            }
            if ($this->request->params['named']['type'] == 'getcitydetail') {
                if (!empty($response) && $response['status']) {
                    echo $response['city'] . '|' . $response['regionname'] . '|' . $response['countrycode'] . '|' . $response['latitude'] . '|' . $response['longitude'];
                }
                exit;
            }
            curl_close($ch);
        }
        if (!empty($longitude) and !empty($latitude)) {
            $dist = Configure::read('site.search_distance');
            $lon1 = $longitude - $dist / abs(cos(deg2rad($latitude)) * 69);
            $lon2 = $longitude + $dist / abs(cos(deg2rad($latitude)) * 69);
            $lat1 = $latitude - ($dist / 69);
            $lat2 = $latitude + ($dist / 69);
            $conditions['City.latitude BETWEEN ? AND ?'] = array(
                $lat1,
                $lat2
            );
            $conditions['City.longitude BETWEEN ? AND ?'] = array(
                $lon1,
                $lon2
            );
            $conditions['City.is_approved'] = 1;
            $fields = "3956 * 2 * ASIN(SQRT(  POWER(SIN((City.latitude - $latitude) * pi()/180 / 2), 2) + COS(City.latitude * pi()/180) *  COS($latitude * pi()/180) * POWER(SIN((City.longitude - $longitude) * pi()/180 / 2), 2)  )) as distance";
            $order = array(
                'distance'
            );
            $get_city = $this->City->find('first', array(
                'conditions' => $conditions,
                'fields' => array(
                    'City.slug',
                    'City.id',
                    'City.name',
                    'City.latitude',
                    'City.longitude',
                    'Country.id',
                    'State.id',
                    'Country.iso2',
                    'Country.name',
                    'State.name',
                    'State.id',
                    $fields,
                ) ,
                'contain' => array(
                    'Country',
                    'State'
                ) ,
                'order' => $order,
                'recursive' => 2
            ));
            if (!empty($get_city)) {
                if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'getcity') {
                    echo $get_city['City']['name'] . '|' . $get_city['State']['name'] . '|' . $get_city['Country']['iso2'] . '|' . $get_city['City']['latitude'] . '|' . $get_city['City']['longitude'];
                } else {
                    echo $get_city['City']['name'];
                }
            }
        }
        exit;
    }
    public function admin_change_city()
    {
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['City']['city_id'])) {
                $this->Session->write('city_filter_id', $this->request->data['City']['city_id']);
            } else {
                $this->Session->delete('city_filter_id');
            }
            $this->redirect(Router::url('/', true) . $this->request->data['City']['r']);
        }
    }
    function update_city_lat_long()
    {
        include_once (APP . DS . 'vendors' . DS . 'googleRequest.php');
        $cities = $this->City->find('all', array(
            'fields' => array(
                'City.name',
                'City.id',
                'City.latitude',
                'City.longitude',
            ) ,
            'recursive' => - 1
        ));
        $obj_google = new googleRequest;
        foreach($cities as $city) {
            flush();
            $obj_google->city = $city['City']['name'];
            $obj_google->gKey = "ABQIAAAAPHLcOOGHX2-uLk3K8q1nMRTkUAbhgKwL1jWWfpv-KGJeCrct7hTsLLnZdnZjzehmRIkaePagQvKNbw";
            $latlng = $obj_google->GetRequest();
            $this->City->updateAll(array(
                'City.latitude' => $latlng[0],
                'City.longitude' => $latlng[1]
            ) , array(
                'City.id' => $city['City']['id']
            ));
        }
    }
}
?>
