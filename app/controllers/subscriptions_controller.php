<?php
class SubscriptionsController extends AppController
{
    public $name = 'Subscriptions';
    public $uses = array(
        'Subscription',
        'User',
    );
    public $permanentCacheAction = array(
        'add' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true,
        )
    );
    public function beforeFilter()
    {
        if (!$this->User->isAllowed($this->Auth->user('user_type_id'))) {
            throw new NotFoundException(__l('Invalid request'));
        }
        parent::beforeFilter();
    }
    public function add()
    {        
		$this->pageTitle = __l('Add Subscription');
        $this->loadModel('MailChimpList');
        $this->loadModel('DealCategory');
        $Currentstep = 1;
        if (Configure::read('site.enable_three_step_subscription') && !$this->Auth->user('id') && empty($this->layoutPath)) {
            $this->layout = 'subscriptions';
        }
        if (Configure::read('site.is_in_prelaunch_mode')) {
            $this->layout = 'ajax';
        }
        if (!empty($this->request->data)) {
            $subscription = $this->Subscription->find('first', array(
                'conditions' => array(
                    'Subscription.email' => $this->request->data['Subscription']['email'],
                    'Subscription.city_id' => $this->request->data['Subscription']['city_id']
                ) ,
                'fields' => array(
                    'Subscription.id',
                    'Subscription.is_subscribed'
                ) ,
                'recursive' => - 1
            ));
            if (!empty($this->request->data['Subscription']['city_id'])) {
                $get_city = $this->Subscription->City->find('first', array(
                    'conditions' => array(
                        'City.id' => $this->request->data['Subscription']['city_id']
                    ) ,
                    'recursive' => - 1
                ));
            }
            $this->request->data['Subscription']['user_id'] = $this->Auth->user('id');
            if (empty($subscription)) {
                $this->Subscription->create();
                if ($this->Subscription->save($this->request->data)) {
                    if (Configure::read('mailchimp.is_enabled') == 1) {
                        $city_list_id = $this->MailChimpList->find('first', array(
                            'conditions' => array(
                                'MailChimpList.city_id' => $this->request->data['Subscription']['city_id']
                            ) ,
                            'fields' => array(
                                'MailChimpList.list_id'
                            )
                        ));
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'MCAPI.class.php');
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'config.inc.php');
                        $api = new MCAPI(Configure::read('mailchimp.api_key'));
                        $email = $this->request->data['Subscription']['email'];
                        $unsub_link = Cache::read('site_url_for_shell', 'long') . preg_replace('/\//', '', Router::url(array(
                            'controller' => 'subscriptions',
                            'action' => 'unsubscribe',
                            $this->Subscription->getLastInsertId() ,
                            'admin' => false
                        ) , false) , 1);
                        $merge_vars = array(
                            'UNSUBSCRIB' => $unsub_link
                        );
                        $retval = $api->listSubscribe($city_list_id['MailChimpList']['list_id'], $email, $merge_vars, 'html', false);
                        $retval = $api->listUpdateMember($city_list_id['MailChimpList']['list_id'], $email, $merge_vars, 'html', false);
                    }
                    // END OF MAIL CHIMP SAVING //
                    $city = $this->Subscription->City->find('first', array(
                        'conditions' => array(
                            'City.id' => $this->request->data['Subscription']['city_id']
                        ) ,
                        'recursive' => - 1
                    ));
                    if (Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer) {
                        $referal_person = "Refer A Friend";
                        $referal_amount = Configure::read('site.currency') . Configure::read('user.referral_amount');
                        $referal_link = Router::url(array(
                            'controller' => 'pages',
                            'action' => 'refer_a_friend',
                        ) , true);
                    }
                    if (Configure::read('referral.referral_enabled_option') == ConstReferralOption::XRefer) {
                        $referal_person = "Refer" . ' ' . Configure::read('referral.no_of_refer_to_get_a_refund') . ' ' . "Friends";
                        $referal_link = Router::url(array(
                            'controller' => 'pages',
                            'action' => 'refer_friend',
                        ) , true);
                        if (Configure::read('referral.refund_type') == ConstReferralRefundType::RefundDealAmount) {
                            $referal_amount = __l('a free');
                        } else {
                            $referal_amount = Configure::read('site.currency') . Configure::read('referral.refund_amount');
                        }
                    }
                    $this->loadModel('EmailTemplate');
                    $this->EmailTemplate = new EmailTemplate();
                    App::import('Core', 'ComponentCollection');
                    $collection = new ComponentCollection();
                    App::import('Component', 'Email');
                    $this->Email = new EmailComponent($collection);
                    $language_code = $this->Subscription->getUserLanguageIso($this->Auth->user('id'));
                    $template = $this->EmailTemplate->selectTemplate('Subscription Welcome Mail', $language_code);
                    $emailFindReplace = array(
                        '##FROM_EMAIL##' => $this->Subscription->changeFromEmail(Configure::read('EmailTemplate.from_email')) ,
                        '##SITE_LINK##' => Router::url('/', true) ,
                        '##SITE_NAME##' => Configure::read('site.name') ,
                        '##FROM_EMAIL##' => ($template['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $template['from'],
                        '##LEARN_HOW_LINK##' => Router::url(array(
                            'controller' => 'pages',
                            'action' => 'view',
                            'whitelist'
                        ) , true) ,
                        '##REFERAL_PERSON##' => $referal_person,
                        '##REFERRAL_AMOUNT##' => $referal_amount,
                        '##REFER_FRIEND_LINK##' => $referal_link,
                        '##FACEBOOK_LINK##' => ($city['City']['facebook_url']) ? $city['City']['facebook_url'] : Configure::read('facebook.site_facebook_url') ,
                        '##TWITTER_LINK##' => ($city['City']['twitter_url']) ? $city['City']['twitter_url'] : Configure::read('twitter.site_twitter_url') ,
                        '##RECENT_DEALS##' => Router::url(array(
                            'controller' => 'deals',
                            'action' => 'index',
                            'admin' => false,
                            'type' => 'recent'
                        ) , true) ,
                        '##CONTACT_US_LINK##' => Router::url(array(
                            'controller' => 'contacts',
                            'action' => 'add',
                            'admin' => false
                        ) , true) ,
                        '##SITE_LOGO##' => Router::url(array(
                            'controller' => 'img',
                            'action' => 'blue-theme',
                            'logo-email.png',
                            'admin' => false
                        ) , true) ,
                        '##UNSUBSCRIBE_LINK##' => Router::url(array(
                            'controller' => 'subscriptions',
                            'action' => 'unsubscribe',
							'city' => $city['City']['slug'],
                            $this->Subscription->getLastInsertId() ,
                            'admin' => false
                        ) , true) ,
                        '##CONTACT_URL##' => Router::url(array(
                            'controller' => 'contacts',
                            'action' => 'add',
                            'city' => $this->request->params['named']['city'],
                            'admin' => false
                        ) , true) ,
                    );
                    $this->Email->from = ($template['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $template['from'];
                    $this->Email->replyTo = ($template['reply_to'] == '##REPLY_TO_EMAIL##') ? Configure::read('EmailTemplate.reply_to_email') : $template['reply_to'];
                    $this->Email->to = $this->request->data['Subscription']['email'];
                    $this->Email->subject = strtr($template['subject'], $emailFindReplace);
                    $this->Email->content = strtr($template['email_content'], $emailFindReplace);
                    $this->Email->sendAs = ($template['is_html']) ? 'html' : 'text';
                    $this->Email->send($this->Email->content);
                    $this->Session->setFlash(__l('You are now subscribed to') . ' ' . Configure::read('site.name') . ' ' . $get_city['City']['name'] . '.', 'default', null, 'success');
                } else {
                    $Currentstep = 2;
                    if (empty($this->Subscription->validationErrors)) {
                        $this->Session->setFlash(__l('You\'ll start receiving your emails soon.') , 'default', null, 'success');
                    } else {
                        $this->Session->setFlash(__l('Could not be subscribed. Please, try again.') , 'default', null, 'error');
                    }
                }
            } elseif (!empty($subscription) && !$subscription['Subscription']['is_subscribed']) {
                $this->request->data['Subscription']['is_subscribed'] = 1;
                $this->request->data['Subscription']['id'] = $subscription['Subscription']['id'];
                $this->Subscription->save($this->request->data);
                $this->Session->setFlash(__l('You are now subscribed to') . ' ' . Configure::read('site.name') . ' ' . $get_city['City']['name'] . '. ' . __l('Thanks for subscribing again.') , 'default', null, 'success');
            } else {
                $this->Session->setFlash(__l('You\'ll start receiving your emails soon.') , 'default', null, 'success');
            }
            if (empty($this->Subscription->validationErrors) && Configure::read('site.is_in_prelaunch_mode')) {
                $ajax_url = Router::url(array(
                    'controller' => 'page',
                    'action' => 'view',
                    'pre-launch'
                ) , true);
                $success_msg = 'redirect*' . $ajax_url;
                echo $success_msg;
                exit;
            }
            if (empty($this->Subscription->validationErrors) && Configure::read('site.enable_three_step_subscription')) {
                $this->Cookie->write('is_subscribed', 1, false); // For skipping subscriptions
                $check_deal_exist = $this->Subscription->User->DealUser->Deal->CitiesDeal->find('first', array(
                    'conditions' => array(
                        'CitiesDeal.city_id' => $get_city['City']['id']
                    ) ,
                    'contain' => array(
                        'Deal' => array(
                            'fields' => array(
                                'Deal.id',
                                'Deal.name',
                                'Deal.slug',
                                'Deal.city_id',
                                'Deal.deal_status_id',
                            ) ,
                            'conditions' => array(
                                'Deal.deal_status_id' => array(
                                    ConstDealStatus::Closed,
                                    ConstDealStatus::Tipped,
                                ) ,
                            )
                        ) ,
                    ) ,
                    'recursive' => 1
                ));
                if (!empty($check_deal_exist['Deal'])) {
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'city' => $get_city['City']['slug']
                    ));
                } else {
                    $this->redirect(array(
                        'controller' => 'page',
                        'action' => 'view',
                        'city' => $get_city['City']['slug'],
                        'how_it_works'
                    ));
                }
            }
        } else {
            $city = $this->Subscription->City->find('first', array(
                'conditions' => array(
                    'City.slug' => $this->request->params['named']['city']
                ) ,
                'fields' => array(
                    'City.id'
                ) ,
                'recursive' => - 1
            ));
            $this->request->data['Subscription']['city_id'] = $city['City']['id'];
        }
        if ($this->Auth->user('user_type_id') == ConstUserTypes::Company) {
            $company = $this->Subscription->User->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'recursive' => - 1
            ));
            $this->set('company', $company);
        }
        $cities = $this->Subscription->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        $deal_categories = $this->DealCategory->find('list', array(
            'order' => array(
                'DealCategory.name' => 'asc'
            )
        ));
        $this->set(compact('cities'));
        $selected = array_keys($deal_categories);
        $this->set('options', $deal_categories);
        $this->set('Currentstep', $Currentstep);
        $this->set('selected', $selected);
        $this->set('pageTitle', __l('Deal of the Day'));
    }
    function admin_update_subscribers()
    {
        $this->Subscription->_updateSubscribersList();
        $this->Session->setFlash(__l('Subscribers list has been updated.') , 'default', null, 'success');
        $this->redirect(array(
            'action' => 'index'
        ));
    }
    function skip()
    {
        $this->Cookie->write('is_subscribed', 1, false); // For skipping subscriptions
        $this->redirect(array(
            'controller' => 'deals',
            'action' => 'index'
        ));
    }
    public function unsubscribe($id = null)
    {
        $this->pageTitle = __l('Unsubscribe');
        $this->loadModel('MailChimpList');
        if (is_null($id) && empty($this->request->data)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            $subscription = $this->Subscription->find('first', array(
                'conditions' => array(
                    'Subscription.id' => $this->request->data['Subscription']['id']
                ) ,                
                'recursive' => - 1
            ));
            if (empty($subscription)) {
                $this->Session->setFlash(__l('Please provide a subscribed email') , 'default', null, 'error');
            } else {
                $this->request->data['Subscription']['is_subscribed'] = 0;
                if ($this->Subscription->save($this->request->data)) {
                    if (Configure::read('mailchimp.is_enabled') == 1) {
                        //unsubscribe the email in mail chimp
                        $city_list_id = $this->MailChimpList->find('first', array(
                            'conditions' => array(
                                'MailChimpList.city_id' => $subscription['Subscription']['city_id']
                            ) ,
                            'fields' => array(
                                'MailChimpList.list_id'
                            )
                        ));
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'MCAPI.class.php');
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'config.inc.php');
                        $api = new MCAPI(Configure::read('mailchimp.api_key'));
                        $retval = $api->listUnsubscribe($city_list_id['MailChimpList']['list_id'], $subscription['Subscription']['email']);
                    }
                    $this->Session->setFlash(__l('You have unsubscribed from the subscribers list') , 'default', null, 'success');
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'index'
                    ));
                }
            }
        } else {
            $this->request->data['Subscription']['id'] = $id;
        }
    }
    function unsubscribe_mailchimp()
    {
        $this->pageTitle = __l('Unsubscribe');
        $this->loadModel('MailChimpList');
		if (!empty($this->request->data)) {
			$city = $this->Subscription->City->find('first', array(
                'conditions' => array(
                    'City.slug' => $this->request->data['Subscription']['city'],
                ) ,
                'recursive' => - 1
            ));
            $get_subscriber = $this->Subscription->find('first', array(
                'conditions' => array(
                    'Subscription.email' => urlencode_rfc3986($this->request->data['Subscription']['email']),
                ) ,
                'recursive' => - 1
            ));
			if(!empty($get_subscriber)){
				$this->Subscription->updateAll(array(
					'Subscription.is_subscribed' => 0
				) , array(
					'Subscription.id' => $get_subscriber['Subscription']['id'],
				));
				if (Configure::read('mailchimp.is_enabled') == 1) {
					//unsubscribe the email in mail chimp
					$city_list_id = $this->MailChimpList->find('first', array(
						'conditions' => array(
							'MailChimpList.city_id' => $city['City']['id']
						) ,
						'fields' => array(
							'MailChimpList.list_id'
						)
					));
					include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'MCAPI.class.php');
					include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'config.inc.php');
					$api = new MCAPI(Configure::read('mailchimp.api_key'));
					$retval = $api->listUnsubscribe($city_list_id['MailChimpList']['list_id'], urlencode_rfc3986($this->request->data['Subscription']['email']));
				}
				$this->Session->setFlash(__l('You have unsubscribed from the subscribers list.') , 'default', null, 'success');
				$this->redirect(array(
					'controller' => 'deals',
					'action' => 'index'
				));
			}
		}
		if (!empty($this->request->params['named']['sub_city']) && !empty($this->request->params['named']['email'])) {
			$this->request->data['Subscription']['email'] = $this->request->params['named']['email'];
			$this->request->data['Subscription']['city'] = $this->request->params['named']['sub_city'];
        } else {
			$this->redirect(array(
				'controller' => 'deals',
				'action' => 'index'
			));
		}
    }
    public function admin_index()
    {
        $this->_redirectGET2Named(array(
            'q'
        ));
        $this->pageTitle = __l('Subscriptions');
        $conditions = array();
        $param_string = '';
        $param_string.= !empty($this->request->params['named']['type']) ? '/type:' . $this->request->params['named']['type'] : $param_string;
        if (!empty($this->request->data['Subscription']['type'])) {
            $this->request->params['named']['type'] = $this->request->data['Subscription']['type'];
        }
        if (empty($this->request->data['Subscription']['q']) && !empty($this->request->params['named']['q'])) {
            $this->request->data['Subscription']['q'] = $this->request->params['named']['q'];
        }
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'subscribed') {
            $this->request->data['Subscription']['type'] = $this->request->params['named']['type'];
            $conditions['Subscription.is_subscribed'] = 1;
            $this->pageTitle = __l('Subscribed Users');
        } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'unsubscribed') {
            $this->request->data['Subscription']['type'] = $this->request->params['named']['type'];
            $conditions['Subscription.is_subscribed'] = 0;
            $this->pageTitle = __l('Unsubscribed Users');
        }
        if (isset($this->request->data['Subscription']['q']) && !empty($this->request->data['Subscription']['q'])) {
            $this->request->params['named']['q'] = $this->request->data['Subscription']['q'];
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->data['Subscription']['q']);
        }
        // Citywise admin filter //
        $city_filter_id = $this->Session->read('city_filter_id');
        if (!empty($city_filter_id)) {
            $conditions['Subscription.city_id'] = $city_filter_id;
        }
        if ($this->RequestHandler->prefers('csv')) {
            Configure::write('debug', 0);
            $this->set('SubscriptionObj', $this);
            $this->set('conditions', $conditions);
            if (isset($this->request->params['named']['q'])) {
                $this->set('q', $this->request->params['named']['q']);
            }
            $this->set('contain', $contain);
        } else {
            $this->Subscription->recursive = 0;
            $this->paginate = array(
                'conditions' => $conditions,
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'User.email',
                            'User.id',
                            'User.username',
                        ) ,
                    ) ,
                    'City',
                ) ,
                'recursive' => 1,
                'order' => array(
                    'Subscription.id' => 'desc'
                ) ,
            );
            $export_subscriptions = $this->Subscription->find('all', array(
                'conditions' => $conditions,
                'recursive' => - 1
            ));
            if (!empty($export_subscriptions)) {
                $ids = array();
                foreach($export_subscriptions as $export_subscription) {
                    $ids[] = $export_subscription['Subscription']['id'];
                }
                $hash = $this->Subscription->getIdHash(implode(',', $ids));
                $_SESSION['export_subscriptions'][$hash] = $ids;
                $this->set('export_hash', $hash);
            }
            if (!empty($this->request->data['Subscription']['q'])) {
                $this->paginate = array_merge($this->paginate, array(
                    'search' => $this->request->params['named']['q']
                ));
            }
            $this->set('subscriptions', $this->paginate());
            // Citywise admin filter //
            $count_conditions = array();
            if (!empty($city_filter_id)) {
                $count_conditions['Subscription.city_id'] = $city_filter_id;
            }
            $this->set('subscribed', $this->Subscription->find('count', array(
                'conditions' => array_merge(array(
                    'Subscription.is_subscribed' => 1,
                ) , $count_conditions) ,
                'recursive' => 0
            )));
            $this->set('unsubscribed', $this->Subscription->find('count', array(
                'conditions' => array_merge(array(
                    'Subscription.is_subscribed' => 0,
                ) , $count_conditions) ,
                'recursive' => 0
            )));
            $this->set('pageTitle', $this->pageTitle);
            $moreActions = $this->Subscription->moreActions;
            if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'unsubscribed')) {
                unset($moreActions[ConstMoreAction::UnSubscripe]);
            }
            $this->set(compact('moreActions'));
            $this->set('param_string', $param_string);
        }
    }
    public function admin_update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data['Subscription'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $userIds = array();
            foreach($this->request->data['Subscription'] as $subscription_id => $is_checked) {
                if ($is_checked['id']) {
                    $subscriptionIds[] = $subscription_id;
                }
            }
            if ($actionid && !empty($subscriptionIds)) {
                if ($actionid == ConstMoreAction::Delete) {
                    $this->Subscription->deleteAll(array(
                        'Subscription.id' => $subscriptionIds
                    ));
                    $this->Session->setFlash(__l('Checked subscriptions has been deleted') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::UnSubscripe) {
                    $this->Subscription->updateAll(array(
                        'Subscription.is_subscribed' => 0,
                    ) , array(
                        'Subscription.id' => $subscriptionIds
                    ));
                    $this->Session->setFlash(__l('Checked subscriptions has been un subscribed') , 'default', null, 'success');
                }
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
        if ($this->Subscription->delete($id)) {
            $this->Session->setFlash(__l('Subscription deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    function subscribes()
    {
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Subscription']['City'])) {
                $cities = $this->request->data['Subscription']['City'];
                unset($this->request->data['Subscription']['City']);
                $this->request->data['Subscription']['email'] = $this->Auth->user('email');
                $this->request->data['Subscription']['user_id'] = $this->Auth->user('id');
                $this->request->data['Subscription']['is_subscribed'] = 1;
                foreach($cities as $city) {
                    $this->request->data['Subscription']['city_id'] = $city;
                    $subscription = $this->Subscription->find('first', array(
                        'conditions' => array(
                            'Subscription.email' => $this->Auth->user('email') ,
                            'Subscription.city_id' => $city
                        ) ,
                        'recursive' => - 1
                    ));
                    if (empty($subscription)) {
                        $this->Subscription->create();
                    } else {
                        $this->request->data['Subscription']['id'] = $subscription['Subscription']['id'];
                        $insert_id = $subscription['Subscription']['id'];
                    }
                    $this->Subscription->save($this->request->data);
                    if (empty($subscription)) {
                        $insert_id = $this->Subscription->getLastInsertId();
                    }
                    if (Configure::read('mailchimp.is_enabled') == 1) {
                        $this->loadModel('MailChimpList');
                        $city_list_id = $this->MailChimpList->find('first', array(
                            'conditions' => array(
                                'MailChimpList.city_id' => $city
                            ) ,
                            'fields' => array(
                                'MailChimpList.list_id'
                            )
                        ));
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'MCAPI.class.php');
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'config.inc.php');
                        $api = new MCAPI(Configure::read('mailchimp.api_key'));
                        $email = $this->request->data['Subscription']['email'];
                        $unsub_link = Cache::read('site_url_for_shell', 'long') . preg_replace('/\//', '', Router::url(array(
                            'controller' => 'subscriptions',
                            'action' => 'unsubscribe',
                            $insert_id,
                            'admin' => false
                        ) , false) , 1);
                        $merge_vars = array(
                            'UNSUBSCRIB' => $unsub_link
                        );
                        $retval = $api->listSubscribe($city_list_id['MailChimpList']['list_id'], $email, $merge_vars, 'html', false);
                        $retval = $api->listUpdateMember($city_list_id['MailChimpList']['list_id'], $email, $merge_vars, 'html', false);
                    }
                }
                $this->Session->setFlash(__l('Checked cities has been subscribed') , 'default', null, 'success');
                if ($this->RequestHandler->isAjax()) {
                    $url = Router::url(array(
                        'controller' => 'subscriptions',
                        'action' => 'manage_subscription',
                        'city' => $this->request->params['named']['city'],
                        'admin' => false
                    ) , true);
                    echo 'redirect*' . $url;
                    exit;
                } else {
                    $this->redirect(array(
                        'controller' => 'subscriptions',
                        'action' => 'manage_subscription'
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Subscriptions Could not be added. please select cities') , 'default', null, 'error');
            }
        }
        $conditions[] = array(
            'OR' => array(
                array(
                    'Subscription.email' => $this->Auth->user('email')
                ) ,
                array(
                    'Subscription.user_id' => $this->Auth->user('id')
                ) ,
            )
        );
        $conditions['Subscription.is_subscribed'] = 1;
        $get_subscribers = $this->Subscription->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'Subscription.id',
                'Subscription.city_id'
            ) ,
            'recursive' => - 1
        ));
        $cities = $this->Subscription->City->find('list', array(
            'conditions' => array(
                'NOT' => array(
                    'City.id' => $get_subscribers
                ) ,
                'City.is_approved' => 1
            ) ,
            'fields' => array(
                'City.id',
                'City.name'
            ) ,
            'recursive' => - 1
        ));
        $this->set(compact('cities'));
    }
    function unsubscribes()
    {
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Subscription']['unsubscribers'])) {
                $subcriber_ids = $this->request->data['Subscription']['unsubscribers'];
                unset($this->request->data['Subscription']['unsubscribers']);
                foreach($subcriber_ids as $subcriber_id) {
                    $this->request->data['Subscription']['id'] = $subcriber_id;
                    $this->request->data['Subscription']['is_subscribed'] = 0;
                    $this->Subscription->save($this->request->data);
                    if (Configure::read('mailchimp.is_enabled') == 1) {
                        $this->loadModel('MailChimpList');
                        //unsubscribe the email in mail chimp
                        $city_list_id = $this->MailChimpList->find('first', array(
                            'conditions' => array(
                                'MailChimpList.city_id' => $subcriber_id
                            ) ,
                            'fields' => array(
                                'MailChimpList.list_id'
                            )
                        ));
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'MCAPI.class.php');
                        include_once (APP . DS . 'vendors' . DS . 'mailchimp' . DS . 'config.inc.php');
                        $api = new MCAPI(Configure::read('mailchimp.api_key'));
                        $retval = $api->listUnsubscribe($city_list_id['MailChimpList']['list_id'], $this->Auth->user('email'));
                    }
                }
                $this->Session->setFlash(__l('Checked city has been Unsubscribed') , 'default', null, 'success');
                if ($this->RequestHandler->isAjax()) {
                    $url = Router::url(array(
                        'controller' => 'subscriptions',
                        'action' => 'manage_subscription',
                        'city' => $this->request->params['named']['city'],
                        'admin' => false
                    ) , true);
                    echo 'redirect*' . $url;
                    exit;
                } else {
                    $this->redirect(array(
                        'controller' => 'subscriptions',
                        'action' => 'manage_subscription'
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Could not be Unsubscribed. please select cities') , 'default', null, 'error');
            }
        }
        $conditions[] = array(
            'OR' => array(
                array(
                    'Subscription.email' => $this->Auth->user('email')
                ) ,
                array(
                    'Subscription.user_id' => $this->Auth->user('id')
                ) ,
            )
        );
        $conditions['Subscription.is_subscribed'] = 1;
        $unsubscribers = $this->Subscription->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'Subscription.id',
                'City.name'
            ) ,
            'recursive' => 2
        ));
        $this->set(compact('unsubscribers'));
    }
    function manage_subscription()
    {
        $this->pageTitle = __l('Manage Subscriptions');
    }
    function admin_subscription_customise()
    {
        $this->pageTitle = __l('Customize Two Step Subscription Page');
        if (!empty($this->request->data)) {
            $this->Subscription->set($this->request->data);
            if ($this->Subscription->validates()) {
                $this->uploadImages($this->request->data);
            }
        }
        $this->loadModel('Attachment');
        $logo = $this->Attachment->find('first', array(
            'conditions' => array(
                'Attachment.foreign_id' => ConstAttachment::Page,
                'Attachment.class' => 'PageLogo',
                'Attachment.description' => 'subscription_logo'
            ) ,
            'fields' => array(
                'Attachment.id',
                'Attachment.dir',
                'Attachment.filename',
                'Attachment.width',
                'Attachment.height',
                'Attachment.description'
            ) ,
            'recursive' => - 1
        ));
        $image_options = array(
            'dimension' => 'subscription_home_thumb',
            'class' => '',
            'alt' => $logo['Attachment']['filename'],
            'title' => $logo['Attachment']['filename'],
            'type' => 'jpg'
        );
        $this->loadModel('Setting');
        $background = $this->Setting->find('all', array(
            'conditions' => array(
                'Setting.setting_category_id' => array(
                    7,
                    27
                )
            ) ,
            'recursive' => - 1
        ));
        foreach($background as $value) {
            if ($value['Setting']['name'] == 'subscription.bgcolor') {
                $this->request->data['Subscription']['bgcolor'] = $value['Setting']['value'];
            } elseif ($value['Setting']['name'] == 'subscription.is_bg_image_center') {
                $this->request->data['Subscription']['is_bg_image_center'] = $value['Setting']['value'];
            } elseif ($value['Setting']['name'] == 'thumb_size.subscription_home_thumb.width') {
                $this->request->data['Subscription']['width'] = $value['Setting']['value'];
            } elseif ($value['Setting']['name'] == 'thumb_size.subscription_home_thumb.height') {
                $this->request->data['Subscription']['height'] = $value['Setting']['value'];
            }
        }
        if (empty($this->request->data['Subscription']['bgcolor'])) {
            $this->request->data['Subscription']['bgcolor'] = '00B5C8';
        }
        $this->set('background', $background);
        $large_image_url = $this->Subscription->getImageUrl('PageLogo', $logo['Attachment'], $image_options);
        @unlink($large_image_url);
        $image_options['dimension'] = 'original';
        $original_thumb_url = $this->Subscription->getImageUrl('PageLogo', $logo['Attachment'], $image_options);
        $image_options['dimension'] = 'medium_thumb';
        $small_thumb_url = $this->Subscription->getImageUrl('PageLogo', $logo['Attachment'], $image_options);
        @unlink($small_thumb_url);
        $this->set('logo', $logo);
        $this->set('large_image_url', $large_image_url);
    }
    function uploadImages($data)
    {
        $this->loadModel('PageLogo');
        $this->loadModel('Attachment');
        $this->loadModel('Setting');
        $is_success = 1;
        $background = $this->Setting->find('all', array(
            'conditions' => array(
                'Setting.setting_category_id' => array(
                    7,
                    27
                )
            ) ,
            'recursive' => - 1
        ));
        foreach($data['PageLogo'] as $user_id => $is_checked) {
            if ($user_id != 'subscription_logo') {
                if ($is_checked['id']) {
                    $this->Attachment->delete($user_id);
                }
                unset($data['PageLogo'][$user_id]);
            }
        }
        $user = $this->Attachment->find('all', array(
            'conditions' => array(
                'Attachment.foreign_id' => ConstAttachment::Page,
                'Attachment.class' => 'PageLogo'
            ) ,
            'recursive' => - 1
        ));
        if (!empty($data['PageLogo']['subscription_logo'])) {
            $uploads = array(
                'subscription_logo' => $data['PageLogo']['subscription_logo']
            );
            $this->PageLogo->Behaviors->attach('ImageUpload', Configure::read('pagelogo.file'));
            foreach($uploads as $key => $upload) {
                if (!empty($upload['name'])) {
                    $attachment_id = $this->Attachment->find('first', array(
                        'conditions' => array(
                            'Attachment.foreign_id' => ConstAttachment::Page,
                            'Attachment.class' => 'PageLogo',
                            'Attachment.description' => $key,
                        ) ,
                        'recursive' => - 1
                    ));
                    if (!empty($upload['name'])) {
                        $upload['type'] = get_mime($upload['tmp_name']);
                    }
                    $ini_upload_error = 1;
                    if ($upload['error'] == 1) {
                        $ini_upload_error = 0;
                    }
                    $tmp['filename'] = $upload;
                    unset($upload);
                    if (!empty($attachment_id)) {
                        $tmp['id'] = $attachment_id['Attachment']['id'];
                    }
                    $upload['PageLogo'] = $tmp;
                    if (!empty($upload['PageLogo']['filename']['name']) || (!Configure::read('pagelogo.file.allowEmpty') && empty($upload['PageLogo']['id']))) {
                        $this->PageLogo->set($upload);
                    }
                    if ($this->PageLogo->validates()) {
                        if (!empty($upload['PageLogo']['filename']['name'])) {
                            $this->Attachment->create();
                            $upload['PageLogo']['class'] = 'PageLogo';
                            $upload['PageLogo']['foreign_id'] = ConstAttachment::Page;
                            $upload['PageLogo']['description'] = $key;
                            $this->Attachment->save($upload['PageLogo']);
                            Cache::delete('subscription_attachment');
                            $this->Session->setFlash(__l('Subcription background image uploaded.') , 'default', null, 'success');
                        }
                        unset($tmp);
                    } else {
                        $this->PageLogo->validationErrors[$key] = __l('The submitted file extension is not permitted, only jpg,jpeg,gif,png permitted.');
                        $this->Session->setFlash(__l('Image not uploaded. Please try again ') , 'default', null, 'error');
                        return $is_success = 0;
                    }
                }
            }
            // End of foreach //
            
        }
        if (!empty($is_success)) {
            Cache::delete('setting_key_value_pairs');
            Cache::delete('subscription_attachment');
            foreach($background as $value) {
                if ($value['Setting']['name'] == 'thumb_size.subscription_home_thumb.width') {
                    $_data['Setting']['id'] = $value['Setting']['id'];
                    $_data['Setting']['value'] = $data['Subscription']['width'];
                    $this->Setting->save($_data);
                    Configure::write('thumb_size.subscription_home_thumb.width', $data['Subscription']['width']);
                    $dir = WWW_ROOT . 'img' . DS . 'subscription_home_thumb' . DS . 'PageLogo';
                    $this->_traverse_directory($dir, 0);
                } elseif ($value['Setting']['name'] == 'thumb_size.subscription_home_thumb.height') {
                    $_data['Setting']['id'] = $value['Setting']['id'];
                    $_data['Setting']['value'] = $data['Subscription']['height'];
                    $this->Setting->save($_data);
                    Configure::write('thumb_size.subscription_home_thumb.height', $data['Subscription']['height']);
                    $dir = WWW_ROOT . 'img' . DS . 'subscription_home_thumb' . DS . 'PageLogo';
                    $this->_traverse_directory($dir, 0);
                } elseif ($value['Setting']['name'] == 'subscription.is_bg_image_center') {
                    $_data['Setting']['id'] = $value['Setting']['id'];
                    $_data['Setting']['value'] = $data['Subscription']['is_bg_image_center'];
                    $this->Setting->save($_data);
                    Configure::write('subscription.is_bg_image_center', $data['Subscription']['is_bg_image_center']);
                } elseif (!empty($data['Subscription']['bgcolor']) && $value['Setting']['name'] == 'subscription.bgcolor') {
                    $_data['Setting']['id'] = $value['Setting']['id'];
                    $_data['Setting']['value'] = $data['Subscription']['bgcolor'];
                    $this->Setting->save($_data);
                    Configure::write('subscription.bgcolor', $data['Subscription']['bgcolor']);
                }
            }
        }
    }
}
?>
