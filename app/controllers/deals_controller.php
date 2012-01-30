<?php
class DealsController extends AppController
{
    public $name = 'Deals';
    public $components = array(
        'Email',
        'Paypal',
        'RequestHandler',
        'PagSeguro',
        'ipay88'
    );
    public $helpers = array(
        'Csv',
        'Gateway',
        'PagSeguro',
        'Cache'
    );
    public $permanentCacheAction = array(
        'index' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true,
        ) ,
        'company_deals' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true,
        ) ,
        'coupons_export' => array(
            'is_user_specific_url' => true,
        ) ,
        'stats' => array(
            'is_user_specific_url' => true,
        ) ,
        'view' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true,
        ) ,
        'edit' => array(
            'is_user_specific_url' => true,
        ) ,
        'add' => array(
            'is_loggedin_and_usertype_url' => true,
        ) ,
        'invite_friends' => array(
            'is_loggedin_url' => true,
        ) ,
        'deals_print' => array(
            'is_user_specific_url' => true,
        ) ,
        'buy' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true
        ) ,
        'widget' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'Attachment',
            'CompanyAddressesDeal',
            'Deal.calculator_min_limit',
            'Deal.calculator_discounted_price',
            'Deal.calculator_bonus_amount',
            'Deal.calculator_commission_percentage',
            'Deal.start_date',
            'Deal.end_date',
            'Deal.coupon_expiry_date',
            'DealStatus.id',
            'DealStatus.name',
            'Deal.original_price',
            'Deal.savings',
            'Deal.is_save_draft',
            'Deal.id',
            'Deal.save_as_draft',
            'Deal.send_to_admin',
            'Deal.gift_email',
            'Deal.gift_from',
            'Deal.gift_to',
            'Deal.message',
            'Deal.old_coupon_code',
            'Deal.quantity',
            'Deal.message',
            'Deal.deal_amount',
            'Deal.deal_id',
            'Deal.is_gift',
            'User.confirm_password',
            'User.email',
            'User.passwd',
            'User.username',
            'Deal.user_available_balance',
            'Deal.gift_to',
            'Deal.is_purchase_via_wallet',
            'Deal.is_show_new_card',
            'Deal.payment_gateway_id',
            'Deal.budget_amt',
            'Deal.original_amt',
            'Deal.discount_amt',
            'Deal.coupon_start_date',
            'Deal.max_limit',
            'Deal.discount_percentage',
            'Deal.discount_amount',
            'Deal.discounted_price',
            'Deal.bonus_amount',
            'Deal.commission_percentage',
            'Deal.name',
            'Deal.is_enable_payment_advance',
            'Deal.payment_remaining',
            'Deal.pay_in_advance',
            'Deal.continue',
        );
        parent::beforeFilter();
    }
    public function index($city_slug = null)
    {
        $has_near_by_deal = 0;
		$sub_title = '';
        // subdeal add redirect changes
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'success') {
            if ($this->Session->read('Deal.id')) {
                $deal_id = $this->Session->read('Deal.id');
                $this->Session->delete('Deal.id');
                $this->redirect(array(
                    'controller' => 'deals',
                    'action' => 'subdeal_add',
                    $deal_id
                ));
            }
        }
        $this->_redirectGET2Named(array(
            'q'
        ));
        $this->loadModel('DealCategory');
        if (!empty($this->request->data['Deal']['q'])) {
            $this->request->params['named']['q'] = $this->request->data['Deal']['q'];
        }
        if (!empty($this->request->data['Deal']['filter_id'])) {
            $this->request->params['named']['filter_id'] = $this->request->data['Deal']['filter_id'];
        }
        if (!empty($this->request->data['Deal']['company_slug'])) {
            $this->request->params['named']['company'] = $this->request->data['Deal']['company_slug'];
        }
        if (!empty($this->request->data['Deal']['type'])) {
            $this->request->params['named']['type'] = $this->request->data['Deal']['type'];
        }
        $limit = (!empty($this->paginate['limit'])) ? $this->paginate['limit'] : 20;
        $city_conditions = array();
        if (!empty($this->request->params['named']['filter_id'])) {
            $conditions['Deal.deal_status_id'] = $this->request->params['named']['filter_id'];
            $status = $this->Deal->DealStatus->find('first', array(
                'conditions' => array(
                    'DealStatus.id' => $this->request->params['named']['filter_id'],
                ) ,
                'fields' => array(
                    'DealStatus.name'
                ) ,
                'recursive' => - 1
            ));
            $title = $status['DealStatus']['name'];
			$this->pageTitle = sprintf(__l(' %s Deals') , $title);
        }
		// check the filer passed through named parameter
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Deal.created) <= '] = 0;
            $this->pageTitle.= __l(' - Created today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Deal.created) <= '] = 7;
            $this->pageTitle.= __l(' - Created in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Deal.created) <= '] = 30;
            $this->pageTitle.= __l(' - Created in this month');
        }

        // deal add sucess message
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'success') {
            $this->Session->setFlash(__l('Deal has been added.') , 'default', null, 'success');
        }
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'near') {
            if (@$_SESSION['maxmaind_latitude'] == '') {
                $this->setMaxmindInfo();
            }
            $city_slug = $this->request->params['named']['city'];
            $cities = $this->Deal->City->find('all', array(
                'fields' => array(
                    'City.id',
                    '( 6371 * acos( cos( radians(' . $_SESSION['maxmaind_latitude'] . ') ) * cos( radians( City.latitude ) ) * cos( radians( City.longitude ) - radians(' . $_SESSION['maxmaind_longitude'] . ') ) + sin( radians(' . $_SESSION['maxmaind_latitude'] . ') ) * sin( radians( City.latitude ) ) ) ) AS distance'
                ) ,
                'group' => array(
                    'City.id HAVING distance <' . Configure::read('deal.nearby_deal_km')
                ) ,
                'conditions' => array(
                    'City.slug !=' => $city_slug
                ) ,
                'order' => array(
                    'distance' => 'asc'
                ) ,
                'recursive' => - 1,
            ));
            if (!empty($cities)) {
                $city_ids = array();
                foreach($cities as $city) {
                    $city_ids[$city['City']['id']] = $city['City']['id'];
                }
                $citiesDeals = $this->Deal->CitiesDeal->find('all', array(
                    'conditions' => array(
                        'CitiesDeal.city_id' => $city_ids,
                    ) ,
                    'recursive' => - 1
                ));
                $city_deal_ids = array();
                $s = 0;
                foreach($citiesDeals as $citiesDeal) {
                    if (!empty($citiesDeal['CitiesDeal']['deal_id']) && (empty($this->request->params['named']['deal_id']) || (!empty($this->request->params['named']['deal_id']) && $citiesDeal['CitiesDeal']['deal_id'] != $this->request->params['named']['deal_id']))) {
                        $city_deal_ids[] = $citiesDeal['CitiesDeal']['deal_id'];
                        $s++;
                    }
                    if (!empty($this->request->params['named']['view']) && $this->request->params['named']['view'] == 'simple' && $s >= Configure::read('deal.nearby_deal_index_limit')) {
                        break;
                    }
                }
                if (!empty($city_deal_ids)) {
                    $has_near_by_deal = 1;
                    $conditions['Deal.id'] = $city_deal_ids;
                }
            }
        } else {
            //home page deals
            if (empty($this->request->params['named']['company'])) {
                $city_slug = $this->request->params['named']['city'];
                $city = $this->Deal->City->find('first', array(
                    'conditions' => array(
                        'City.slug' => $city_slug
                    ) ,
                    'fields' => array(
                        'City.name',
                        'City.id'
                    ) ,
                    'contain' => array(
                        'Deal' => array(
                            'fields' => array(
                                'Deal.id'
                            ) ,
                        )
                    ) ,
                    'recursive' => 1
                ));
                if (empty($city)) {
                    throw new NotFoundException(__l('Invalid request'));
                }
                $city_deal_ids = array();
                foreach($city['Deal'] as $deal) {
                    $city_deal_ids[] = $deal['id'];
                }
                $conditions['Deal.id'] = $city_deal_ids;
                $city_conditions['Topic.city_id'] = $city['City']['id'];
            }
        }
        //company users deals list ends
        $order = array(
            'Deal.id' => 'desc'
        );
        //recent and company deals list
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'near') {
            $this->pageTitle = __l('Nearby Deals');
			$sub_title = __l('Nearby Deals');
            /*if (Configure::read('deal.is_side_deal_enabled')) {
                $conditions['Deal.is_side_deal'] = 0;
            }*/
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Open,
                ConstDealStatus::Tipped,
            );
        } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'main') {
			$this->pageTitle = __l('Main Deals');
			$sub_title = __l('Main Deals');
            if (Configure::read('deal.is_side_deal_enabled')) {
                $conditions['Deal.is_side_deal'] = 0;
            }
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Open,
                ConstDealStatus::Tipped,
            );
        }elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'side') {
			$this->pageTitle = __l('Side Deals');
			$sub_title = __l('Side Deals');
            if (Configure::read('deal.is_side_deal_enabled')) {
                $conditions['Deal.is_side_deal'] = 1;
            }
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Open,
                ConstDealStatus::Tipped,
            );
        } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recent') {
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Closed,
                ConstDealStatus::Expired,
                ConstDealStatus::Canceled,
                ConstDealStatus::PaidToCompany,
                ConstDealStatus::Refunded
            );
            $conditions['Deal.end_date <'] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
            $this->pageTitle = __l('Recent Deals');
			$sub_title = __l('Recent Deals');
            $order = array(
                'Deal.end_date' => 'DESC'
            );
        } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'anytime') {
            $conditions['Deal.is_anytime_deal'] = 1;
            $this->pageTitle = __l('Virtual Store');
			$sub_title = __l('Virtual Store');
        } elseif (empty($this->request->params['named']['company'])) {
            /*if (Configure::read('deal.is_side_deal_enabled')) {
                $conditions['Deal.is_side_deal'] = 0;
            }*/
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Open,
                ConstDealStatus::Tipped,
            );
            $this->pageTitle = ucfirst($city['City']['name']) . ' ' . __l('Deals of the Day');
            $order = array(
                'Deal.end_date' => 'desc',
				'Deal.is_side_deal' => 'asc'
            );
            $limit = 1;
            $this->set('city_name', $city['City']['name']);
        }
        //for company
        if (!empty($this->request->params['named']['company'])) {
            $company = $this->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.slug = ' => $this->request->params['named']['company'],
                ) ,
                'fields' => array(
                    'Company.id',
                    'Company.name',
                    'Company.slug',
                    'Company.user_id',
                ) ,
                'recursive' => - 1
            ));
            if ((!$this->Auth->user('id')) || ($company['Company']['user_id'] != $this->Auth->user('id'))) {
                throw new NotFoundException(__l('Invalid request'));
            }
            $conditions['Deal.company_id'] = $company['Company']['id'];
            if (!empty($this->request->params['named']['filter_id'])) {
                $conditions['Deal.deal_status_id'] = $this->request->params['named']['filter_id'];
            }
            $headings = __l('My Deals');
            $this->set('headings', $headings);
			$this->set('pageTitle', $this->pageTitle);
            $this->set('company_slug', $company['Company']['slug']);
        }
        if (isset($this->request->params['named']['q'])) {
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->params['named']['q']);
        }
        $not_conditions = array();
        if ($this->Auth->user('user_type_id') == ConstUserTypes::User) {
            $not_conditions['Not']['Deal.deal_status_id'] = array(
                ConstDealStatus::PendingApproval,
                ConstDealStatus::Upcoming
            );
        }
        $not_conditions['Not']['Deal.deal_status_id'][] = ConstDealStatus::SubDeal;
        if (!empty($this->request->query['ext']) && ($this->request->query['ext'] == 'rss')) {
            unset($conditions['Deal.is_side_deal']);
        }
		if($this->layoutPath == 'touch')
		{
			unset($conditions['Deal.is_side_deal']);
			$limit = (!empty($this->paginate['limit'])) ? $this->paginate['limit'] : 20;
		}
        // <-- For iPhone App code
        if ($this->RequestHandler->prefers('json')) {
            unset($conditions['Deal.is_side_deal']);
        }
        //For iPhone App code -->
        $this->paginate = array(
            'conditions' => array(
                $conditions,
                $not_conditions,
            ) ,
            'contain' => array(
                'SubDeal',
                'Charity' => array(
                    'fields' => array(
                        'Charity.name',
                        'Charity.url',
                        'Charity.id',
                    )
                ) ,
                'User' => array(
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                        'User.email',
                        'User.password',
                    )
                ) ,
                'CompanyAddressesDeal',
                'Company' => array(
                    'fields' => array(
                        'Company.name',
                        'Company.slug',
                        'Company.id',
                        'Company.user_id',
                        'Company.url',
                        'Company.zip',
                        'Company.address1',
                        'Company.address2',
                        'Company.city_id',
                        'Company.latitude',
                        'Company.longitude',
                        'Company.is_company_profile_enabled',
                        'Company.is_online_account',
                        'Company.map_zoom_level'
                    ) ,
                    'CompanyAddress' => array(
                        'limit' => 5,
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
                    )
                ) ,
                'Attachment' => array(
                    'fields' => array(
                        'Attachment.id',
                        'Attachment.dir',
                        'Attachment.filename',
                        'Attachment.width',
                        'Attachment.height'
                    )
                ) ,
                'City' => array(
                    'fields' => array(
                        'City.id',
                        'City.name',
                        'City.slug',
                        'City.latitude',
                        'City.longitude',
                        'City.fb_access_token'
                    )
                ) ,
                'DealStatus' => array(
                    'fields' => array(
                        'DealStatus.name',
                    )
                ) ,
                'Topic' => array(
                    'TopicDiscussion' => array(
                        'limit' => 1,
                        'order' => array(
                            'TopicDiscussion.id' => 'desc'
                        )
                    ) ,
                    'LastRepliedUser' => array(
                        'fields' => array(
                            'LastRepliedUser.id',
                            'LastRepliedUser.user_type_id',
                            'LastRepliedUser.username',
                            'LastRepliedUser.id',
                        ) ,
                        'UserAvatar' => array(
                            'fields' => array(
                                'UserAvatar.id',
                                'UserAvatar.filename',
                                'UserAvatar.dir',
                                'UserAvatar.width',
                                'UserAvatar.height'
                            )
                        ) ,
                    ) ,
                    'conditions' => $city_conditions,
                    'limit' => 1,
                    'fields' => array(
                        'Topic.topic_discussion_count',
                        'Topic.id',
                        'Topic.deal_id'
                    )
                ) ,
            ) ,
            'order' => $order,
            'recursive' => 3,
            'limit' => $limit,
        );
        if (!empty($this->request->params['named']['q'])) {
            $this->paginate = array_merge($this->paginate, array(
                'search' => $this->request->params['named']['q']
            ));
        }
        $deals = $this->paginate();
        $this->set('deals', $deals);
		$this->set('sub_title', $sub_title);
        $slug = (!empty($deals[0]['Deal']['slug'])) ? $deals[0]['Deal']['slug'] : '';
        // for side bar main deal
        $conditions['Deal.deal_status_id'] = array(
            ConstDealStatus::Open,
            ConstDealStatus::Tipped,
        );
        $main_deals = $this->Deal->find('all', array(
            'conditions' => array_merge(array(
                'Deal.slug !=' => $slug,
                'Deal.is_side_deal' => 0
            ) , $conditions) ,
            'contain' => array(
                'SubDeal' => array(
                    'fields' => array(
                        'SubDeal.id',
                        'SubDeal.name',
                        'SubDeal.slug',
                        'SubDeal.original_price',
                        'SubDeal.discounted_price',
                    )
                ) ,
				'City',
                'Attachment' => array(
                    'fields' => array(
                        'Attachment.id',
                        'Attachment.dir',
                        'Attachment.filename',
                        'Attachment.width',
                        'Attachment.height'
                    )
                ) ,
            ) ,
            'recursive' => 1,
            'limit' => Configure::read('deal.main_deal_index_limit'),
        ));
        $this->set('main_deals', $main_deals);
        // Coding for API Call
        if (Configure::read('site.is_api_enabled') and !empty($this->request->query['api_key']) and !empty($this->request->query['api_token'])) {
            $response['status'] = (empty($deals)) ? 0 : 1001; // Not Deals found Eror Code
            $response['message'] = (empty($deals)) ? __l('No Deals available') : __l('Deals found');
            $response['deals'] = array();
            if (!empty($deals)) {
                foreach($deals as $deal) {
                    $image_options = array(
                        'dimension' => 'small_thumb',
                        'class' => '',
                        'alt' => $deal['Deal']['name'],
                        'title' => $deal['Deal']['name'],
                        'type' => 'jpg'
                    );
                    $small_image_url = $this->Deal->getImageUrl('Deal', $deal['Attachment'][0], $image_options);
                    $image_options = array(
                        'dimension' => 'normal_thumb',
                        'class' => '',
                        'alt' => $deal['Deal']['name'],
                        'title' => $deal['Deal']['name'],
                        'type' => 'jpg'
                    );
                    $medium_image_url = $this->Deal->getImageUrl('Deal', $deal['Attachment'][0], $image_options);
                    $image_options = array(
                        'dimension' => 'medium_big_thumb',
                        'class' => '',
                        'alt' => $deal['Deal']['name'],
                        'title' => $deal['Deal']['name'],
                        'type' => 'jpg'
                    );
                    $large_image_url = $this->Deal->getImageUrl('Deal', $deal['Attachment'][0], $image_options);
                    $deal_xml_content = array(
                        'id' => $deal['Deal']['id'],
                        'deal_url' => Router::url(array(
                            'controller' => 'deals',
                            'action' => 'view',
                            $deal['Deal']['slug']
                        ) , true) ,
                        'title' => $deal['Deal']['name'],
                        'small_image_url' => $small_image_url,
                        'medium_image_url' => $medium_image_url,
                        'large_image_url' => $large_image_url,
                        'division_id' => $deal['City']['id'],
                        'division_name' => $deal['City']['name'],
                        'division_lat' => $deal['City']['latitude'],
                        'division_lng' => $deal['City']['longitude'],
                        'vendor_id' => $deal['Company']['id'],
                        'vendor_name' => $deal['Company']['name'],
                        'vendor_website_url' => $deal['Company']['url'],
                        'status' => $deal['DealStatus']['name'],
                        'start_date' => strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['start_date'])))) ,
                        'end_date' => strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['end_date'])))) ,
                        'tipped' => ($deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped) ? __l('true') : __l('false') ,
                        'tipping_point' => $deal['Deal']['min_limit'],
                        'tipped_date' => ($deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped) ? strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['deal_tipped_time'])))) : __l('Not Yet Tipped') ,
                        'quantity_sold' => $deal['Deal']['deal_user_count'],
                        'price' => Configure::read('site.currency') . $deal['Deal']['discounted_price'],
                        'value' => Configure::read('site.currency') . $deal['Deal']['original_price'],
                        'discount_amount' => Configure::read('site.currency') . $deal['Deal']['savings'],
                        'discount_percent' => $deal['Deal']['discount_percentage'] . "%",
                        'conditions' => array(
                            'limited_quantity' => (!empty($deal['Deal']['max_limit'])) ? __l('true') : __l('false') ,
                            'initial_quantity' => $deal['Deal']['min_limit'],
                            'quantity_remaining' => (empty($deal['Deal']['max_limit'])) ? __l('No Limit') : ($deal['Deal']['max_limit'] - $deal['Deal']['deal_user_count']) ,
                            'minimum_purchase' => $deal['Deal']['buy_min_quantity_per_user'],
                            'maximum_purchase' => $deal['Deal']['buy_max_quantity_per_user'],
                            'expiration_date' => strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['coupon_expiry_date'])))) ,
                            'details' => array(
                                'detail' => $deal['Deal']['coupon_condition']
                            )
                        )
                    );
                    $response['deals'][] = $deal_xml_content;
                }
            }
            $this->set('api_response', $response);
        } else {
            //for side deal in index page
            if (Configure::read('deal.is_side_deal_enabled')) {
				$not_conditions['Not']['Deal.slug'] = $slug;
                $conditions['Deal.is_side_deal'] = 1;
                $side_deals = $this->Deal->find('all', array(
                    'conditions' => array(
                        $conditions,
                        $not_conditions,
                    ) ,
                    'contain' => array(
						'SubDeal' => array(
							'fields' => array(
								'SubDeal.id',
								'SubDeal.name',
								'SubDeal.slug',
								'SubDeal.original_price',
								'SubDeal.discounted_price',
							)
						) ,
                        'City',
                        'Attachment' => array(
                            'fields' => array(
                                'Attachment.id',
                                'Attachment.dir',
                                'Attachment.filename',
                                'Attachment.width',
                                'Attachment.height'
                            )
                        ) ,
                    ) ,
                    'recursive' => 1,
                	'limit' => Configure::read('deal.side_deal_index_limit'),
                ));
                if (!$deals and $side_deals) {
                    $this->paginate['conditions'] = array(
                        $conditions,
                        $not_conditions,
                    );
                    $side_deals = array();
                    $deals = $this->paginate();
                    $this->set('deals', $deals);
                }
                $this->set('side_deals', $side_deals);
            }
            //for company user
            if (empty($deals) && $this->Auth->user('user_type_id') == ConstUserTypes::Company && !$this->Deal->User->isAllowed($this->Auth->user('user_type_id')) && empty($this->request->params['named']['company'])) {
                $company = $this->Deal->Company->find('first', array(
                    'conditions' => array(
                        'Company.user_id = ' => $this->Auth->user('id')
                    ) ,
                    'fields' => array(
                        'Company.slug',
                    ) ,
                    'recursive' => - 1
                ));
                $this->Session->setFlash(__l('No deals available in this city.') , 'default', null, 'error');
                $this->redirect(array(
                    'controller' => 'deals',
                    'action' => 'index',
                    'company' => $company['Company']['slug'],
                    'admin' => false
                ));
            }
            if (!empty($this->request->params['named']['city'])) {
                $get_current_city = $this->request->params['named']['city'];
            } else {
                $get_current_city = Configure::read('site.city');
            }
            $this->set('get_current_city', $get_current_city);
            //render view file depends on the page
            if (!empty($this->request->params['named']['company'])) {
                $dealStatuses = $this->Deal->DealStatus->find('list');
                $dealStatusesCount = array();
                foreach($dealStatuses as $id => $dealStatus) {
                    $dealStatusesCount[$id] = $this->Deal->find('count', array(
                        'conditions' => array(
                            'Deal.deal_status_id' => $id,
                            'Deal.company_id' => $company['Company']['id'],
                        ) ,
                        'recursive' => - 1
                    ));
                }
                $this->set('dealStatusesCount', $dealStatusesCount);
                $this->set('dealStatuses', $dealStatuses);
                $this->render('index_company_deals');
            } else if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recent') {
                $this->render('index_recent_deals');
            } else if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'anytime') {
                $this->render('index_anytime_deals');
            }
        }
		if (!$this->RequestHandler->prefers('json')) {
			if ((empty($this->request->params['named']['type']) || ($this->request->params['named']['type'] != 'geocity' && $this->request->params['named']['type'] != 'recent' && empty($this->request->params['named']['view']))) && empty($this->request->params['named']['company']) && ((!empty($deals) && (!isset($_COOKIE['CakeCookie']['is_subscribed']) && !$this->Auth->user() && Configure::read('site.enable_three_step_subscription'))) || empty($deals))) {
				if ((!empty($_COOKIE['CakeCookie']['is_subscribed']) || $this->Auth->user('id')) && empty($deals)) { // Already Subscribed
					$this->Session->setFlash(__l('Its seems that the current select city does\'t have any open deals. Please select another city') , 'default', null, 'success');
					$this->redirect(array(
						'controller' => 'page',
						'action' => 'view',
						'how_it_works'
					));
				} else {
					$this->redirect(array(
						'controller' => 'subscriptions',
						'action' => 'add'
					));
				}
			}
		}
        // Subscrition page
        if (Configure::read('site.enable_three_step_subscription') && (empty($deals) || (!$this->Auth->user('user_type_id') && !isset($_COOKIE['CakeCookie']['is_subscribed']))) && ((empty($this->request->params['named']['type']) || ($this->request->params['named']['type'] != 'near' && empty($this->request->params['named']['view']))) && empty($this->request->params['named']['company'])) && empty($this->layoutPath)) {
            if (!empty($_COOKIE['CakeCookie']['is_subscribed']) || $this->Auth->user('id')) { // Already Subscribed
                if (isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recent') {
                    $this->Session->setFlash(__l('Its seems that the current select city does\'t have any recent deals. Please select another city') , 'default', null, 'success');
                } else {
                    $this->Session->setFlash(__l('Its seems that the current select city does\'t have any open deals. Please select another city') , 'default', null, 'success');
                }
                $this->redirect(array(
                    'controller' => 'page',
                    'action' => 'view',
                    'how_it_works'
                ));
            }
			if (isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'geocity') {
				$this->redirect(array(
					'controller' => 'subscriptions',
					'action' => 'add'
				));
			}
            $this->layout = 'subscriptions';
            $deal_categories = $this->DealCategory->find('list', array(
                'order' => array(
                    'DealCategory.name' => 'asc'
                )
            ));
            $selected = array_keys($deal_categories);
            $this->set('options', $deal_categories);
            $this->set('selected', $selected);
        }
        // <-- For iPhone App code
        if ($this->RequestHandler->prefers('json')) {
            $this->view = 'Json';
            $deals = $this->paginate();
            $total_deals = count($deals);
            for ($i = 0; $i < $total_deals; $i++) {
                $this->Deal->saveiPhoneAppThumb($deals[$i]['Attachment']);
                $image_options = array(
                    'dimension' => 'iphone_big_thumb',
                    'class' => '',
                    'alt' => $deals[$i]['Deal']['name'],
                    'title' => $deals[$i]['Deal']['name'],
                    'type' => 'jpg'
                );
                $iphone_big_thumb = $this->Deal->getImageUrl('Deal', $deals[$i]['Attachment'][0], $image_options);
                $deals[$i]['Deal']['iphone_big_thumb'] = $iphone_big_thumb;
                $image_options = array(
                    'dimension' => 'iphone_small_thumb',
                    'class' => '',
                    'alt' => $deals[$i]['Deal']['name'],
                    'title' => $deals[$i]['Deal']['name'],
                    'type' => 'jpg'
                );
                $iphone_small_thumb = $this->Deal->getImageUrl('Deal', $deals[$i]['Attachment'][0], $image_options);
                $deals[$i]['Deal']['iphone_small_thumb'] = $iphone_small_thumb;
                $deals[$i]['Deal']['end_time'] = intval(strtotime($deals[$i]['Deal']['end_date'] . ' GMT') - time());
                $deals[$i]['Deal']['end_date'] = date('m/d/Y', strtotime($deals[$i]['Deal']['end_date']));
                $deals[$i]['Deal']['start_date'] = date('m/d/Y', strtotime($deals[$i]['Deal']['start_date']));
                $deals[$i]['Deal']['coupon_expiry_date'] = ($deals[$i]['Deal']['coupon_expiry_date']) ? date('m/d/Y', strtotime($deals[$i]['Deal']['coupon_expiry_date'])) : $deals[$i]['Deal']['coupon_expiry_date'];
                $deals[$i]['Deal']['coupon_start_date'] = date('m/d/Y', strtotime($deals[$i]['Deal']['coupon_start_date']));
            }
            $this->view = 'Json';
            $this->set('json', (empty($this->viewVars['iphone_response'])) ? $deals : $this->viewVars['iphone_response']);
        }
        if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'near' || $this->request->params['named']['type'] == 'main' || $this->request->params['named']['type'] == 'side')) {
            $this->set('has_near_by_deal', $has_near_by_deal);
            if (!empty($this->request->params['named']['view']) && $this->request->params['named']['view'] == 'simple') {
                $this->render('index_simple_near');
            } else {
                $this->render('index_near');
            }
        }
        // For iPhone App code -->
        
    }
    //comapny deals listing
    public function company_deals()
    {
        $conditions = array();
        if (!empty($this->request->params['named']['company_id'])) {
            $statusList = array(
                ConstDealStatus::Open,
                ConstDealStatus::Expired,
                ConstDealStatus::Tipped,
                ConstDealStatus::Closed,
                ConstDealStatus::PaidToCompany
            );
            $companyUser = $this->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.id' => $this->request->params['named']['company_id'],
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'fields' => array(
                    'Company.user_id'
                ) ,
                'recursive' => - 1
            ));
            if (!empty($companyUser) || $this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                $statusList[] = ConstDealStatus::Draft;
                $statusList[] = ConstDealStatus::PendingApproval;
                $statusList[] = ConstDealStatus::Upcoming;
                $statusList[] = ConstDealStatus::Refunded;
                $statusList[] = ConstDealStatus::Canceled;
            }
            $conditions = array(
                'Deal.company_id' => $this->request->params['named']['company_id'],
                'Deal.deal_status_id' => $statusList
            );
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'City' => array(
                    'fields' => array(
                        'City.id'
                    )
                ) ,
                'DealUser' => array(
                    'fields' => array(
                        'DealUser.discount_amount'
                    )
                ) ,
                'Attachment' => array(
                    'fields' => array(
                        'Attachment.id',
                        'Attachment.dir',
                        'Attachment.filename',
                        'Attachment.width',
                        'Attachment.height'
                    )
                ) ,
                'City',
                'DealStatus' => array(
                    'fields' => array(
                        'DealStatus.name',
                    )
                )
            ) ,
            'recursive' => 1,
        );
        $this->set('company_deals', $this->paginate());
    }
    //export deal listing in csv file
    public function coupons_export()
    {
        if (empty($this->request->params['named']['deal_id'])) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $dealusers = $this->Deal->DealUser->find('all', array(
            'conditions' => array(
                'DealUser.deal_id' => $this->request->params['named']['deal_id'],
                'DealUser.is_canceled' => 0
            ) ,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                    )
                ) ,
                'Deal' => array(
                    'fields' => array(
                        'Deal.id',
                        'Deal.name',
                        'Deal.coupon_start_date',
                        'Deal.coupon_expiry_date'
                    )
                ) ,
                'DealUserCoupon'
            ) ,
            'fields' => array(
                'DealUser.id',
                'DealUser.discount_amount',
                'DealUser.quantity'
            ) ,
            'recursive' => 1
        ));
        Configure::write('debug', 0);
        if (!empty($dealusers)) {
            foreach($dealusers as $dealuser) {
                $coupon_array = array();
                $unique_coupon_array = array();
                foreach($dealuser['DealUserCoupon'] as $deal_user_coupon) {
                    $coupon_array[] = $deal_user_coupon['coupon_code'];
                    $unique_coupon_array[] = $deal_user_coupon['unique_coupon_code'];
                }
                $data[]['Deal'] = array(
                    __l('User name') => $dealuser['User']['username'],
                    __l('Quantity') => $dealuser['DealUser']['quantity'],
                    __l('Amount') => Configure::read('site.currency') . $dealuser['DealUser']['discount_amount'],
                    __l('Top Code') => !empty($coupon_array) ? implode(',', $coupon_array) : '',
                    __l('Bottom Code') => !empty($unique_coupon_array) ? implode(',', $unique_coupon_array) : '',
                    __l('Valid From') => strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($dealuser['Deal']['coupon_start_date'])))) ,
                    __l('Expires On') => !empty($dealuser['Deal']['coupon_expiry_date']) ? strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($dealuser['Deal']['coupon_expiry_date'])))) : '-',
                );
                $deal_name = $dealuser['Deal']['name'];
            }
        }
        $this->set('data', $data);
        $this->set('deal_name', $deal_name);
    }
    function stats()
    {
        if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
            $conditions = array(
                'Deal.id' => $this->request->params['named']['deal_id']
            );
        } else {
            $company = $this->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'recursive' => - 1
            ));
            if (empty($company)) {
                throw new NotFoundException(__l('Invalid request'));
            }
            $conditions = array(
                'Deal.id' => $this->request->params['named']['deal_id'],
                'Deal.company_id' => $company['Company']['id']
            );
        }
        $deal = $this->Deal->find('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.id',
                        'User.username',
                    )
                ) ,
                'Company' => array(
                    'fields' => array(
                        'Company.id',
                        'Company.name',
                        'Company.slug',
                    )
                ) ,
                'DealStatus' => array(
                    'fields' => array(
                        'DealStatus.id',
                        'DealStatus.name',
                    )
                ) ,
                'CitiesDeal',
                'City' => array(
                    'fields' => array(
                        'City.id',
                        'City.name',
                        'City.slug',
                    )
                )
            )
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->set('deal', $deal);
    }
    //admin export deal listing in csv file
    public function admin_export()
    {
        $this->setAction('coupons_export');
    }
    function view($slug = null, $count = null)
    {
        $this->pageTitle = __l('Deal');
        if (is_null($slug)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        // for side bar deal
        $city_slug = $this->request->params['named']['city'];
        $city = $this->Deal->City->find('first', array(
            'conditions' => array(
                'City.slug' => $city_slug
            ) ,
            'fields' => array(
                'City.name',
                'City.id'
            ) ,
            'recursive' => 1
        ));
        if (empty($city)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $city_deal_ids = array();
        foreach($city['Deal'] as $deal) {
            $city_deal_ids[] = $deal['id'];
        }
        $conditions['Deal.id'] = $city_deal_ids;
        $deal = $this->Deal->find('first', array(
            'conditions' => array(
                'Deal.slug = ' => $slug
            ) ,
            'contain' => array(
                'SubDeal',
                'Charity' => array(
                    'fields' => array(
                        'Charity.name',
                        'Charity.url',
                        'Charity.id',
                    )
                ) ,
                'User' => array(
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                        'User.email',
                        'User.password',
                    )
                ) ,
                'CompanyAddressesDeal',
                'Company' => array(
                    'fields' => array(
                        'Company.name',
                        'Company.slug',
                        'Company.id',
                        'Company.user_id',
                        'Company.url',
                        'Company.zip',
                        'Company.address1',
                        'Company.address2',
                        'Company.city_id',
                        'Company.latitude',
                        'Company.longitude',
                        'Company.is_company_profile_enabled',
                        'Company.is_online_account',
                        'Company.map_zoom_level',
                    ) ,
                    'CompanyAddress' => array(
                        'limit' => 5,
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
                    )
                ) ,
                'Attachment' => array(
                    'fields' => array(
                        'Attachment.id',
                        'Attachment.dir',
                        'Attachment.filename',
                        'Attachment.width',
                        'Attachment.height'
                    )
                ) ,
                'City' => array(
                    'fields' => array(
                        'City.id',
                        'City.name',
                        'City.slug',
                    )
                ) ,
                'Topic' => array(
                    'TopicDiscussion' => array(
                        'limit' => 1,
                        'order' => array(
                            'TopicDiscussion.id' => 'desc'
                        )
                    ) ,
                    'LastRepliedUser' => array(
                        'fields' => array(
                            'LastRepliedUser.id',
                            'LastRepliedUser.username',
                            'LastRepliedUser.user_type_id',
                            'LastRepliedUser.fb_user_id',
                        ) ,
                        'UserAvatar' => array(
                            'fields' => array(
                                'UserAvatar.id',
                                'UserAvatar.filename',
                                'UserAvatar.dir',
                                'UserAvatar.width',
                                'UserAvatar.height'
                            )
                        ) ,
                    ) ,
                    'limit' => 1,
                    'fields' => array(
                        'Topic.topic_discussion_count',
                        'Topic.id',
                        'Topic.deal_id'
                    )
                ) ,
            ) ,
            'recursive' => 3,
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (empty($deal['Deal']['is_redeem_in_main_address'])) {
            unset($deal['Company']['latitude']);
            unset($deal['Company']['longitude']);
        }
        //  Inserting record into company array, so to get in app_helper map "formGooglemap" //
        $deal['Company']['is_redeem_at_all_branch_address'] = $deal['Deal']['is_redeem_at_all_branch_address'];
        $deal['Company']['is_redeem_in_main_address'] = $deal['Deal']['is_redeem_in_main_address'];
        if (!empty($deal['Deal']['meta_keywords'])) {
            Configure::write('meta.keywords', $deal['Deal']['meta_keywords']);
        }
        if (!empty($deal['Deal']['meta_description'])) {
            Configure::write('meta.description', $deal['Deal']['meta_description']);
        }
        if (!empty($deal['Deal']['name'])) {
            Configure::write('meta.deal_name', $deal['Deal']['name']);
        }
        if (!empty($deal['Attachment'])) {
            $image_options = array(
                'dimension' => 'medium_thumb',
                'class' => '',
                'alt' => $deal['Deal']['name'],
                'title' => $deal['Deal']['name'],
                'type' => 'png'
            );
            $deal_image = $this->Deal->getImageUrl('Deal', $deal['Attachment'][0], $image_options);
            Configure::write('meta.deal_image', $deal_image);
        }
        // Check For Normal User
        if (($this->Auth->user('user_type_id') == ConstUserTypes::User or !$this->Auth->user('user_type_id')) && ($deal['Deal']['deal_status_id'] == ConstDealStatus::PendingApproval || $deal['Deal']['deal_status_id'] == ConstDealStatus::Upcoming || $deal['Deal']['deal_status_id'] == ConstDealStatus::Draft)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        // Check for Company User
        if ($this->Auth->user('user_type_id') == ConstUserTypes::Company && ($deal['Deal']['deal_status_id'] == ConstDealStatus::PendingApproval || $deal['Deal']['deal_status_id'] == ConstDealStatus::Upcoming || $deal['Deal']['deal_status_id'] == ConstDealStatus::Draft)) {
            $companyUser = $this->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'fields' => array(
                    'Company.id'
                ) ,
                'recursive' => - 1
            ));
            if ($deal['Deal']['company_id'] != $companyUser['Company']['id']) throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle.= ' - ' . $deal['Deal']['name'];
        if (!empty($this->request->params['named']['city'])) {
            $get_current_city = $this->request->params['named']['city'];
        } else {
            $get_current_city = Configure::read('site.city');
        }
        // for side bar main deal
        $conditions['Deal.deal_status_id'] = array(
            ConstDealStatus::Open,
            ConstDealStatus::Tipped,
        );
        $main_deals = $this->Deal->find('all', array(
            'conditions' => array_merge(array(
                'Deal.slug !=' => $slug,
                'Deal.is_side_deal' => 0
            ) , $conditions) ,
            'contain' => array(
                'SubDeal' => array(
                    'fields' => array(
                        'SubDeal.id',
                        'SubDeal.name',
                        'SubDeal.slug',
                        'SubDeal.original_price',
                        'SubDeal.discounted_price',
                    )
                ) ,
                'City',
                'Attachment' => array(
                    'fields' => array(
                        'Attachment.id',
                        'Attachment.dir',
                        'Attachment.filename',
                        'Attachment.width',
                        'Attachment.height'
                    )
                ) ,
            ) ,
            'recursive' => 1,
            'limit' => Configure::read('deal.main_deal_index_limit'),
        ));
        $this->set('main_deals', $main_deals);
        // for side bar side deal
        if (Configure::read('deal.is_side_deal_enabled')) {
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Open,
                ConstDealStatus::Tipped,
            );
            $side_deals = $this->Deal->find('all', array(
                'conditions' => array_merge(array(
                    'Deal.slug !=' => $slug,
                    'Deal.is_side_deal' => 1
                ) , $conditions) ,
                'contain' => array(
					'SubDeal' => array(
						'fields' => array(
							'SubDeal.id',
							'SubDeal.name',
							'SubDeal.slug',
							'SubDeal.original_price',
							'SubDeal.discounted_price',
						)
					) ,
                    'City',
                    'Attachment' => array(
                        'fields' => array(
                            'Attachment.id',
                            'Attachment.dir',
                            'Attachment.filename',
                            'Attachment.width',
                            'Attachment.height'
                        )
                    ) ,
                ) ,
                'recursive' => 1,
                'limit' => Configure::read('deal.side_deal_index_limit'),
            ));
            $this->set('side_deals', $side_deals);
        }
        $this->set('get_current_city', $get_current_city);
        $this->set('count', $count);
        $this->set('deal', $deal);
        $this->set('from_page', 'deal_view');
    }
    public function edit($id = null)
    {
        $this->pageTitle = __l('Edit Deal');
        $this->loadModel('Attachment');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['OldAttachment'])) {
                $attachmentIds = array();
                foreach($this->request->data['OldAttachment'] as $attachment_id => $is_checked) {
                    if (isset($is_checked['id']) && ($is_checked['id'] == 1)) {
                        $attachmentIds[] = $attachment_id;
                    }
                }
                $attachmentIds = array(
                    'Attachment' => $attachmentIds
                );
                if (!empty($attachmentIds)) {
                    $this->Deal->Attachment->delete($attachmentIds);
                }
            }
            unset($this->request->data['OldAttachment']);
            unset($this->Deal->validate['start_date']['rule2']);
            //update button
            if (!empty($this->request->data['Deal']['send_to_admin'])) {
                $this->request->data['Deal']['deal_status_id'] = ConstDealStatus::PendingApproval;
            }
            //payment calculation
            $this->request->data['Deal']['savings'] = (!empty($this->request->data['Deal']['discount_percentage'])) ? ($this->request->data['Deal']['original_price'] * ($this->request->data['Deal']['discount_percentage'] / 100)) : $this->request->data['Deal']['discount_amount'];
            $this->request->data['Deal']['discounted_price'] = $this->request->data['Deal']['original_price'] - $this->request->data['Deal']['savings'];
            // If advance amount given, calculating that amount //
            if ((Configure::read('deal.is_enable_payment_advance') == 1) && !empty($this->request->data['Deal']['is_enable_payment_advance'])) {
                $remaining_amount = $this->request->data['Deal']['discounted_price'] - $this->request->data['Deal']['pay_in_advance'];
                if (!empty($remaining_amount) && $remaining_amount > 0) {
                    $this->request->data['Deal']['discounted_price'] = $this->request->data['Deal']['pay_in_advance'];
                }
            }
            // Free deal validation unset process
            if ($this->request->data['Deal']['discounted_price'] == 0) {
                unset($this->Deal->validate['discounted_price']['rule2']);
                unset($this->Deal->validate['commission_percentage']['rule2']);
                unset($this->Deal->validate['commission_percentage']['rule4']);
            } else {
                unset($this->Deal->validate['discounted_price']['rule3']);
                unset($this->Deal->validate['commission_percentage']['rule3']);
                unset($this->Deal->validate['bonus_amount']['rule2']);
            }
            // multiple deal validation unset process
            if ($this->request->data['Deal']['is_subdeal_available']) {
                unset($this->Deal->validate['bonus_amount']);
                unset($this->Deal->validate['commission_percentage']);
                unset($this->Deal->validate['discounted_price']);
                unset($this->Deal->validate['original_price']);
                unset($this->Deal->validate['discount_amount']);
            }
            // An time deal validation unset process
            if ($this->request->data['Deal']['is_anytime_deal']) {
                unset($this->Deal->validate['end_date']);
                unset($this->Deal->validate['coupon_expiry_date']);
                unset($this->Deal->validate['coupon_start_date']['rule2']);
                unset($this->request->data['Deal']['coupon_expiry_date']);
                unset($this->request->data['Deal']['end_date']);
            }
            if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                unset($this->Deal->validate['commission_percentage']['rule4']);
            }
            $this->Deal->set($this->request->data);
            $this->Deal->City->set($this->request->data);
            if (empty($this->request->data['Deal']['is_redeem_at_all_branch_address']) && empty($this->request->data['Deal']['is_redeem_in_main_address']) && empty($this->request->data['CompanyAddressesDeal']['company_address_id'])) {
                $this->request->data['Deal']['is_redeem_in_main_address'] = 1;
            }
            if ($this->Deal->validates() & $this->Deal->City->validates()) {
                if (empty($this->request->data['Deal']['is_subdeal_available'])) {
                    $this->request->data['Deal']['sub_deal_count'] = 0;
                }
                if ($this->Deal->save($this->request->data)) {
                    // normal deal that delete subdeals
                    if (empty($this->request->data['Deal']['is_subdeal_available'])) {
                        $this->Deal->deleteAll(array(
                            'Deal.parent_id' => $this->request->data['Deal']['id']
                        ));
                    }
                    // Saving listing locations //
                    if (empty($this->request->data['CompanyAddressesDeal']['company_address_id']) && empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                        $this->Deal->CompanyAddressesDeal->deleteAll(array(
                            'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
                        ));
                    }
                    if (!empty($this->request->data['CompanyAddressesDeal']['company_address_id']) && empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                        // Deleting previous inserted records //
                        $this->Deal->CompanyAddressesDeal->deleteAll(array(
                            'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
                        ));
                        // Inserting new records //
                        $company_addresses_deal = array();
                        foreach($this->request->data['CompanyAddressesDeal']['company_address_id'] as $key => $value) {
                            $this->Deal->CompanyAddressesDeal->create();
                            $company_addresses_deal['CompanyAddressesDeal']['deal_id'] = $this->request->data['Deal']['id'];
                            $company_addresses_deal['CompanyAddressesDeal']['company_address_id'] = $value;
                            $this->Deal->CompanyAddressesDeal->save($company_addresses_deal);
                        }
                    }
                    if (!empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                        // Deleting previous inserted records //
                        $this->Deal->CompanyAddressesDeal->deleteAll(array(
                            'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
                        ));
                    }
                    // Inserting coupon, if given //
                    if (!empty($this->request->data['Deal']['coupon_code'])) {
                        $split_codes = explode(',', $this->request->data['Deal']['coupon_code']);
                        $deal_coupons = array();
                        foreach($split_codes as $key => $value) {
                            $coupon_value = trim($value);
                            if (!empty($coupon_value)) {
                                $this->Deal->DealCoupon->create();
                                $deal_coupons['deal_id'] = $this->request->data['Deal']['id'];
                                $deal_coupons['coupon_code'] = $coupon_value;
                                $this->Deal->DealCoupon->save($deal_coupons);
                            }
                        }
                    }
                    $deals = $this->Deal->find('first', array(
                        'conditions' => array(
                            'Deal.id' => $id
                        ) ,
                        'contain' => array(
                            'CitiesDeal',
                            'City' => array(
                                'fields' => array(
                                    'City.id',
                                    'City.name',
                                    'City.slug',
                                )
                            ) ,
                            'Attachment',
                            'Company',
                        ) ,
                        'recursive' => 2
                    ));
                    $slug = $deals['Deal']['slug'];
                    $deal_id = $deals['Deal']['id'];
                    foreach($deals['City'] as $k => $city) {
                        $city_slug = $city['slug'];
                        $city_id = $city['id'];
                        $this->Deal->_updateDealBitlyURL($slug, $city_slug, $city_id, $deal_id);
                    }
                    $this->Deal->_updateCityDealCount();
                    $foreign_id = $this->request->data['Deal']['id'];
                    $this->Deal->Attachment->create();
                    if (!isset($this->request->data['Attachment']) && $this->RequestHandler->isAjax()) { // Flash Upload
                        $this->request->data['Attachment']['foreign_id'] = $foreign_id;
                        $this->request->data['Attachment']['description'] = 'Deal';
                        $this->XAjax->flashuploadset($this->request->data);
                    } else { // Normal Upload
                        $is_form_valid = true;
                        $upload_photo_count = 0;
                        for ($i = 0; $i < count($this->request->data['Attachment']); $i++) {
                            if (!empty($this->request->data['Attachment'][$i]['filename']['tmp_name'])) {
                                $upload_photo_count++;
                                $image_info = getimagesize($this->request->data['Attachment'][$i]['filename']['tmp_name']);
                                $this->request->data['Attachment']['filename'] = $this->request->data['Attachment'][$i]['filename'];
                                $this->request->data['Attachment']['filename']['type'] = $image_info['mime'];
                                $this->request->data['Attachment'][$i]['filename']['type'] = $image_info['mime'];
                                $this->Deal->Attachment->Behaviors->attach('ImageUpload', Configure::read('photo.file'));
                                $this->Deal->Attachment->set($this->request->data);
                                if (!$this->Deal->validates() | !$this->Deal->Attachment->validates()) {
                                    $attachmentValidationError[$i] = $this->Deal->Attachment->validationErrors;
                                    $is_form_valid = false;
                                    $this->Session->setFlash(__l('Deal could not be added. Please, try again.') , 'default', null, 'error');
                                }
                            }
                        }
                        if (!$upload_photo_count) {
                            $this->Deal->validates();
                            $this->Deal->Attachment->validationErrors[0]['filename'] = __l('Required');
                            $is_form_valid = false;
                        }
                        if (!empty($attachmentValidationError)) {
                            foreach($attachmentValidationError as $key => $error) {
                                $this->Deal->Attachment->validationErrors[$key]['filename'] = $error;
                            }
                        }
                        if ($is_form_valid) {
                            $this->request->data['foreign_id'] = $foreign_id;
                            $this->request->data['Attachment']['description'] = 'Deal';
                            $this->XAjax->normalupload($this->request->data, false);
                            $this->Session->setFlash(__l('Deal has been added.') , 'default', null, 'success');
                        }
                    }
                    $this->Session->setFlash(__l('Deal has been updated') , 'default', null, 'success');
                    $deal = $this->Deal->find('first', array(
                        'conditions' => array(
                            'Deal.id' => $this->request->data['Deal']['id']
                        ) ,
                        'contain' => array(
                            'City' => array(
                                'fields' => array(
                                    'City.id',
                                    'City.name',
                                    'City.slug',
                                )
                            ) ,
                            'Attachment',
                            'Company',
                        ) ,
                        'recursive' => 2
                    ));
                    $slug = $deal['Deal']['slug'];
                    $city_name = $deal['City']['name'];
                    $this->Session->setFlash(__l('Deal has been updated') , 'default', null, 'success');
                    if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                        $this->redirect(array(
                            'controller' => 'deals',
                            'action' => 'index',
                        ));
                    } else {
                        $this->redirect(array(
                            'controller' => 'deals',
                            'action' => 'company',
                            $deal['Company']['slug']
                        ));
                    }
                }
            } else {
                $this->Session->setFlash(__l('Deal could not be updated. Please, try again.') , 'default', null, 'error');
            }
            $attachment = $this->Attachment->find('all', array(
                'conditions' => array(
                    'Attachment.foreign_id' => $this->request->data['Deal']['id'],
                    'Attachment.class' => 'Deal'
                ) ,
                'recursive' => - 1,
            ));
            if (!empty($attachments)) {
                foreach($attachments as $attachment) {
                    $this->request->data['Attachment'][] = $attachment['Attachment'];
                }
            }
        } else {
            $conditions = array();
            if ($this->Auth->user('id') != ConstUserTypes::Admin) {
                $companyUser = $this->Deal->Company->find('first', array(
                    'conditions' => array(
                        'Company.user_id' => $this->Auth->user('id')
                    ) ,
                    'fields' => array(
                        'Company.id',
                        'Company.slug'
                    ) ,
                    'recursive' => - 1
                ));
                $conditions['Deal.company_id'] = $companyUser['Company']['id'];
                $this->set('company_slug', $companyUser['Company']['slug']);
            }
            $conditions['Deal.id'] = $id;
            $this->request->data = $this->Deal->find('first', array(
                'conditions' => array(
                    $conditions,
                ) ,
                /*'contain' => array(
                'DealCoupon'
                ),*/
                'recursive' => 1,
            ));
            if (!empty($this->request->data['Deal']['start_date'])) {
                $this->request->data['Deal']['start_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['start_date']));
            }
            if (!empty($this->request->data['Deal']['end_date'])) {
                $this->request->data['Deal']['end_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['end_date']));
            }
            if (!empty($this->request->data['Deal']['coupon_start_date'])) {
                $this->request->data['Deal']['coupon_start_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['coupon_start_date']));
            }
            if (!empty($this->request->data['Deal']['coupon_expiry_date'])) {
                $this->request->data['Deal']['coupon_expiry_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['coupon_expiry_date']));
            }
            if (!empty($this->request->data['DealCoupon'])) {
                foreach($this->request->data['DealCoupon'] as $coupon_codes) {
                    $coupon_code[] = $coupon_codes['coupon_code'];
                }
                $coupon_code = implode(',', $coupon_code);
                $this->set('manual_coupon_codes', $coupon_code);
            }
            if (empty($this->request->data) || ($this->request->data['Deal']['deal_status_id'] != ConstDealStatus::Draft)) {
                throw new NotFoundException(__l('Invalid request'));
            }
            //set values for deal amount calculator
            $this->request->data['Deal']['original_amt'] = $this->request->data['Deal']['original_price'];
            $this->request->data['Deal']['discount_amt'] = $this->request->data['Deal']['discounted_price'];
            $this->request->data['Deal']['calculator_discounted_price'] = $this->request->data['Deal']['discounted_price'];
            $this->request->data['Deal']['calculator_min_limit'] = $this->request->data['Deal']['min_limit'];
            $this->request->data['Deal']['calculator_commission_percentage'] = $this->request->data['Deal']['commission_percentage'];
            $this->request->data['Deal']['calculator_bonus_amount'] = $this->request->data['Deal']['bonus_amount'];
        }
        if (!empty($this->request->data['Deal']['original_price']) && !empty($this->request->data['Deal']['discounted_price']) && !empty($this->request->data['Deal']['budget_amt'])) {
            $diff = $this->request->data['Deal']['original_price'] - $this->request->data['Deal']['discounted_price'];
            if ($diff > 0) {
                $this->request->data['Deal']['calculator_qty'] = $this->request->data['Deal']['budget_amt'] / $diff;
            } else $this->request->data['Deal']['calculator_qty'] = 0;
        } else {
            $this->request->data['Deal']['calculator_qty'] = 0;
        }
        //set values for deal amount calculator
        if (!empty($this->request->data['Deal']['calculator_discounted_price']) && !empty($this->request->data['Deal']['calculator_min_limit']) && !empty($this->request->data['Deal']['calculator_commission_percentage']) && !empty($this->request->data['Deal']['calculator_bonus_amount'])) {
            $this->request->data['Deal']['calculator_total_purchased_amount'] = $this->request->data['Deal']['calculator_discounted_price'] * $this->request->data['Deal']['calculator_min_limit'];
            $this->request->data['Deal']['calculator_total_commission_amount'] = ($this->request->data['Deal']['calculator_total_purchased_amount'] * ($this->request->data['Deal']['calculator_commission_percentage'] / 100)) + $this->request->data['Deal']['calculator_bonus_amount'];
            $this->request->data['Deal']['calculator_net_profit'] = $this->request->data['Deal']['calculator_total_commission_amount'];
        }
        $this->pageTitle.= ' - ' . $this->request->data['Deal']['name'];
        $discounts = array();
        for ($i = 1; $i <= 100; $i++) {
            $discounts[$i] = $i;
        }
        $cities = $this->Deal->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        $companies = $this->Deal->Company->find('list');
        $dealStatuses = $this->Deal->DealStatus->find('list');
        $this->set(compact('cities', 'dealStatuses', 'companies', 'discounts'));
        if (Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::CompanyUser) {
            $charities = $this->Deal->Charity->find('list', array(
                'conditions' => array(
                    'Charity.is_active =' => 1
                ) ,
                'order' => array(
                    'Charity.name' => 'asc'
                )
            ));
            $this->set(compact('charities'));
        }
        $subdeal = $this->Deal->find('all', array(
            'conditions' => array(
                'Deal.parent_id' => $id
            ) ,
            'recursive' => - 1
        ));
        $this->set('subdeal', $subdeal);
        // Getting branch address for listing information //
        $company_id = (!empty($this->request->data['Deal']['company_id']) ? $this->request->data['Deal']['company_id'] : $company['Company']['id']);
        $branch_addresses = $this->Deal->getBranchAddresses($company_id);
        $this->set('branch_addresses', $branch_addresses);
    }
    public function add()
    {
        $this->pageTitle = __l('Add Deal');
        $this->loadModel('Attachment');
        $this->Deal->Behaviors->attach('ImageUpload', Configure::read('image.file'));
        if (!empty($this->request->data)) {
            $this->request->data['Deal']['bonus_amount'] = (!empty($this->request->data['Deal']['bonus_amount'])) ? $this->request->data['Deal']['bonus_amount'] : 0;
            $this->request->data['Deal']['commission_percentage'] = (!empty($this->request->data['Deal']['commission_percentage'])) ? $this->request->data['Deal']['commission_percentage'] : 0;
            //pricing calculation
            $this->request->data['Deal']['savings'] = (!empty($this->request->data['Deal']['discount_percentage'])) ? ($this->request->data['Deal']['original_price'] * ($this->request->data['Deal']['discount_percentage'] / 100)) : $this->request->data['Deal']['discount_amount'];
            $this->request->data['Deal']['discounted_price'] = $this->request->data['Deal']['original_price'] - $this->request->data['Deal']['savings'];
            // If advance amount given, calculating that amount //
            if ((Configure::read('deal.is_enable_payment_advance') == 1) && !empty($this->request->data['Deal']['is_enable_payment_advance'])) {
                $remaining_amount = $this->request->data['Deal']['discounted_price'] - $this->request->data['Deal']['pay_in_advance'];
                if (!empty($remaining_amount) && $remaining_amount > 0) {
                    $this->request->data['Deal']['discounted_price'] = $this->request->data['Deal']['pay_in_advance'];
                }
            }
            if (!empty($this->request->data['OldAttachment'])) {
                $attachmentIds = array();
                foreach($this->request->data['OldAttachment'] as $attachment_id => $is_checked) {
                    if (isset($is_checked['id']) && ($is_checked['id'] == 1)) {
                        $attachmentIds[] = $attachment_id;
                    }
                }
                $attachmentIds = array(
                    'Attachment' => $attachmentIds
                );
                if (!empty($attachmentIds) && empty($this->request->data['Deal']['clone_deal_id'])) {
                    $this->Deal->Attachment->delete($attachmentIds);
                }
            }
            $oldAttachmentArray = $this->request->data['OldAttachment'];
            unset($this->request->data['OldAttachment']);
            $ini_clone_attachment = 0;
            if (!empty($this->request->data['CloneAttachment'])) {
                $ini_clone_attachment = 1;
            }
            // Free deal validation unset process
            if ($this->request->data['Deal']['discounted_price'] == 0) {
                unset($this->Deal->validate['discounted_price']['rule2']);
                unset($this->Deal->validate['commission_percentage']['rule2']);
                unset($this->Deal->validate['commission_percentage']['rule4']);
            } else {
                unset($this->Deal->validate['discounted_price']['rule3']);
                unset($this->Deal->validate['commission_percentage']['rule3']);
                unset($this->Deal->validate['bonus_amount']['rule2']);
            }
            // multiple deal validation unset process
            if ($this->request->data['Deal']['is_subdeal_available']) {
                unset($this->Deal->validate['bonus_amount']);
                unset($this->Deal->validate['commission_percentage']);
                unset($this->Deal->validate['discounted_price']);
                unset($this->Deal->validate['original_price']);
                unset($this->Deal->validate['discount_amount']);
            }
            // An time deal validation unset process
            if ($this->request->data['Deal']['is_anytime_deal']) {
                unset($this->Deal->validate['end_date']);
                unset($this->Deal->validate['coupon_expiry_date']);
                unset($this->Deal->validate['coupon_start_date']['rule2']);
                unset($this->request->data['Deal']['coupon_expiry_date']);
                unset($this->request->data['Deal']['end_date']);
            }
            if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                unset($this->Deal->validate['commission_percentage']['rule4']);
            }
            $this->Deal->set($this->request->data);
            $this->Deal->City->set($this->request->data);
            if (empty($this->request->data['Deal']['is_redeem_at_all_branch_address']) && empty($this->request->data['Deal']['is_redeem_in_main_address']) && empty($this->request->data['CompanyAddressesDeal']['company_address_id'])) {
                $this->request->data['Deal']['is_redeem_in_main_address'] = 1;
            }
            if ($this->Deal->validates() & $this->Deal->City->validates()) {
                $this->Deal->create();
                if (!empty($this->request->data['Deal']['save_as_draft']) || !empty($this->request->data['Deal']['is_save_draft'])) {
                    $this->request->data['Deal']['deal_status_id'] = ConstDealStatus::Draft;
                } elseif (!empty($this->request->data['Deal']['is_subdeal_available'])) { // For subdeal, untill subdeal gets added, the status will be  in draft status //
                    $this->request->data['Deal']['deal_status_id'] = ConstDealStatus::Draft;
                } elseif ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                    $this->request->data['Deal']['deal_status_id'] = ConstDealStatus::Upcoming;
                } else {
                    $this->request->data['Deal']['deal_status_id'] = ConstDealStatus::PendingApproval;
                }
				if(empty($this->request->data['Attachment']) && !$this->RequestHandler->isAjax()){
			        $this->Deal->Behaviors->detach('ImageUpload');
				}
                $this->Deal->save($this->request->data);
                $deal_id = $this->Deal->getLastInsertId();
                // Saving listing locations //
                if (!empty($this->request->data['CompanyAddressesDeal']['company_address_id']) && empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                    $company_addresses_deal = array();
                    foreach($this->request->data['CompanyAddressesDeal']['company_address_id'] as $key => $value) {
                        $this->Deal->CompanyAddressesDeal->create();
                        $company_addresses_deal['CompanyAddressesDeal']['deal_id'] = $deal_id;
                        $company_addresses_deal['CompanyAddressesDeal']['company_address_id'] = $value;
                        $this->Deal->CompanyAddressesDeal->save($company_addresses_deal);
                    }
                }
                // Inserting coupon, if given //
                if (!empty($this->request->data['Deal']['coupon_code'])) {
                    $split_codes = explode(',', $this->request->data['Deal']['coupon_code']);
                    $deal_coupons = array();
                    foreach($split_codes as $key => $value) {
                        if (!empty($value)) {
							$this->Deal->DealCoupon->create();
                            $deal_coupons['deal_id'] = $deal_id;
                            $deal_coupons['coupon_code'] = trim($value);
                            $this->Deal->DealCoupon->save($deal_coupons);
                        }
                    }
                }
                $this->Deal->Attachment->create();
                if (!empty($ini_clone_attachment)) {
                    $this->Deal->Attachment->enableUpload(false); //don't trigger upload behavior on save
                    $this->Deal->Attachment->create();
                    foreach($this->request->data['CloneAttachment'] as $key => $value) {
                        if (!$oldAttachmentArray[$value['id']]['id']) {
                            $cloneAttachment = $this->Deal->Attachment->find('first', array(
                                'conditions' => array(
                                    'Attachment.id' => $value['id']
                                )
                            ));
                            $this->Deal->Attachment->create();
                            $data['Attachment']['foreign_id'] = $deal_id;
                            $data['Attachment']['class'] = 'Deal';
                            $data['Attachment']['mimetype'] = $cloneAttachment["Attachment"]['mimetype'];
                            $data['Attachment']['dir'] = 'Deal/' . $deal_id;
                            $data['Attachment']['filename'] = $cloneAttachment["Attachment"]['filename'];
                            $upload_path = APP . 'media' . DS . 'Deal' . DS . $deal_id . DS;
                            new Folder($upload_path, true);
                            $upload_path = $upload_path . $cloneAttachment["Attachment"]['filename'];
                            $source_path = APP . 'media' . DS . 'Deal' . DS . $cloneAttachment["Attachment"]['foreign_id'] . DS . $cloneAttachment["Attachment"]['filename'];
                            copy($source_path, $upload_path);
                            $this->Deal->Attachment->save($data['Attachment']);
                        }
                    }
                }
                if (!isset($this->request->data['Attachment']) && $this->RequestHandler->isAjax()) { // Flash Upload
                    $this->request->data['Attachment']['foreign_id'] = $deal_id;
                    $this->request->data['Attachment']['description'] = 'Deal';
                    if ($this->request->data['Deal']['is_subdeal_available']) {
                        $this->Session->write('Deal.id', $deal_id);
                    }
                    $this->XAjax->flashuploadset($this->request->data);
                } else { // Normal Upload
					if(!empty($this->request->data['Attachment'])){
						$is_form_valid = true;
						$upload_photo_count = 0;
						for ($i = 0; $i < count($this->request->data['Attachment']); $i++) {
							if (!empty($this->request->data['Attachment'][$i]['filename']['tmp_name'])) {
								$upload_photo_count++;
								$image_info = getimagesize($this->request->data['Attachment'][$i]['filename']['tmp_name']);
								$this->request->data['Attachment']['filename'] = $this->request->data['Attachment'][$i]['filename'];
								$this->request->data['Attachment']['filename']['type'] = $image_info['mime'];
								$this->request->data['Attachment'][$i]['filename']['type'] = $image_info['mime'];
								$this->Deal->Attachment->Behaviors->attach('ImageUpload', Configure::read('photo.file'));
								$this->Deal->Attachment->set($this->request->data);
								if (!$this->Deal->validates() | !$this->Deal->Attachment->validates()) {
									$attachmentValidationError[$i] = $this->Deal->Attachment->validationErrors;
									$is_form_valid = false;
									$this->Session->setFlash(__l('Deal could not be added. Please, try again.') , 'default', null, 'error');
								}
							}
						}
						if (!$upload_photo_count) {
							$this->Deal->validates();
							$this->Deal->Attachment->validationErrors[0]['filename'] = __l('Required');
							$is_form_valid = false;
						}
						if (!empty($attachmentValidationError)) {
							foreach($attachmentValidationError as $key => $error) {
								$this->Deal->Attachment->validationErrors[$key]['filename'] = $error;
							}
						}
						if ($is_form_valid) {
							$this->request->data['foreign_id'] = $this->Deal->getLastInsertId();
							$this->request->data['Attachment']['description'] = 'Deal';
							$this->XAjax->normalupload($this->request->data, false);
							$this->Session->setFlash(__l('Deal has been added.') , 'default', null, 'success');
						}
					}
                }
                $deals = $this->Deal->find('first', array(
                    'conditions' => array(
                        'Deal.id' => $this->Deal->getLastInsertId()
                    ) ,
                    'contain' => array(
                        'City' => array(
                            'fields' => array(
                                'City.id',
                                'City.name',
                                'City.slug',
                            )
                        ) ,
                        'Attachment',
                        'Company',
                    ) ,
                    'recursive' => 2
                ));
				$slug = $deals['Deal']['slug'];
				$deal_id = $deals['Deal']['id'];
				foreach($deals['City'] as $k => $city)
				{
					$city_slug = $city['slug'];
					$city_id = $city['id'];
                	$this->Deal->_updateDealBitlyURL($slug, $city_slug, $city_id, $deal_id);
				}
                $this->Session->setFlash(__l('Deal has been added') , 'default', null, 'success');
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                    $this->redirect(array(
                        'action' => 'index',
                    ));
                } else {
                    $this->redirect(array(
                        'action' => 'company',
                        $deals['Company']['slug']
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Deal could not be added. Please, try again.') , 'default', null, 'error');
                if (!empty($this->request->data['Deal']['clone_deal_id'])) {
                    $cloneDeal = $this->Deal->find('first', array(
                        'conditions' => array(
                            'Deal.id' => $this->request->data['Deal']['clone_deal_id'],
                        ) ,
                        'contain' => array(
                            'Attachment'
                        ) ,
                        'fields' => array(
                            'Deal.user_id',
                            'Deal.name',
                        ) ,
                        'recursive' => 2
                    ));
                    $this->request->data['CloneAttachment'] = $cloneDeal['Attachment'];
                }
            }
        } else {
            if ($this->Auth->user('user_type_id') == ConstUserTypes::User) {
                throw new NotFoundException(__l('Invalid request'));
            } elseif ($this->Auth->user('user_type_id') == ConstUserTypes::Company) {
                $company = $this->Deal->Company->find('first', array(
                    'conditions' => array(
                        'Company.user_id' => $this->Auth->user('id')
                    ) ,
                    'fields' => array(
                        'Company.id',
                        'Company.slug',
                    ) ,
                    'recursive' => - 1
                ));
                if (empty($company)) {
                    throw new NotFoundException(__l('Invalid request'));
                }
                $this->request->data['Deal']['company_id'] = $company['Company']['id'];
                $this->request->data['Deal']['company_slug'] = $company['Company']['slug'];
            }
            if (!empty($this->request->params['named']['clone_deal_id'])) {
                $cloneDeal = $this->Deal->find('first', array(
                    'conditions' => array(
                        'Deal.id' => $this->request->params['named']['clone_deal_id'],
                    ) ,
                    'contain' => array(
                        'Attachment',
                        'CitiesDeal',
                        'Company' => array(
                            'fields' => array(
                                'Company.id',
                                'Company.slug'
                            ) ,
                        ) ,
                    ) ,
                    'fields' => array(
                        'Deal.user_id',
                        'Deal.name',
                        'Deal.description',
                        'Deal.private_note',
                        'Deal.original_price',
                        'Deal.discounted_price',
                        'Deal.discount_percentage',
                        'Deal.discount_amount',
                        'Deal.is_anytime_deal',
                        'Deal.savings',
                        'Deal.min_limit',
                        'Deal.max_limit',
                        'Deal.company_id',
                        'Deal.review',
                        'Deal.buy_min_quantity_per_user',
                        'Deal.buy_max_quantity_per_user',
                        'Deal.coupon_condition',
                        'Deal.coupon_highlights',
                        'Deal.comment',
                        'Deal.meta_keywords',
                        'Deal.meta_description',
                        'Deal.bonus_amount',
                        'Deal.commission_percentage',
                        'Deal.is_side_deal',
                    ) ,
                    'recursive' => 2
                ));
                $this->request->data['Deal'] = $cloneDeal['Deal'];
                $this->request->data['Deal']['clone_deal_id'] = $this->request->params['named']['clone_deal_id'];
                $this->request->data['Deal']['company_slug'] = $cloneDeal['Company']['slug'];
                $this->request->data['CloneAttachment'] = $cloneDeal['Attachment'];
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Company && $this->request->data['Deal']['company_id'] != $company['Company']['id']) {
                    throw new NotFoundException(__l('Invalid request'));
                }
                foreach($cloneDeal['CitiesDeal'] as $city_deal) {
                    $city_id[] = $city_deal['city_id'];
                }
                $this->set('city_id', $city_id);
            }
            if (($this->Auth->user('user_type_id') != ConstUserTypes::Admin) && (Configure::read('deal.is_admin_enable_commission'))):
                $this->request->data['Deal']['commission_percentage'] = Configure::read('deal.commission_amount');
            endif;
            $this->request->data['Deal']['user_id'] = $this->Auth->user('id');
            $this->request->data['Deal']['buy_min_quantity_per_user'] = 1;
            $this->request->data['Deal']['is_redeem_at_all_branch_address'] = 1;
            $this->request->data['Deal']['is_redeem_in_main_address'] = 1;
            //set values for deal amount calculator
            $this->request->data['Deal']['original_amt'] = (!empty($this->request->data['Deal']['original_price'])) ? $this->request->data['Deal']['original_price'] : '';
            $this->request->data['Deal']['discount_amt'] = (!empty($this->request->data['Deal']['discounted_price'])) ? $this->request->data['Deal']['discounted_price'] : '';
            $this->request->data['Deal']['calculator_discounted_price'] = (!empty($this->request->data['Deal']['discounted_price'])) ? $this->request->data['Deal']['discounted_price'] : '';
            $this->request->data['Deal']['calculator_min_limit'] = (!empty($this->request->data['Deal']['min_limit'])) ? $this->request->data['Deal']['min_limit'] : '';
            $this->request->data['Deal']['calculator_commission_percentage'] = (!empty($this->request->data['Deal']['commission_percentage'])) ? $this->request->data['Deal']['commission_percentage'] : '';
            $this->request->data['Deal']['calculator_bonus_amount'] = (!empty($this->request->data['Deal']['bonus_amount'])) ? $this->request->data['Deal']['bonus_amount'] : '';
            if (empty($this->request->params['named']['clone_deal_id']) && $this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                $this->request->data['Deal']['city_id'] = $this->Session->read('city_filter_id');
            }
        }
        //set values for deal Budget calculator
        if (!empty($this->request->data['Deal']['original_price']) && !empty($this->request->data['Deal']['discounted_price']) && !empty($this->request->data['Deal']['budget_amt'])) {
            $diff = $this->request->data['Deal']['original_price'] - $this->request->data['Deal']['discounted_price'];
            if ($diff > 0) {
                $this->request->data['Deal']['calculator_qty'] = $this->request->data['Deal']['budget_amt'] / $diff;
            } else $this->request->data['Deal']['calculator_qty'] = 0;
        } else {
            $this->request->data['Deal']['calculator_qty'] = 0;
        }
        //set values for deal amount calculator
        if (!empty($this->request->data['Deal']['calculator_discounted_price']) && !empty($this->request->data['Deal']['calculator_min_limit']) && !empty($this->request->data['Deal']['calculator_commission_percentage']) && !empty($this->request->data['Deal']['calculator_bonus_amount'])) {
            $this->request->data['Deal']['calculator_total_purchased_amount'] = $this->request->data['Deal']['calculator_discounted_price'] * $this->request->data['Deal']['calculator_min_limit'];
            $this->request->data['Deal']['calculator_total_commission_amount'] = ($this->request->data['Deal']['calculator_total_purchased_amount'] * ($this->request->data['Deal']['calculator_commission_percentage'] / 100)) + $this->request->data['Deal']['calculator_bonus_amount'];
            $this->request->data['Deal']['calculator_net_profit'] = $this->request->data['Deal']['calculator_total_commission_amount'];
        }
        if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
            $companies = $this->Deal->Company->find('list');
            $this->set(compact('companies'));
        }
        $cities = $this->Deal->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        if (Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::CompanyUser) {
            $charities = $this->Deal->Charity->find('list', array(
                'conditions' => array(
                    'Charity.is_active =' => 1
                ) ,
                'order' => array(
                    'Charity.name' => 'asc'
                )
            ));
            $this->set(compact('charities'));
        }
        $this->set(compact('cities'));
        // Getting branch address for listing information //
        $companyid = (!empty($company['Company']['id']) ? $company['Company']['id'] : '');
        $company_id = (!empty($this->request->data['Deal']['company_id']) ? $this->request->data['Deal']['company_id'] : $companyid);
        $branch_addresses = $this->Deal->getBranchAddresses($company_id);
        $this->set('branch_addresses', $branch_addresses);
    }
    public function flashupload()
    {
        $this->Deal->Attachment->Behaviors->attach('ImageUpload', Configure::read('photo.file'));
        $this->XAjax->flashupload();
    }
    public function invite_friends()
    {
    }
    public function delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->Deal->delete($id)) {
            $this->Deal->_updateCityDealCount();
            $this->Session->setFlash(__l('Deal deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function update_status($deal_id = null)
    {
        if (is_null($deal_id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->Deal->updateAll(array(
            'Deal.deal_status_id' => ConstDealStatus::PendingApproval
        ) , array(
            'Deal.id' => $deal_id
        ));
        $deal = $this->Deal->find('first', array(
            'conditions' => array(
                'Deal.id' => $deal_id
            ) ,
            'contain' => array(
                'City' => array(
                    'fields' => array(
                        'City.id',
                        'City.name',
                        'City.slug',
                    )
                ) ,
                'Attachment',
                'Company',
            ) ,
            'recursive' => 2
        ));
        $slug = $deal['Deal']['slug'];
        $city_name = $deal['City']['name'];
        $this->Deal->_updateCityDealCount();
        $this->Session->setFlash(__l('Deal has been updated') , 'default', null, 'success');
        $this->redirect(array(
            'controller' => 'deals',
            'action' => 'company',
            $deal['Company']['slug']
        ));
    }
    public function admin_index()
    {
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'success') {
            if ($this->Session->read('Deal.id')) {
                $deal_id = $this->Session->read('Deal.id');
                $this->Session->delete('Deal.id');
                $this->redirect(array(
                    'controller' => 'deals',
                    'action' => 'subdeal_add',
                    $deal_id
                ));
            }
        }
        $this->disableCache();
        $title = '';
        $this->_redirectGET2Named(array(
            'filter_id',
            'q'
        ));
        $conditions = array();
        if (!empty($this->request->params['named']['company'])) {
            $company_id = $this->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.slug' => $this->request->params['named']['company']
                ) ,
                'recursive' => - 1
            ));
            $conditions['Deal.company_id'] = $company_id['Company']['id'];
        }
        if (!empty($this->request->params['named']['city_slug'])) {
            $city_id = $this->Deal->City->find('first', array(
                'conditions' => array(
                    'City.slug' => $this->request->params['named']['city_slug']
                ) ,
                'recursive' => - 1
            ));
            $city_filter_id = $city_id['City']['id'];
        }
        if (!empty($this->request->data['Deal']['filter_id'])) {
            $this->request->params['named']['filter_id'] = $this->request->data['Deal']['filter_id'];
        }
        if (!empty($this->request->data['Deal']['q'])) {
            $this->request->params['named']['q'] = $this->request->data['Deal']['q'];
        }
        if (!empty($this->request->params['named']['filter_id'])) {
            $conditions['Deal.deal_status_id'] = $this->request->params['named']['filter_id'];
            $status = $this->Deal->DealStatus->find('first', array(
                'conditions' => array(
                    'DealStatus.id' => $this->request->params['named']['filter_id'],
                ) ,
                'fields' => array(
                    'DealStatus.name'
                ) ,
                'recursive' => - 1
            ));
            $title = $status['DealStatus']['name'];
        }
        $this->pageTitle = sprintf(__l(' %s Deals') , $title);
        if (isset($this->request->params['named']['q'])) {
            $conditions['Deal.name LIKE'] = '%' . $this->request->params['named']['q'] . '%';
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->params['named']['q']);
        }
        // check the filer passed through named parameter
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Deal.created) <= '] = 0;
            $this->pageTitle.= __l(' - Created today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Deal.created) <= '] = 7;
            $this->pageTitle.= __l(' - Created in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(Deal.created) <= '] = 30;
            $this->pageTitle.= __l(' - Created in this month');
        }
        // Citywise admin filter //
        if (!empty($this->request->data['Deal']['deal_city_id'])) {
            $city_filter_id = $this->request->data['Deal']['deal_city_id'];
        }
        if (empty($city_filter_id)) {
            $city_filter_id = $this->Session->read('city_filter_id');
        }
        if (!empty($city_filter_id)) {
            $deal_cities = $this->Deal->City->find('first', array(
                'conditions' => array(
                    'City.id' => $city_filter_id
                ) ,
                'fields' => array(
                    'City.name'
                ) ,
                'contain' => array(
                    'Deal' => array(
                        'fields' => array(
                            'Deal.id'
                        ) ,
                    )
                ) ,
                'recursive' => 1
            ));
            foreach($deal_cities['Deal'] as $deal_city) {
                $city_deal_id[] = $deal_city['id'];
            }
            if (!empty($city_deal_id)) {
                $conditions['Deal.id'] = $city_deal_id;
            }
        }
        $not_conditions['Not']['Deal.deal_status_id'] = array(
            ConstDealStatus::SubDeal
        );
        //$this->Deal->recursive = 2;
        $this->paginate = array(
            'conditions' => array(
                $conditions,
                $not_conditions,
            ) ,
            'contain' => array(
                'SubDeal' => array(
                    'fields' => array(
                        'SubDeal.id',
                        'SubDeal.name',
                        'SubDeal.slug',
                        'SubDeal.original_price',
                        'SubDeal.discounted_price',
                        'SubDeal.discount_percentage',
                        'SubDeal.discount_amount',
                        'SubDeal.max_limit',
                        'SubDeal.min_limit',
                        'SubDeal.deal_user_count',
                        'SubDeal.total_purchased_amount',
                        'SubDeal.bonus_amount',
                        'SubDeal.commission_percentage',
                        'SubDeal.total_commission_amount',
                        'SubDeal.private_note',
                    )
                ) ,
                'User' => array(
                    'UserAvatar',
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                        'User.email',
                        'User.password',
                        'User.fb_user_id'
                    )
                ) ,
                'City' => array(
                    'fields' => array(
                        'City.id',
                        'City.name',
                        'City.slug',
                    )
                ) ,
                'DealStatus' => array(
                    'fields' => array(
                        'DealStatus.name',
                    )
                ) ,
                'DealUser' => array(
                    'fields' => array(
                        'distinct(DealUser.user_id) as count_user'
                    )
                ) ,
                'Company' => array(
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
                    'fields' => array(
                        'Company.id',
                        'Company.name',
                        'Company.slug',
                        'Company.address1',
                        'Company.address2',
                        'Company.city_id',
                        'Company.state_id',
                        'Company.country_id',
                        'Company.zip',
                        'Company.url',
                    )
                ) ,
                'Attachment' => array(
                    'fields' => array(
                        'Attachment.id',
                        'Attachment.dir',
                        'Attachment.filename'
                    )
                ) ,
            ) ,
            'order' => array(
                'Deal.id' => 'desc'
            )
        );
        if (!empty($this->request->params['named']['q'])) {
            $this->paginate = array_merge($this->paginate, array(
                'search' => $this->request->params['named']['q']
            ));
        }
        $dealStatuses = $this->Deal->DealStatus->find('list');
        $dealStatusesCount = array();
        $count_conditions = array();
        if (!empty($this->request->params['named']['company'])) {
            $company_id = $this->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.slug' => $this->request->params['named']['company']
                ) ,
                'recursive' => - 1
            ));
            $count_conditions['Deal.company_id'] = $company_id['Company']['id'];
        }
        if (!empty($this->request->params['named']['city_slug'])) {
            $city_id = $this->Deal->City->find('first', array(
                'conditions' => array(
                    'City.slug' => $this->request->params['named']['city_slug']
                ) ,
                'recursive' => - 1
            ));
            $city_filter_id = $city_id['City']['id'];
        }
        // Citywise admin filter //
        if (!empty($city_filter_id)) {
            $deal_cities = $this->Deal->City->find('first', array(
                'conditions' => array(
                    'City.id' => $city_filter_id
                ) ,
                'fields' => array(
                    'City.name'
                ) ,
                'contain' => array(
                    'Deal' => array(
                        'fields' => array(
                            'Deal.id'
                        ) ,
                    )
                ) ,
                'recursive' => 1
            ));
            foreach($deal_cities['Deal'] as $deal_city) {
                $city_deal_id[] = $deal_city['id'];
            }
            if (!empty($city_deal_id)) {
                $count_conditions['Deal.id'] = $city_deal_id;
            }
        }
        foreach($dealStatuses as $id => $dealStatus) {
            $count_conditions['Deal.deal_status_id'] = $id;
            $dealStatusesCount[$id] = $this->Deal->find('count', array(
                'conditions' => $count_conditions,
                'recursive' => - 1
            ));
        }
        $this->set('dealStatusesCount', $dealStatusesCount);
        $this->set('dealStatuses', $dealStatuses);
        $this->set('deals', $this->paginate());
        //add more actions depends on the deal status
        $moreActions = array();
        if (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Upcoming) {
            $moreActions = array(
                ConstDealStatus::Open => __l('Open') ,
                ConstDealStatus::Canceled => __l('Canceled') ,
                ConstDealStatus::Rejected => __l('Rejected') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Open) {
            $moreActions = array(
                ConstDealStatus::Canceled => __l('Cancel and refund') ,
                ConstDealStatus::Expired => __l('Expired') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Canceled) {
            $moreActions = array(
                ConstDealStatus::Open => __l('Open') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Expired) {
            $moreActions = array(
                ConstDealStatus::Refunded => __l('Refunded') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Tipped) {
            $moreActions = array(
                ConstDealStatus::Closed => __l('Closed') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::PendingApproval) {
            $moreActions = array(
                ConstDealStatus::Upcoming => __l('Upcoming') ,
                ConstDealStatus::Open => __l('Open') ,
                ConstDealStatus::Canceled => __l('Canceled') ,
                ConstDealStatus::Rejected => __l('Rejected') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Rejected) {
            $moreActions = array(
                ConstDealStatus::Upcoming => __l('Upcoming') ,
                ConstDealStatus::Open => __l('Open') ,
                ConstDealStatus::Canceled => __l('Canceled') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Draft) {
            $moreActions = array(
                ConstDealStatus::Upcoming => __l('Upcoming') ,
                ConstDealStatus::Delete => __l('Delete') ,
                ConstDealStatus::Canceled => __l('Canceled') ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Closed) {
            $moreActions = array(
                ConstDealStatus::PaidToCompany => __l('Pay To Company')
            );
        }
        if (!empty($moreActions)) {
            $this->set(compact('moreActions'));
        }
        $cities = $this->Deal->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'success') {
            $this->Session->setFlash(__l('Deal has been added.') , 'default', null, 'success');
        }
        $this->set('deal_selected_city', $city_filter_id);
        $this->set('cities', $cities);
        $this->set('pageTitle', $this->pageTitle);
    }
    public function deals_print()
    {
        //print deal details and deal users list
        $this->autoRender = false;
        // Checking whether the deal belows to the logged in company user.
        $company = $this->Deal->Company->find('first', array(
            'conditions' => array(
                'Company.user_id' => $this->Auth->user('id')
            ) ,
            'recursive' => - 1
        ));
        if (!empty($this->request->params['named']['page_type']) && ((!empty($company) && $this->Auth->user('user_type_id') == ConstUserTypes::Company) || ($this->Auth->user('user_type_id') == ConstUserTypes::Admin)) && $this->request->params['named']['page_type'] == 'print') {
            $this->layout = 'print';
            if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
                $conditions['Deal.company_id'] = $company['Company']['id']; // Checking whether the deal belows to the logged in company user.
                
            }
            if (!empty($this->request->params['named']['deal_id'])) {
                $conditions['Deal.id'] = $this->request->params['named']['deal_id'];
            }
            if (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] != 'all')) {
                $conditions['Deal.deal_status_id'] = $this->request->params['named']['filter_id'];
            }
            $deals = $this->Deal->find('all', array(
                'conditions' => $conditions,
                'contain' => array(
                    'DealUser' => array(
                        'User' => array(
                            'fields' => array(
                                'id',
                                'username',
                                'email',
                            )
                        ) ,
                        'fields' => array(
                            'id',
                            'discount_amount',
                            'quantity',
                            'is_canceled'
                        ) ,
                        'DealUserCoupon'
                    ) ,
                    'DealStatus' => array(
                        'fields' => array(
                            'DealStatus.id',
                            'DealStatus.name',
                        )
                    ) ,
                ) ,
                'fields' => array(
                    'Deal.id',
                    'Deal.name',
                    'Deal.deal_user_count',
                    'Deal.coupon_expiry_date',
                ) ,
                'recursive' => 2
            ));
            Configure::write('debug', 0);
            if (!empty($deals)) {
                foreach($deals as $deal) {
                    foreach($deal['DealUser'] as $dealusers) {
                        if ($dealusers['is_canceled'] == 0) {
                            $data[]['Deal'] = array(
                                'dealname' => $deal['Deal']['name'],
                                'username' => $dealusers['User']['username'],
                                'quantity' => $dealusers['quantity'],
                                'discount_amount' => $dealusers['discount_amount'],
                                'coupon_code' => $dealusers['DealUserCoupon'],
                                'coupon_expiry_date' => strftime(Configure::read('site.datetime.format') , strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['coupon_expiry_date'])))) ,
                                'is_used' => $dealusers['DealUserCoupon']
                            );
                        }
                    }
                }
                $deal_list['deal_name'] = $deals['0']['Deal']['name'];
                $deal_list['coupon_expiry_date'] = !empty($deals['0']['Deal']['coupon_expiry_date']) ? $deals['0']['Deal']['coupon_expiry_date'] : '-';
                $deal_list['deal_user_count'] = $deals['0']['Deal']['deal_user_count'];
                $deal_list['deal_status'] = $deals['0']['DealStatus']['name'];
                $this->set('deals', $data);
                $this->set('deal_list', $deal_list);
                $this->render('index_print_deal_users');
            }
        }
    }
    public function admin_deals_print()
    {
        $this->setAction('deals_print');
    }
    public function admin_add()
    {
        $this->setAction('add');
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Deal');
        $this->loadModel('Attachment');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $id = !empty($id) ? $id : $this->request->data['Deal']['id'];
        $deal = $this->Deal->find('first', array(
            'conditions' => array(
                'Deal.id' => $id
            ) ,
            'contain' => array(
                'City'
            ) ,
            'recursive' => 1
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['OldAttachment'])) {
                $attachmentIds = array();
                foreach($this->request->data['OldAttachment'] as $attachment_id => $is_checked) {
                    if (isset($is_checked['id']) && ($is_checked['id'] == 1)) {
                        $attachmentIds[] = $attachment_id;
                    }
                }
                $attachmentIds = array(
                    'Attachment.id' => $attachmentIds
                );                
				if (!empty($attachmentIds['Attachment.id'])) {
                    $this->Deal->Attachment->deleteAll($attachmentIds);
                }
            }
            unset($this->request->data['OldAttachment']);
            unset($this->Deal->validate['start_date']['rule2']);
            $this->request->data['Deal']['savings'] = (!empty($this->request->data['Deal']['discount_percentage'])) ? ($this->request->data['Deal']['original_price'] * ($this->request->data['Deal']['discount_percentage'] / 100)) : $this->request->data['Deal']['discount_amount'];
            $this->request->data['Deal']['discounted_price'] = $this->request->data['Deal']['original_price'] - $this->request->data['Deal']['savings'];
            // If advance amount given, calculating that amount //
            if ((Configure::read('deal.is_enable_payment_advance') == 1) && !empty($this->request->data['Deal']['is_enable_payment_advance'])) {
                $remaining_amount = $this->request->data['Deal']['discounted_price'] - $this->request->data['Deal']['pay_in_advance'];
                if (!empty($remaining_amount) && $remaining_amount > 0) {
                    $this->request->data['Deal']['discounted_price'] = $this->request->data['Deal']['pay_in_advance'];
                }
            }
            if (!empty($this->request->data['Deal']['send_to_admin']) && $deal['Deal']['deal_status_id'] == ConstDealStatus::Draft) {
                $this->request->data['Deal']['deal_status_id'] = ConstDealStatus::Upcoming;
            }
            if ($this->request->data['Deal']['discounted_price'] == 0) {
                unset($this->Deal->validate['discounted_price']['rule2']);
                unset($this->Deal->validate['commission_percentage']['rule2']);
                unset($this->Deal->validate['commission_percentage']['rule4']);
            } else {
                unset($this->Deal->validate['discounted_price']['rule3']);
                unset($this->Deal->validate['commission_percentage']['rule3']);
                unset($this->Deal->validate['bonus_amount']['rule2']);
            }
            // multiple deal validation unset process
            if ($this->request->data['Deal']['is_subdeal_available']) {
                unset($this->Deal->validate['bonus_amount']);
                unset($this->Deal->validate['commission_percentage']);
                unset($this->Deal->validate['discounted_price']);
                unset($this->Deal->validate['original_price']);
                unset($this->Deal->validate['discount_amount']);
            }
            // An time deal validation unset process
            if ($this->request->data['Deal']['is_anytime_deal']) {
                unset($this->Deal->validate['end_date']);
                unset($this->Deal->validate['coupon_expiry_date']);
                unset($this->Deal->validate['coupon_start_date']['rule2']);
                unset($this->request->data['Deal']['coupon_expiry_date']);
                unset($this->request->data['Deal']['end_date']);
            }
            if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                unset($this->Deal->validate['commission_percentage']['rule4']);
            }
            $this->Deal->City->set($this->request->data);
            if (empty($this->request->data['Deal']['is_redeem_at_all_branch_address']) && empty($this->request->data['Deal']['is_redeem_in_main_address']) && empty($this->request->data['CompanyAddressesDeal']['company_address_id'])) {
                $this->request->data['Deal']['is_redeem_in_main_address'] = 1;
            }
            if ($this->Deal->validates() & $this->Deal->City->validates()) {
                if (empty($this->request->data['Deal']['is_subdeal_available'])) {
                    $this->request->data['Deal']['sub_deal_count'] = 0;
                }
                if ($this->Deal->save($this->request->data)) {
                    if (empty($this->request->data['Deal']['is_subdeal_available'])) {
                        $this->Deal->deleteAll(array(
                            'Deal.parent_id' => $this->request->data['Deal']['id']
                        ));
                    }
                    // Saving listing locations //
                    if (empty($this->request->data['CompanyAddressesDeal']['company_address_id']) && empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                        $this->Deal->CompanyAddressesDeal->deleteAll(array(
                            'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
                        ));
                    }
                    if (!empty($this->request->data['CompanyAddressesDeal']['company_address_id']) && empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                        // Deleting previous inserted records //
                        $this->Deal->CompanyAddressesDeal->deleteAll(array(
                            'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
                        ));
                        // Inserting new records //
                        $company_addresses_deal = array();
                        foreach($this->request->data['CompanyAddressesDeal']['company_address_id'] as $key => $value) {
                            $this->Deal->CompanyAddressesDeal->create();
                            $company_addresses_deal['CompanyAddressesDeal']['deal_id'] = $this->request->data['Deal']['id'];
                            $company_addresses_deal['CompanyAddressesDeal']['company_address_id'] = $value;
                            $this->Deal->CompanyAddressesDeal->save($company_addresses_deal);
                        }
                    }
                    if (!empty($this->request->data['Deal']['is_redeem_at_all_branch_address'])) {
                        // Deleting previous inserted records //
                        $this->Deal->CompanyAddressesDeal->deleteAll(array(
                            'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
                        ));
                    }
                    // Inserting coupon, if given //
                    if (!empty($this->request->data['Deal']['coupon_code'])) {
                        $split_codes = explode(',', $this->request->data['Deal']['coupon_code']);
                        $deal_coupons = array();
                        foreach($split_codes as $key => $value) {
                            $coupon_value = trim($value);
                            if (!empty($coupon_value)) {
                                $deal_coupons['id'] = '';
                                $deal_coupons['deal_id'] = $this->request->data['Deal']['id'];
                                $deal_coupons['coupon_code'] = $coupon_value;
                                $this->Deal->DealCoupon->save($deal_coupons);
                            }
                        }
                    }
                    // finding again, coz deal slug has been changed during edit and forming Bitly Url based on the new slug
                    $deal = $this->Deal->find('first', array(
                        'conditions' => array(
                            'Deal.id' => $this->request->data['Deal']['id']
                        ) ,
                        'contain' => array(
                            'CitiesDeal',
                            'City'
                        ) ,
                        'recursive' => 1
                    ));
                    $slug = $deal['Deal']['slug'];
                    $deal_id = $deal['Deal']['id'];
                    foreach($deal['City'] as $k => $city) {
                        $city_slug = $city['slug'];
                        $city_id = $city['id'];
                        $this->Deal->_updateDealBitlyURL($slug, $city_slug, $city_id, $deal_id);
                    }
                    $this->Deal->_updateCityDealCount();
                    // Tipping Deals
                    if (($this->request->data['Deal']['min_limit'] <= $deal['Deal']['deal_user_count']) && $deal['Deal']['deal_status_id'] == ConstDealStatus::Open) {
                        $this->Deal->updateAll(array(
                            'Deal.deal_status_id' => ConstDealStatus::Tipped,
                            'Deal.deal_tipped_time' => '\'' . date('Y-m-d H:i:s') . '\''
                        ) , array(
                            'Deal.deal_status_id' => ConstDealStatus::Open,
                            'Deal.id' => $deal['Deal']['id']
                        ));
                        $this->Deal->processDealStatus($deal['Deal']['id']);
                    }
                    $this->Deal->set($this->request->data);
                    $foreign_id = $this->request->data['Deal']['id'];
                    $this->Deal->Attachment->create();
                    if (!isset($this->request->data['Attachment']) && $this->RequestHandler->isAjax()) { // Flash Upload
                        $this->request->data['Attachment']['foreign_id'] = $foreign_id;
                        $this->request->data['Attachment']['description'] = 'Deal';
                        $this->XAjax->flashuploadset($this->request->data);
                    } else { // Normal Upload
                        $is_form_valid = true;
                        $upload_photo_count = 0;						
						if(!empty($this->request->data['Attachment'])){
							for ($i = 0; $i < count($this->request->data['Attachment']); $i++) {
								if (!empty($this->request->data['Attachment'][$i]['filename']['tmp_name'])) {
									$upload_photo_count++;
									$image_info = getimagesize($this->request->data['Attachment'][$i]['filename']['tmp_name']);
									$this->request->data['Attachment']['filename'] = $this->request->data['Attachment'][$i]['filename'];
									$this->request->data['Attachment']['filename']['type'] = $image_info['mime'];
									$this->request->data['Attachment'][$i]['filename']['type'] = $image_info['mime'];
									$this->Deal->Attachment->Behaviors->attach('ImageUpload', Configure::read('photo.file'));
									$this->Deal->Attachment->set($this->request->data);
									if (!$this->Deal->validates() | !$this->Deal->Attachment->validates()) {
										$attachmentValidationError[$i] = $this->Deal->Attachment->validationErrors;
										$is_form_valid = false;
										$this->Session->setFlash(__l('Deal could not be added. Please, try again.') , 'default', null, 'error');
									}
								}
							}
							if (!$upload_photo_count) {
								$this->Deal->validates();
								$this->Deal->Attachment->validationErrors[0]['filename'] = __l('Required');
								$is_form_valid = false;
							}
							if (!empty($attachmentValidationError)) {
								foreach($attachmentValidationError as $key => $error) {
									$this->Deal->Attachment->validationErrors[$key]['filename'] = $error;
								}
							}
							if ($is_form_valid) {
								$this->request->data['foreign_id'] = $foreign_id;
								$this->request->data['Attachment']['description'] = 'Deal';
								$this->XAjax->normalupload($this->request->data, false);
								$this->Session->setFlash(__l('Deal has been added.') , 'default', null, 'success');
							}                        }
                    }
                    $this->Session->setFlash(__l('Deal has been updated') , 'default', null, 'success');
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'index'
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Deal could not be updated. Please, try again.') , 'default', null, 'error');
            }
            $attachments = $this->Deal->Attachment->find('all', array(
                'conditions' => array(
                    'Attachment.foreign_id' => $this->request->data['Deal']['id'],
                    'Attachment.class = ' => 'Deal'
                ) ,
                'recursive' => 1,
            ));
            if (!empty($attachments)) {
                foreach($attachments as $attachment) {
                    $this->request->data['Attachment'][] = $attachment['Attachment'];
                }
            }
        } else {
            $this->request->data = $this->Deal->find('first', array(
                'conditions' => array(
                    'Deal.id' => $id
                ) ,
                'recursive' => 1
            ));
            if (!empty($this->request->data['Deal']['start_date'])) {
                $this->request->data['Deal']['start_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['start_date']));
            }
            if (!empty($this->request->data['Deal']['end_date'])) {
                $this->request->data['Deal']['end_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['end_date']));
            }
            if (!empty($this->request->data['Deal']['coupon_start_date'])) {
                $this->request->data['Deal']['coupon_start_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['coupon_start_date']));
            }
            if (!empty($this->request->data['Deal']['coupon_expiry_date'])) {
                $this->request->data['Deal']['coupon_expiry_date'] = _formatDate('Y-m-d H:i:s', strtotime($this->request->data['Deal']['coupon_expiry_date']));
            }
            if (!empty($this->request->data['DealCoupon'])) {
                foreach($this->request->data['DealCoupon'] as $coupon_codes) {
                    $coupon_code[] = $coupon_codes['coupon_code'];
                }
                $coupon_code = implode(',', $coupon_code);
                $this->set('manual_coupon_codes', $coupon_code);
            }
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
            //set values for deal amount calculator data[Deal][discount_amt]
            $this->request->data['Deal']['original_amt'] = $this->request->data['Deal']['original_price'];
            $this->request->data['Deal']['discount_amt'] = $this->request->data['Deal']['discounted_price'];
            $this->request->data['Deal']['calculator_discounted_price'] = $this->request->data['Deal']['discounted_price'];
            $this->request->data['Deal']['calculator_min_limit'] = $this->request->data['Deal']['min_limit'];
            $this->request->data['Deal']['calculator_commission_percentage'] = $this->request->data['Deal']['commission_percentage'];
            $this->request->data['Deal']['calculator_bonus_amount'] = $this->request->data['Deal']['bonus_amount'];
        }
        if (!empty($this->request->data['Deal']['original_price']) && !empty($this->request->data['Deal']['discounted_price']) && !empty($this->request->data['Deal']['budget_amt'])) {
            $diff = $this->request->data['Deal']['original_price'] - $this->request->data['Deal']['discounted_price'];
            if ($diff > 0) {
                $this->request->data['Deal']['calculator_qty'] = $this->request->data['Deal']['budget_amt'] / $diff;
            } else $this->request->data['Deal']['calculator_qty'] = 0;
        } else {
            $this->request->data['Deal']['calculator_qty'] = 0;
        }
        //set values for deal amount calculator
        if (!empty($this->request->data['Deal']['calculator_discounted_price']) && !empty($this->request->data['Deal']['calculator_min_limit']) && !empty($this->request->data['Deal']['calculator_commission_percentage']) && !empty($this->request->data['Deal']['calculator_bonus_amount'])) {
            $this->request->data['Deal']['calculator_total_purchased_amount'] = $this->request->data['Deal']['calculator_discounted_price'] * $this->request->data['Deal']['calculator_min_limit'];
            $this->request->data['Deal']['calculator_total_commission_amount'] = ($this->request->data['Deal']['calculator_total_purchased_amount'] * ($this->request->data['Deal']['calculator_commission_percentage'] / 100)) + $this->request->data['Deal']['calculator_bonus_amount'];
            $this->request->data['Deal']['calculator_net_profit'] = $this->request->data['Deal']['calculator_total_commission_amount'];
        }
        $this->pageTitle.= ' - ' . $this->request->data['Deal']['name'];
        $discounts = array();
        for ($i = 1; $i <= 100; $i++) {
            $discounts[$i] = $i;
        }
        $cities = $this->Deal->City->find('list', array(
            'conditions' => array(
                'City.is_approved =' => 1
            ) ,
            'order' => array(
                'City.name' => 'asc'
            )
        ));
        $companies = $this->Deal->Company->find('list');
        $dealStatuses = $this->Deal->DealStatus->find('list');
        $this->set('deal', $deal);
        if (Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::CompanyUser) {
            $charities = $this->Deal->Charity->find('list', array(
                'conditions' => array(
                    'Charity.is_active =' => 1
                ) ,
                'order' => array(
                    'Charity.name' => 'asc'
                )
            ));
            $this->set(compact('charities'));
        }
        $this->set(compact('cities', 'dealStatuses', 'companies', 'discounts'));
        // Getting branch address for listing information //
        $branch_checked_addresses = $this->Deal->CompanyAddressesDeal->find('list', array(
            'conditions' => array(
                'CompanyAddressesDeal.deal_id' => $this->request->data['Deal']['id']
            ) ,
            'fields' => array(
                'CompanyAddressesDeal.company_address_id',
                'CompanyAddressesDeal.company_address_id',
            ) ,
            'recursive' => - 1
        ));
        $this->set('branch_checked_addresses', $branch_checked_addresses);
        $subdeal = $this->Deal->find('all', array(
            'conditions' => array(
                'Deal.parent_id' => $id
            ) ,
            'recursive' => - 1
        ));
        $this->set('subdeal', $subdeal);
        $companyid = (!empty($company['Company']['id']) ? $company['Company']['id'] : '');
        $company_id = (!empty($this->request->data['Deal']['company_id']) ? $this->request->data['Deal']['company_id'] : $companyid);
        $branch_addresses = $this->Deal->getBranchAddresses($company_id);
        $this->set('branch_addresses', $branch_addresses);
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->Deal->delete($id)) {
            $this->Deal->_updateCityDealCount();
            $this->Session->setFlash(__l('Deal deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    //more actions in admin index page
    public function admin_update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data['Deal'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $dealIds = array();
            foreach($this->request->data['Deal'] as $deal_id => $is_checked) {
                if ($is_checked['id']) {
                    $dealIds[] = $deal_id;
                }
            }
            if ($actionid && !empty($dealIds)) {
                if ($actionid == ConstDealStatus::Open) {
                    $dealsLeft = false;
                    foreach($this->request->data['Deal'] as $deal_id => $is_checked) {
                        if ($is_checked['id']) {
                            $deal = $this->Deal->find('first', array(
                                'conditions' => array(
                                    'Deal.id' => $deal_id
                                ) ,
                                'fields' => array(
                                    'Deal.end_date',
                                    'Deal.coupon_expiry_date',
                                    'Deal.is_anytime_deal',
                                    'Deal.is_subdeal_available'
                                ) ,
                                'recursive' => - 1
                            ));
                            if ((strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['end_date']))) >= strtotime(date('Y-m-d H:i:s')) && strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['coupon_expiry_date']))) >= strtotime(date('Y-m-d H:i:s'))) || !empty($deal['Deal']['is_anytime_deal']) || !empty($deal['Deal']['is_subdeal_available'])) {
                                $this->Deal->updateAll(array(
                                    'Deal.deal_status_id' => ConstDealStatus::Open,
                                    'Deal.start_date' => '"' . date('Y-m-d H:i:s') . '"',
                                ) , array(
                                    'Deal.id' => $deal_id
                                ));
                                $this->Deal->_processOpenStatus($deal_id);
                            } else {
                                $dealsLeft = true;
                            }
                        }
                    }
                    $this->Deal->_sendSubscriptionMail();
                    $msg = __l('Checked deals have been moved to open status. ');
                    if ($dealsLeft) {
                        $msg.= __l('Some of the deals are not opened due to the end date and coupon expiry date in past.');
                    }
                    $this->Session->setFlash($msg, 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Canceled) {
                    $openDealIds = $this->Deal->find('list', array(
                        'conditions' => array(
                            'Deal.id' => $dealIds,
                            'Deal.deal_status_id' => ConstDealStatus::Open,
                        ) ,
                        'recursive' => - 1,
                    ));
                    if (!empty($openDealIds)) {
                        $this->Deal->_refundDealAmount('update', array_keys($openDealIds));
                    }
                    //manual refund for deals. So deals are not closed
                    foreach($dealIds as $deal_id) {
                        $is_not_already_refunded = $this->Deal->find('first', array(
                            'conditions' => array(
                                'Deal.id' => $deal_id,
                                'Deal.deal_status_id !=' => ConstDealStatus::Refunded,
                            ) ,
                            'recursive' => - 1,
                        ));
                        if (!empty($is_not_already_refunded)) {
                            $data = array();
                            $data['Deal']['id'] = $deal_id;
                            $data['Deal']['deal_status_id'] = ConstDealStatus::Canceled;
                            $data['Deal']['deal_user_count'] = 0;
                            $data['Deal']['deal_tipped_time'] = "'0000-00-00 00:00:00'";
                            $data['Deal']['is_coupon_mail_sent'] = 0;
                            $data['Deal']['is_subscription_mail_sent'] = 0;
                            $data['Deal']['total_purchased_amount'] = 0;
                            $data['Deal']['total_commission_amount'] = 0;
                            $this->Deal->save($data);
                        }
                    }
                    $this->Session->setFlash(__l('Checked deals have been canceled') , 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Rejected) {
                    $this->Deal->updateAll(array(
                        'Deal.deal_status_id' => ConstDealStatus::Rejected
                    ) , array(
                        'Deal.id' => $dealIds
                    ));
                    $this->Session->setFlash(__l('Checked deals have been rejected') , 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Expired) {
                    //Get the Quantity for selected deals.
                    $quantities = $this->Deal->find('all', array(
                        'conditions' => array(
                            'Deal.id' => $dealIds
                        ) ,
                        'fields' => array(
                            'Deal.deal_user_count',
                            'Deal.id',
                            'Deal.is_anytime_deal'
                        ) ,
                        'recursive' => - 1
                    ));
                    foreach($quantities as $quantity) {
                        if (empty($quantity['Deal']['is_anytime_deal'])) {
                            if ($quantity['Deal']['deal_user_count'] == 0) {
                                $data = array();
                                $data['Deal']['id'] = $quantity['Deal']['id'];
                                $data['Deal']['deal_status_id'] = ConstDealStatus::Expired;
                                $this->Deal->save($data);
                            } else {
                                $this->Deal->_refundDealAmount('admin_update', $quantity['Deal']['id']);
                                $this->Deal->updateAll(array(
                                    'Deal.end_date' => '"' . date('Y-m-d H:i:s') . '"',
                                ) , array(
                                    'Deal.id' => $quantity['Deal']['id']
                                ));
                            }
                        } else {
                            $dealsLeft = true;
                        }
                    }
                    $msg = __l('Deals have been changed as expired. ');
                    if ($dealsLeft) {
                        $msg.= __l('Some of the deals are not expired becasue "AnyTime" Deal cannot be expired. It can be either cancelled or closed.');
                    }
                    $this->Session->setFlash($msg, 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Refunded) {
                    $this->Deal->_refundDealAmount('admin_update', $dealIds);
                    $this->Session->setFlash(__l('Expired deals have been refunded') , 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Closed) {
                    $this->Deal->_closeDeals($dealIds);
                    $this->Session->setFlash(__l('Checked deals have been closed') , 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::PaidToCompany) {
                    // each deal closing time update total_purchased_amount, total_commission_amount. so we commented this code.
                    /*$this->Deal->updateAll(array(
                    'Deal.total_purchased_amount' => '(Deal.discounted_price * Deal.deal_user_count)',
                    'Deal.total_commission_amount' => '(Deal.bonus_amount + ( Deal.total_purchased_amount * ( Deal.commission_percentage / 100 )))',
                    ) , array(
                    'Deal.deal_status_id' => ConstDealStatus::Closed,
                    'Deal.id' => $dealIds
                    ));*/
                    $this->Deal->_payToCompany('admin_update', $dealIds);
                    //$this->Deal->_payToCharity($dealIds);
                    $this->Session->setFlash(__l('Checked deals amount have been transferred') , 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Upcoming) {
                    $dealsLeft = false;
                    foreach($this->request->data['Deal'] as $deal_id => $is_checked) {
                        if ($is_checked['id']) {
                            $deal = $this->Deal->find('first', array(
                                'conditions' => array(
                                    'Deal.id' => $deal_id
                                ) ,
                                'fields' => array(
                                    'Deal.end_date',
                                    'Deal.coupon_expiry_date',
                                    'Deal.is_anytime_deal',
                                ) ,
                                'recursive' => - 1
                            ));
                            if ((strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['end_date']))) >= strtotime(date('Y-m-d H:i:s')) && strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal['Deal']['coupon_expiry_date']))) >= strtotime(date('Y-m-d H:i:s'))) || !empty($deal['Deal']['is_anytime_deal'])) {
                                $this->Deal->updateAll(array(
                                    'Deal.deal_status_id' => ConstDealStatus::Upcoming
                                ) , array(
                                    'Deal.id' => $deal_id
                                ));
                            } else {
                                $dealsLeft = true;
                            }
                        }
                    }
                    $msg = __l('Checked deals have been moved to upcoming status. ');
                    if ($dealsLeft) {
                        $msg.= __l('Some of the deals are not upcoming due to the end date and coupon expiry date in past.');
                    }
                    $this->Session->setFlash($msg, 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::Delete) {
                    $this->Deal->deleteAll(array(
                        'Deal.id' => $dealIds
                    ));
                    $this->Session->setFlash(__l('Checked deals have been deleted') , 'default', null, 'success');
                } else if ($actionid == ConstDealStatus::PendingApproval) {
                    $this->Deal->updateAll(array(
                        'Deal.deal_status_id' => ConstDealStatus::PendingApproval
                    ) , array(
                        'Deal.id' => $dealIds
                    ));
                    $this->Session->setFlash(__l('Checked deals have been inactive') , 'default', null, 'success');
                }
            }
        }
        $this->Deal->_updateCityDealCount();
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(Router::url('/', true) . $r);
        } else {
            $this->redirect($r);
        }
    }
    //run cron manually from admin side
    public function admin_update_status()
    {
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'cron');
        $this->Cron = new CronComponent($collection);
        $this->Cron->update_deal();
        $this->Deal->_updateCityDealCount();
        $this->redirect(array(
            'controller' => 'deals',
            'action' => 'index'
        ));
    }
    //buy a new deal
    public function buy($deal_id = null, $sub_deal_id = null)
    {
        $this->pageTitle = __l('Buy Deal');
        if ((!$this->Deal->User->isAllowed($this->Auth->user('user_type_id'))) || (is_null($deal_id) && empty($this->request->data['Deal']['deal_id']))) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!$this->Auth->user('id') and !empty($this->request->url)) $this->Session->write('Auth.redirectUrl', $this->request->url);
        if (!empty($this->request->data['Deal']['deal_id'])) {
            $deal_id = $this->request->data['Deal']['deal_id'];
        }
        if (!empty($this->request->data['Deal']['sub_deal_id'])) {
            $sub_deal_id = $this->request->data['Deal']['sub_deal_id'];
        }
        $conditions = array();
        $conditions['Deal.id'] = $deal_id;
        $conditions['Deal.deal_status_id'] = array(
            ConstDealStatus::Open,
            ConstDealStatus::Tipped
        );
        // Subdeal: If subdeal, change deal id //
        $get_deal = $this->Deal->_getDealInfo($deal_id); // Checking with Main deal //
        if ((!empty($get_deal['Deal']['is_subdeal_available']) || !empty($parent_id)) && empty($sub_deal_id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($sub_deal_id)) {
            if (!empty($get_deal['Deal']['is_subdeal_available'])) {
                if (!empty($get_deal) && ($get_deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $get_deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped)) {
                    $conditions = array();
                    $conditions['Deal.id'] = $sub_deal_id;
                } else {
                    $this->Session->setFlash(__l('You\'re too late, this deal has been expired.') , 'default', null, 'error');
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'index'
                    ));
                }
            }
        }
        $deal = $this->Deal->find('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'Deal.id',
                'Deal.name',
                'Deal.slug',
                'Deal.original_price',
                'Deal.discount_amount',
                'Deal.discounted_price',
                'Deal.description',
                'Deal.discount_percentage',
                'Deal.min_limit',
                'Deal.max_limit',
                'Deal.deal_user_count',
                'Deal.discounted_price',
                'Deal.buy_min_quantity_per_user',
                'Deal.buy_max_quantity_per_user',
                'Deal.end_date',
                'Deal.is_anytime_deal',
                'Deal.charity_id',
                'Deal.charity_percentage',
                'Charity.name',
                'Charity.url',
                'Deal.parent_id',
            ) ,
            'recursive' => 0
        ));
        if (!empty($get_deal['Deal']['is_subdeal_available'])) {
            $deal['Deal']['charity_id'] = $get_deal['Deal']['charity_id'];
            $deal['Deal']['charity_percentage'] = $get_deal['Deal']['charity_percentage'];
            $deal['Charity'] = $get_deal['Charity'];
        }
        $user = $this->Deal->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id') ,
            ) ,
            'fields' => array(
                'User.id',
                'User.fb_user_id',
                'User.email',
            ) ,
            'contain' => array(
                'DealUser'
            ) ,
            'recursive' => 1
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (empty($deal['Deal']['is_anytime_deal']) && $deal['Deal']['end_date'] <= _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true)) {
            $this->Session->setFlash(__l('You\'re too late, this deal has been expired.') , 'default', null, 'error');
				$this->redirect(array(
					'controller' => 'deals',
					'action' => 'view',
					$get_deal['Deal']['slug']
				));
        }
        $user_quantity = '';
        if (!empty($user) && !empty($deal['Deal']['buy_max_quantity_per_user'])) {
            foreach($user['DealUser'] as $user_coupon) {
                if ($user_coupon['deal_id'] == $deal_id) {
                    $user_quantity+= $user_coupon['quantity'];
                }
            }
         }
        $user_available_balance = 0;
        if ($this->Auth->user('id')) {
            $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
        }
        //MOHAN - Executed on submit of confirm payment
        if (!empty($this->request->data)) {
        	//echo "<pre>";print_r($this->Deal);echo "<pre/>";
        	//die('dood');
            if ($this->request->data['Deal']['user_id'] == $this->Auth->user('id')) {
                //purchase deal before login and do the validations
                if (!$this->Auth->user('id')) {
                    $this->Deal->User->set($this->request->data);
                    $this->Deal->User->validates();
                }
                //before login user_id is null
                if (empty($this->request->data['Deal']['user_id'])) {
                    unset($this->Deal->validate['user_id']);
                }
                // If wallet act like groupon enabled, and purchase with wallet enabled, setting below for making purchase through wallet //
                if (Configure::read('wallet.is_handle_wallet_as_in_groupon') && !empty($this->request->data['Deal']['is_purchase_via_wallet'])) {
                    $this->request->data['Deal']['payment_gateway_id'] = ConstPaymentGateways::Wallet;
                }
                // Free deal check
                //MOHAN - This is where the type of validation that will be done is determined - the values are assigned to $this->Deal->validate
                if ($deal['Deal']['discounted_price'] != 0) {
                    //MOHAN - validation for credit card details
                    //MOHAN - ConstPaymentGateways::CreditCard - id == 2 - CreditCard using paypal
                    //MOHAN - Removing this code cause we don't collect any user input for iPay88 but we are using the ConstPaymentGateways::CreditCard  
                    /*if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard) {
                        $this->Deal->validate = array_merge($this->Deal->validate, $this->Deal->validateCreditCard);
                    } */
                    //MOHAN - ConstPaymentGateways::AuthorizeNet  id == 4 - Authorize.net
                    // MOHAN - changed this from an else if to an if because we commented out the block above
                    if (($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::AuthorizeNet && isset($this->request->data['Deal']['payment_profile_id']) && empty($this->request->data['Deal']['payment_profile_id']))) {
                            $this->Deal->validate = array_merge($this->Deal->validate, $this->Deal->validateCreditCard);
                        if ($this->request->data['Deal']['is_show_new_card'] == 0) {
                            $payment_gateway_id_validate = array(
                                'payment_profile_id' => array(
                                    'rule1' => array(
                                        'rule' => 'notempty',
                                        'message' => __l('Required')
                                    )
                                )
                            );
                            $this->Deal->validate = array_merge($this->Deal->validate, $payment_gateway_id_validate);
                        }
                    } 
                    else if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::AuthorizeNet && (!isset($this->request->data['Deal']['payment_profile_id']))) {
                        $this->Deal->validate = array_merge($this->Deal->validate, $this->Deal->validateCreditCard);
                    }
                } else {
                    $this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::Wallet;
                }
                $this->Deal->set($this->request->data);
                
                //MOHAN - total_deal_amount is here 
                $total_deal_amount = $this->request->data['Deal']['total_deal_amount'] = $deal['Deal']['discounted_price'] * $this->request->data['Deal']['quantity'];
                $this->Deal->validates();
                $user_details_updated = true;
                //for facebook users need to update email address at first time
                if (!empty($user['User']['fb_user_id']) && empty($user['User']['email'])) {
                    $this->request->data['User']['id'] = $this->Auth->user('id');
                    $this->Deal->User->set($this->request->data['User']);
                    if ($this->Deal->User->validates() && empty($this->Deal->User->validationErrors)) {
                        $this->Deal->User->save($this->request->data['User']);
                        if (empty($_SESSION['Auth']['User']['cim_profile_id'])) {
                            $this->Deal->User->_createCimProfile($this->Auth->user('id'));
                        }
                    } else {
                        $user_details_updated = false;
                    }
                }
                // <-- For iPhone App code
                if ($this->RequestHandler->prefers('json') && $this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::AuthorizeNet && empty($this->request->data['Deal']['payment_profile_id'])) {
                    $resonse = array(
                        'status' => 1,
                        'message' => __l('Your Purchase could not be completed.')
                    );
                    $this->view = 'Json';
                    $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
                }
                // For iPhone App code -->
                //MOHAN - No problems with validation start processing the order
                if (empty($this->Deal->validationErrors) && $user_details_updated) {
                    //for wallet payment if user have enough wallet amt send to _buyDeal method
                    if ($this->Auth->user('id') && $this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::Wallet && $user_available_balance >= $total_deal_amount) {
                        $this->_buyDeal($this->request->data);
                    } else {
                        //guset users and users who have less amount in wallet or credit card payment or paypal auth payment
                        $this->process_user($deal);
                    }
                }
                // <-- For iPhone App code
                //MOHAN - validation errors - handle it 
                else {
                    if ($this->RequestHandler->prefers('json')) {
                        $resonse = array(
                            'status' => 1,
                            'message' => __l('Your Purchase could not be completed.')
                        );
                        $this->view = 'Json';
                        $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
                    } else {
                        $this->Session->setFlash(__l('Your Purchase could not be completed.') , 'default', null, 'error');
                    }
                }
                // For iPhone App code -->
                //ehrn validation errors for user fields unset passwords
                if (!$this->Auth->user('id')) {
                    unset($this->request->data['User']['passwd']);
                    unset($this->request->data['User']['confirm_password']);
                }
            } 
            //MOHAN - user id inconsistency - abort
            else {
                $this->Session->setFlash(__l('Invalid data entered. Your purchase has been cancelled.') , 'default', null, 'error');
                $this->redirect(array(
                    'controller' => 'deals',
                    'action' => 'view',
                    $deal['Deal']['slug']
                ));
            }
        } 
        //MOHAN - excuted on first time load of buy page
        else {
            $this->request->data['Deal']['is_gift'] = (!empty($this->request->params['named']['type'])) ? 1 : 0;
            $this->request->data['Deal']['quantity'] = 1;
            $this->request->data['Deal']['deal_amount'] = $deal['Deal']['discounted_price'];
            $this->request->data['Deal']['deal_id'] = $deal_id;
            $this->request->data['Deal']['sub_deal_id'] = $sub_deal_id;
            $this->request->data['Deal']['total_deal_amount'] = $deal['Deal']['discounted_price'];
            $this->request->data['Deal']['is_show_new_card'] = 0;
            $this->request->data['Deal']['charity_id'] = $deal['Deal']['charity_id'];
            //if user logged in check whether user eligible to buy deal
            if ($this->Auth->user('id')) {
                if (!$this->Deal->isEligibleForBuy($deal_id, $this->Auth->user('id') , $deal['Deal']['buy_max_quantity_per_user'])) {
                    $this->Session->setFlash(sprintf(__l('You can\'t buy this deal. Your maximum allowed limit %s is over') , $deal['Deal']['buy_max_quantity_per_user']) , 'default', null, 'error');
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'view',
                        $deal['Deal']['slug']
                    ));
                }
            }
            //intially merge credit card validation array
            $this->Deal->validate = array_merge($this->Deal->validate, $this->Deal->validateCreditCard);
        }
        $this->request->data['Deal']['user_id'] = $this->Auth->user('id');
        // Checking payment settings enabled
        $payment_options = $this->Deal->getGatewayTypes('is_enable_for_buy_a_deal');
        // If 'handle like groupon' enabled, unset wallet. Since, all purchase should proceed through wallet first, coz it is compulsary.
        if (Configure::read('wallet.is_handle_wallet_as_in_groupon')) {
            unset($payment_options[ConstPaymentGateways::Wallet]);
        }
        if ($deal['Deal']['discounted_price'] != 0) {
            //credit card related fields
            if (!empty($payment_options[ConstPaymentGateways::CreditCard]) || !empty($payment_options[ConstPaymentGateways::AuthorizeNet])) {
                $gateway_options['cities'] = $this->Deal->Company->City->find('list', array(
                    'conditions' => array(
                        'City.is_approved =' => 1
                    ) ,
                    'fields' => array(
                        'City.name',
                        'City.name'
                    ) ,
                    'order' => array(
                        'City.name' => 'asc'
                    )
                ));
                $gateway_options['states'] = $this->Deal->Company->State->find('list', array(
                    'conditions' => array(
                        'State.is_approved =' => 1
                    ) ,
                    'fields' => array(
                        'State.code',
                        'State.name'
                    ) ,
                    'order' => array(
                        'State.name' => 'asc'
                    )
                ));
                $gateway_options['countries'] = $this->Deal->Company->Country->find('list', array(
                    'fields' => array(
                        'Country.iso2',
                        'Country.name'
                    ) ,
                    'conditions' => array(
                        'Country.iso2 != ' => '',
                    ) ,
                    'order' => array(
                        'Country.name' => 'asc'
                    ) ,
                ));
                $gateway_options['creditCardTypes'] = array(
                    'Visa' => __l('Visa') ,
                    'MasterCard' => __l('MasterCard') ,
                    'Discover' => __l('Discover') ,
                    'Amex' => __l('Amex')
                );
                if (empty($this->request->data['Deal']['payment_gateway_id'])) {
                    if (!empty($payment_options[ConstPaymentGateways::AuthorizeNet])) {
                        $this->request->data['Deal']['payment_gateway_id'] = ConstPaymentGateways::AuthorizeNet;
                    } elseif (!empty($payment_options[ConstPaymentGateways::CreditCard])) {
                        $this->request->data['Deal']['payment_gateway_id'] = ConstPaymentGateways::CreditCard;
                    }
                }
            } elseif (!empty($payment_options[ConstPaymentGateways::PayPalAuth]) && empty($this->request->data['Deal']['payment_gateway_id'])) {
                $this->request->data['Deal']['payment_gateway_id'] = ConstPaymentGateways::PayPalAuth;
            } elseif (!empty($payment_options[ConstPaymentGateways::Wallet]) && empty($this->request->data['Deal']['payment_gateway_id'])) {
                $this->request->data['Deal']['payment_gateway_id'] = ConstPaymentGateways::Wallet;
            }
        } else {
            $this->request->data['Deal']['payment_gateway_id'] = ConstPaymentGateways::Wallet;
        }
        $gateway_options['paymentGateways'] = $payment_options;
        if (!$this->Auth->user()) {
            unset($gateway_options['paymentGateways'][ConstPaymentGateways::Wallet]);
        } else {
            $userPaymentProfiles = $this->Deal->User->UserPaymentProfile->find('all', array(
                'fields' => array(
                    'UserPaymentProfile.masked_cc',
                    'UserPaymentProfile.cim_payment_profile_id',
                    'UserPaymentProfile.is_default'
                ) ,
                'conditions' => array(
                    'UserPaymentProfile.user_id' => $this->Auth->user('id')
                ) ,
            ));
            foreach($userPaymentProfiles as $userPaymentProfile) {
                $gateway_options['Paymentprofiles'][$userPaymentProfile['UserPaymentProfile']['cim_payment_profile_id']] = $userPaymentProfile['UserPaymentProfile']['masked_cc'];
                if (!empty($userPaymentProfile['UserPaymentProfile']['is_default'])) {
                    $this->request->data['Deal']['payment_profile_id'] = $userPaymentProfile['UserPaymentProfile']['cim_payment_profile_id'];
                }
            }
        }
        $states = $this->Deal->Company->State->find('list', array(
            'conditions' => array(
                'State.is_approved =' => 1
            ) ,
            'fields' => array(
                'State.code',
                'State.name'
            ) ,
            'order' => array(
                'State.name' => 'asc'
            )
        ));
        if (empty($gateway_options['Paymentprofiles'])) {
            $this->request->data['Deal']['is_show_new_card'] = 1;
        }
        $this->set('states', $states);
        $this->set('gateway_options', $gateway_options);
        $this->set('deal', $deal);
        $this->set('sub_deal_id', $sub_deal_id);
        $this->set('user', $user);
        $this->set('user_quantity', $user_quantity);
        $this->set('user_available_balance', $user_available_balance);
        $this->request->data['Deal']['cvv2Number'] = $this->request->data['Deal']['creditCardNumber'] = '';
        if (Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::Buyer) {
            $charities = $this->Deal->Charity->find('list', array(
                'conditions' => array(
                    'Charity.is_active =' => 1
                ) ,
                'order' => array(
                    'Charity.name' => 'asc'
                )
            ));
            $this->set(compact('charities'));
        }
    }
    
    //for new users or who have low balance amount or credit card payment or paypal auth
    public function process_user($deal)
    {
        //die("in process user");
        $this->loadModel('TempPaymentLog');
        $this->loadModel('EmailTemplate');
        $is_purchase_with_wallet_amount = 0;
        $this->Session->write('Auth.last_bought_deal_slug', $deal['Deal']['slug']);
        if (!empty($this->request->data)) {
            $total_deal_amount = $deal['Deal']['discounted_price'] * $this->request->data['Deal']['quantity'];
            $valid_user = true;
            //already registered users
            if ($this->Auth->user('id')) {
                //already logged in user
                $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
                $amount_needed = $total_deal_amount;
                //when wallet amount less than total amount check needed amount
                if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::Wallet) {
                    $valid_user = false;
                    $is_show_credit_card = 0;
                    $this->set('is_show_credit_card', $is_show_credit_card);
                    // <-- For iPhone App code
                    if ($this->RequestHandler->prefers('json')) {
                        $resonse = array(
                            'status' => 1,
                            'message' => __l('Purchase via wallet not possible as the total purchase amount exceeded your wallet balance.')
                        );
                        $this->view = 'Json';
                        $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
                    } else {
                        $this->Session->setFlash(__l('Purchase via wallet not possible as the total purchase amount exceeded your wallet balance.') , 'default', null, 'error');
                    }
                    // For iPhone App code -->
                    //$amount_needed = $total_deal_amount-$user_available_balance;
                    
                }
            } else {
                //new users register process
                $amount_needed = $total_deal_amount;
                $this->Deal->User->create();
                $this->Deal->User->set($this->request->data['User']);
                if ($this->Deal->User->validates()) {
                    $this->request->data['User']['is_active'] = 1;
                    $this->request->data['User']['is_email_confirmed'] = 1;
                    $this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['passwd']);
                    $this->request->data['User']['user_type_id'] = ConstUserTypes::User;
                    $this->request->data['User']['signup_ip'] = $this->RequestHandler->getClientIP();
                    $this->request->data['User']['dns'] = gethostbyaddr($this->RequestHandler->getClientIP());
                    if ($this->Deal->User->save($this->request->data['User'], false)) {
                        $user_id = $this->Deal->User->getLastInsertId();
                        $this->Deal->User->_createCimProfile($user_id);
                        $this->_sendWelcomeMail($user_id, $this->request->data['User']['email'], $this->request->data['User']['username']);
                        $this->request->data['UserProfile']['user_id'] = $user_id;
                        $this->Deal->User->UserProfile->create();
                        $this->Deal->User->UserProfile->save();
                        $this->Auth->login($this->request->data['User']);
                        $this->request->data['Deal']['user_id'] = $user_id;
                        // send to admin mail if is_admin_mail_after_register is true
                        if (Configure::read('user.is_admin_mail_after_register')) {
                            $email = $this->EmailTemplate->selectTemplate('New User Join');
                            $emailFindReplace = array(
                                '##SITE_LINK##' => Router::url('/', true) ,
                                '##USERNAME##' => $this->request->data['User']['username'],
                                '##SITE_NAME##' => Configure::read('site.name') ,
                                '##SIGNUP_IP##' => $this->RequestHandler->getClientIP() ,
                                '##EMAIL##' => $this->request->data['User']['email'],
                                '##CONTACT_URL##' => Router::url(array(
                                    'controller' => 'contacts',
                                    'action' => 'add',
                                    'city' => $this->request->params['named']['city'],
                                    'admin' => false
                                ) , true) ,
                                '##FROM_EMAIL##' => $this->Deal->User->changeFromEmail(($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from']) ,
                                '##SITE_LOGO##' => Router::url(array(
                                    'controller' => 'img',
                                    'action' => 'blue-theme',
                                    'logo-email.png',
                                    'admin' => false
                                ) , true) ,
                            );
                            // Send e-mail to users
                            $this->Email->from = ($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from'];
                            $this->Email->replyTo = ($email['reply_to'] == '##REPLY_TO_EMAIL##') ? Configure::read('EmailTemplate.reply_to_email') : $email['reply_to'];
                            $this->Email->to = Configure::read('site.contact_email');
                            $this->Email->subject = strtr($email['subject'], $emailFindReplace);
                            $this->Email->sendAs = ($email['is_html']) ? 'html' : 'text';
                            $this->Email->send(strtr($email['email_content'], $emailFindReplace));
                        }
                    }
                } else {
                    $valid_user = false;
                }
            }
            //payment process
            if ($valid_user) {
                //MOHAN - Stop ConstPaymentGateways::CreditCard) from being handled by _buyDeal
                /*if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard) {
                    $this->_buyDeal($this->request->data);
                } else*/ 
                if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::Wallet) {
                    $this->_buyDeal($this->request->data);
                } else {
                    //paypal process
                    //MOHAN - Bizzaro code - if it is ConstPaymentGateways::CreditCard is actually the CC through paypal and hence it is 
                    // reset it to ConstPaymentGateways::PayPalAuth - Probably good place to hook in code
                    if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard) {
                        //MOHAN $payment_gateway_id = ConstPaymentGateways::PayPalAuth; - don't overide the constant - retain it, we want to use it 
                        $payment_gateway_id = ConstPaymentGateways::CreditCard;
                    } elseif ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::AuthorizeNet) {
                        $payment_gateway_id = ConstPaymentGateways::AuthorizeNet;
                    } elseif ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::PagSeguro) {
                        $payment_gateway_id = ConstPaymentGateways::PagSeguro;
                    } else {
                        $payment_gateway_id = ConstPaymentGateways::PayPalAuth;
                    }
                    //MOHAN - retrieve the payment gateway information from the database
                    $paymentGateway = $this->Deal->User->Transaction->PaymentGateway->find('first', array(
                        'conditions' => array(
                            'PaymentGateway.id' => $payment_gateway_id,
                        ) ,
                        'contain' => array(
                            'PaymentGatewaySetting' => array(
                                'fields' => array(
                                    'PaymentGatewaySetting.key',
                                    'PaymentGatewaySetting.test_mode_value',
                                    'PaymentGatewaySetting.live_mode_value',
                                ) ,
                            ) ,
                        ) ,
                        'recursive' => 1
                    ));
                    $this->pageTitle.= sprintf(__l('Buy %s Deal') , $deal['Deal']['name']);
                    $this->set('gateway_name', $paymentGateway['PaymentGateway']['name']);
                    if (empty($paymentGateway)) {
                        throw new NotFoundException(__l('Invalid request'));
                    }
                    
                    $action = strtolower(str_replace(' ', '', $paymentGateway['PaymentGateway']['name']));
                    
                   
                    if ($paymentGateway['PaymentGateway']['name'] == 'CreditCard') {

                        if (Configure::read('wallet.is_handle_wallet_as_in_groupon')) {
                            $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
							if(!empty($user_available_balance) && $user_available_balance != '0.00'){
								$amount_needed = $amount_needed - $user_available_balance;
								$is_purchase_with_wallet_amount = 1;
							}
                        }
                        
                        //if more then one customer goes through payment at the same second - we could be screwed 
                        $refno = time();

                        //gateway options set
                        $gateway_options = array(
                            'refno' => $refno,
                            'notify_url' => Router::url('/', true) . 'deals/processpayment/cred',
                            'cancel_return' => Router::url('/', true) . 'deals/payment_cancel/' . $payment_gateway_id,
                            'return' => Router::url('/', true) . 'deals/payment_success/' . $payment_gateway_id . '/' . $this->request->data['Deal']['deal_id'],
                            'item_name' => $deal['Deal']['name'],
                            'currency_code' => 'MYR',
                            'amount' => $amount_needed,
                            'user_defined' => array(
                                'username' => $this->Auth->user('username') ,
                                'email' => $this->Auth->user('email') ,
                                'user_id' => $this->Auth->user('id') ,
                                'deal_id' => $this->request->data['Deal']['deal_id'],
                                'sub_deal_id' => (!empty($this->request->data['Deal']['sub_deal_id'])) ? $this->request->data['Deal']['sub_deal_id'] : '',
                                'is_gift' => $this->request->data['Deal']['is_gift'],
                                'quantity' => $this->request->data['Deal']['quantity'],
                                'payment_gateway_id' => $this->request->data['Deal']['payment_gateway_id'],
                                'is_purchase_with_wallet_amount' => $is_purchase_with_wallet_amount
                            ) 
                        );
                        //need to add the request_signature
                        $gateway_options['request_signature'] = $this->ipay88->generateRequestSignature($gateway_options);
                        $transaction_data['TempPaymentLog'] = array(
                            'trans_id' => $refno,
                            'payment_type' => 'Buy deal',
                            'user_id' => $this->Auth->user('id') ,
                            'deal_id' => $this->request->data['Deal']['deal_id'],
                            'is_gift' => $this->request->data['Deal']['is_gift'],
                            'quantity' => $this->request->data['Deal']['quantity'],
                            'payment_gateway_id' => $this->request->data['Deal']['payment_gateway_id'],
                            'gift_to' => !empty($this->request->data['Deal']['gift_to']) ? $this->request->data['Deal']['gift_to'] : '',
                            'gift_from' => !empty($this->request->data['Deal']['gift_from']) ? $this->request->data['Deal']['gift_from'] : '',
                            'gift_email' => !empty($this->request->data['Deal']['gift_email']) ? $this->request->data['Deal']['gift_email'] : '',
                            'ip' => $this->RequestHandler->getClientIP() ,
                            'amount_needed' => $amount_needed,
                            'currency_code' => Configure::read('paypal.currency_code') ,
                            'message' => !empty($this->request->data['Deal']['message']) ? $this->request->data['Deal']['message'] : '',
                        );
                        if (!empty($this->request->data['Deal']['sub_deal_id'])) {
                            $transaction_data['TempPaymentLog']['sub_deal_id'] = $this->request->data['Deal']['sub_deal_id'];
                        }
                        // For affiliates ( //
                        $cookie_value = $this->Cookie->read('referrer');
                        $refer_id = (!empty($cookie_value)) ? $cookie_value['refer_id'] : null;
                        if (!empty($refer_id)) {
                            $transaction_data['TempPaymentLog']['referred_user_id'] = $refer_id;
                        }
                        // ) affiliates //
                        $this->TempPaymentLog->save($transaction_data);
                        //	$this->Session->write('transaction_data',$transaction_data);
                        $this->set('gateway_options', $gateway_options);
                        
                    }
                    else if ($paymentGateway['PaymentGateway']['name'] == 'PayPal') {
                        Configure::write('paypal.is_testmode', $paymentGateway['PaymentGateway']['is_test_mode']);
                        if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                            foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                                if ($paymentGatewaySetting['key'] == 'payee_account') {
                                    Configure::write('paypal.account', $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value']);
                                }
                                if ($paymentGatewaySetting['key'] == 'receiver_emails') {
                                    $this->Paypal->paypal_receiver_emails = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                                }
                            }
                        }
                        // If enabled, purchase amount is first taken with amount in wallet and then passed to CreditCard //
                        if (Configure::read('wallet.is_handle_wallet_as_in_groupon')) {
                            $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
							if(!empty($user_available_balance) && $user_available_balance != '0.00'){
								$amount_needed = $amount_needed - $user_available_balance;
								$is_purchase_with_wallet_amount = 1;
							}
                        }
                        $city = $this->Deal->City->find('first', array(
                            'conditions' => array(
                                'City.slug' => $this->request->params['named']['city']
                            ) ,
                            'fields' => array(
                                'City.id'
                            ) ,
                            'recursive' => - 1
                        ));
                        $cmd = '_xclick';
                        $cookie_value = $this->Cookie->read('referrer');
                        // Currency Conversion Process //
                        $get_conversion = $this->_convertAmount($amount_needed);
                        //gateway options set
                        $gateway_options = array(
                            'cmd' => $cmd,
                            'notify_url' => Router::url('/', true) . 'deals/processpayment/paypal',
                            'cancel_return' => Router::url('/', true) . 'deals/payment_cancel/' . $payment_gateway_id,
                            'return' => Router::url('/', true) . 'deals/payment_success/' . $payment_gateway_id . '/' . $this->request->data['Deal']['deal_id'],
                            'item_name' => $deal['Deal']['name'],
                            'currency_code' => $get_conversion['currency_code'],
                            'amount' => $get_conversion['amount'],
                            'user_defined' => array(
                                'user_id' => $this->Auth->user('id') ,
                                'deal_id' => $this->request->data['Deal']['deal_id'],
                                'sub_deal_id' => (!empty($this->request->data['Deal']['sub_deal_id'])) ? $this->request->data['Deal']['sub_deal_id'] : '',
                                'is_gift' => $this->request->data['Deal']['is_gift'],
                                'quantity' => $this->request->data['Deal']['quantity'],
                                'payment_gateway_id' => $this->request->data['Deal']['payment_gateway_id'],
                                'is_purchase_with_wallet_amount' => $is_purchase_with_wallet_amount
                            ) ,
                            'g_defined' => array(
                                'gift_to' => !empty($this->request->data['Deal']['gift_to']) ? $this->request->data['Deal']['gift_to'] : '',
                                'gift_from' => !empty($this->request->data['Deal']['gift_from']) ? $this->request->data['Deal']['gift_from'] : '',
                                'gift_email' => !empty($this->request->data['Deal']['gift_email']) ? $this->request->data['Deal']['gift_email'] : '',
                            ) ,
                            'system_defined' => array(
                                'ip' => $this->RequestHandler->getClientIP() ,
                                'amount_needed' => $get_conversion['amount'],
                                'currency_code' => $get_conversion['currency_code'],
                            ) ,
                            'm_defined' => array(
                                'message' => !empty($this->request->data['Deal']['message']) ? $this->request->data['Deal']['message'] : '',
                            ) ,
                            'r_defined' => array(
                                'refer_id' => (!empty($cookie_value['refer_id'])) ? $cookie_value['refer_id'] : null,
                                'city_id' => $city['City']['id'],
                                'latitude' => $_COOKIE['geoip_latitude'],
                                'longitude' => $_COOKIE['geoip_longitude'],
                                'charity_id' => (!empty($this->request->data['Deal']['charity_id']) ? $this->request->data['Deal']['charity_id'] : ''),
                                'original_amount_needed' => $amount_needed,
                            )
                        );
                        //for paypal auth
                        if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard) {
                            $gateway_options['paymentaction'] = 'authorization';
                        }
                        $this->set('gateway_options', $gateway_options);
                    } elseif ($paymentGateway['PaymentGateway']['name'] == 'AuthorizeNet') {
                        // If enabled, purchase amount is first taken with amount in wallet and then passed to CreditCard //
                        if (Configure::read('wallet.is_handle_wallet_as_in_groupon')) {
                            $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
                            $amount_needed1 = $amount_needed - $user_available_balance;
                            $is_purchase_with_wallet_amount = 1;
                        }
                        if (Configure::read('wallet.is_handle_wallet_as_in_groupon')) {
                            $this->request->data['Deal']['amount'] = $amount_needed1;
                        } else {
                            $this->request->data['Deal']['amount'] = $amount_needed;
                        }
                        $this->request->data['Deal']['amount'] = $this->Deal->_convertAuthorizeAmount($this->request->data['Deal']['amount']);
                        $user = $this->Deal->User->find('first', array(
                            'conditions' => array(
                                'User.id' => $this->Auth->user('id')
                            ) ,
                            'fields' => array(
                                'User.id',
                                'User.cim_profile_id'
                            )
                        ));
                        if (!empty($this->request->data['Deal']['creditCardNumber'])) {
                            //create payment profile
                            $data = $this->request->data['Deal'];
                            $data['expirationDate'] = $this->request->data['Deal']['expDateYear']['year'] . '-' . $this->request->data['Deal']['expDateMonth']['month'];
                            $data['customerProfileId'] = $user['User']['cim_profile_id'];
                            $payment_profile_id = $this->Deal->User->_createCimPaymentProfile($data);
                            if (is_array($payment_profile_id) && !empty($payment_profile_id['payment_profile_id']) && !empty($payment_profile_id['masked_cc'])) {
                                $payment['UserPaymentProfile']['user_id'] = $this->Auth->user('id');
                                $payment['UserPaymentProfile']['cim_payment_profile_id'] = $payment_profile_id['payment_profile_id'];
                                $payment['UserPaymentProfile']['masked_cc'] = $payment_profile_id['masked_cc'];
                                $payment['UserPaymentProfile']['is_default'] = 0;
                                $this->Deal->User->UserPaymentProfile->save($payment);
                                $this->request->data['Deal']['payment_profile_id'] = $payment_profile_id['payment_profile_id'];
                            } else {
                                $this->Session->setFlash(sprintf(__l('Gateway error: %s <br>Note: Due to security reasons, error message from gateway may not be verbose. Please double check your card number, security number and address details. Also, check if you have enough balance in your card.') , $payment_profile_id['message']) , 'default', null, 'error');
                                $this->redirect(array(
                                    'controller' => 'deals',
                                    'action' => 'index'
                                ));
                            }
                        }
                        if (!empty($this->request->data['Deal']['payment_profile_id'])) {
                            $data['customerProfileId'] = $user['User']['cim_profile_id'];
                            $data['customerPaymentProfileId'] = $this->request->data['Deal']['payment_profile_id'];
                            $data['amount'] = $this->request->data['Deal']['amount'];
                            $data['quantity'] = $this->request->data['Deal']['quantity'];
                            $data['deal_id'] = $this->request->data['Deal']['deal_id'];
                            $tmp_deal = $this->Deal->find('first', array(
                                'conditions' => array(
                                    'Deal.id' => $this->request->data['Deal']['deal_id'],
                                    'Deal.deal_status_id' => array(
                                        ConstDealStatus::Open,
                                        ConstDealStatus::Tipped
                                    )
                                ) ,
                                'recursive' => - 1
                            ));
                            if ($tmp_deal['Deal']['min_limit'] <= ($tmp_deal['Deal']['deal_user_count'] + $this->request->data['Deal']['quantity'])) {
                                // is going to tipped or already tipped. so no need to authorize the transaction
                                $type = 'profileTransAuthCapture';
                            } else {
                                $type = 'profileTransAuthOnly';
                            }
                            $response = $this->Deal->User->_createCustomerProfileTransaction($data, $type);
                            if (!empty($response['cim_approval_code'])) {
                                if (!empty($response['cim_approval_code'])) {
                                    $deal_user['DealUser']['cim_approval_code'] = $response['cim_approval_code'];
                                }
                                if (!empty($response['cim_transaction_id'])) {
                                    $deal_user['DealUser']['cim_transaction_id'] = $response['cim_transaction_id'];
                                }
                                $city = $this->Deal->City->find('first', array(
                                    'conditions' => array(
                                        'City.slug' => $this->request->params['named']['city']
                                    ) ,
                                    'fields' => array(
                                        'City.id'
                                    ) ,
                                    'recursive' => - 1
                                ));
                                $deal_user['DealUser']['city_id'] = $city['City']['id'];
                                $deal_user['DealUser']['latitude'] = $_COOKIE['geoip_latitude'];
                                $deal_user['DealUser']['longitude'] = $_COOKIE['geoip_longitude'];
                                $deal_user['DealUser']['quantity'] = $this->request->data['Deal']['quantity'];
                                $deal_user['DealUser']['deal_id'] = $this->request->data['Deal']['deal_id'];
                                if (!empty($this->request->data['Deal']['sub_deal_id'])) {
                                    $deal_user['DealUser']['sub_deal_id'] = $this->request->data['Deal']['sub_deal_id'];
                                }
                                $deal_user['DealUser']['is_paid'] = (!empty($response['capture'])) ? 1 : 0;
                                $deal_user['DealUser']['is_gift'] = $this->request->data['Deal']['is_gift'];
                                $deal_user['DealUser']['user_id'] = $this->Auth->user('id');
                                $deal_user['DealUser']['discount_amount'] = $amount_needed;
                                $deal_user['DealUser']['payment_gateway_id'] = !empty($this->request->data['Deal']['payment_gateway_id']) ? $this->request->data['Deal']['payment_gateway_id'] : ConstPaymentGateways::AuthorizeNet;
                                $deal_user['DealUser']['payment_profile_id'] = $this->request->data['Deal']['payment_profile_id'];
                                $coupon_code = $this->_uuid();
                                $deal_user['DealUser']['coupon_code'] = $coupon_code;
                                if ($this->request->data['Deal']['is_gift']) {
                                    $deal_user['DealUser']['gift_email'] = $this->request->data['Deal']['gift_email'];
                                    $deal_user['DealUser']['message'] = $this->request->data['Deal']['message'];
                                    $deal_user['DealUser']['gift_to'] = $this->request->data['Deal']['gift_to'];
                                    $deal_user['DealUser']['gift_from'] = $this->request->data['Deal']['gift_from'];
                                }
                                // For affiliates ( //
                                $cookie_value = $this->Cookie->read('referrer');
                                $refer_id = (!empty($cookie_value)) ? $cookie_value['refer_id'] : null;
                                if (!empty($refer_id)) {
                                    $deal_user['DealUser']['referred_by_user_id'] = $refer_id;
                                }
                                // ) affiliates //
                                $this->Deal->DealUser->create();
                                $this->Deal->DealUser->set($deal_user);
                                if ($this->Deal->DealUser->save($deal_user)) {
                                    // For affiliates ( //
                                    $cookie_value = $this->Cookie->read('referrer');
                                    if (!empty($cookie_value)) {
                                        $this->Cookie->delete('referrer'); // Delete referer cookie
                                        
                                    }
                                    // ) affiliates //
                                    $last_inserted_id = $this->Deal->DealUser->getLastInsertId();
									if(!empty($this->request->data['Deal']['charity_id'])){
										$this->_set_charity_detail($this->request->data['Deal']['charity_id'], $last_inserted_id);
									}
                                    $data_authorize_docapture_log['AuthorizenetDocaptureLog']['id'] = $response['pr_authorize_id'];
                                    $data_authorize_docapture_log['AuthorizenetDocaptureLog']['deal_user_id'] = $last_inserted_id;
                                    $get_conversion = $this->getAuthorizeConversionCurrency();
                                    if (!empty($get_conversion)) {
                                        $data_authorize_docapture_log['AuthorizenetDocaptureLog']['currency_id'] = $get_conversion['CurrencyConversion']['currency_id'];
                                        $data_authorize_docapture_log['AuthorizenetDocaptureLog']['converted_currency_id'] = $get_conversion['CurrencyConversion']['converted_currency_id'];
                                        $data_authorize_docapture_log['AuthorizenetDocaptureLog']['original_amount'] = $this->request->data['Deal']['amount'];
                                        $data_authorize_docapture_log['AuthorizenetDocaptureLog']['rate'] = $get_conversion['CurrencyConversion']['rate'];
                                    }
                                    if (empty($response['capture'])) {									
                                        $data_authorize_docapture_log['AuthorizenetDocaptureLog']['payment_status'] = 'Pending';
									} else {
                                        $data_authorize_docapture_log['AuthorizenetDocaptureLog']['payment_status'] = 'Completed';
									}									
                                    $this->Deal->DealUser->AuthorizenetDocaptureLog->save($data_authorize_docapture_log);
                                    if (!empty($response['capture'])) {
                                        $deal_user['DealUser']['is_gift'] = (!empty($this->request->data['Deal']['is_gift'])) ? 1 : 0;
                                        $deal_user['DealUser']['id'] = $last_inserted_id;
                                        if (!empty($get_conversion)) {
                                            $deal_user['DealUser']['currency_id'] = $get_conversion['CurrencyConversion']['currency_id'];
                                            $deal_user['DealUser']['converted_currency_id'] = $get_conversion['CurrencyConversion']['converted_currency_id'];
                                            $deal_user['DealUser']['authorize_amt'] = $this->request->data['Deal']['amount'];
                                            $deal_user['DealUser']['rate'] = $get_conversion['CurrencyConversion']['rate'];
                                        }
                                        $this->Deal->_updateTransaction($deal_user['DealUser']);
                                    }
                                    $deal_user_coupons = array();
                                    $coupon_code = $this->_uuid();
                                    for ($i = 1; $i <= $deal_user['DealUser']['quantity']; $i++) {
                                        $this->Deal->DealUser->DealUserCoupon->create();
                                        $deal_user_coupons['deal_user_id'] = $last_inserted_id;
                                        $deal_user_coupons['coupon_code'] = $coupon_code . '-' . $i;
                                        $deal_user_coupons['unique_coupon_code'] = $this->_unum();
                                        $this->Deal->DealUser->DealUserCoupon->save($deal_user_coupons);
                                    }
                                    // If enabled, and after purchase, deduct partial amount from wallet //
                                    if (Configure::read('wallet.is_handle_wallet_as_in_groupon') && (!empty($is_purchase_with_wallet_amount))) {
                                        // Deduct amount ( zero will be updated ) //
                                        $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
                                        $this->Deal->User->updateAll(array(
                                            'User.available_balance_amount' => 'User.available_balance_amount -' . $user_available_balance,
                                        ) , array(
                                            'User.id' => $deal_user['DealUser']['user_id']
                                        ));
                                        // Update transaction, This is firs transaction, to notify user that partial amount taken from wallet. Second transaction will be updated after deal gets tipped.//
                                        if (!empty($user_available_balance) && $user_available_balance != '0.00') {
                                            $amount_taken_from_wallet = $total_deal_amount - $user_available_balance;
                                            $transaction['Transaction']['user_id'] = $deal_user['DealUser']['user_id'];
                                            $transaction['Transaction']['foreign_id'] = $last_inserted_id;
                                            $transaction['Transaction']['class'] = 'DealUser';
                                            $transaction['Transaction']['amount'] = $amount_taken_from_wallet;
                                            $transaction['Transaction']['transaction_type_id'] = ConstTransactionTypes::PartallyAmountTakenForDealPurchase;
                                            $transaction['Transaction']['payment_gateway_id'] = ConstPaymentGateways::Wallet;
                                            $this->Deal->User->Transaction->log($transaction);
                                        }
                                    }
                                    $last_inserted_id = $this->Deal->DealUser->getLastInsertId();
                                    $this->_dealPurchaseViaAuthorizeNet($this->request->data, $last_inserted_id);
                                } else {
                                    if ($this->RequestHandler->prefers('json')) {
										$resonse = array(
											'status' => 1,
											'message' => __l('Payment failed. Please try again.')
										);
										$this->view = 'Json';
										$this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
									}
									else{
										$this->Session->setFlash(__l('Payment failed. Please try again.') , 'default', null, 'error');
										$this->redirect(array(
											'controller' => 'deals',
											'action' => 'index'
										));
									}
                                }
                            } else {
                                if ($this->RequestHandler->prefers('json')) {
									$resonse = array(
										'status' => 1,
										'message' => sprintf(__l('Gateway error: %s <br>Note: Due to security reasons, error message from gateway may not be verbose. Please double check your card number, security number and address details. Also, check if you have enough balance in your card.') , $response['message'])
									);
									$this->view = 'Json';
									$this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
								}
								else{
									$this->Session->setFlash(sprintf(__l('Gateway error: %s <br>Note: Due to security reasons, error message from gateway may not be verbose. Please double check your card number, security number and address details. Also, check if you have enough balance in your card.') , $response['message']) , 'default', null, 'error');
									$this->redirect(array(
										'controller' => 'deals',
										'action' => 'index'
									));
								}
                            }
                        } else {
                            if ($this->RequestHandler->prefers('json')) {
									$resonse = array(
										'status' => 1,
										'message' => __l('Credit card could not be updated. Please, try again.')
									);
									$this->view = 'Json';
									$this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
								}
								else{
									$this->Session->setFlash(__l('Credit card could not be updated. Please, try again.') , 'default', null, 'error');
									$this->redirect(array(
										'controller' => 'deals',
										'action' => 'index'
									));
								}
                        }
                    } else if ($paymentGateway['PaymentGateway']['name'] == 'PagSeguro') {
                        Configure::write('PagSeguro.is_testmode', $paymentGateway['PaymentGateway']['is_test_mode']);
                        if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                            foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                                if ($paymentGatewaySetting['key'] == 'payee_account') {
                                    $email = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                                }
                                if ($paymentGatewaySetting['key'] == 'token') {
                                    $token = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                                }
                            }
                        }
                        //gateway options set
                        $ref = time();
                        $gateway_options['init'] = array(
                            'pagseguro' => array( // Array com informações pertinentes ao pagseguro
                                'email' => $email,
                                'token' => $token,
                                'type' => 'CBR', // Obrigatório passagem para pagseguro:tipo
                                'reference' => $ref, // Obrigatório passagem para pagseguro:ref_transacao
                                'freight_type' => 'EN', // Obrigatório passagem para pagseguro:tipo_frete
                                'theme' => 1, // Opcional Este parametro aceita valores de 1 a 5, seu efeito é a troca dos botões padrões do pagseguro
                                'currency' => 'BRL', // Obrigatório passagem para pagseguro:moeda,
                                'extra' => 0
                                // Um valor extra que você queira adicionar no valor total da venda, obs este valor pode ser negativo
                                
                            ) ,
                            'definitions' => array( // Array com informações para manusei das informações
                                'currency_type' => 'dolar', // Especifica qual o tipo de separador de decimais, suportados (dolar, real)
                                'weight_type' => 'kg', // Especifica qual a medida utilizada para peso, suportados (kg, g)
                                'encode' => 'utf-8'
                                // Especifica o encode não implementado
                                
                            ) ,
                            'format' => array(
                                'item_id' => '0000' . $deal['Deal']['id'],
                                'item_descr' => 'Bought Deal', //used to differ return array fron payment
                                'item_quant' => $this->request->data['Deal']['quantity'],
                                'item_valor' => $amount_needed,
                                'item_frete' => '0',
                                'item_peso' => '20',
                            ) ,
                        );
                        $transaction_data['TempPaymentLog'] = array(
                            'trans_id' => $ref,
                            'payment_type' => 'Buy deal',
                            'user_id' => $this->Auth->user('id') ,
                            'deal_id' => $this->request->data['Deal']['deal_id'],
                            'is_gift' => $this->request->data['Deal']['is_gift'],
                            'quantity' => $this->request->data['Deal']['quantity'],
                            'payment_gateway_id' => $this->request->data['Deal']['payment_gateway_id'],
                            'gift_to' => !empty($this->request->data['Deal']['gift_to']) ? $this->request->data['Deal']['gift_to'] : '',
                            'gift_from' => !empty($this->request->data['Deal']['gift_from']) ? $this->request->data['Deal']['gift_from'] : '',
                            'gift_email' => !empty($this->request->data['Deal']['gift_email']) ? $this->request->data['Deal']['gift_email'] : '',
                            'ip' => $this->RequestHandler->getClientIP() ,
                            'amount_needed' => $amount_needed,
                            'currency_code' => Configure::read('paypal.currency_code') ,
                            'message' => !empty($this->request->data['Deal']['message']) ? $this->request->data['Deal']['message'] : '',
                        );
                        if (!empty($this->request->data['Deal']['sub_deal_id'])) {
                            $transaction_data['TempPaymentLog']['sub_deal_id'] = $this->request->data['Deal']['sub_deal_id'];
                        }
                        // For affiliates ( //
                        $cookie_value = $this->Cookie->read('referrer');
                        $refer_id = (!empty($cookie_value)) ? $cookie_value['refer_id'] : null;
                        if (!empty($refer_id)) {
                            $transaction_data['TempPaymentLog']['referred_user_id'] = $refer_id;
                        }
                        // ) affiliates //
                        $this->TempPaymentLog->save($transaction_data);
                        //	$this->Session->write('transaction_data',$transaction_data);
                        $this->set('gateway_options', $gateway_options);
                    }
                    $this->set('action', $action);
                    $this->set('amount', $amount_needed);
                    $this->set('deal', $deal);
                    $this->render('do_payment');
                }
            }
        }
    }
    public function _dealPurchaseViaAuthorizeNet($data, $last_inserted_id)
    {
        $this->loadModel('EmailTemplate');
		$sub_deal_conditions = array();
        if (!empty($data)) {
            $gateways = $this->Deal->User->Transaction->PaymentGateway->find('first', array(
                'conditions' => array(
                    'PaymentGateway.id' => ConstPaymentGateways::AuthorizeNet
                ) ,
                'recursive' => - 1
            ));
            //Process for deals pay
            $deal_id = $data['Deal']['deal_id'];
            // Subdeal: Adding Sub deal conditions //
            if (!empty($data['Deal']['sub_deal_id'])) {
                $sub_deal_conditions = array(
                    'SubDeal.id' => $data['Deal']['sub_deal_id']
                );
            }
            $deal = $this->Deal->find('first', array(
                'conditions' => array(
                    'Deal.id' => $data['Deal']['deal_id'],
                    'Deal.deal_status_id' => array(
                        ConstDealStatus::Open,
                        ConstDealStatus::Tipped
                    )
                ) ,
                'contain' => array(
                    'DealStatus' => array(
                        'fields' => array(
                            'DealStatus.name',
                        )
                    ) ,
                    'SubDeal' => array(
                        'conditions' => $sub_deal_conditions
                    ) ,
                    'Company' => array(
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
                    )
                ) ,
                'recursive' => 2
            ));
            if (empty($deal)) {
                throw new NotFoundException(__l('Invalid request'));
            } else {
                $deal_user['DealUser']['quantity'] = $data['Deal']['quantity'];
                if (!empty($data['Deal']['is_gift'])) {
                    $deal_user['DealUser']['gift_to'] = $data['Deal']['gift_to'];
                }
                $total_deal_amount = $data['Deal']['amount'];
                //in paypal process we will not get Auth
                $user = $this->Deal->User->find('first', array(
                    'conditions' => array(
                        'User.id' => $this->Auth->user('id')
                    ) ,
                    'fields' => array(
                        'User.available_balance_amount',
                        'User.referred_by_user_id',
                        'User.username',
                        'User.email',
                        'User.created',
                        'User.id'
                    ) ,
                    'recursive' => - 1
                ));
                $this->Deal->updateAll(array(
                    'Deal.deal_user_count' => 'Deal.deal_user_count +' . $data['Deal']['quantity'],
                ) , array(
                    'Deal.id' => $deal_id
                ));
                //increasing deal_user_count for sub deal //
                if (!empty($deal['SubDeal'][0])) {
					$subdeal = $deal['SubDeal'][0];
                    $this->Deal->updateAll(array(
                        'Deal.deal_user_count' => 'Deal.deal_user_count +' . $data['Deal']['quantity'],
                    ) , array(
                        'Deal.id' => $subdeal['id']
                    ));
                }
                //update deal is on
                if ($deal['Deal']['deal_status_id'] == ConstDealStatus::Open) {
                    $db = $this->Deal->getDataSource();
                    $this->Deal->updateAll(array(
                        'Deal.deal_status_id' => ConstDealStatus::Tipped,
                        'Deal.deal_tipped_time' => '\'' . date('Y-m-d H:i:s') . '\''
                    ) , array(
                        'Deal.deal_status_id' => ConstDealStatus::Open,
                        'Deal.deal_user_count >=' => $db->expression('Deal.min_limit') ,
                        'Deal.id' => $deal_id
                    ));
                    $this->Deal->processDealStatus($deal_id, $last_inserted_id = null);
                } else {
                    //send coupon mail to users or close the deal
                    $this->Deal->processDealStatus($deal_id, $last_inserted_id);
                }
                //pay to referer
                $referred_by_user_id = $user['User']['referred_by_user_id'];
                if ((Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer) && !empty($referred_by_user_id)) {
                    $this->_pay_to_referrer($deal_id, $user['User']);
                }
                //deal on end
                $company_address = ($deal['Company']['address1']) ? $deal['Company']['address1'] : '';
                $company_address.= ($deal['Company']['address2']) ? ', ' . $deal['Company']['address2'] : '';
                $company_address.= !empty($deal['Company']['City']['name']) ? ', ' . $deal['Company']['City']['name'] : '';
                $company_address.= !empty($deal['Company']['State']['name']) ? ', ' . $deal['Company']['State']['name'] : '';
                $company_address.= !empty($deal['Company']['Country']['name']) ? ', ' . $deal['Company']['Country']['name'] : '';
                $company_address.= '.';
                $language_code = $this->Deal->getUserLanguageIso($this->Auth->user('id'));
                $email_message = $this->EmailTemplate->selectTemplate('Deal Bought', $language_code);
                $emailFindReplace = array(
                    '##FROM_EMAIL##' => $this->Deal->changeFromEmail(($email_message['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email_message['from']) ,
                    '##SITE_NAME##' => Configure::read('site.name') ,
                    '##USERNAME##' => $user['User']['username'],
                    '##DEAL_TITLE##' => $deal['Deal']['name'] . (!empty($subdeal['name']) ? ' - ' . $subdeal['name'] : '') ,
                    '##DEAL_AMOUNT##' => Configure::read('site.currency') . $total_deal_amount,
                    '##SITE_LINK##' => Router::url('/', true) ,
                    '##QUANTITY##' => $deal_user['DealUser']['quantity'],
                    '##PURCHASE_ON##' => strftime(Configure::read('site.datetime.format')) ,
                    '##DEAL_STATUS##' => $deal['DealStatus']['name'],
                    '##COMPANY_NAME##' => $deal['Company']['name'],
                    '##COMPANY_ADDRESS##' => ($company_address) ? $company_address : '',
                    '##CONTACT_URL##' => Router::url(array(
                        'controller' => 'contacts',
                        'action' => 'add',
                        'city' => $this->request->params['named']['city'],
                        'admin' => false
                    ) , true) ,
                    '##GIFT_RECEIVER##' => !empty($deal_user['DealUser']['gift_to']) ? $deal_user['DealUser']['gift_to'] : '',
                    '##SITE_LOGO##' => Router::url(array(
                        'controller' => 'img',
                        'action' => 'blue-theme',
                        'logo-email.png',
                        'admin' => false
                    ) , true) ,
                );
                $this->_sendMail($emailFindReplace, $email_message, $user['User']['email']);
                $this->Session->setFlash(__l('You have bought a deal sucessfully.') , 'default', null, 'success');
                $get_updated_status = $this->Deal->find('first', array(
                    'conditions' => array(
                        'Deal.id' => $deal_id
                    ) ,
                    'recursive' => - 1
                ));
                if ($this->RequestHandler->prefers('json')) {
                    $resonse = array(
                        'status' => 0,
                        'message' => __l('Success')
                    );
                    $this->view = 'Json';
                    $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
                } else {
                    if (Configure::read('Deal.invite_after_deal_add') && $get_updated_status['Deal']['deal_status_id'] != ConstDealStatus::Closed) {
                        $this->redirect(array(
                            'controller' => 'user_friends',
                            'action' => 'deal_invite',
                            'type' => 'deal',
                            'deal' => $deal['Deal']['slug']
                        ));
                    } else {
                        $this->redirect(array(
                            'controller' => 'users',
                            'action' => 'my_stuff#My_Purchases'
                        ));
                    }
                }
            }
        } else {
            $this->Session->setFlash(__l('Payment failed.Please try again.') , 'default', null, 'error');
            $this->redirect(array(
                'controller' => 'deals',
                'action' => 'index'
            ));
        }
    }
    //send welcome mail for new user
    public function _sendWelcomeMail($user_id, $user_email, $username)
    {
        $email = $this->EmailTemplate->selectTemplate('Welcome Email');
        $emailFindReplace = array(
            '##FROM_EMAIL##' => $this->Deal->changeFromEmail(($email['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email['from']) ,
            '##SITE_LINK##' => Router::url('/', true) ,
            '##SITE_NAME##' => Configure::read('site.name') ,
            '##USERNAME##' => $username,
            '##SUPPORT_EMAIL##' => Configure::read('site.contact_email') ,
            '##SITE_URL##' => Router::url('/', true) ,
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
        $this->Email->to = $user_email;
        $this->Email->subject = strtr($email['subject'], $emailFindReplace);
        $this->Email->sendAs = ($email['is_html']) ? 'html' : 'text';
        $this->Email->send(strtr($email['email_content'], $emailFindReplace));
    }
    
    public function processpayment($gateway_name, $return_details = null)
    {
        
        $this->loadModel('TempPaymentLog');
        //paypal ipn
        $return_details = $_REQUEST;
        //MOHAN - we highjack the pagseguro flow cause its closest to what we want
        $gateway = array(
            'paypal' => ConstPaymentGateways::PayPalAuth,
            'creditcard' => ConstPaymentGateways::PagSeguro
        );

        //$gateway['paypal'] = ConstPaymentGateways::PayPalAuth;
        $gateway_id = (!empty($gateway[$gateway_name])) ? $gateway[$gateway_name] : 0;
        $paymentGateway = $this->Deal->User->Transaction->PaymentGateway->find('first', array(
            'conditions' => array(
                'PaymentGateway.id' => $gateway_id
            ) ,
            'contain' => array(
                'PaymentGatewaySetting' => array(
                    'fields' => array(
                        'PaymentGatewaySetting.key',
                        'PaymentGatewaySetting.test_mode_value',
                        'PaymentGatewaySetting.live_mode_value',
                    )
                )
            ) ,
            'recursive' => 1
        ));

        
        switch ($gateway_name) {
         case 'creditcard':
             /*if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                    if ($paymentGatewaySetting['key'] == 'payee_account') {
                        $email = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                    }
                    if ($paymentGatewaySetting['key'] == 'token') {
                        $token = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                    }
                }
            }*/
                    

            $post_array = $_REQUEST;
            if (!empty($post_array) && $post_array['RefNo']) {
                $temp_ary = $this->TempPaymentLog->find('first', array(
                    'conditions' => array(
                        'TempPaymentLog.trans_id' => $post_array['RefNo']
                    )
                ));
                $transaction_data = $temp_ary['TempPaymentLog'];

            } else {
                $this->Session->setFlash(__l('Error in payment.') , 'default', null, 'error');
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action' => 'index',
                    'admin' => false
                ));
            }

            if (empty($transaction_data['deal_id'])) {
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action' => 'index',
                    'admin' => false
                ));
            }
            
       
            $allow_to_process = 1;
            $verified = 0;
            //$pagseguro_data = $return_details;
            $result = $this->ipay88->validateResponseSignature($post_array);
            if ($result == 1) {
                $verified = 1;
                //$result = $this->PagSeguro->getDataPayment();
                $log_data = array_merge($return_details, $transaction_data);
                //$pagseguro_transaction_log_id = $this->Deal->DealUser->PagseguroTransactionLog->logPagSeguroTransactions($log_data);
            } else {
                $verified = 0;
                $log_data = array_merge($return_details, $transaction_data);
                //$pagseguro_transaction_log_id = $this->Deal->DealUser->PagseguroTransactionLog->logPagSeguroTransactions($log_data);
            }
            
            $cc_transaction_log_id = $this->ipay88->logTransaction($post_array, $transaction_data);          
            
            if ($transaction_data['payment_type'] == 'Buy deal' && $verified == 1) {
                
                $conditions = array();
                $conditions['Deal.id'] = $transaction_data['deal_id'];
                if (!empty($transaction_data['sub_deal_id'])) {
                    $conditions['Deal.id'] = $sub_deal_id;
                }
                $quantity = $transaction_data['quantity'];
                $paid_amount = $transaction_data['amount_needed'];
                $payer_user_id = $transaction_data['user_id'];
                $get_deal = $this->Deal->find('first', array(
                    'conditions' => $conditions,
                    'recursive' => - 1
                ));
                $get_user = $this->Deal->User->find('first', array(
                    'conditions' => array(
                        'User.id' => $payer_user_id
                    ) ,
                    'recursive' => - 1
                ));
                //print_r($get_deal);
                //die("me here2");
                if (!empty($get_deal) && !empty($allow_to_process)) {
                    if (!empty($verified)) {
                        $id = $transaction_data['user_id'];
                        //add amount to wallet for normal paypal
                        $data['Transaction']['user_id'] = $id;
                        $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                        $data['Transaction']['class'] = 'SecondUser';
                        $data['Transaction']['amount'] = $paid_amount;
                        $data['Transaction']['payment_gateway_id'] = $paymentGateway['PaymentGateway']['id'];
                        $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AddedToWallet;
                        $transaction_id = $this->Deal->User->Transaction->log($data);
                        if (!empty($transaction_id)) {
                            $transaction_id = $transaction_id;
                            $this->Deal->User->updateAll(array(
                                'User.available_balance_amount' => 'User.available_balance_amount +' . $paid_amount,
                            ) , array(
                                'User.id' => $id
                            ));
                        }
                        //buy deal
                        $deal_data['Deal']['deal_id'] = $transaction_data['deal_id'];
                        $deal_data['Deal']['quantity'] = $transaction_data['quantity'];
                        $deal_data['Deal']['is_gift'] = $transaction_data['is_gift'];
                        $deal_data['Deal']['gift_to'] = $transaction_data['gift_to'];
                        $deal_data['Deal']['gift_from'] = $transaction_data['gift_from'];
                        $deal_data['Deal']['gift_email'] = $transaction_data['gift_email'];
                        $deal_data['Deal']['message'] = $transaction_data['message'];
                        $deal_data['Deal']['user_id'] = $transaction_data['user_id'];
                        $deal_data['Deal']['payment_gateway_id'] = ConstPaymentGateways::PagSeguro;
                        $deal_data['Deal']['cc_transaction_log_id'] = $cc_transaction_log_id;
                        //MOHAN - commented out the following line , otherwise page does not redirect after purchase  - which is odd

                        //$deal_data['Deal']['is_process_payment'] = 1;
                        
                        $this->TempPaymentLog->delete($transaction_data['id']);
                        // For affiliates ( //
                        if (!empty($transaction_data['referred_user_id'])) {
                            $deal_data['DealUser']['referred_user_id'] = $transaction_data['referred_user_id'];
                        }
                             
                        //die("b4 _buyDeal");
                        // ) For affiliates //
                        $this->_buyDeal($deal_data);
                    }
                }
            } else if ($transaction_data['payment_type'] == 'wallet' && $verified) {
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'processpayment',
                    'pagseguro',
                    'order' => $transaction_data['trans_id'],
                    'admin' => false
                ));
            } elseif ($transaction_data['payment_type'] == 'gift card' && $verified) {
                $this->redirect(array(
                    'controller' => 'gift_users',
                    'action' => 'processpayment',
                    'pagseguro',
                    'order' => $transaction_data['trans_id'],
                    'admin' => false
                ));
            } else {
                $this->Session->setFlash(__l('Error in payment.') , 'default', null, 'error');
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action' => 'index',
                    'admin' => false
                ));
            }
            break;
              
        case 'paypal':
            $this->Paypal->initialize($this);
            if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                    if ($paymentGatewaySetting['key'] == 'payee_account') {
                        $this->Paypal->payee_account = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                    }
                    if ($paymentGatewaySetting['key'] == 'receiver_emails') {
                        $this->Paypal->paypal_receiver_emails = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                    }
                }
            }
            $this->Paypal->sanitizeServerVars($_POST);
            $this->Paypal->is_test_mode = $paymentGateway['PaymentGateway']['is_test_mode'];
            $allow_to_process = 0;
            $deal_id = $this->Paypal->paypal_post_arr['deal_id'];
            $sub_deal_id = $this->Paypal->paypal_post_arr['sub_deal_id'];
            $quantity = $this->Paypal->paypal_post_arr['quantity'];
            $paid_amount = $this->Paypal->paypal_post_arr['mc_gross'];
            $payer_user_id = $this->Paypal->paypal_post_arr['user_id'];
            $is_purchase_with_wallet_amount = $this->Paypal->paypal_post_arr['is_purchase_with_wallet_amount'];
            $conditions = array();
            $conditions['Deal.id'] = $deal_id;
            if (!empty($sub_deal_id)) {
                $conditions['Deal.id'] = $sub_deal_id;
            }
            $get_deal = $this->Deal->find('first', array(
                'conditions' => $conditions,
                'recursive' => - 1
            ));
            $get_user = $this->Deal->User->find('first', array(
                'conditions' => array(
                    'User.id' => $payer_user_id
                ) ,
                'recursive' => - 1
            ));
            $payment_gateway_id = !empty($this->Paypal->paypal_post_arr['auth_id']) ? $this->Paypal->paypal_post_arr['payment_gateway_id'] : ConstPaymentGateways::Wallet;
            $is_supported = Configure::read('paypal.is_supported');
            if (isset($is_supported) && empty($is_supported)) {	 //**NEED TO FIX**//
				$get_conversion_amount = $this->_convertAmount($get_deal['Deal']['discounted_price'] * $quantity);
				if ($get_conversion_amount['amount'] == $paid_amount) {
					$allow_to_process = 1;					
				} elseif (!empty($is_purchase_with_wallet_amount)) {
					$get_conversion_amount = $this->_convertAmount(($get_deal['Deal']['discounted_price'] * $quantity) - $get_user['User']['available_balance_amount']);
					if(($get_conversion_amount['amount']) == ($paid_amount)) {
						$allow_to_process = 1;
					}
				}
            } else {
                if ($payment_gateway_id == ConstPaymentGateways::Wallet) {
                    if ((($get_deal['Deal']['discounted_price'] * $quantity) - $get_user['User']['available_balance_amount']) == ($paid_amount)) {
                        $allow_to_process = 1;
                    }
                } elseif ($payment_gateway_id == ConstPaymentGateways::PayPalAuth) {
                    if (($get_deal['Deal']['discounted_price'] * $quantity) == $paid_amount) {
                        $allow_to_process = 1;
                    } elseif (!empty($is_purchase_with_wallet_amount)) {
						if((($get_deal['Deal']['discounted_price'] * $quantity) - $get_user['User']['available_balance_amount']) == ($paid_amount)) {
							$allow_to_process = 1;
						}
                    }
                } elseif ($payment_gateway_id == ConstPaymentGateways::CreditCard) {
                    $allow_to_process = 1;
                }
            }
            if (!empty($get_deal) && !empty($allow_to_process)) {
                $this->Paypal->amount_for_item = $this->Paypal->paypal_post_arr['amount_needed'];
                if ($this->Paypal->process() || (!empty($this->Paypal->paypal_post_arr['auth_id']))) {
                    //for normal payment through wallet
                    if ($this->Paypal->paypal_post_arr['payment_status'] == 'Completed' && empty($this->Paypal->paypal_post_arr['auth_id'])) {
                        $id = $this->Paypal->paypal_post_arr['user_id'];
                        //add amount to wallet for normal paypal
                        $data['Transaction']['user_id'] = $id;
                        $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                        $data['Transaction']['class'] = 'SecondUser';
                        $data['Transaction']['amount'] = $this->Paypal->paypal_post_arr['mc_gross'];
                        $data['Transaction']['payment_gateway_id'] = $paymentGateway['PaymentGateway']['id'];
                        $data['Transaction']['gateway_fees'] = $this->Paypal->paypal_post_arr['mc_fee'];
                        $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AddedToWallet;
                        $transaction_id = $this->Deal->User->Transaction->log($data);
                        if (!empty($transaction_id)) {
                            $this->Paypal->paypal_post_arr['transaction_id'] = $transaction_id;
                            $this->Deal->User->updateAll(array(
                                'User.available_balance_amount' => 'User.available_balance_amount +' . $this->Paypal->paypal_post_arr['mc_gross'],
                            ) , array(
                                'User.id' => $id
                            ));
                        }
                        //buy deal
                        $deal_data['Deal']['deal_id'] = $deal_id;
                        if (!empty($sub_deal_id)) {
                            $deal_data['Deal']['sub_deal_id'] = $sub_deal_id;
                        }
                        $deal_data['Deal']['quantity'] = $this->Paypal->paypal_post_arr['quantity'];
                        $deal_data['Deal']['is_gift'] = $this->Paypal->paypal_post_arr['is_gift'];
                        $deal_data['Deal']['gift_to'] = $this->Paypal->paypal_post_arr['gift_to'];
                        $deal_data['Deal']['gift_from'] = $this->Paypal->paypal_post_arr['gift_from'];
                        $deal_data['Deal']['gift_email'] = $this->Paypal->paypal_post_arr['gift_email'];
                        $deal_data['Deal']['message'] = $this->Paypal->paypal_post_arr['message'];
                        $deal_data['Deal']['user_id'] = $this->Paypal->paypal_post_arr['user_id'];
                        $deal_data['Deal']['payment_gateway_id'] = !empty($this->Paypal->paypal_post_arr['auth_id']) ? $this->Paypal->paypal_post_arr['payment_gateway_id'] : ConstPaymentGateways::Wallet;
                        $paypal_transaction_log_id = $this->Paypal->logPaypalTransactions();
                        $deal_data['Deal']['paypal_transaction_log_id'] = $paypal_transaction_log_id;
                        $deal_data['Deal']['is_process_payment'] = 1;
                        $this->_buyDeal($deal_data);
                    } else if ($this->Paypal->paypal_post_arr['payment_status'] == 'Pending' && !empty($this->Paypal->paypal_post_arr['auth_id']) && $this->Paypal->paypal_post_arr['pending_reason'] == 'authorization') {
                        //for paypal auth first time
                        //buy deal
						$is_duplicate_ipn = $this->Deal->DealUser->PaypalTransactionLog->find('first', array(
							'conditions' => array(
								'PaypalTransactionLog.authorization_auth_id' => $this->Paypal->paypal_post_arr['auth_id']
							),
							'recursive' => -1
						));
						if(!empty($is_duplicate_ipn)){
							//for paypal duplicate IPN check
							// Also, we're using auth_id instead of txt_id coz, txt_id varies for second duplicate ID in set of all Duplicate IPN's
							exit;
						}
                        $deal_data['Deal']['deal_id'] = $deal_id;
                        if (!empty($sub_deal_id)) {
                            $deal_data['Deal']['sub_deal_id'] = $sub_deal_id;
                        }
                        $deal_data['Deal']['quantity'] = $this->Paypal->paypal_post_arr['quantity'];
                        $deal_data['Deal']['is_gift'] = $this->Paypal->paypal_post_arr['is_gift'];
                        $deal_data['Deal']['gift_to'] = $this->Paypal->paypal_post_arr['gift_to'];
                        $deal_data['Deal']['gift_from'] = $this->Paypal->paypal_post_arr['gift_from'];
                        $deal_data['Deal']['gift_email'] = $this->Paypal->paypal_post_arr['gift_email'];
                        $deal_data['Deal']['message'] = $this->Paypal->paypal_post_arr['message'];
                        $deal_data['Deal']['user_id'] = $this->Paypal->paypal_post_arr['user_id'];
                        $deal_data['Deal']['payment_gateway_id'] = $this->Paypal->paypal_post_arr['payment_gateway_id'];
                        $paypal_transaction_log_id = $this->Paypal->logPaypalTransactions();
                        $deal_data['Deal']['is_purchase_with_wallet_amount'] = $this->Paypal->paypal_post_arr['is_purchase_with_wallet_amount'];
                        $deal_data['Deal']['paypal_transaction_log_id'] = $paypal_transaction_log_id;
                        $deal_data['Deal']['is_process_payment'] = 1;
                        // For affiliates ( //
                        $refer_id = $this->Paypal->paypal_post_arr['refer_id'];
                        if (!empty($refer_id)) {
                            $deal_data['DealUser']['referred_by_user_id'] = $refer_id;
                        }
                        $deal_data['DealUser']['city_id'] = $this->Paypal->paypal_post_arr['city_id'];
                        $deal_data['DealUser']['latitude'] = $this->Paypal->paypal_post_arr['latitude'];
                        $deal_data['DealUser']['longitude'] = $this->Paypal->paypal_post_arr['longitude'];
                        $deal_data['Deal']['charity_id'] = $this->Paypal->paypal_post_arr['charity_id'];
                        // ) affiliates //
                        //update deal user id in PaypalTransactionLog table
                        $get_conversion = $this->getConversionCurrency();
                        $this->Deal->DealUser->PaypalTransactionLog->updateAll(array(
                            'PaypalTransactionLog.currency_id' => $get_conversion['CurrencyConversion']['currency_id'],
                            'PaypalTransactionLog.converted_currency_id' => $get_conversion['CurrencyConversion']['converted_currency_id'],
                            'PaypalTransactionLog.orginal_amount' => $this->Paypal->paypal_post_arr['original_amount_needed'],
                            'PaypalTransactionLog.rate' => $get_conversion['CurrencyConversion']['rate'],
                        ) , array(
                            'PaypalTransactionLog.id' => $paypal_transaction_log_id
                        ));
                        $this->_buyDeal($deal_data);
                    } else if (!empty($this->Paypal->paypal_post_arr['auth_id'])) {
                        //for paypal auth second time ipn
                        exit;
                    }
                }
            }
            $this->Paypal->logPaypalTransactions();
            break;

        /*case 'pagseguro':
            if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                    if ($paymentGatewaySetting['key'] == 'payee_account') {
                        $email = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                    }
                    if ($paymentGatewaySetting['key'] == 'token') {
                        $token = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                    }
                }
            }
            $post_array = $_POST;
            if (!empty($post_array) && $post_array['Referencia']) {
                $temp_ary = $this->TempPaymentLog->find('first', array(
                    'conditions' => array(
                        'TempPaymentLog.trans_id' => $post_array['Referencia']
                    )
                ));
                $transaction_data = $temp_ary['TempPaymentLog'];
            } else {
                $this->Session->setFlash(__l('Error in payment.') , 'default', null, 'error');
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action' => 'index',
                    'admin' => false
                ));
            }
            $this->PagSeguro->init(array(
                'pagseguro' => array(
                    'email' => $email,
                    'token' => $token,
                ) ,
                'format' => array(
                    'item_id' => $transaction_data['deal_id'],
                    'item_descr' => 'Bought Deal',
                    'item_quant' => $transaction_data['quantity'],
                    'item_valor' => $transaction_data['amount_needed'],
                )
            ));
            if (empty($transaction_data['deal_id'])) {
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action' => 'index',
                    'admin' => false
                ));
            }
            $allow_to_process = 1;
            $verified = 0;
            $pagseguro_data = $return_details;
            $verificado = $this->PagSeguro->confirm();
            if ($verificado == 'VERIFICADO') {
                $verified = 1;
                $result = $this->PagSeguro->getDataPayment();
                $log_data = array_merge($pagseguro_data, $transaction_data);
                $pagseguro_transaction_log_id = $this->Deal->DealUser->PagseguroTransactionLog->logPagSeguroTransactions($log_data);
            } elseif ($verificado == 'FALSO') {
                $verified = 0;
                $log_data = array_merge($pagseguro_data, $transaction_data);
                $pagseguro_transaction_log_id = $this->Deal->DealUser->PagseguroTransactionLog->logPagSeguroTransactions($log_data);
            }
            if ($transaction_data['payment_type'] == 'Buy deal') {
                $conditions = array();
                $conditions['Deal.id'] = $transaction_data['deal_id'];
                if (!empty($transaction_data['sub_deal_id'])) {
                    $conditions['Deal.id'] = $sub_deal_id;
                }
                $quantity = $transaction_data['quantity'];
                $paid_amount = $transaction_data['amount_needed'];
                $payer_user_id = $transaction_data['user_id'];
                $get_deal = $this->Deal->find('first', array(
                    'conditions' => $conditions,
                    'recursive' => - 1
                ));
                $get_user = $this->Deal->User->find('first', array(
                    'conditions' => array(
                        'User.id' => $payer_user_id
                    ) ,
                    'recursive' => - 1
                ));
                if (!empty($get_deal) && !empty($allow_to_process)) {
                    if (!empty($verified)) {
                        $id = $transaction_data['user_id'];
                        //add amount to wallet for normal paypal
                        $data['Transaction']['user_id'] = $id;
                        $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                        $data['Transaction']['class'] = 'SecondUser';
                        $data['Transaction']['amount'] = $paid_amount;
                        $data['Transaction']['payment_gateway_id'] = $paymentGateway['PaymentGateway']['id'];
                        $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AddedToWallet;
                        $transaction_id = $this->Deal->User->Transaction->log($data);
                        if (!empty($transaction_id)) {
                            $transaction_id = $transaction_id;
                            $this->Deal->User->updateAll(array(
                                'User.available_balance_amount' => 'User.available_balance_amount +' . $paid_amount,
                            ) , array(
                                'User.id' => $id
                            ));
                        }
                        //buy deal
                        $deal_data['Deal']['deal_id'] = $deal_id;
                        $deal_data['Deal']['quantity'] = $transaction_data['quantity'];
                        $deal_data['Deal']['is_gift'] = $transaction_data['is_gift'];
                        $deal_data['Deal']['gift_to'] = $transaction_data['gift_to'];
                        $deal_data['Deal']['gift_from'] = $transaction_data['gift_from'];
                        $deal_data['Deal']['gift_email'] = $transaction_data['gift_email'];
                        $deal_data['Deal']['message'] = $transaction_data['message'];
                        $deal_data['Deal']['user_id'] = $transaction_data['user_id'];
                        $deal_data['Deal']['payment_gateway_id'] = ConstPaymentGateways::PagSeguro;
                        $deal_data['Deal']['pagseguro_transaction_log_id'] = $pagseguro_transaction_log_id;
                        $deal_data['Deal']['is_process_payment'] = 1;
                        $this->TempPaymentLog->delete($transaction_data['id']);
                        // For affiliates ( //
                        if (!empty($transaction_data['referred_user_id'])) {
                            $deal_data['DealUser']['referred_user_id'] = $transaction_data['referred_user_id'];
                        }
                        // ) For affiliates //
                        $this->_buyDeal($deal_data);
                    }
                }
            } else if ($transaction_data['payment_type'] == 'wallet' && $verified) {
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'processpayment',
                    'pagseguro',
                    'order' => $transaction_data['trans_id'],
                    'admin' => false
                ));
            } elseif ($transaction_data['payment_type'] == 'gift card' && $verified) {
                $this->redirect(array(
                    'controller' => 'gift_users',
                    'action' => 'processpayment',
                    'pagseguro',
                    'order' => $transaction_data['trans_id'],
                    'admin' => false
                ));
            } else {
                $this->Session->setFlash(__l('Error in payment.') , 'default', null, 'error');
                $this->redirect(array(
                    'controller' => 'transactions',
                    'action' => 'index',
                    'admin' => false
                ));
            }
            break;*/

        default:
            throw new NotFoundException(__l('Invalid request'));
        } // switch
        $this->autoRender = false;
    }
    //before login deal buy process
    public function _buyDeal($data)
    {
        
        $this->loadModel('EmailTemplate');
        $is_purchase_with_wallet_amount = 0; // Used for 'handle with wallet like groupon //
        $deal_id = $data['Deal']['deal_id'];
        $conditions = array();
        $sub_deal_conditions = array();
        $conditions['Deal.id'] = $data['Deal']['deal_id'];
        $conditions['Deal.deal_status_id'] = array(
            ConstDealStatus::Open,
            ConstDealStatus::Tipped
        );
        
        // Subdeal: Adding Sub deal conditions //
        if (!empty($data['Deal']['sub_deal_id'])) {
            $sub_deal_conditions = array(
                'SubDeal.id' => $data['Deal']['sub_deal_id']
            );
        }
        
        $deal = $this->Deal->find('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'DealStatus' => array(
                    'fields' => array(
                        'DealStatus.name',
                    )
                ) ,
                'SubDeal' => array(
                    'conditions' => $sub_deal_conditions
                ) ,
                'Company' => array(
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
                    )
                )
            ) ,
            'recursive' => 2
        ));

        
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        } else {
            if (!empty($data['Deal']['sub_deal_id']) && !empty($deal['SubDeal'][0])) {
                $subdeal = $deal['SubDeal'][0];
                $total_deal_amount = $subdeal['discounted_price'] * $data['Deal']['quantity'];
            } else {
                $total_deal_amount = $deal['Deal']['discounted_price'] * $data['Deal']['quantity'];
            }
            //in paypal process we will not get Auth
            $user = $this->Deal->User->find('first', array(
                'conditions' => array(
                    'User.id' => $data['Deal']['user_id']
                ) ,
                'fields' => array(
                    'User.available_balance_amount',
                    'User.referred_by_user_id',
                    'User.username',
                    'User.created',
                    'User.email',
                    'User.id'
                ) ,
                'recursive' => - 1
            ));
            $paymentGateway = $this->Deal->User->Transaction->PaymentGateway->find('first', array(
                'conditions' => array(
                    'PaymentGateway.id' => ConstPaymentGateways::CreditCard,
                ) ,
                'contain' => array(
                    'PaymentGatewaySetting' => array(
                        'fields' => array(
                            'PaymentGatewaySetting.key',
                            'PaymentGatewaySetting.test_mode_value',
                            'PaymentGatewaySetting.live_mode_value',
                        ) ,
                    ) ,
                ) ,
                'recursive' => 1
            ));
            //deal user table record add
            if (empty($data['DealUser']['city_id'])) {
                $city = $this->Deal->City->find('first', array(
                    'conditions' => array(
                        'City.slug' => $this->request->params['named']['city']
                    ) ,
                    'fields' => array(
                        'City.id'
                    ) ,
                    'recursive' => - 1
                ));
                $deal_user['DealUser']['city_id'] = $city['City']['id'];
                if (!empty($_COOKIE['geoip_latitude'])) {
                    $deal_user['DealUser']['latitude'] = $_COOKIE['geoip_latitude'];
                }
                if (!empty($_COOKIE['geoip_longitude'])) {
                    $deal_user['DealUser']['longitude'] = $_COOKIE['geoip_longitude'];
                }
            } else {
                $deal_user['DealUser']['city_id'] = $data['DealUser']['city_id'];
                $deal_user['DealUser']['latitude'] = $data['DealUser']['latitude'];
                $deal_user['DealUser']['longitude'] = $data['DealUser']['longitude'];
            }
            $deal_user['DealUser']['quantity'] = $data['Deal']['quantity'];
            $deal_user['DealUser']['deal_id'] = $data['Deal']['deal_id'];
            if (!empty($subdeal['id'])) {
                $deal_user['DealUser']['sub_deal_id'] = $subdeal['id'];
            }
            $deal_user['DealUser']['is_gift'] = $data['Deal']['is_gift'];
            $deal_user['DealUser']['user_id'] = $data['Deal']['user_id'];
            $deal_user['DealUser']['discount_amount'] = $total_deal_amount;
            $deal_user['DealUser']['payment_gateway_id'] = !empty($data['Deal']['payment_gateway_id']) ? $data['Deal']['payment_gateway_id'] : ConstPaymentGateways::Wallet;
            //    $coupon_code = $this->_uuid();
            //    $deal_user['DealUser']['coupon_code'] = $coupon_code;
            if ($data['Deal']['is_gift']) {
                $deal_user['DealUser']['gift_email'] = $data['Deal']['gift_email'];
                $deal_user['DealUser']['message'] = $data['Deal']['message'];
                $deal_user['DealUser']['gift_to'] = $data['Deal']['gift_to'];
                $deal_user['DealUser']['gift_from'] = $data['Deal']['gift_from'];
            }
            
            //for credit card and paypal auth it should be 0
            if (($data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard) || ($data['Deal']['payment_gateway_id'] == ConstPaymentGateways::PayPalAuth)) {
                $deal_user['DealUser']['is_paid'] = 0;
            }
            $this->Deal->DealUser->create();
            $this->Deal->DealUser->set($deal_user);
  
            //for credit card doDirectPayment function call in paypal component
            if ($data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard) {
                if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                    foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                        if ($paymentGatewaySetting['key'] == 'directpay_API_UserName') {
                            $sender_info['API_UserName'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                        }
                        if ($paymentGatewaySetting['key'] == 'directpay_API_Password') {
                            $sender_info['API_Password'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                        }
                        if ($paymentGatewaySetting['key'] == 'directpay_API_Signature') {
                            $sender_info['API_Signature'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                        }
                    }
                }
                // If enabled, purchase amount is first taken with amount in wallet and then passed to CreditCard //
                if (Configure::read('wallet.is_handle_wallet_as_in_groupon')) {
                    $user_available_balance = $this->Deal->User->checkUserBalance($this->Auth->user('id'));
                    $total_deal_amount = $total_deal_amount - $user_available_balance;
                    $is_purchase_with_wallet_amount = 1;
                }
                // Currency Conversion Process //
                $get_conversion = $this->_convertAmount($total_deal_amount);
                $sender_info['is_testmode'] = $paymentGateway['PaymentGateway']['is_test_mode'];
                $data_credit_card['firstName'] = $data['Deal']['firstName'];
                $data_credit_card['lastName'] = $data['Deal']['lastName'];
                $data_credit_card['creditCardType'] = $data['Deal']['creditCardType'];
                $data_credit_card['creditCardNumber'] = $data['Deal']['creditCardNumber'];
                $data_credit_card['expDateMonth'] = $data['Deal']['expDateMonth'];
                $data_credit_card['expDateYear'] = $data['Deal']['expDateYear'];
                $data_credit_card['cvv2Number'] = $data['Deal']['cvv2Number'];
                $data_credit_card['address'] = $data['Deal']['address'];
                $data_credit_card['city'] = $data['Deal']['city'];
                $data_credit_card['state'] = $data['Deal']['state'];
                $data_credit_card['zip'] = $data['Deal']['zip'];
                $data_credit_card['country'] = $data['Deal']['country'];
                $data_credit_card['paymentType'] = 'Authorization';
                $data_credit_card['amount'] = $get_conversion['amount'];
                $data_credit_card['currency_code'] = $get_conversion['currency_code'];
                //calling doDirectPayment fn in paypal component
                $payment_response = $this->Paypal->doDirectPayment($data_credit_card, $sender_info);
                //if not success show error msg as it received from paypal
                if (!empty($payment_response) && $payment_response['ACK'] != 'Success') {
                    $this->Session->setFlash(sprintf(__l('%s') , $payment_response['L_LONGMESSAGE0']) , 'default', null, 'error');
                    return;
                }
            }

            
            // For affiliates ( //
            if (!empty($data['DealUser']['referred_by_user_id'])) { // For Wallet and Credit Card //
                $deal_user['DealUser']['referred_by_user_id'] = $data['DealUser']['referred_by_user_id'];
            } else {
                $cookie_value = $this->Cookie->read('referrer');
                $refer_id = (!empty($cookie_value)) ? $cookie_value['refer_id'] : null;
                if (!empty($refer_id)) {
                    $deal_user['DealUser']['referred_by_user_id'] = $refer_id;
                }
            }
            // ) affiliates //
            //save deal user record
            if ($this->Deal->DealUser->save($deal_user)) {
                // For affiliates ( //
                $cookie_value = $this->Cookie->read('referrer');
                if (!empty($cookie_value)) {
                    $this->Cookie->delete('referrer'); // Delete referer cookie
                    
                }
                // ) affiliates //
                $last_inserted_id = $this->Deal->DealUser->getLastInsertId();
				if(!empty($data['Deal']['charity_id'])){
					$this->_set_charity_detail($data['Deal']['charity_id'], $last_inserted_id);
				}
                // Multiple coupon - Saving //
                $deal_user_coupons = array();
                $coupons = $this->_getCoupons($data['Deal']['deal_id'], $data['Deal']['quantity']);
                foreach($coupons as $key => $value) {
                    $deal_user_coupons['id'] = '';
                    $deal_user_coupons['deal_user_id'] = $last_inserted_id;
                    $deal_user_coupons['coupon_code'] = $value;
                    $deal_user_coupons['unique_coupon_code'] = $this->_unum();
                    $this->Deal->DealUser->DealUserCoupon->save($deal_user_coupons);
                }
                
                      
                
                if ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard && !empty($payment_response)) {
                    $data_paypal_docapture_log['PaypalDocaptureLog']['authorizationid'] = $payment_response['TRANSACTIONID'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['deal_user_id'] = $last_inserted_id;
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_correlationid'] = $payment_response['CORRELATIONID'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_ack'] = $payment_response['ACK'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_build'] = $payment_response['BUILD'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_amt'] = $payment_response['AMT'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_avscode'] = $payment_response['AVSCODE'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_cvv2match'] = $payment_response['CVV2MATCH'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_timestamp'] = $payment_response['TIMESTAMP'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['dodirectpayment_response'] = serialize($payment_response);
                    $data_paypal_docapture_log['PaypalDocaptureLog']['version'] = $payment_response['VERSION'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['currencycode'] = $payment_response['CURRENCYCODE'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['payment_status'] = 'Pending';
                    //update deal user id in PaypalDocaptureLog table
                    $get_conversion = $this->getConversionCurrency();
                    $data_paypal_docapture_log['PaypalDocaptureLog']['currency_id'] = $get_conversion['CurrencyConversion']['currency_id'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['converted_currency_id'] = $get_conversion['CurrencyConversion']['converted_currency_id'];
                    $data_paypal_docapture_log['PaypalDocaptureLog']['original_amount'] = $total_deal_amount;
                    $data_paypal_docapture_log['PaypalDocaptureLog']['rate'] = $get_conversion['CurrencyConversion']['rate'];
                    //save do capture log records
                    $this->Deal->DealUser->PaypalDocaptureLog->save($data_paypal_docapture_log);
                } else if ($data['Deal']['payment_gateway_id'] == ConstPaymentGateways::PayPalAuth) {
                    $is_purchase_with_wallet_amount = $data['Deal']['is_purchase_with_wallet_amount'];
                    //update deal user id in PaypalTransactionLog table
                    $this->Deal->DealUser->PaypalTransactionLog->updateAll(array(
                        'PaypalTransactionLog.deal_user_id' => $last_inserted_id
                    ) , array(
                        'PaypalTransactionLog.id' => $data['Deal']['paypal_transaction_log_id']
                    ));
                } else {
                    //buy deal through wallet
                    $transaction['Transaction']['user_id'] = $deal_user['DealUser']['user_id'];
                    $transaction['Transaction']['foreign_id'] = $last_inserted_id;
                    $transaction['Transaction']['class'] = 'DealUser';
                    $transaction['Transaction']['amount'] = $total_deal_amount;
                    $transaction['Transaction']['transaction_type_id'] = (!empty($data['Deal']['is_gift'])) ? ConstTransactionTypes::DealGift : ConstTransactionTypes::BuyDeal;
                    //original_amount_needed
                    $tbl_transaction_id = $this->Deal->User->Transaction->log($transaction);
                    if ($data['Deal']['payment_gateway_id'] == ConstPaymentGateways::PagSeguro) {
                        //update deal user id in PaypalTransactionLog table
                        //MOHAN - Need to figure out where to log this
                        $this->Deal->DealUser->PaypalTransactionLog->updateAll(array(
                            'PaypalTransactionLog.deal_user_id' => $last_inserted_id,
                            'PaypalTransactionLog.transaction_id' => $tbl_transaction_id
                        ) , array(
                            'PaypalTransactionLog.id' => $data['Deal']['cc_transaction_log_id']
                        ));
                    }
                   
                    //user update
                    $this->Deal->User->updateAll(array(
                        'User.available_balance_amount' => 'User.available_balance_amount -' . $total_deal_amount,
                    ) , array(
                        'User.id' => $deal_user['DealUser']['user_id']
                    ));
                }
                // If enabled, and after purchase, deduct partial amount from wallet //
                if (Configure::read('wallet.is_handle_wallet_as_in_groupon') && (!empty($is_purchase_with_wallet_amount))) {
                    // Deduct amount ( zero will be updated ) //
                    $user_available_balance = $this->Deal->User->checkUserBalance($deal_user['DealUser']['user_id']);
                    $this->Deal->User->updateAll(array(
                        'User.available_balance_amount' => 'User.available_balance_amount -' . $user_available_balance,
                    ) , array(
                        'User.id' => $deal_user['DealUser']['user_id']
                    ));
                    // Update transaction, This is firs transaction, to notify user that partial amount taken from wallet. Second transaction will be updated after deal gets tipped.//
                    if (!empty($user_available_balance) && $user_available_balance != '0.00') {
                        $transaction['Transaction']['user_id'] = $deal_user['DealUser']['user_id'];
                        $transaction['Transaction']['foreign_id'] = $last_inserted_id;
                        $transaction['Transaction']['class'] = 'DealUser';
                        $transaction['Transaction']['amount'] = $user_available_balance;
                        $transaction['Transaction']['transaction_type_id'] = ConstTransactionTypes::PartallyAmountTakenForDealPurchase;
                        $transaction['Transaction']['payment_gateway_id'] = ConstPaymentGateways::Wallet;
                        $this->Deal->User->Transaction->log($transaction);
                    }
                }
               
                //increasing deal_user_count
                $this->Deal->updateAll(array(
                    'Deal.deal_user_count' => 'Deal.deal_user_count +' . $data['Deal']['quantity'],
                ) , array(
                    'Deal.id' => $deal_id
                ));
                //increasing deal_user_count for sub deal //
                if (!empty($subdeal)) {
                    $this->Deal->updateAll(array(
                        'Deal.deal_user_count' => 'Deal.deal_user_count +' . $data['Deal']['quantity'],
                    ) , array(
                        'Deal.id' => $subdeal['id']
                    ));
                }
                //update deal is on
                if ($deal['Deal']['deal_status_id'] == ConstDealStatus::Open) {
                    $db = $this->Deal->getDataSource();
                    $this->Deal->updateAll(array(
                        'Deal.deal_status_id' => ConstDealStatus::Tipped,
                        'Deal.deal_tipped_time' => '\'' . date('Y-m-d H:i:s') . '\''
                    ) , array(
                        'Deal.deal_status_id' => ConstDealStatus::Open,
                        'Deal.deal_user_count >=' => $db->expression('Deal.min_limit') ,
                        'Deal.id' => $deal_id
                    ));
                }
                               

                //send coupon mail to users or close the deal
                $this->Deal->processDealStatus($deal_id, $last_inserted_id);
                //pay to referer
                $referred_by_user_id = $user['User']['referred_by_user_id'];
                //pay referral amount of referred users
                if ((Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer) && !empty($referred_by_user_id)) {
                    $this->_pay_to_referrer($deal_id, $user['User']);
                }
                //deal on end
                $company_address = ($deal['Company']['address1']) ? $deal['Company']['address1'] : '';
                $company_address.= ($deal['Company']['address2']) ? ', ' . $deal['Company']['address2'] : '';
                $company_address.= !empty($deal['Company']['City']['name']) ? ', ' . $deal['Company']['City']['name'] : '';
                $company_address.= !empty($deal['Company']['State']['name']) ? ', ' . $deal['Company']['State']['name'] : '';
                $company_address.= !empty($deal['Company']['Country']['name']) ? ', ' . $deal['Company']['Country']['name'] : '';
                $company_address.= '.';
                $language_code = $this->Deal->getUserLanguageIso($deal_user['DealUser']['user_id']);
                $email_message = $this->EmailTemplate->selectTemplate('Deal Bought', $language_code);
                $emailFindReplace = array(
                    '##FROM_EMAIL##' => $this->Deal->changeFromEmail(($email_message['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $email_message['from']) ,
                    '##SITE_NAME##' => Configure::read('site.name') ,
                    '##USERNAME##' => $user['User']['username'],
                    '##DEAL_TITLE##' => $deal['Deal']['name'] . (!empty($subdeal['name']) ? ' - ' . $subdeal['name'] : '') ,
                    '##DEAL_AMOUNT##' => Configure::read('site.currency') . $total_deal_amount . (!empty($subdeal['is_enable_payment_advance']) ? (' ' . __l('(Payment remaining:') . ' ' . Configure::read('site.currency') . $subdeal['payment_remaining'] . ')') : '') ,
                    '##SITE_LINK##' => Router::url('/', true) ,
                    '##QUANTITY##' => $deal_user['DealUser']['quantity'],
                    '##PURCHASE_ON##' => strftime(Configure::read('site.datetime.format')) ,
                    '##DEAL_STATUS##' => $deal['DealStatus']['name'],
                    '##COMPANY_NAME##' => $deal['Company']['name'],
                    '##COMPANY_ADDRESS##' => ($company_address) ? $company_address : '',
                    '##CONTACT_URL##' => Router::url(array(
                        'controller' => 'contacts',
                        'action' => 'add',
                        'city' => $this->request->params['named']['city'],
                        'admin' => false
                    ) , true) ,
                    '##GIFT_RECEIVER##' => !empty($deal_user['DealUser']['gift_to']) ? $deal_user['DealUser']['gift_to'] : '',
                    '##SITE_LOGO##' => Router::url(array(
                        'controller' => 'img',
                        'action' => 'blue-theme',
                        'logo-email.png',
                        'admin' => false
                    ) , true) ,
                );
                
                $this->_sendMail($emailFindReplace, $email_message, $user['User']['email']);
               
                if (!empty($data['Deal']['is_gift'])) { // Deal gift mail
                    $emailFindReplace['##USERNAME##'] = $deal_user['DealUser']['gift_to'];
                    $emailFindReplace['##FRIEND_NAME##'] = $deal_user['DealUser']['gift_from'];
                    $language_code = $this->Deal->getUserLanguageIso($deal_user['DealUser']['user_id']);
                    $email_message = $this->EmailTemplate->selectTemplate('Deal gift mail', $language_code);
                    $this->_sendMail($emailFindReplace, $email_message, $deal_user['DealUser']['gift_email']);
                }
                $this->Session->setFlash(__l('You have bought a deal successfully.') , 'default', null, 'success');
                $get_updated_status = $this->Deal->find('first', array(
                    'conditions' => array(
                        'Deal.id' => $deal_id
                    ) ,
                    'recursive' => - 1
                ));
				if($this->layoutPath == 'touch')
				{
					$this->redirect(array(
						'controller' => 'deals',
						'action' => 'index',
						'admin' => false
					));
				}
			    // <-- For iPhone App code
                if ($this->RequestHandler->prefers('json')) {
                    $resonse = array(
                        'status' => 0,
                        'message' => __l('Success')
                    );
                    $this->view = 'Json';
                    $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
                    // For iPhone App code -->
                    
                } else {

                    if (empty($data['Deal']['is_process_payment'])) {
                        if (Configure::read('Deal.invite_after_deal_add') && $get_updated_status['Deal']['deal_status_id'] != ConstDealStatus::Closed) {
                            $this->redirect(array(
                                'controller' => 'user_friends',
                                'action' => 'deal_invite',
                                'type' => 'deal',
                                'deal' => $deal['Deal']['slug']
                            ));
                        } else {
                            $this->redirect(array(
                            'controller' => 'users',
                            'action' => 'my_stuff#My_Purchases'
                            ));
                            //MOHAN Changing the redirect from below because it does not seem to forwar anywhere useful
                            /*$this->redirect(array(
                                'controller' => 'deals',
                                'action' => 'index',
                                'bought' => 'bought',
                                'dealid' => $deal['Deal']['id'],
                                'dealqnty' => $data['Deal']['quantity'],
                                'dealprc' => $data['Deal']['deal_amount'],
                                'dealtotal' => $total_deal_amount,
                            ));*/
                        }
                    }
                }
            } else {
                if (empty($data['Deal']['is_process_payment'])) {
                    if ($this->RequestHandler->prefers('json')) {
						if($this->layoutPath == 'touch')
						{
							$this->Session->setFlash(__l('You can\'t buy this deal.') , 'default', null, 'error');
							$this->redirect(array(
								'controller' => 'deals',
								'action' => 'index',
								'admin' => false
							));
						}
                        $resonse = array(
                            'status' => 1,
                            'message' => __l('You can\'t buy this deal.')
                        );
                        $this->view = 'Json';
                        $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
                    } else {
                        $this->Session->setFlash(__l('You can\'t buy this deal.') , 'default', null, 'error');
                        $this->redirect(array(
                            'controller' => 'deals',
                            'action' => 'index'
                        ));
                    }
                }
            }
        }
    }
    function _set_charity_detail($charity_id, $deal_user_id)
    {
        if (!empty($charity_id)) {
            $dealUser = $this->Deal->DealUser->find('first', array(
                'conditions' => array(
                    'DealUser.id' => $deal_user_id
                ) ,
                'contain' => array(
                    'Deal',
                    'SubDeal'
                ) ,
                'recursive' => 2
            ));
            if (!empty($dealUser['DealUser']['sub_deal_id'])) {
                $dealUser['Deal']['bonus_amount'] = $dealUser['SubDeal']['bonus_amount'];
                $dealUser['Deal']['commission_percentage'] = $dealUser['SubDeal']['commission_percentage'];
            }
            $site_commission_amount = $seller_commission_amount = 0;
            $site_commission_amount =  $dealUser['DealUser']['discount_amount'] * ($dealUser['Deal']['commission_percentage'] / 100);
            $seller_commission_amount = $dealUser['DealUser']['discount_amount'] - $site_commission_amount;
            if (Configure::read('charity.who_will_pay') == ConstCharityWhoWillPay::CompanyUser) {
                $site_commission_amount = 0;
            }
            if (Configure::read('charity.who_will_pay') == ConstCharityWhoWillPay::Admin) {
                $seller_commission_amount = 0;
            }
            $_data['CharitiesDealUser']['charity_id'] = $charity_id;
            $_data['CharitiesDealUser']['deal_user_id'] = $deal_user_id;
            $_data['CharitiesDealUser']['site_commission_amount'] = (empty($site_commission_amount)) ? 0 : ($dealUser['Deal']['charity_percentage'] * ($site_commission_amount / 100));
            $_data['CharitiesDealUser']['seller_commission_amount'] = (empty($seller_commission_amount)) ? 0 : ($dealUser['Deal']['charity_percentage'] * ($seller_commission_amount / 100));
            $_data['CharitiesDealUser']['amount'] = $_data['CharitiesDealUser']['site_commission_amount'] + $_data['CharitiesDealUser']['seller_commission_amount'];
            $this->Deal->DealUser->CharitiesDealUser->save($_data);
        }
    }
    //pay referal amount to user when the new user buy his first deal
    public function _pay_to_referrer($deal_id, $deal_buyer_data)
    {
        $dealUserCount = $this->Deal->DealUser->find('count', array(
            'conditions' => array(
                'DealUser.user_id' => $deal_buyer_data['id']
            ) ,
            'recursive' => - 1
        ));
        $today = strtotime(date('Y-m-d H:i:s'));
        $registered_date = strtotime(_formatDate('Y-m-d H:i:s', strtotime($deal_buyer_data['created'])));
        $hours_diff = intval(($today - $registered_date) / 60 / 60);
        //check whether this is user's first deal and bought with in correct limit
        if (($dealUserCount == 1) && $hours_diff <= Configure::read('user.referral_deal_buy_time')) {
            //pay amount to referred user
            $transaction['Transaction']['user_id'] = $deal_buyer_data['referred_by_user_id'];
            $transaction['Transaction']['foreign_id'] = ConstUserIds::Admin;
            $transaction['Transaction']['class'] = 'SecondUser';
            $transaction['Transaction']['amount'] = Configure::read('user.referral_amount');
            $transaction['Transaction']['transaction_type_id'] = ConstTransactionTypes::ReferralAmount;
            $this->Deal->User->Transaction->log($transaction);
            $transaction = array();
            //admin record for referral amount
            $transaction['Transaction']['user_id'] = ConstUserIds::Admin;
            $transaction['Transaction']['foreign_id'] = $deal_buyer_data['referred_by_user_id'];
            $transaction['Transaction']['class'] = 'SecondUser';
            $transaction['Transaction']['amount'] = Configure::read('user.referral_amount');
            $transaction['Transaction']['transaction_type_id'] = ConstTransactionTypes::ReferralAmountPaid;
            $this->Deal->User->Transaction->log($transaction);
            $this->Deal->User->updateAll(array(
                'User.available_balance_amount' => 'User.available_balance_amount +' . Configure::read('user.referral_amount')
            ) , array(
                'User.id' => $deal_buyer_data['referred_by_user_id']
            ));
        }
    }
    public function _sendMail($email_content_array, $template, $to, $sendAs = 'text')
    {
        $this->loadModel('EmailTemplate');
        $this->Email->from = ($template['from'] == '##FROM_EMAIL##') ? Configure::read('EmailTemplate.from_email') : $template['from'];
        $this->Email->replyTo = ($template['reply_to'] == '##REPLY_TO_EMAIL##') ? Configure::read('EmailTemplate.reply_to_email') : $template['reply_to'];
        $this->Email->to = $to;
        $this->Email->subject = strtr($template['subject'], $email_content_array);
        $this->Email->content = strtr($template['email_content'], $email_content_array);
        $this->Email->sendAs = ($template['is_html']) ? 'html' : 'text';
        $this->Email->send($this->Email->content);
    }
    //commission calcualtor
    public function commission_calculator()
    {
        $this->pageTitle = __l('Commission Calculator');
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Deal']['calculator_discounted_price']) && !empty($this->request->data['Deal']['calculator_min_limit']) && !empty($this->request->data['Deal']['calculator_commission_percentage']) && !empty($this->request->data['Deal']['calculator_bonus_amount'])) {
                $this->request->data['Deal']['calculator_total_purchased_amount'] = $this->request->data['Deal']['calculator_discounted_price'] * $this->request->data['Deal']['calculator_min_limit'];
                $this->request->data['Deal']['calculator_total_commission_amount'] = ($this->request->data['Deal']['calculator_total_purchased_amount'] * ($this->request->data['Deal']['calculator_commission_percentage'] / 100)) + $this->request->data['Deal']['calculator_bonus_amount'];
                $this->request->data['Deal']['net_profit'] = $this->request->data['Deal']['calculator_total_commission_amount'];
                $this->Session->setFlash(__l('Deal commission amount calculated successfully.') , 'default', null, 'success');
            } else {
                $this->Session->setFlash(__l('Please enter all the values.') , 'default', null, 'error');
            }
        } else {
            $this->request->data['Deal']['calculator_total_purchased_amount'] = $this->request->data['Deal']['calculator_total_commission_amount'] = $this->request->data['Deal']['calculator_net_profit'] = 0;
        }
    }
    public function payment_success($gateway_id, $deal_id = null)
    {
        $this->pageTitle = __l('Payment Success');
        $pay_pal_repsonse = $_POST;
        $deal_slug = $this->Session->read('Auth.last_bought_deal_slug');
        $this->Session->delete('Auth.last_bought_deal_slug');
        if (!is_null($deal_id)) {
            $deal = $this->Deal->find('first', array(
                'conditions' => array(
                    'Deal.id' => $deal_id
                ) ,
                'fields' => array(
                    'Deal.slug'
                ) ,
                'recursive' => - 1
            ));
            if (!empty($deal)) {
                $deal_slug = $deal['Deal']['slug'];
            }
        }
        if (!empty($pay_pal_repsonse['auth_status'])) {
            $this->Session->write('Message.TransactionSuccessMessage', __l('Your payment has been successfully finished. We will update this transactions after deal has been tipped.'));
            $this->Session->setFlash(__l('Your payment has been successfully finished. We will update this transactions after deal has been tipped.') , 'default', null, 'success');
            $get_updated_status = $this->Deal->find('first', array(
                'conditions' => array(
                    'Deal.id' => $deal_id
                ) ,
                'recursive' => - 1
            ));
			if($this->layoutPath == 'touch')
			{
				$this->redirect(array(
					'controller' => 'deals',
					'action' => 'index',
					'admin' => false
				));
			}            
			if (Configure::read('Deal.invite_after_deal_add') && $get_updated_status['Deal']['deal_status_id'] != ConstDealStatus::Closed) {
                $this->redirect(array(
                    'controller' => 'user_friends',
                    'action' => 'deal_invite',
                    'type' => 'deal',
                    'deal' => $deal_slug
                ));
            } else {
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'my_stuff#My_Purchases'
                ));
            }
        }
        $this->Session->write('Message.TransactionSuccessMessage', __l('Your payment has been successfully finished. We will update this transactions page after receiving the confirmation from PayPal'));
        $this->Session->setFlash(__l('Your payment has been successfully finished. We will update this transactions page after receiving the confirmation from PayPal') , 'default', null, 'success');
		if($this->layoutPath == 'touch')
		{
			$this->redirect(array(
				'controller' => 'deals',
				'action' => 'index',
				'admin' => false
			));
		}            
		if (Configure::read('Deal.invite_after_deal_add') && $get_updated_status['Deal']['deal_status_id'] != ConstDealStatus::Closed) {
            $this->redirect(array(
                'controller' => 'user_friends',
                'action' => 'deal_invite',
                'type' => 'deal',
                'deal' => $deal_slug
            ));
        } else {
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'my_stuff#My_Purchases'
            ));
        }
    }
    public function payment_cancel()
    {
        $this->pageTitle = __l('Payment Cancel');
        $this->Session->setFlash(__l('Transaction failure. Please try once again.') , 'default', null, 'error');
        $this->redirect(array(
            'controller' => 'users',
            'action' => 'my_stuff',
            '#My_Transactions'
        ));
    }
    public function admin_update_bitlyurl()
    {
        $deals = $this->Deal->find('all', array(
            'contain' => array(
                'City',
            ) ,
            'recursive' => 2
        ));
        foreach($deals as $deal)
		{
			$slug = $deal['Deal']['slug'];
			$deal_id = $deal['Deal']['id'];
			foreach($deal['City'] as $k => $city)
			{
				$city_slug = $city['slug'];
				$city_id = $city['id'];
				$this->Deal->_updateDealBitlyURL($slug, $city_slug, $city_id, $deal_id);
			}	
		}
        $this->Session->setFlash(__l('Bitly URL has been successfully updated for all the deals in the site') , 'default', null, 'success');
        $this->redirect(array(
            'action' => 'index',
        ));
    }
    //generate barcode
    public function barcode($barcode = null)
    {
        $this->autoRender = false;
        define(__TRACE_ENABLED__, false);
        define(__DEBUG_ENABLED__, false);
        include_once (APP . DS . 'vendors' . DS . 'barcode' . DS . 'barcode.php');
        include_once (APP . DS . 'vendors' . DS . 'barcode' . DS . Configure::read('barcode.symbology') . 'object.php');
        $output = "png";
        $width = Configure::read('barcode.width');
        $height = Configure::read('barcode.height');
        $xres = "2";
        $font = "5";
        $type = Configure::read('barcode.symbology');
        if (!empty($barcode)) {
            $style = BCS_ALIGN_CENTER;
            $style|= ($output == "png") ? BCS_IMAGE_PNG : 0;
            $style|= ($output == "jpeg") ? BCS_IMAGE_JPEG : 0;
            $style|= ($border == "on") ? BCS_BORDER : 0;
            $style|= ($drawtext == "on") ? BCS_DRAW_TEXT : 0;
            $style|= ($stretchtext == "on") ? BCS_STRETCH_TEXT : 0;
            $style|= ($negative == "on") ? BCS_REVERSE_COLOR : 0;
            switch ($type) {
            case "i25":
                $obj = new I25Object($width, $height, $style, $barcode);
                break;

            case "c39":
                $obj = new C39Object($width, $height, $style, $barcode);
                break;

            case "c128a":
                $obj = new C128AObject($width, $height, $style, $barcode);
                break;

            case "c128b":
                $obj = new C128BObject($width, $height, $style, $barcode);
                break;

            case "c128c":
                $obj = new C128CObject($width, $height, $style, $barcode);
                break;

            default:
                $obj = false;
            }
            if ($obj) {
                if ($obj->DrawObject($xres)) {
                    $obj->SetFont($font);
                    $obj->DrawObject($xres);
                    $obj->FlushObject();
                    $obj->DestroyObject();
                    unset($obj);
                }
            }
        }
    }
    function widget()
    {
        $this->loadModel('AffiliateWidgetSize');
        $affiliateWidgetSize = $this->AffiliateWidgetSize->find('first', array(
            'conditions' => array(
                'AffiliateWidgetSize.id =' => $this->request->params['named']['size']
            ) ,
            'recursive' => 1
        ));
        $user = $this->Deal->User->find('first', array(
            'conditions' => array(
                'User.username =' => $this->request->params['named']['user']
            ) ,
            'fields' => array(
                'User.username'
            ) ,
            'recursive' => - 1
        ));
        if (!$affiliateWidgetSize['AffiliateWidgetSize']['is_display_side_deal']) $conditions['Deal.is_side_deal'] = 0;
        $conditions['Deal.deal_status_id'] = array(
            ConstDealStatus::Open,
            ConstDealStatus::Tipped,
        );
        $city = $this->Deal->City->find('first', array(
            'conditions' => array(
                'City.slug' => $this->request->params['named']['city_name']
            ) ,
            'fields' => array(
                'City.name',
                'City.id'
            ) ,
            'recursive' => 1
        ));
        $city_deal_ids = array();
        foreach($city['Deal'] as $deal) {
            $city_deal_ids[] = $deal['id'];
        }
        $conditions['Deal.id'] = $city_deal_ids;
        $deals = $this->Deal->find('all', array(
            'conditions' => $conditions,
            'contain' => array(
                'Attachment',
                'SubDeal',
            ) ,
            'recursive' => 2
        ));
        if (!empty($city) && !empty($user) && !empty($affiliateWidgetSize)) {
            if (!empty($deals)) {
                $template_content = $affiliateWidgetSize['AffiliateWidgetSize']['content'];
                $template_id = $affiliateWidgetSize['AffiliateWidgetSize']['id'];
                switch ($template_id) {
                case 2:
                    $title_length = 15;
                    break;

                case 3:
                    $title_length = 15;
                    break;

                case 4:
                    $title_length = 25;
                    break;

                case 5:
                    $title_length = 35;
                    break;

                case 6:
                    $title_length = 30;
                    break;

                case 7:
                    $title_length = 30;
                    break;

                case 8:
                    $title_length = 30;
                    break;

                case 9:
                    $title_length = 50;
                    break;

                case 10:
                    $title_length = 20;
                    break;

                default:
                    $title_length = 15;
                    break;
                }
                foreach($deals as $deal) {
                    $image_options = array(
                        'dimension' => 'original',
                        'class' => '',
                        'alt' => '',
                        'title' => '',
                        'type' => 'jpg'
                    );
                    $deal_image_options = array(
                        'dimension' => 'original',
                        'class' => '',
                        'alt' => '',
                        'title' => '',
                        'type' => 'jpg'
                    );
                    if (!empty($deal['Deal']['is_subdeal_available'])) {
                        $discounted_price = $deal['SubDeal'][0]['discounted_price'];
                        $discount_percentage = $deal['SubDeal'][0]['discount_percentage'];
                        $discount_amount = $deal['SubDeal'][0]['discount_amount'];
                        $original_price = $deal['SubDeal'][0]['original_price'];
                    } else {
                        $discounted_price = $deal['Deal']['discounted_price'];
                        $discount_percentage = $deal['Deal']['discount_percentage'];
                        $discount_amount = $deal['Deal']['discount_amount'];
                        $original_price = $deal['Deal']['original_price'];
                    }
                    $adFindReplace = array(
                        '##DEAL_LINK##' => Router::url(array(
                            'controller' => 'deals',
                            'action' => 'view',
                            $deal['Deal']['slug'],
                            'city' => $this->request->params['named']['city_name'],
                            'r' => $user['User']['username'],
                            'admin' => false
                        ) , true) ,
                        '##DEAL_MAIN_TITLE##' => (strlen($deal['Deal']['name']) > $title_length) ? substr($deal['Deal']['name'], 0, $title_length) . '...' : $deal['Deal']['name'],
                        '##DEAL_TITLE##' => $deal['Deal']['name'],
                        '##AD_IMAGE##' => $this->Deal->getImageUrl('AffiliateWidgetSize', $affiliateWidgetSize['Attachment'], $image_options) ,
                        '##COLOR##' => '#' . $this->request->params['named']['color'],
                        '##DEAL_HEADING##' => __l('Today\'s Deal') ,
                        '##DEAL_IMAGE##' => $this->Deal->getImageUrl('Deal', $deal['Attachment'][0], $deal_image_options) ,
                        '##DEAL_BOUGHT_COUNT##' => $deal['Deal']['deal_user_count'],
                        '##DEAL_DISCOUNT_PERCENTAGE##' => round($discount_percentage) . '%',
                        '##DEAL_DISCOUNT##' => round($discount_amount) ,
                        '##DEAL_PRICE##' => Configure::read('site.currency') . round($discounted_price) ,
                        '##DEAL_ORIGINAL_PRICE##' => Configure::read('site.currency') . round($original_price) ,
                        '##DEAL_SAVE_AMOUNT##' => round($original_price * $discount_percentage / 100)
                    );
                    if (empty($deal['Deal']['is_anytime_deal'])) {
                        $TIME_LEFT = '<div class="js-widget-deal-end-countdown">&nbsp;</div><span class="js-time hide">' . intval(strtotime($deal['Deal']['end_date'] . ' GMT') - time()) . '</span>';
                    } else {
                        $TIME_LEFT = __l('Unlimited');
                    }
                    $adFindReplace['##TIME_LEFT##'] = $TIME_LEFT;
                    $content.= '<li>' . strtr($template_content, $adFindReplace) . '</li>';
                    $skip_scroll = Configure::read('widget_no_scroll');
                    if (in_array($affiliateWidgetSize['AffiliateWidgetSize']['id'], $skip_scroll)) {
                        break;
                    }
                }
            }
        }
        if (empty($content)) {
            $content = '<li>' . '<a class="no-deal-found" target="_blank" href="' . Router::url('/', true) . $this->request->params['named']['city_name'] . '">No Deal Found</a>' . '</li>';
            $content = "<div class='js-wiget js-wiget-" . $affiliateWidgetSize['AffiliateWidgetSize']['id'] . "'> <div><ul>" . $content . "</ul></div>";
        } else {
            $redirect_url = Router::url(array(
                'controller' => 'deals',
                'action' => 'view',
                $deal['Deal']['slug'],
                'city' => $this->request->params['named']['city_name'],
                'r' => $user['User']['username'],
                'admin' => false
            ) , true);
            if (in_array($affiliateWidgetSize['AffiliateWidgetSize']['id'], $skip_scroll)) {
                $content = "<div class='js-wiget js-widget-target {widget_redirect:\"$redirect_url\"} js-wiget-" . $affiliateWidgetSize['AffiliateWidgetSize']['id'] . "'> <div><ul>" . $content . "</ul></div>";
            } else {
                $content = "<div class='js-wiget js-widget-target {widget_redirect:\"$redirect_url\"} js-wiget-" . $affiliateWidgetSize['AffiliateWidgetSize']['id'] . "'> <button class='prev'><<</button><button class='next'>>></button> <div class='js-jcarousellite'><ul>" . $content . "</ul></div> </div>";
            }
        }
        $this->set('content', $content);
        $this->layout = 'affiliate';
    }
    function _getCoupons($deal_id, $quantity)
    {
        $coupons = $this->Deal->DealCoupon->find('list', array(
            'conditions' => array(
                'DealCoupon.deal_id' => $deal_id,
                'DealCoupon.is_used' => 0
            ) ,
            'fields' => array(
                'DealCoupon.id',
                'DealCoupon.coupon_code',
            ) ,
            'limit' => $quantity,
            'order' => array(
                'DealCoupon.id' => 'asc'
            ) ,
            'recursive' => - 1
        ));
        // If not sufficent, insert System generated coupons //
        if (count($coupons) < $quantity) {
            $remaining = $quantity - count($coupons);
            for ($i = count($coupons) + 1; $i <= $quantity; $i++) {
                $system_gen_code = $this->_uuid() . '-' . $i;
                $system_coupon_code[] = $system_gen_code;
                // Inserting System generated code in tables //
                $deal_coupons['id'] = '';
                $deal_coupons['deal_id'] = $deal_id;
                $deal_coupons['coupon_code'] = $system_gen_code;
                $deal_coupons['is_used'] = 1;
                $deal_coupons['is_system_generated'] = 1;
                $this->Deal->DealCoupon->save($deal_coupons);
            }
        }
        if (!empty($system_coupon_code)) {
            $coupons = array_merge($coupons, $system_coupon_code);
        }
        // Updating Used Codes //
        $this->Deal->DealCoupon->updateAll(array(
            'DealCoupon.is_used' => 1
        ) , array(
            'DealCoupon.id' => array_keys($coupons)
        ));
        return $coupons;
    }
    function admin_subdeal_add($id = null)
    {
        if (is_null($id) && empty($this->request->data)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->setAction('subdeal_add', $id);
    }
    function subdeal_add($id = null)
    {
		$check_max_count = 0;
		$is_update_main_deal_max_count = 1;
        if (is_null($id) && empty($this->request->data)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (is_null($id) && !empty($this->request->data)) {
            $is_save_draft = $this->request->data['Deal']['is_save_draft'];
            unset($this->request->data['Deal']['is_save_draft']);
        }
        if (is_null($id) && !empty($this->request->data)) {
            $id = $this->request->data['Deal']['main_deal_id'];
            unset($this->request->data['Deal']['main_deal_id']);
        }
        $deal = $this->Deal->find('first', array(
            'conditions' => array(
                'Deal.id' => $id
            ) ,
            'contain' => array(
                'DealStatus',
                'Company'
            ) ,
            'recursive' => 2
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $subDealError = array();
        if (!empty($this->request->data)) {
            $subdeals = $this->request->data['Deal'];
            $this->Deal->validate = $this->Deal->validateSubDeal;
            if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                unset($this->Deal->validate['commission_percentage']['rule3']);
            }
            // An time deal validation unset process
            if ($deal['Deal']['is_anytime_deal']) {
                unset($this->Deal->validate['end_date']);
                unset($this->Deal->validate['coupon_expiry_date']);
                unset($this->Deal->validate['coupon_start_date']);
            }
            $j = 0;
            foreach($subdeals as & $subdeal) {
                $subdeal['start_date'] = $deal['Deal']['start_date'];
                $subdeal['end_date'] = $deal['Deal']['end_date'];
                $subdeal['coupon_start_date'] = $deal['Deal']['coupon_start_date'];
                $subdeal['coupon_expiry_date'] = $deal['Deal']['coupon_expiry_date'];
                $subdeal['parent_id'] = $deal['Deal']['id'];
                $subdeal['user_id'] = $deal['Deal']['user_id'];
                $subdeal['description'] = $deal['Deal']['description'];
                $subdeal['start_date'] = $deal['Deal']['start_date'];
                $subdeal['end_date'] = $deal['Deal']['end_date'];
                $subdeal['company_id'] = $deal['Deal']['company_id'];
                $subdeal['min_limit'] = $deal['Deal']['min_limit'];
                $subdeal['deal_status_id'] = 13;
                $this->Deal->set($subdeal);
                if (!$this->Deal->validates()) {
                    $subDealError[$j] = $this->Deal->validationErrors;
                }
                $j++;
				// Validating //
				$check_max_count+= $subdeal['max_limit'];
				// Chking Max limit for main Deal //
				if(empty($subdeal['max_limit'])){
					$is_update_main_deal_max_count = 0;
				}
            }
			// Validating with Main Deal (Only if all the max_limit for the sub deal is set //
			if($is_update_main_deal_max_count && ($check_max_count < $deal['Deal']['min_limit'])){
				for($f = 0;$f < $j;$f++){
					$subDealError[$f]['max_limit'] = __l('Total maximum coupon limit of the all the subdeal should be greater than minimum limit of the main deal.');
				}
			}
            if (!empty($subDealError)) {
                foreach($subDealError as $key => & $errors) {
                    foreach($errors as $index => $error) {
                        $this->Deal->validationErrors[$key][$index] = $error;
                    }
                }
            } else {
                foreach($subdeals as $sub) {
					if(!empty($sub)) {
						$this->Deal->create();
						// If advance amount given, calculating that amount //
						if ((Configure::read('deal.is_enable_payment_advance') == 1) && !empty($sub['is_enable_payment_advance'])) {
							$remaining_amount = $sub['discounted_price'] - $sub['pay_in_advance'];
							if (!empty($remaining_amount) && $remaining_amount > 0) {
								$sub['discounted_price'] = $sub['pay_in_advance'];
							}
						}
						
						$this->Deal->save($sub, false);
					}	
                }
                $maindeal['Deal']['id'] = $deal['Deal']['id'];
                $maindeal['Deal']['sub_deal_count'] = count($subdeals);
                if (!empty($this->request->data['Deal']['save_as_draft']) || !empty($is_save_draft)) {
                    $maindeal['Deal']['deal_status_id'] = ConstDealStatus::Draft;
                } elseif (!empty($this->request->data['Deal']['is_subdeal_available'])) { // For subdeal, untill subdeal gets added, the status will be  in draft status //
                    $maindeal['Deal']['deal_status_id'] = ConstDealStatus::Draft;
                } elseif ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                    $maindeal['Deal']['deal_status_id'] = ConstDealStatus::Upcoming;
                } else {
                    $maindeal['Deal']['deal_status_id'] = ConstDealStatus::PendingApproval;
                }
                $maindeal['Deal']['sub_deal_count'] = count($subdeals);
				if(!empty($is_update_main_deal_max_count)){				
					$maindeal['Deal']['max_limit'] = $check_max_count;
				}
                $this->Deal->save($maindeal, false);
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'index',
                        'type' => 'success',
                        'admin' => true
                    ));
                else:
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'company',
                        $deal['Company']['slug'],
                        'success',
                        'admin' => false
                    ));
                endif;
            }
        } else {
            if (($this->Auth->user('user_type_id') != ConstUserTypes::Admin) && (Configure::read('deal.is_admin_enable_commission'))):
                for ($i = 0; $i < 2; $i++) {
                    $this->request->data['Deal'][$i]['commission_percentage'] = Configure::read('deal.commission_amount');
                }
            endif;
        }
        $this->request->data['Deal']['main_deal_id'] = $id;
        $this->set('deal', $deal);
    }
    function subdeal_more($id = null)
    {
        $this->set('i', $id);
        if (($this->Auth->user('user_type_id') != ConstUserTypes::Admin) && (Configure::read('deal.is_admin_enable_commission'))):
            $this->request->data['Deal'][$id]['commission_percentage'] = Configure::read('deal.commission_amount');
        endif;
    }
    function admin_subdeal_edit($id = null)
    {
        if (is_null($id) && empty($this->request->data)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->setAction('subdeal_edit', $id);
    }
    function subdeal_edit($id = null)
    {
		$check_max_count = 0;
		$is_update_main_deal_max_count = 1;
        if (is_null($id) && empty($this->request->data)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (is_null($id) && !empty($this->request->data)) {
            $id = $this->request->data['Deal']['main_deal_id'];
            unset($this->request->data['Deal']['main_deal_id']);
        }
        $deal = $this->Deal->find('first', array(
            'conditions' => array(
                'Deal.id' => $id
            ) ,
            'contain' => array(
                'DealStatus',
                'Company',
                'SubDeal'
            ) ,
            'recursive' => 2
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $subDealError = array();
        if (!empty($this->request->data)) {
            $subdeals = $this->request->data['Deal'];
            $this->Deal->validate = $this->Deal->validateSubDeal;
            if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                unset($this->Deal->validate['commission_percentage']['rule3']);
            }
            $j = 0;
            foreach($subdeals as & $subdeal) {
                $subdeal['start_date'] = $deal['Deal']['start_date'];
                $subdeal['end_date'] = $deal['Deal']['end_date'];
                $subdeal['coupon_start_date'] = $deal['Deal']['coupon_start_date'];
                $subdeal['coupon_expiry_date'] = $deal['Deal']['coupon_expiry_date'];
                $subdeal['parent_id'] = $deal['Deal']['id'];
                $subdeal['user_id'] = $deal['Deal']['user_id'];
                $subdeal['description'] = $deal['Deal']['description'];
                $subdeal['start_date'] = $deal['Deal']['start_date'];
                $subdeal['end_date'] = $deal['Deal']['end_date'];
                $subdeal['company_id'] = $deal['Deal']['company_id'];
                $subdeal['min_limit'] = $deal['Deal']['min_limit'];
                $subdeal['deal_status_id'] = 13;
                $this->Deal->set($subdeal);
                if (!$this->Deal->validates()) {
                    $subDealError[($j) ] = $this->Deal->validationErrors;
                }
                $j++;
				// Validating //
				$check_max_count+= $subdeal['max_limit'];
				// Chking Max limit for main Deal //
				if(empty($subdeal['max_limit'])){
					$is_update_main_deal_max_count = 0;
				}
            }
			// Validating with Main Deal (Only if all the max_limit for the sub deal is set //
			if($is_update_main_deal_max_count && ($check_max_count < $deal['Deal']['min_limit'])){
				for($f = 0;$f < $j;$f++){
					$subDealError[$f]['max_limit'] = __l('Total maximum coupon limit of the all the subdeal should be greater than minimum limit of the main deal.');
				}
			}
            if (!empty($subDealError)) {
                foreach($subDealError as $key => & $errors) {
                    foreach($errors as $index => $error) {
                        $this->Deal->validationErrors[$key][$index] = $error;
                    }
                }
            } else {
                foreach($subdeals as $sub) {
                    if (empty($sub['Deal']['id'])) {
                        $this->Deal->create();
                    }
                    // If advance amount given, calculating that amount //
                    if ((Configure::read('deal.is_enable_payment_advance') == 1) && !empty($sub['is_enable_payment_advance'])) {
                        $remaining_amount = $sub['discounted_price'] - $sub['pay_in_advance'];
                        if (!empty($remaining_amount) && $remaining_amount > 0) {
                            $sub['discounted_price'] = $sub['pay_in_advance'];
                        }
                    }
                    $this->Deal->save($sub, false);
                }
                $maindeal['Deal']['id'] = $deal['Deal']['id'];
                $maindeal['Deal']['deal_status_id'] = $deal['Deal']['deal_status_id'];
                $maindeal['Deal']['sub_deal_count'] = count($subdeals);
				if(!empty($is_update_main_deal_max_count)){				
					$maindeal['Deal']['max_limit'] = $check_max_count;
				}
                $this->Deal->save($maindeal, false);
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'edit',
                        $id,
                        'admin' => true
                    ));
                } else {
                    $this->redirect(array(
                        'controller' => 'deals',
                        'action' => 'company',
                        $deal['Company']['slug'],
                        'success',
                        'admin' => false
                    ));
                }
            }
            $this->request->data['Deal']['main_deal_id'] = $id;
        } else {
            $this->request->data['Deal'] = $deal['SubDeal'];
            $this->request->data['Deal']['main_deal_id'] = $deal['Deal']['id'];
        }
        $this->set('deal', $deal);
    }
    function subdeal_delete($id = null, $main_deal_id = null)
    {
        $this->autoRender = false;
        if (is_null($id) || is_null($main_deal_id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $deal = $this->Deal->find('first', array(
            'conditions' => array(
                'Deal.id' => $main_deal_id
            ) ,
            'contain' => array(
                'DealStatus',
                'Company',
                'SubDeal'
            ) ,
            'recursive' => 2
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $available = false;
        foreach($deal['SubDeal'] as $subdeal) {
            if ($subdeal['id'] == $id) $available = true;
        }
        if ($available) {
            if (count($deal['SubDeal']) > 2) {
                $this->Deal->delete($id);
                echo "Success";
            } else {
                echo "Need Two Deals";
            }
        } else {
            echo "Fail";
        }
    }
}
?>
