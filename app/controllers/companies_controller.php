<?php
class CompaniesController extends AppController
{
    public $name = 'Companies';
    public $components = array(
        'Email',
    );
    public $helpers = array(
        'Csv',
    );
    public $permanentCacheAction = array(
        'view' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true,
        ) ,
        'edit' => array(
            'is_user_specific_url' => true
        ) ,
        'dashboard' => array(
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'City',
            'State',
            'Company.latitude',
            'Company.longitude',
            'Company.map_zoom_level',
            'UserAvatar.filename',
            'User.id',
            'Company.id',
            'Company.address1',
            'Company.address2',
            'Company.company_profile',
            'Company.country_id',
            'Company.is_company_profile_enabled',
            'Company.name',
            'Company.phone',
            'Company.url',
            'Company.zip',
            'User.UserProfile.paypal_account'
        );
        parent::beforeFilter();
    }
    function get_company_address($comapny_id)
    {
        $conditions = array();
        $conditions['CompanyAddress.company_id'] = $comapny_id;
        $this->set('branch_addresses', $this->Company->CompanyAddress->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'CompanyAddress.id',
                'CompanyAddress.address1',
            ) ,
            'recursive' => - 1
        )));
    }
    public function view($slug = null, $deal_slug = null)
    {
        $this->pageTitle = __l('Company');
        $allowed_company_addresses = array();
        if (is_null($slug)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $conditions['Company.slug'] = $slug;
        if (ConstUserTypes::Admin != $this->Auth->user('user_type_id')) {
            $conditions['Company.is_company_profile_enabled'] = 1;
            if (!$this->RequestHandler->prefers('kml')) {
                $conditions['Company.is_online_account'] = 1;
            }
        }
        // Checking whether which address allowed display in KML //
        if (!empty($this->request->params['named']['deal']) || !empty($deal_slug)) {
            $deal_conditions['Deal.slug'] = (!empty($this->request->params['named']['deal']) ? $this->request->params['named']['deal'] : $deal_slug);
            $deal = $this->Company->Deal->find('first', array(
                'conditions' => array(
                    'Deal.slug' => (!empty($this->request->params['named']['deal']) ? $this->request->params['named']['deal'] : $deal_slug)
                ) ,
                'fields' => array(
                    'Deal.id',
                    'Deal.name',
                    'Deal.is_redeem_at_all_branch_address',
                    'Deal.is_redeem_in_main_address',
                ) ,
                'contain' => array(
                    'CompanyAddressesDeal'
                ) ,
                'recursive' => 1
            ));
            // if redeem all branch address unchecked, we are checking which branch address was allowed to display //
            if (empty($deal['Deal']['is_redeem_at_all_branch_address'])) {
                foreach($deal['CompanyAddressesDeal'] as $company_addresses) {
                    $allowed_addresses[] = $company_addresses['company_address_id'];
                }
                $allowed_company_addresses['CompanyAddress.id'] = $allowed_addresses;
            }
        }
        $company = $this->Company->find('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.email',
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                        'User.available_balance_amount',
                        'User.is_email_confirmed',
                        'User.is_active'
                    ) ,
                    'UserAvatar',
                ) ,
                'CompanyAddress' => array(
                    'conditions' => $allowed_company_addresses,
                    'City' => array(
                        'fields' => array(
                            'City.name'
                        )
                    ) ,
                    'State' => array(
                        'fields' => array(
                            'State.name'
                        )
                    ) ,
                    'Country' => array(
                        'fields' => array(
                            'Country.name'
                        )
                    ) ,
                    'order' => array(
                        'CompanyAddress.id' => 'desc'
                    )
                ) ,
                'City' => array(
                    'fields' => array(
                        'City.id',
                        'City.name',
                        'City.slug',
                    )
                ) ,
                'State' => array(
                    'fields' => array(
                        'State.id',
                        'State.name'
                    )
                ) ,
                'Country' => array(
                    'fields' => array(
                        'Country.id',
                        'Country.name',
                        'Country.slug',
                    )
                ) ,
                'Deal' => array(
                    'conditions' => array(
                        'Deal.deal_status_id' => array(
                            ConstDealStatus::Open,
                            ConstDealStatus::Expired,
                            ConstDealStatus::Tipped,
                            ConstDealStatus::Closed,
                            ConstDealStatus::PaidToCompany
                        )
                    ) ,
                    'fields' => array(
                        'Deal.id',
                        'Deal.name',
                        'Deal.slug',
                        'Deal.description'
                    ) ,
                    'limit' => 5
                )
            ) ,
            'recursive' => 2,
        ));
        // If no need to show in main address, we'll remove it, so it wont show in KML //
        if (isset($deal['Deal']) && empty($deal['Deal']['is_redeem_in_main_address'])) {
            unset($company['Company']['address1']);
        }
        if ($this->RequestHandler->prefers('kml')) {
            $this->set('company', $company);
        } else {
            $statistics = array();
            $statistics['referred_users'] = $this->Company->User->find('count', array(
                'conditions' => array(
                    'User.referred_by_user_id' => $company['Company']['user_id']
                )
            ));
            $deal_status_conditions = array(
                ConstDealStatus::Open,
                ConstDealStatus::Expired,
                ConstDealStatus::Tipped,
                ConstDealStatus::Closed,
                ConstDealStatus::PaidToCompany
            );
            if ($company['Company']['user_id'] == $this->Auth->user('id')) {
                $deal_status_conditions[] = ConstDealStatus::Draft;
                $deal_status_conditions[] = ConstDealStatus::PendingApproval;
                $deal_status_conditions[] = ConstDealStatus::Upcoming;
                $deal_status_conditions[] = ConstDealStatus::Refunded;
                $deal_status_conditions[] = ConstDealStatus::Canceled;
            }
            $statistics['deal_created'] = $this->Company->Deal->find('count', array(
                'conditions' => array(
                    'OR' => array(
                        'Deal.user_id' => $company['Company']['user_id'],
                        'Deal.company_id' => $company['Company']['id'],
                    ) ,
                    'Deal.deal_status_id' => $deal_status_conditions
                )
            ));
            $statistics['deal_purchased'] = $this->Company->User->DealUser->find('count', array(
                'conditions' => array(
                    'DealUser.user_id' => $company['Company']['user_id'],
                    'DealUser.is_gift' => 0
                )
            ));
            $statistics['gift_sent'] = $this->Company->User->GiftUser->find('count', array(
                'conditions' => array(
                    'GiftUser.user_id' => $company['Company']['user_id']
                )
            ));
            $statistics['gift_received'] = $this->Company->User->GiftUser->find('count', array(
                'conditions' => array(
                    'GiftUser.friend_mail' => $company['Company']['user_id']
                )
            ));
            $statistics['user_friends'] = $this->Company->User->UserFriend->find('count', array(
                'conditions' => array(
                    'UserFriend.user_id' => $company['Company']['user_id'],
                    'UserFriend.friend_status_id' => 2,
                    'UserFriend.is_requested' => 0,
                )
            ));
            if (empty($company)) {
                throw new NotFoundException(__l('Invalid request'));
            }
            $this->set('statistics', $statistics);
            $this->pageTitle.= ' - ' . $company['Company']['name'];
            $this->set('company', $company);
            $this->request->data['UserComment']['user_id'] = $company['User']['id'];
        }
    }
    public function edit($id = null)
    {
        $this->pageTitle = __l('Edit Company');
        $this->loadModel('Attachment');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->Company->User->UserAvatar->Behaviors->attach('ImageUpload', Configure::read('avatar.file'));
        if (!empty($this->request->data)) {
            $user = $this->Company->User->find('first', array(
                'conditions' => array(
                    'User.id' => $this->request->data['User']['id']
                ) ,
                'contain' => array(
                    'UserAvatar' => array(
                        'fields' => array(
                            'UserAvatar.id',
                            'UserAvatar.filename',
                            'UserAvatar.dir',
                            'UserAvatar.width',
                            'UserAvatar.height'
                        )
                    ) ,
                    'UserProfile' => array(
                        'fields' => array(
                            'UserProfile.paypal_account',
                            'UserProfile.language_id'
                        )
                    ) ,
                ) ,
                'recursive' => 0
            ));
            if (!empty($user['UserAvatar']['id'])) {
                $this->request->data['UserAvatar']['id'] = $user['UserAvatar']['id'];
            }
            if (!empty($this->request->data['UserAvatar']['filename']['name'])) {
                $this->request->data['UserAvatar']['filename']['type'] = get_mime($this->request->data['UserAvatar']['filename']['tmp_name']);
            }
            if (!empty($this->request->data['UserAvatar']['filename']['name']) || (!Configure::read('avatar.file.allowEmpty') && empty($this->request->data['UserAvatar']['id']))) {
                $this->Company->User->UserAvatar->set($this->request->data);
            }
            $ini_upload_error = 1;
            if (!empty($this->request->data['UserAvatar']['filename']['error']) && ($this->request->data['UserAvatar']['filename']['error'] == 1)) {
                $ini_upload_error = 0;
            }
            $this->request->data['Company']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->Company->City->findOrSaveAndGetId($this->request->data['City']['name']);
            $this->request->data['Company']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->Company->State->findOrSaveAndGetId($this->request->data['State']['name']);
            unset($this->Company->validate['city_id']);
            unset($this->Company->validate['state_id']);
            $this->Company->State->set($this->request->data);
            $this->Company->City->set($this->request->data);
            $this->Company->set($this->request->data['Company']);
            //pr($this->Company->City->validates());
            unset($this->Company->City->validate['City']);
            if ($this->Company->validates() && $this->Company->State->validates() && $this->Company->City->validates() && $this->Company->User->UserAvatar->validates() && $ini_upload_error) {
				$this->Company->Behaviors->detach('i18n');
                if ($this->Company->save($this->request->data, false)) {
                    if (!empty($this->request->data['UserProfile']['language_id'])) {
                        $this->Company->User->UserProfile->updateAll(array(
                            'UserProfile.language_id' => $this->request->data['UserProfile']['language_id']
                        ) , array(
                            'UserProfile.user_id' => $this->Auth->user('id')
                        ));
                    }
                    if ($this->request->data['UserProfile']['language_id'] != $user['UserProfile']['language_id']) {
                        $this->Company->User->UserProfile->User->UserLogin->updateUserLanguage();
                    }
                    if (!empty($this->request->data['User']['UserProfile']['paypal_account'])) {
                        $this->Company->User->UserProfile->updateAll(array(
                            'UserProfile.paypal_account' => '\'' . $this->request->data['User']['UserProfile']['paypal_account'] . '\'',
                            'UserProfile.language_id' => '\'' . $this->request->data['UserProfile']['language_id'] . '\''
                        ) , array(
                            'UserProfile.user_id' => $this->Auth->user('id')
                        ));
                    }
                    if (!empty($this->request->data['UserAvatar']['filename']['name'])) {
                        $this->Attachment->create();
                        $this->request->data['UserAvatar']['class'] = 'UserAvatar';
                        $this->request->data['UserAvatar']['foreign_id'] = $this->request->data['User']['id'];
                        $this->Attachment->save($this->request->data['UserAvatar']);
                    }
                    $this->Session->setFlash(__l('Company has been updated') , 'default', null, 'success');
                    if (!empty($this->request->params['form']['is_iframe_submit'])) {
                        $this->layout = 'ajax';
                    }
                } else {
                    $this->Session->setFlash(__l('Company could not be updated. Please, try again.') , 'default', null, 'error');
                }
                if ($this->Company->User->isAllowed($this->Auth->user('user_type_id'))) {
                    $ajax_url = Router::url(array(
                        'controller' => 'users',
                        'action' => 'my_stuff',
                    ) , true);
                    $success_msg = 'redirect*' . $ajax_url;
                    echo $success_msg;
                    exit;
                }
            } else {
                $this->Session->setFlash(__l('Company could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } 
		unset($this->Company->City->validate['City']);
		$this->Company->Behaviors->detach('i18n');
		$this->request->data = $this->Company->find('first', array(
			'conditions' => array(
				'Company.id = ' => $id,
			) ,
			'contain' => array(
				'User' => array(
					'UserAvatar' => array(
						'fields' => array(
							'UserAvatar.id',
							'UserAvatar.dir',
							'UserAvatar.filename',
							'UserAvatar.width',
							'UserAvatar.height'
						)
					) ,
					'UserProfile' => array(
						'fields' => array(
							'UserProfile.paypal_account',
							'UserProfile.language_id'
						)
					) ,
					'fields' => array(
						'User.user_type_id',
						'User.username',
						'User.id',
						'User.available_balance_amount',
						'User.email',
					)
				) ,
				'City' => array(
					'fields' => array(
						'City.name'
					)
				) ,
				'State' => array(
					'fields' => array(
						'State.name'
					)
				)
			) ,
			'recursive' => 2
		));
		if (empty($this->request->data)) {
			throw new NotFoundException(__l('Invalid request'));
		}
		if (!empty($this->request->data['Company']['City'])) {
			$this->request->data['City']['name'] = $this->request->data['Company']['City']['name'];
		}
		if (!empty($this->request->data['Company']['State']['name'])) {
			$this->request->data['State']['name'] = $this->request->data['Company']['State']['name'];
		}        
        $this->pageTitle.= ' - ' . $this->request->data['Company']['name'];
        $cities = $this->Company->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        $states = $this->Company->State->find('list');
        $countries = $this->Company->Country->find('list');
        //get languages
        $languageLists = $this->Company->User->UserProfile->Language->Translation->find('all', array(
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
        $this->set(compact('cities', 'states', 'countries', 'languages'));
    }
    public function delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->Company->delete($id)) {
            $this->Session->setFlash(__l('Company deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_index()
    {
        $this->disableCache();
        $this->pageTitle = __l('Companies');
        if (!empty($this->request->data['Company']['q'])) {
            $this->request->params['named']['q'] = $this->request->data['Company']['q'];
            $this->pageTitle.= __l(' - Search - ') . $this->request->params['named']['q'];
        }
        if (!empty($this->request->data['Company']['main_filter_id'])) {
            $this->request->params['named']['filter_id'] = $this->request->data['Company']['main_filter_id'];
        }
        $this->set('online', $this->Company->find('count', array(
            'conditions' => array(
                'Company.is_online_account = ' => 1,
            ) ,
            'recursive' => - 1
        )));
        // total approved users list
        $this->set('offline', $this->Company->find('count', array(
            'conditions' => array(
                'Company.is_online_account = ' => 0,
            ) ,
            'recursive' => - 1
        )));
        // total openid users list
        $this->set('all', $this->Company->find('count', array(
            'recursive' => - 1
        )));
        $conditions = $count_conditions = array();
        if (!empty($this->request->params['named']['main_filter_id'])) {
            if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::OpenID) {
                $conditions['User.is_openid_register'] = 1;
                $this->pageTitle.= __l(' - Registered through OpenID ');
            } else if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::FaceBook) {
                $conditions['User.is_facebook_register'] = 1;
                $this->pageTitle.= __l(' - Registered through Facebook ');
            } else if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::Twitter) {
                $conditions['User.is_twitter_register'] = 1;
                $this->pageTitle.= __l(' - Registered through Twitter ');
            } else if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::Gmail) {
                $conditions['User.is_gmail_register'] = 1;
                $this->pageTitle.= __l(' - Registered through Gmail ');
            } else if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::Yahoo) {
                $conditions['User.is_yahoo_register'] = 1;
                $this->pageTitle.= __l(' - Registered through Yahoo ');
            }
        }
        if (!empty($this->request->params['named']['main_filter_id'])) {
            if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::Online) {
                $conditions['Company.is_online_account'] = 1;
                $this->pageTitle.= __l(' - Online Account');
            } else if ($this->request->params['named']['main_filter_id'] == ConstMoreAction::Offline) {
                $conditions['Company.is_online_account'] = 0;
                $this->pageTitle.= __l(' - Offline Account');
            }
            $this->request->data['Company']['main_filter_id'] = $this->request->params['named']['main_filter_id'];
            $count_conditions = $conditions;
        }
        if (!empty($this->request->params['named']['filter_id'])) {
            if ($this->request->params['named']['filter_id'] == ConstMoreAction::Active) {
                $conditions['User.is_active'] = 1;
                $this->pageTitle.= __l(' - Active ');
            } else if ($this->request->params['named']['filter_id'] == ConstMoreAction::Inactive) {
                $conditions['User.is_active'] = 0;
                $this->pageTitle.= __l(' - Inactive ');
            }
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Company.created) <= '] = 0;
            $this->pageTitle.= __l(' - Registered today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Company.created) <= '] = 7;
            $this->pageTitle.= __l(' - Registered in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Company.created) <= '] = 30;
            $this->pageTitle.= __l(' - Registered in this month');
        }
        if ($this->RequestHandler->prefers('csv')) {
            Configure::write('debug', 0);
            $this->set('company', $this);
            $this->set('conditions', $conditions);
            if (isset($this->request->data['Company']['q'])) {
                $this->set('q', $this->request->data['Company']['q']);
            }
            $this->set('contain', $contain);
        } else {
            $this->paginate = array(
                'conditions' => array(
                    $conditions,
                ) ,
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'User.email',
                            'User.user_type_id',
                            'User.username',
                            'User.id',
                            'User.available_balance_amount',
                            'User.is_email_confirmed',
                            'User.is_active'
                        ) ,
                        'UserAvatar'
                    )
                ) ,
                'order' => array(
                    'Company.id' => 'desc'
                ) ,
                'recursive' => 2,
            );
            if (!empty($this->request->params['named']['q'])) {
                $this->paginate = array_merge($this->paginate, array(
                    'search' => $this->request->params['named']['q']
                ));
                $this->request->data['Company']['q'] = $this->request->params['named']['q'];
            }
            if (!empty($this->request->params['named']['main_filter_id']) && $this->request->params['named']['main_filter_id'] == ConstMoreAction::Offline) {
                $moreActions[ConstMoreAction::DeductAmountFromWallet] = __l('Set As Paid');
            } else {
                $moreActions = $this->Company->moreActions;
            }
            $this->set(compact('moreActions'));
            $this->set('companies', $this->paginate());
            $this->set('pageTitle', $this->pageTitle);
            // total approved users list
            $this->set('active', $this->Company->find('count', array(
                'conditions' => array(
                    'User.is_active' => 1,
                    $count_conditions
                ) ,
                'recursive' => 1
            )));
            // total approved users list
            $this->set('inactive', $this->Company->find('count', array(
                'conditions' => array(
                    'User.is_active' => 0,
                    $count_conditions
                ) ,
                'recursive' => 1
            )));
        }
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Company');
        $this->loadModel('EmailTemplate');
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['City']['name'])) {
                $this->request->data['Company']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->Company->City->findOrSaveAndGetId($this->request->data['City']['name']);
            }
            if (!empty($this->request->data['State']['name'])) {
                $this->request->data['Company']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->Company->State->findOrSaveAndGetId($this->request->data['State']['name']);
            }
            $this->Company->create();
            $this->Company->set($this->request->data);
            $this->Company->User->set($this->request->data);
            $this->Company->State->set($this->request->data);
            $this->Company->City->set($this->request->data);
            unset($this->Company->City->validate['City']);
            if ($this->Company->User->validates() & $this->Company->validates() & $this->Company->City->validates() & $this->Company->State->validates()) {
                if (empty($this->request->data['Company']['user_id'])) {
                    $this->request->data['User']['user_type_id'] = ConstUserTypes::Company;
                    $this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['passwd']);
                    if ($this->request->data['Company']['is_online_account']) {
                        $this->request->data['User']['is_email_confirmed'] = '1';
                        $this->request->data['User']['is_active'] = '1';
                    } else {
                        $this->request->data['User']['is_email_confirmed'] = '0';
                        $this->request->data['User']['is_active'] = '0';
                    }
                    if ($this->Company->User->save($this->request->data)) {
                        $user_id = $this->Company->User->getLastInsertId();
                        $this->request->data['Company']['user_id'] = $user_id;
                        $this->request->data['UserProfile']['user_id'] = $user_id;
                        $this->request->data['UserProfile']['address'] = $this->request->data['Company']['address1'];
                        $this->request->data['UserProfile']['city_id'] = $this->request->data['Company']['city_id'];
                        $this->request->data['UserProfile']['state_id'] = $this->request->data['Company']['state_id'];
                        $this->request->data['UserProfile']['zip_code'] = $this->request->data['Company']['zip'];
                        $this->request->data['UserProfile']['paypal_account'] = $this->request->data['User']['UserProfile']['paypal_account'];
                        $this->Company->User->UserProfile->create();
                        $this->Company->User->UserProfile->save($this->request->data);
                    }
                }
				$this->Company->Behaviors->detach('i18n');
                if ($this->Company->save($this->request->data)) {
                    if (!empty($this->request->data['Company']['is_online_account'])) {
                        $email = $this->EmailTemplate->selectTemplate('Admin User Add');
                        $emailFindReplace = array(
                            '##FROM_EMAIL##' => $this->Company->changeFromEmail(($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from']) ,
                            '##USERNAME##' => $this->request->data['User']['username'],
                            '##LOGINLABEL##' => ucfirst(Configure::read('user.using_to_login')) ,
                            '##USEDTOLOGIN##' => $this->request->data['User'][Configure::read('user.using_to_login') ],
                            '##SITE_NAME##' => Configure::read('site.name') ,
                            '##PASSWORD##' => $this->request->data['User']['passwd'],
                            '##SITE_LINK##' => Router::url('/', true) ,
                            '##CONTACT_URL##' => Router::url(array(
                                'controller' => 'contacts',
                                'action' => 'add',
                                'city' => $this->request->params['named']['city'],
                                'admin' => false
                            ) , true) ,
                            '##SITE_LOGO##' => Router::url(array(
                                'controller' => 'img',
                                'action' => 'theme-image',
                                'logo-email.png',
                                'admin' => false
                            ) , true) ,
                        );
                        $this->Email->from = ($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from'];
                        $this->Email->replyTo = ($email['reply_to'] == '##REPLY_TO_EMAIL##') ? Configure::read('EmailTemplate.reply_to_email') : $email['reply_to'];
                        $this->Email->to = $this->request->data['User']['email'];
                        $this->Email->subject = strtr($email['subject'], $emailFindReplace);
                        $this->Email->sendAs = ($email['is_html']) ? 'html' : 'text';
                        $this->Email->send(strtr($email['email_content'], $emailFindReplace));
                    }
                    $this->Session->setFlash(__l('Company has been added') , 'default', null, 'success');
                    $this->redirect(array(
                        'action' => 'index'
                    ));
                } else {
                    $this->Session->setFlash(__l('Company could not be added. Please, try again.') , 'default', null, 'error');
                }
            } else {
                $this->Session->setFlash(__l('Company could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
        unset($this->Company->City->validate['City']);
        $countries = $this->Company->Country->find('list');
        $this->set(compact('countries'));
        unset($this->request->data['User']['passwd']);
        unset($this->request->data['User']['confirm_password']);
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Company');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $id = (!empty($this->request->data['Company']['id'])) ? $this->request->data['Company']['id'] : $id;
        $company = $this->Company->find('first', array(
            'conditions' => array(
                'Company.id' => $id
            ) ,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.email',
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                    )
                )
            ) ,
            'fields' => array(
                'Company.id',
            ) ,
            'recursive' => 0
        ));
        if (!empty($this->request->data)) {
            $this->request->data['Company']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->Company->City->findOrSaveAndGetId($this->request->data['City']['name']);
            $this->request->data['Company']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->Company->State->findOrSaveAndGetId($this->request->data['State']['name']);
            $this->Company->set($this->request->data);
            $this->Company->State->set($this->request->data);
            $this->Company->City->set($this->request->data);
            $this->Company->User->set($this->request->data);
            unset($this->Company->City->validate['City']);
            if ($company['User']['email'] == $this->request->data['User']['email']) {
                unset($this->Company->User->validate['email']['rule3']);
            }
            if ($this->Company->validates() && $this->Company->City->validates() && $this->Company->State->validates() && $this->Company->User->validates()) {
			$this->Company->Behaviors->detach('i18n');
                if ($this->Company->save($this->request->data)) {
                    $company = $this->Company->find('first', array(
                        'fields' => array(
                            'Company.user_id'
                        ) ,
                        'recursive' => - 1,
                    ));
                    if ($this->request->data['Company']['is_online_account']) {
                        $this->request->data['User']['is_email_confirmed'] = '1';
                        $this->request->data['User']['is_active'] = '1';
                    } else {
                        $this->request->data['User']['is_email_confirmed'] = '0';
                        $this->request->data['User']['is_active'] = '0';
                    }
                    $this->request->data['User']['id'] = $company['Company']['user_id'];
                    $this->Company->User->save($this->request->data);
                    $this->Company->User->UserProfile->updateAll(array(
                        'UserProfile.paypal_account' => '\'' . $this->request->data['User']['UserProfile']['paypal_account'] . '\''
                    ) , array(
                        'UserProfile.user_id' => $company['Company']['user_id']
                    ));
                    $this->Session->setFlash(__l('Company has been updated') , 'default', null, 'success');
                    $this->redirect(array(
                        'action' => 'index'
                    ));
                } else {
                    $this->Session->setFlash(__l('Company could not be updated. Please, try again.') , 'default', null, 'error');
                }
            } else {
                $this->Session->setFlash(__l('Company could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
			$this->Company->Behaviors->detach('i18n');
            $this->request->data = $this->Company->find('first', array(
                'conditions' => array(
                    'Company.id ' => $id,
                ) ,
                'contain' => array(
                    'User' => array(
                        'UserAvatar' => array(
                            'fields' => array(
                                'UserAvatar.id',
                                'UserAvatar.dir',
                                'UserAvatar.filename',
                                'UserAvatar.width',
                                'UserAvatar.height'
                            )
                        ) ,
                        'UserProfile' => array(
                            'fields' => array(
                                'UserProfile.first_name',
                                'UserProfile.last_name',
                                'UserProfile.middle_name',
                                'UserProfile.gender_id',
                                'UserProfile.about_me',
                                'UserProfile.address',
                                'UserProfile.country_id',
                                'UserProfile.state_id',
                                'UserProfile.city_id',
                                'UserProfile.zip_code',
                                'UserProfile.dob',
                                'UserProfile.language_id',
                                'UserProfile.paypal_account'
                            ) ,
                        ) ,
                    ) ,
                    'City' => array(
                        'fields' => array(
                            'City.name'
                        )
                    ) ,
                    'State' => array(
                        'fields' => array(
                            'State.name'
                        )
                    ) ,
                    'CompanyAddress' => array(
                        'City' => array(
                            'fields' => array(
                                'City.name'
                            )
                        ) ,
                        'State' => array(
                            'fields' => array(
                                'State.name'
                            )
                        ) ,
                        'Country' => array(
                            'fields' => array(
                                'Country.name'
                            )
                        ) ,
                        'order' => array(
                            'CompanyAddress.id' => 'desc'
                        )
                    ) ,
                ) ,
                'recursive' => 2
            ));
            if (!empty($this->request->data['City'])) {
                $this->request->data['City']['name'] = $this->request->data['City']['name'];
            }
            if (!empty($this->request->data['State']['name'])) {
                $this->request->data['State']['name'] = $this->request->data['State']['name'];
            }
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        unset($this->Company->City->validate['City']);
        $this->pageTitle.= ' - ' . $this->request->data['Company']['name'];
        $users = $this->Company->User->find('list');
        $cities = $this->Company->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        $states = $this->Company->State->find('list');
        $countries = $this->Company->Country->find('list');
        $this->set(compact('users', 'cities', 'states', 'countries'));
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $company = $this->Company->find('first', array(
            'conditions' => array(
                'Company.id' => $id,
            ) ,
            'recursive' => - 1
        ));
        if (!empty($company['Company']['user_id']) && $this->Company->User->delete($company['Company']['user_id'])) {
            $this->Session->setFlash(__l('Company deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data['Company'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $companyIds = array();
            foreach($this->request->data['Company'] as $company_id => $is_checked) {
                if ($is_checked['id']) {
                    $companyIds[] = $company_id;
                }
            }
            if ($actionid && !empty($companyIds)) {
                if ($actionid == ConstMoreAction::EnableCompanyProfile) {
                    $this->Company->updateAll(array(
                        'Company.is_company_profile_enabled' => 1
                    ) , array(
                        'Company.id' => $companyIds
                    ));
                    $this->Session->setFlash(__l('Checked companies has been enabled') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::DisableCompanyProfile) {
                    $this->Company->updateAll(array(
                        'Company.is_company_profile_enabled' => 0
                    ) , array(
                        'Company.id' => $companyIds
                    ));
                    $this->Session->setFlash(__l('Checked companies has been disabled') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::Active) {
                    foreach($companyIds as $companyId) {
                        $get_company_user = $this->Company->find('first', array(
                            'conditions' => array(
                                'Company.id' => $companyId
                            ) ,
                            'recursive' => - 1
                        ));
                        $this->Company->User->updateAll(array(
                            'User.is_active' => 1
                        ) , array(
                            'User.id' => $get_company_user['Company']['user_id']
                        ));
                        $this->_sendAdminActionMail($companyId, 'Admin User Active');
                    }
                    $this->Session->setFlash(__l('Checked companies user has been activated') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::Inactive) {
                    foreach($companyIds as $companyId) {
                        $get_company_user = $this->Company->find('first', array(
                            'conditions' => array(
                                'Company.id' => $companyId
                            ) ,
                            'recursive' => - 1
                        ));
                        $this->Company->User->updateAll(array(
                            'User.is_active' => 0
                        ) , array(
                            'User.id' => $get_company_user['Company']['user_id']
                        ));
                        $this->_sendAdminActionMail($companyId, 'Admin User Deactivate');
                    }
                    $this->Session->setFlash(__l('Checked companies user has been deactivated') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::DeductAmountFromWallet) {
                    $this->Session->write('companies_list.data', $companyIds);
                    $this->redirect(array(
                        'controller' => 'companies',
                        'action' => 'admin_deductamount',
                        'admin' => true
                    ));
                }
            }
        }
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(Router::url('/', true) . $r);
        } else {
            $this->redirect($r);
        }
    }
    public function _sendAdminActionMail($company_id, $email_template)
    {
        $this->loadModel('EmailTemplate');
        $company = $this->Company->find('first', array(
            'conditions' => array(
                'Company.id' => $company_id
            ) ,
            'fields' => array(
                'Company.id',
                'Company.name',
            ) ,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.id',
                        'User.username',
                        'User.email',
                    )
                )
            ) ,
            'recursive' => 1
        ));
        if (!empty($company['User']['email'])) {
            $email = $this->EmailTemplate->selectTemplate($email_template);
            $emailFindReplace = array(
                '##SITE_LINK##' => Router::url('/', true) ,
                '##USERNAME##' => $company['User']['username'],
                '##SITE_NAME##' => Configure::read('site.name') ,
                '##FROM_EMAIL##' => $this->Company->User->changeFromEmail(($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from']) ,
                '##CONTACT_URL##' => Router::url(array(
                    'controller' => 'contacts',
                    'action' => 'add',
                    'city' => $this->request->params['named']['city'],
                    'admin' => false
                ) , true) ,
                '##SITE_LOGO##' => Router::url(array(
                    'controller' => 'img',
                    'action' => 'blue-theme',
                    'logo-email.png',
                    'admin' => false
                ) , true) ,
            );
            $this->Email->from = ($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from'];
            $this->Email->replyTo = ($email['reply_to'] == '##REPLY_TO_EMAIL##') ? Configure::read('EmailTemplate.reply_to_email') : $email['reply_to'];
            $this->Email->to = $this->Company->User->formatToAddress($company);
            $this->Email->sendAs = ($email['is_html']) ? 'html' : 'text';
            $this->Email->subject = strtr($email['subject'], $emailFindReplace);
            $this->Email->send(strtr($email['email_content'], $emailFindReplace));
        }
    }
    public function admin_deductamount($companies_list = null)
    {
        if (empty($companies_list)) {
            $companies_list = $this->Session->read('companies_list.data');
        }
        if (!empty($companies_list)) {
            $companies = $this->Company->find('all', array(
                'conditions' => array(
                    'Company.id' => $companies_list
                ) ,
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'User.user_type_id',
                            'User.username',
                            'User.id',
                            'User.available_balance_amount'
                        )
                    )
                ) ,
                'recursive' => 0
            ));
            $this->set('companies', $companies);
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            foreach($this->request->data['Company'] as $company_id => $company) {
                $get_company = $this->Company->find('first', array(
                    'conditions' => array(
                        'Company.id' => $company_id
                    ) ,
                    'contain' => array(
                        'User' => array(
                            'fields' => array(
                                'User.user_type_id',
                                'User.username',
                                'User.id',
                                'User.available_balance_amount'
                            )
                        )
                    ) ,
                    'recursive' => 0
                ));
                if ($this->request->data['Company'][$company_id]['amount'] > $get_company['User']['available_balance_amount']) {
                    $this->Company->validationErrors[$company_id]['amount'] = __l('Should be less than available balance amount');
                }
                if (empty($company['amount'])) {
                    $this->Company->validationErrors[$company_id]['amount'] = __l('Required');
                }
            }
            if (empty($this->Company->validationErrors)) {
                $transactions = array();
                $transactions['Transaction']['foreign_id'] = $this->Auth->user('id');
                foreach($this->request->data['Company'] as $company_id => $company) {
                    $transactions['Transaction']['user_id'] = $company['user_id'];
                    $transactions['Transaction']['class'] = 'SecondUser';
                    $transactions['Transaction']['amount'] = $company['amount'];
                    $transactions['Transaction']['description'] = $company['description'];
                    $transactions['Transaction']['transaction_type_id'] = ConstTransactionTypes::DeductedAmountForOfflineCompany;
                    $this->Company->User->Transaction->log($transactions);
                    $this->Company->User->updateAll(array(
                        'User.available_balance_amount' => 'User.available_balance_amount -' . $company['amount'],
                    ) , array(
                        'User.id' => $company['user_id']
                    ));
                }
                $this->Session->delete('companies_list');
                $this->Session->setFlash(__l('Amount deducted for the selected companies') , 'default', null, 'success');
                $r = "san-diego/admin/companies/main_filter_id:10/city:san-diego";
                if (!$this->RequestHandler->isAjax()) {
                    $this->redirect(Router::url('/', true) . $r);
                } else {
                    $this->redirect($r);
                }
            } else {
                $this->Session->setFlash(__l('Amount could not be deducted for the selected companies. Please, try again.') , 'default', null, 'error');
            }
        }
    }
    function dashboard()
    {
        if (($this->Auth->user('user_type_id')) == ConstUserTypes::Company) {
            $this->loadModel('Deal');
            $this->loadModel('DealUser');
            $this->loadModel('Company');
            $this->loadModel('UserCashWithdrawal');
            $this->loadModel('Transaction');
            $this->loadModel('DealUserCoupon');
            $this->loadModel('DealUserCoupon');
            $this->pageTitle = __l('Dashboard');
            $not_conditions['Not']['Deal.deal_status_id'] = array(
                ConstDealStatus::SubDeal
            );
            $periods = array(
                'day' => array(
                    'display' => __l('Today') ,
                    'conditions' => array(
                        'TO_DAYS(NOW()) - TO_DAYS(created) <= ' => 0,
                    )
                ) ,
                'week' => array(
                    'display' => __l('This week') ,
                    'conditions' => array(
                        'TO_DAYS(NOW()) - TO_DAYS(created) <= ' => 7,
                    )
                ) ,
                'month' => array(
                    'display' => __l('This month') ,
                    'conditions' => array(
                        'TO_DAYS(NOW()) - TO_DAYS(created) <= ' => 30,
                    )
                ) ,
                'total' => array(
                    'display' => __l('Total') ,
                    'conditions' => array()
                )
            );
            $company_id = $this->User->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'fields' => array(
                    'Company.id',
                    'Company.slug'
                ) ,
                'recursive' => 0
            ));
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Deals') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index'
                    ) ,
                    'rowspan' => 12
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Open') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Open,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Open,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealOpen',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Upcoming') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Upcoming,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Upcoming,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealUpcoming',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Draft') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Draft,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Draft,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealDraft',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Pending Approval') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::PendingApproval,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::PendingApproval,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealPendingApproval',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Tipped') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Tipped,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Tipped,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealTipped',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Closed') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Closed,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Closed,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealClosed',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Paid To Company') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::PaidToCompany,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::PaidToCompany,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealPaidToCompany',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Expired') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Expired,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Expired,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealExpired',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Refunded') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Refunded,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Refunded,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealRefunded',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Rejected') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Rejected,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Rejected,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealRejected',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Cancelled') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'filter_id' => ConstDealStatus::Canceled,
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.deal_status_id' => ConstDealStatus::Canceled,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealCanceled',
                    'isSub' => 'Deal'
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Total.no.of purchase count/All') ,
                    'link' => array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'type' => 'all',
                        'company' => $company_id['Company']['slug']
                    ) ,
                    'conditions' => array(
                        'Deal.company_id' => $company_id['Company']['id'],
                        $not_conditions
                    ) ,
                    'alias' => 'DealAll',
                    'isSub' => 'Deal'
                )
            );
			$tra_rwspn = 4;
			if(!$this->isAllowed($this->Auth->user('user_type_id'))){
				$tra_rwspn--;
			}
            $models[] = array(
                'Transaction' => array(
                    'display' => __l('Transactions') . ' (' . Configure::read('site.currency') . ')',
                    'link' => array(
                        'controller' => 'Transaction',
                        'action' => 'index'
                    ) ,
                    'rowspan' => $tra_rwspn
                )
            );
            $models[] = array(
                'Transaction' => array(
                    'display' => __l('Paid Referral Amount') ,
                    'link' => array(
                        'controller' => 'transactions',
                        'action' => 'index',
                        'type' => 11
                    ) ,
                    'conditions' => array(
                        'Transaction.transaction_type_id' => ConstTransactionTypes::ReferralAmountPaid,
                        'Transaction.user_id' => $this->Auth->user('id')
                    ) ,
                    'alias' => 'TransactionReferral',
                    'isSub' => 'Transaction'
                )
            );
            $models[] = array(
                'Transaction' => array(
                    'display' => __l('Paid Amount to Company') ,
                    'link' => array(
                        'controller' => 'transactions',
                        'action' => 'index',
                        'type' => ConstTransactionTypes::ReceivedDealPurchasedAmount
                    ) ,
                    'conditions' => array(
                        'Transaction.transaction_type_id' => ConstTransactionTypes::ReceivedDealPurchasedAmount,
                        'Transaction.user_id' => $this->Auth->user('id')
                    ) ,
                    'alias' => 'TransactionPaidToCompany',
                    'isSub' => 'Transaction'
                )
            );
            $models[] = array(
                'Transaction' => array(
                    'display' => __l('Withdrawn Amount by Company') ,
                    'link' => array(
                        'controller' => 'transactions',
                        'action' => 'index',
                        'type' => ConstTransactionTypes::UserCashWithdrawalAmount
                    ) ,
                    'conditions' => array(
                        'Transaction.transaction_type_id' => ConstTransactionTypes::UserCashWithdrawalAmount,
                        'Transaction.user_id' => $this->Auth->user('id')
                    ) ,
                    'alias' => 'TransactionWithdrawAmount',
                    'isSub' => 'Transaction'
                )
            );
			if($this->isAllowed($this->Auth->user('user_type_id'))){
				$models[] = array(
					'Transaction' => array(
						'display' => __l('Deposits to Wallet') ,
						'link' => array(
							'controller' => 'transactions',
							'action' => 'index',
							'type' => ConstTransactionTypes::AddedToWallet
						) ,
						'conditions' => array(
							'Transaction.transaction_type_id' => ConstTransactionTypes::AddedToWallet,
							'Transaction.user_id' => $this->Auth->user('id')
						) ,
						'alias' => 'TransactionAmountToWallet',
						'isSub' => 'Transaction'
					)
				);
			}
            // Redeem
            $reedeem_deal_id = $this->Deal->find('list', array(
                'conditions' => array(
                    'Deal.company_id' => $company_id['Company']['id']
                ) ,
                'fields' => array(
                    'Deal.id'
                ) ,
                'recursive' => - 1
            ));
            $reedeem_deal_user = $this->DealUser->find('list', array(
                'conditions' => array(
                    'DealUser.deal_id' => $reedeem_deal_id
                ) ,
                'fields' => array(
                    //  'DealUser.id',
                    'DealUser.id'
                ) ,
                'recursive' => 0
            ));
            $models[] = array(
                'DealUserCoupon' => array(
                    'display' => __l('Reedeem') ,
                    'conditions' => array(
                        'DealUserCoupon.deal_user_id' => $reedeem_deal_user,
                        'DealUserCoupon.is_used' => 1,
                    ) ,
                    'fields' => array(
                        'SUM(is_used) AS used'
                    ) ,
                    'recursive' => 0,
                    'colspan' => 2,
                    'alias' => 'DealReedeem'
                )
            );
            //not redeem
            $models[] = array(
                'DealUserCoupon' => array(
                    'display' => __l('Not Reedeem') ,
                    'conditions' => array(
                        'DealUserCoupon.deal_user_id' => $reedeem_deal_user,
                        'DealUserCoupon.is_used' => 0,
                    ) ,
                    'fields' => array(
                        'SUM(is_used) AS used'
                    ) ,
                    'recursive' => 0,
                    'colspan' => 2,
                    'alias' => 'DealNotReedeem',
                )
            );
            //all
            $models[] = array(
                'DealUserCoupon' => array(
                    'display' => __l('All') ,
                    'conditions' => array(
                        'DealUserCoupon.deal_user_id' => $reedeem_deal_user,
                    ) ,
                    'fields' => array(
                        'SUM(is_used) AS used'
                    ) ,
                    'recursive' => 0,
                    'colspan' => 2,
                    'alias' => 'DealAllCoupons',
                )
            );
            $models[] = array(
                'UserCashWithdrawal' => array(
                    'display' => __l('No. of Pending Withdraw Request') ,
                    'link' => array(
                        'controller' => 'user_cash_withdrawals',
                        'action' => 'index',
                        'filter_id' => ConstWithdrawalStatus::Pending,
                    ) ,
                    'conditions' => array(
                        'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Pending,
                        'UserCashWithdrawal.user_id' => $this->Auth->user('id') ,
                    ) ,
                    'colspan' => 2
                )
            );
            $models[] = array(
                'Deal' => array(
                    'display' => __l('Total Commission Amount Earned') . ' (' . Configure::read('site.currency') . ')',
                    'conditions' => array(
                        'Deal.deal_status_id' => array(
                            ConstDealStatus::PaidToCompany
                        ) ,
                        'Deal.company_id' => $company_id['Company']['id']
                    ) ,
                    'alias' => 'DealCommssionAmount',
                    'type' => 'cFloat',
                    'colspan' => 2
                )
            );
            foreach($models as $unique_model) {
                foreach($unique_model as $model => $fields) {
                    foreach($periods as $key => $period) {
                        $conditions = $period['conditions'];
                        if (!empty($fields['conditions'])) {
                            $conditions = array_merge($periods[$key]['conditions'], $fields['conditions']);
                        }
                        $aliasName = !empty($fields['alias']) ? $fields['alias'] : $model;
                        if ($model == 'Transaction') {
                            $TransTotAmount = $this->{$model}->find('first', array(
                                'conditions' => $conditions,
                                'fields' => array(
                                    'SUM(Transaction.amount) as total_amount'
                                ) ,
                                'recursive' => - 1
                            ));
                            $this->set($aliasName . $key, $TransTotAmount['0']['total_amount']);
                        } else if ($model == 'Deal' && $aliasName == 'DealCommssionAmount') {
                            $TransTotAmount = $this->{$model}->find('first', array(
                                'conditions' => $conditions,
                                'fields' => array(
                                    'SUM(Deal.total_commission_amount) as total_amount'
                                ) ,
                                'recursive' => - 1
                            ));
                            $this->set($aliasName . $key, $TransTotAmount['0']['total_amount']);
                        } else {
                            $this->set($aliasName . $key, $this->{$model}->find('count', array(
                                'conditions' => $conditions,
                                'recursive' => - 1
                            )));
                        }
                    }
                }
            }
            $this->set(compact('periods', 'models'));
        }
    }
}
?>