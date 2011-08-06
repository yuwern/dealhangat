<?php
class DealUsersController extends AppController
{
    public $name = 'DealUsers';
    public $permanentCacheAction = array(
        'index' => array(
            'is_user_specific_url' => true
        ) ,
        'view' => array(
            'is_user_specific_url' => true
        ) ,
        'user_deals' => array(
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'DealUser.more_action_id',
            'DealUser.deal_name',
            'DealUser.coupon_code',
            'DealUser.r',
            'DealUser.id',
            'DealUser.filter_id',
            'DealUser.deal_id',
            'Deal.id',
        );
        parent::beforeFilter();
    }
    public function index($deal_id = null)
    {
        $this->disableCache();
        $this->_redirectGET2Named(array(
            'coupon_code'
        ));
        $db = $this->DealUser->getDataSource();
        $conditions = array();
        $this->set('deal_id', $deal_id);
        $company = $this->DealUser->Deal->User->Company->find('first', array(
            'conditions' => array(
                'Company.user_id' => $this->Auth->User('id')
            ) ,
            'fields' => array(
                'Company.id'
            ) ,
            'recursive' => - 1
        ));
        if (empty($this->request->params['named']['type']) && empty($this->request->params['named']['deal_id']) && empty($this->request->data)) {
            // Available tab count //
            $available_count_conditions = $deal_conditions = array();
            /*if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
            $available_count_conditions['DealUser.is_gift'] = 0;
            }*/
            if (!empty($deal_id) && ($this->Auth->user('user_type_id') != ConstUserTypes::User)) {
                $deal_conditions['DealUser.deal_id'] = $deal_id;
            } else {
                $deal_conditions['OR'] = array(
                    'DealUser.user_id' => $this->Auth->user('id') ,
                    'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                );
            }
            if ($this->Auth->user('user_type_id') != ConstUserTypes::User && !empty($deal_id)) {
                $deal_conditions['DealUser.deal_id'] = $deal_id;
            }
            if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Company && (!empty($this->request->params['named']['deal_id']) || !empty($deal_id))) {
                    $available_count_conditions['OR'] = array(
                        'Deal.user_id' => $this->Auth->user('id') ,
                        'Deal.company_id' => $company['Company']['id']
                    );
                } else {
                    $available_count_conditions['OR'] = array(
                        'AND' => array(
                            'DealUser.user_id' => $this->Auth->user('id') ,
                            'DealUser.is_gift' => 0
                        ) ,
                        'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                    );
                }
            }
            $available_count_conditions['DealUser.quantity >'] = $db->expression('DealUser.deal_user_coupon_count');
            $available_count_conditions['DealUser.is_canceled'] = 0;
            $available_count_conditions['DealUser.is_paid'] = 1;
            $available_count_conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Closed,
                ConstDealStatus::Tipped,
                ConstDealStatus::PaidToCompany
            );
            //$available_count_conditions['Deal.coupon_expiry_date >='] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
            $available_count_conditions['AND'] = array(
                'OR' => array(
                    'Deal.is_anytime_deal' => 1,
                    'Deal.coupon_expiry_date >=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true)
                ) ,
            );
            $availableCount = $this->DealUser->find('all', array(
                'conditions' => array_merge($deal_conditions, $available_count_conditions) ,
                'fields' => array(
                    'SUM(DealUser.quantity - DealUser.deal_user_coupon_count) as available_count'
                ) ,
                'contain' => array(
                    'Deal'
                ) ,
                'group' => array(
                    'DealUser.user_id'
                ) ,
                'recursive' => 1
            ));
            $this->set('available', !empty($availableCount[0][0]['available_count']) ? $availableCount[0][0]['available_count'] : '0');
            // Used tab count //
            $used = $this->DealUser->find('all', array(
                'conditions' => array_merge(array(
                    'DealUser.deal_user_coupon_count !=' => 0,
                    'DealUser.is_canceled' => 0
                ) , $deal_conditions) ,
                'fields' => array(
                    'SUM(DealUser.deal_user_coupon_count) as used_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('used', !empty($used[0][0]['used_count']) ? $used[0][0]['used_count'] : '0');
            unset($conditions['DealUser.is_repaid']); // NEED TO REMOVE/CHECK
            // Refund tab count //
            $refund = $this->DealUser->find('all', array(
                'conditions' => array_merge(array(
                    'DealUser.is_repaid' => 1,
                    'DealUser.is_canceled' => 0
                ) , $deal_conditions) ,
                'fields' => array(
                    'SUM(DealUser.quantity) as refund_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('refund', !empty($refund[0][0]['refund_count']) ? $refund[0][0]['refund_count'] : '0');
            // Expired tab count //
            $expired_conditions = array(); // Quick Fix //
            $expired_conditions['DealUser.is_repaid'] = 0;
            $expired_conditions['DealUser.is_canceled'] = 0;
            if (!empty($deal_id) && ($this->Auth->user('user_type_id') != ConstUserTypes::User)) {
                $expired_conditions['DealUser.deal_id'] = $deal_id;
                $expired_conditions['OR'] = array(
                    'Deal.coupon_expiry_date <=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
                    'Deal.deal_status_id' => ConstDealStatus::Expired
                );
            } else {
                $expired_conditions['AND'] = array(
                    array(
                        'OR' => array(
                            'DealUser.user_id' => $this->Auth->user('id') ,
                            'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                        ) ,
                    ) ,
                    array(
                        'OR' => array(
                            'Deal.coupon_expiry_date <=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
                            'Deal.deal_status_id' => ConstDealStatus::Expired
                        )
                    )
                );
            }
            $expired = $this->DealUser->find('all', array(
                'conditions' => $expired_conditions,
                'fields' => array(
                    'SUM(DealUser.quantity) as expired_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('expired', !empty($expired[0][0]['expired_count']) ? $expired[0][0]['expired_count'] : 0);
            // Open tab count //
            $open = $this->DealUser->find('all', array(
                'conditions' => array_merge(array(
                    'Deal.deal_status_id' => ConstDealStatus::Open,
                    'DealUser.is_canceled' => 0
                ) , $deal_conditions) ,
                'fields' => array(
                    'SUM(DealUser.quantity) as open_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('open', !empty($open[0][0]['open_count']) ? $open[0][0]['open_count'] : '0');
            // Canceled tab count //
            $canceled = $this->DealUser->find('all', array(
                'conditions' => array_merge(array(
                    'DealUser.is_canceled' => 1
                ) , $deal_conditions) ,
                'fields' => array(
                    'SUM(DealUser.quantity) as canceled_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('canceled', !empty($canceled[0][0]['canceled_count']) ? $canceled[0][0]['canceled_count'] : '0');
            // Gifted tab count //
            $gifted_count_conditions = array();
            if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Company) {
                    $gifted_count_conditions['OR'] = array(
                        'Deal.user_id' => $this->Auth->user('id') ,
                        'Deal.company_id' => $company['Company']['id']
                    );
                } else {
                    $gifted_count_conditions['DealUser.user_id'] = $this->Auth->user('id');
                }
            }
            $gifted_count_conditions['DealUser.is_gift'] = 1;
            $gifted_deals = $this->DealUser->find('all', array(
                'conditions' => array_merge($gifted_count_conditions, $deal_conditions) ,
                'fields' => array(
                    'SUM(DealUser.quantity) as gifted_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('gifted_deals', !empty($gifted_deals[0][0]['gifted_count']) ? $gifted_deals[0][0]['gifted_count'] : '0');
            // Recieved Gift tab count //
            $recieved_gift = $this->DealUser->find('all', array(
                'conditions' => array(
                    'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-',
                ) ,
                'fields' => array(
                    'SUM(DealUser.quantity) as recieved_gift_count'
                ) ,
                'recursive' => 1
            ));
            $this->set('recieved_gift', !empty($recieved_gift[0][0]['recieved_gift_count']) ? $recieved_gift[0][0]['recieved_gift_count'] : '0');
            // All tab count //
            if (!empty($deal_id)) {
                $all_conditions['DealUser.deal_id'] = $deal_id;
            } else {
                $all_conditions['DealUser.user_id'] = $this->Auth->User('id');
                $all_conditions['OR'] = array_merge(array(
                    'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-',
                ) , $all_conditions);
            }
            unset($all_conditions['DealUser.user_id']); // removed coz of multiple user id get merged, we're removing the one outside the OR condition.
            $all = $this->DealUser->find('all', array(
                'conditions' => $all_conditions,
                'fields' => array(
                    'SUM(DealUser.quantity) as all_count'
                ) ,
                'recursive' => - 1
            ));
            $this->set('all_deals', !empty($all[0][0]['all_count']) ? $all[0][0]['all_count'] : '0');
        } else {
            $show_coupon_code = 0;
            if (!empty($this->request->data['DealUser']['deal_id'])) {
                $this->request->params['named']['deal_id'] = $this->request->data['DealUser']['deal_id'];
            }
            $conditions = array();
            if (!empty($this->request->params['named']['deal_id'])) {
                $deal_id = $this->request->params['named']['deal_id'];
                $conditions['DealUser.is_canceled'] = 0;
            }
            $this->pageTitle = __l('Deal Coupons');
            $coupon_find_id = array();
            if (!empty($this->request->data['DealUser']['coupon_code'])) {
                $get_deal_user_id = $this->DealUser->DealUserCoupon->find('first', array(
                    'conditions' => array(
                        'OR' => array(
                            'DealUserCoupon.coupon_code like' => $this->request->data['DealUser']['coupon_code'] . '%',
                            'DealUserCoupon.unique_coupon_code like' => $this->request->data['DealUser']['coupon_code'] . '%',
                        )
                    ) ,
                    'recursive' => - 1
                ));
                $conditions['DealUser.id'] = $get_deal_user_id['DealUserCoupon']['deal_user_id'];
                $coupon_find_id[] = $get_deal_user_id['DealUserCoupon']['id'];
                $this->pageTitle.= ' - ' . $this->request->data['DealUser']['coupon_code'];
            }
            if (!empty($this->request->data['DealUser']['type'])) {
                $this->request->params['named']['type'] = $this->request->data['DealUser']['type'];
            }
            if ($this->Auth->user('user_type_id') == ConstUserTypes::Company && !empty($deal_id)) {
                $conditions['DealUser.deal_id'] = $deal_id;
                $conditions['DealUser.is_repaid'] = 0;
                $deal = $this->DealUser->Deal->find('first', array(
                    'conditions' => array(
                        'Deal.id' => $deal_id
                    ) ,
                    'recursive' => - 1
                ));
                if (empty($this->request->params['named']['type'])) {
                    $conditions['DealUser.is_canceled'] = 0;
                }
                if (!empty($deal) && ($deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped || $deal['Deal']['deal_status_id'] == ConstDealStatus::Closed || $deal['Deal']['deal_status_id'] == ConstDealStatus::PaidToCompany)) {
                    $show_coupon_code = 1;
                    if (!empty($this->request->data['DealUser']['deal_user_view'])) {
                        $this->request->params['named']['deal_user_view'] = $this->request->data['DealUser']['deal_user_view'];
                    }
                }
                $conditions['Deal.id'] = $this->request->params['named']['deal_id'];
                $this->pageTitle = '';
            } else if (isset($this->request->params['named']['deal_id'])) {
                $this->request->data['DealUser']['deal_id'] = $this->request->params['named']['deal_id'];
                if (!empty($deal) && ($deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped || $deal['Deal']['deal_status_id'] == ConstDealStatus::Closed || $deal['Deal']['deal_status_id'] == ConstDealStatus::PaidToCompany)) {
                    $show_coupon_code = 1;
                    if (!empty($this->request->data['DealUser']['deal_user_view'])) {
                        $this->request->params['named']['deal_user_view'] = $this->request->data['DealUser']['deal_user_view'];
                    }
                }
                $conditions['Deal.id'] = $this->request->params['named']['deal_id'];
            } elseif ($this->DealUser->User->isAllowed($this->Auth->user('user_type_id'))) {
                if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
                    $conditions['OR'] = array(
                        'DealUser.user_id' => $this->Auth->user('id') ,
                        'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                    );
                }
                if (empty($deal_id)) { // Checked for admin viewing his grouponpro in my stuffs tab
                    $conditions['OR'] = array(
                        'DealUser.user_id' => $this->Auth->user('id') ,
                        'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                    );
                }
            } else {
                throw new NotFoundException(__l('Invalid request'));
            }
            $company = $this->DealUser->Deal->User->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->User('id')
                ) ,
                'fields' => array(
                    'Company.id'
                ) ,
                'recursive' => - 1
            ));
            if (!empty($deal_id) && ($this->Auth->user('user_type_id') != ConstUserTypes::User)) {
                $conditions['DealUser.deal_id'] = $deal_id;
            } else {
                $conditions['OR'] = array(
                    'DealUser.user_id' => $this->Auth->user('id') ,
                    'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                );
            }
            $user = $this->DealUser->Deal->User->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->User('id')
                ) ,
                'fields' => array(
                    'Company.id'
                ) ,
                'recursive' => - 1
            ));
            $this->set('user', $user);
            $this->set('coupon_find_id', $coupon_find_id); //Used for search //
            $deal_user_count = !empty($deal_users['0']['Deal']['deal_user_count']) ? $deal_users['0']['Deal']['deal_user_count'] : '0';
            $this->set('deal_user_count', $deal_user_count);
            $this->set('pageTitle', $this->pageTitle);
            $moreActions = $this->DealUser->moreActions;
            if (!empty($this->request->params['named']['deal_user_view']) && $this->request->params['named']['deal_user_view'] == 'coupon') {
                $moreActions = array(
                    ConstMoreAction::Used => __l('Used') ,
                    ConstMoreAction::NotUsed => __l('Not Used')
                );
            }
            $this->set(compact('moreActions'));
            if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available') {
                /*if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
                $conditions['DealUser.is_gift'] = 0;
                }*/
            }
            if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available') {
                if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
                    if ($this->Auth->user('user_type_id') == ConstUserTypes::Company && !empty($this->request->params['named']['deal_id'])) {
                        $conditions['OR'] = array(
                            'Deal.user_id' => $this->Auth->user('id') ,
                            'Deal.company_id' => $company['Company']['id']
                        );
                    } else {
                        $conditions['OR'] = array(
                            'AND' => array(
                                'DealUser.user_id' => $this->Auth->user('id') ,
                                'DealUser.is_gift' => 0
                            ) ,
                            'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                        );
                    }
                }
                $conditions['DealUser.quantity >'] = $db->expression('DealUser.deal_user_coupon_count');
                $conditions['Deal.deal_status_id'] = array(
                    ConstDealStatus::Closed,
                    ConstDealStatus::Tipped,
                    ConstDealStatus::PaidToCompany
                );
                //$conditions['Deal.coupon_expiry_date >='] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
                $conditions['AND'] = array(
                    'OR' => array(
                        'Deal.is_anytime_deal' => 1,
                        'Deal.coupon_expiry_date >=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true)
                    ) ,
                );
                $conditions['DealUser.is_canceled'] = 0;
                $conditions['DealUser.is_paid'] = 1;
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'used') {
                $conditions['DealUser.deal_user_coupon_count !='] = 0;
                $conditions['DealUser.is_canceled'] = 0;
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'refund') {
                $conditions['DealUser.is_repaid'] = 1;
                $conditions['DealUser.is_canceled'] = 0;
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'gifted_deals') {
                if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
                    if ($this->Auth->user('user_type_id') == ConstUserTypes::Company && !empty($this->request->params['named']['deal_id'])) {
                        unset($conditions['DealUser.is_repaid']); // Quick fix //
                        $conditions['OR'] = array(
                            'Deal.user_id' => $this->Auth->user('id') ,
                            'Deal.company_id' => $company['Company']['id']
                        );
                    } else {
                        $conditions['DealUser.user_id'] = $this->Auth->user('id');
                    }
                }
                unset($conditions['DealUser.is_canceled']);
                $conditions['DealUser.is_gift'] = 1;
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recieved_gift_deals') {
                $conditions = array();
                $conditions['DealUser.gift_email'] = $this->Auth->user('email');
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'expired') {
                $conditions = array(); // Quick Fix //
                $conditions['DealUser.is_repaid'] = 0;
                $conditions['DealUser.is_canceled'] = 0;
                if (!empty($deal_id) && ($this->Auth->user('user_type_id') != ConstUserTypes::User)) {
                    $conditions['DealUser.deal_id'] = $deal_id;
                    $conditions['OR'] = array(
                        'Deal.coupon_expiry_date <=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
                        'Deal.deal_status_id' => ConstDealStatus::Expired
                    );
                } else {
                    $conditions['AND'] = array(
                        array(
                            'OR' => array(
                                'DealUser.user_id' => $this->Auth->user('id') ,
                                'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
                            ) ,
                        ) ,
                        array(
                            'OR' => array(
                                'Deal.coupon_expiry_date <=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
                                'Deal.deal_status_id' => ConstDealStatus::Expired
                            )
                        )
                    );
                }
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'open') {
                $conditions['Deal.deal_status_id'] = ConstDealStatus::Open;
                $conditions['DealUser.is_canceled'] = 0;
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'canceled') {
                $conditions['DealUser.is_canceled'] = 1;
            } elseif (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'all') {
                if ($this->Auth->user('user_type_id') == ConstUserTypes::Company) {
                    $this->pageTitle = sprintf(__l('My %s Coupons') , Configure::read('site.name'));
                } else {
                    $this->pageTitle = sprintf(__l('My %s Deals') , Configure::read('site.name'));
                    $conditions['OR'] = array_merge(array(
                        'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-',
                    ) , $conditions);
                }
                unset($conditions['DealUser.user_id']); // removed coz of multiple user id get merged.
                unset($conditions['DealUser.is_canceled']);
                unset($conditions['DealUser.is_repaid']);
            }
            if (!empty($this->request->params['named']['type'])) {
                $this->request->data['DealUser']['type'] = $this->request->params['named']['type'];
            }
            $this->paginate = array(
                'conditions' => $conditions,
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'User.user_type_id',
                            'User.username',
                            'User.id',
                        )
                    ) ,
                    'SubDeal' => array(
                        'fields' => array(
                            'SubDeal.id',
                            'SubDeal.name',
                            'SubDeal.discount_amount',
                        ) ,
                    ) ,
                    'Deal' => array(
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
                            )
                        ) ,
                        'DealStatus' => array(
                            'fields' => array(
                                'DealStatus.name',
                            )
                        ) ,
                        'Attachment',
                        'fields' => array(
                            'Deal.name',
                            'Deal.slug',
                            'Deal.coupon_start_date',
                            'Deal.coupon_expiry_date',
                            'Deal.deal_status_id',
                            'Deal.company_id',
                            'Deal.deal_user_count',
                            'Deal.coupon_condition',
                            'Deal.is_anytime_deal',
                            'Deal.parent_id',
                        )
                    ) ,
                    'CharitiesDealUser' => array(
                        'Charity' => array(
                            'fields' => array(
                                'Charity.id',
                                'Charity.name',
                                'Charity.url'
                            )
                        ) ,
                        'fields' => array(
                            'CharitiesDealUser.amount',
                            'CharitiesDealUser.site_commission_amount',
                            'CharitiesDealUser.seller_commission_amount'
                        )
                    ) ,
                    'DealUserCoupon',
                    'City',
                ) ,
                'order' => array(
                    'DealUser.created' => 'desc'
                ) ,
                'recursive' => 3
            );
            $deal_users = $this->paginate();
            $this->set('show_coupon_code', $show_coupon_code);
            $this->set('dealUsers', $deal_users);
            // <-- For iPhone App code
            if ($this->RequestHandler->prefers('json')) {
                if (!empty($deal_users)) {
                    $total_deals = count($deal_users);
                    for ($i = 0; $i < $total_deals; $i++) {
                        if (!empty($deal_users[$i]['DealUserCoupon'])) {
                            $total_coupons = count($deal_users[$i]['DealUserCoupon']);
                            for ($j = 0; $j < $total_coupons; $j++) {
                                if (Configure::read('barcode.is_barcode_enabled') == 1) {
                                    $symbology_code_url = '';
                                    if (Configure::read('barcode.symbology') == 'qr') {
                                        $parsed_url = parse_url(Router::url('/', true));
                                        $qr_mobile_site_url = str_ireplace($parsed_url['host'], 'm.' . $parsed_url['host'], Router::url(array(
                                            'controller' => 'deal_user_coupons',
                                            'action' => 'check_qr',
                                            $deal_users[$i]['DealUserCoupon'][$j]['coupon_code'],
                                            $deal_users[$i]['DealUserCoupon'][$j]['unique_coupon_code'],
                                            'admin' => false
                                        ) , true));
                                        $symbology_code_url = 'http://chart.apis.google.com/chart?cht=qr&chs=140x140&chl=' . $qr_mobile_site_url;
                                    } elseif (Configure::read('barcode.symbology') == 'c39') {
                                        $symbology_code_url = Router::url(array(
                                            'controller' => 'deals',
                                            'action' => 'barcode',
                                            $deal_users[$i]['Deal']['id']
                                        ) , true);
                                    }
                                    if (!empty($symbology_code_url)) {
                                        $symbology_code_url = '<p class="coupon-code-img"><img class="img" src="' . $symbology_code_url . '" /></p>';
                                    }
                                    $deal_users[$i]['DealUserCoupon'][$j]['symbology_code_url'] = $symbology_code_url;
                                    $deal_users[$i]['DealUserCoupon'][$j]['use_coupon_url'] = Router::url(array(
                                        'controller' => 'deal_user_coupons',
                                        'action' => 'update_status',
                                        $deal_users[$i]['DealUser']['id'],
                                        'coupon_id' => $deal_users[$i]['DealUserCoupon'][$j]['id'],
                                        'is_used',
                                        'admin' => false
                                    ) , true);
                                    $deal_users[$i]['Deal']['start_date'] = date('m/d/Y', strtotime($deal_users[$i]['Deal']['start_date']));
                                    $deal_users[$i]['Deal']['coupon_expiry_date'] = ($deal_users[$i]['Deal']['coupon_expiry_date']) ? date('m/d/Y', strtotime($deal_users[$i]['Deal']['coupon_expiry_date'])) : $deal_users[$i]['Deal']['coupon_expiry_date'];
                                    $deal_users[$i]['Deal']['coupon_start_date'] = date('m/d/Y', strtotime($deal_users[$i]['Deal']['coupon_start_date']));
                                } else {
                                    $deal_users[$i]['DealUserCoupon'][$j]['symbology_code_url'] = '';
                                    $deal_users[$i]['DealUserCoupon'][$j]['use_coupon_url'] = Router::url(array(
                                        'controller' => 'deal_user_coupons',
                                        'action' => 'update_status',
                                        $deal_users[$i]['DealUser']['id'],
                                        'coupon_id' => $deal_users[$i]['DealUserCoupon'][$j]['id'],
                                        'is_used',
                                        'admin' => false
                                    ) , true);
                                    $deal_users[$i]['Deal']['start_date'] = date('m/d/Y', strtotime($deal_users[$i]['Deal']['start_date']));
                                    $deal_users[$i]['Deal']['coupon_expiry_date'] = ($deal_users[$i]['Deal']['coupon_expiry_date']) ? date('m/d/Y', strtotime($deal_users[$i]['Deal']['coupon_expiry_date'])) : $deal_users[$i]['Deal']['coupon_expiry_date'];
                                    $deal_users[$i]['Deal']['coupon_start_date'] = date('m/d/Y', strtotime($deal_users[$i]['Deal']['coupon_start_date']));
                                }
                            }
                        }
                        $this->DealUser->Deal->saveiPhoneAppThumb($deal_users[$i]['Deal']['Attachment']);
                        $image_options = array(
                            'dimension' => 'iphone_big_thumb',
                            'class' => '',
                            'alt' => $deal_users[$i]['Deal']['name'],
                            'title' => $deal_users[$i]['Deal']['name'],
                            'type' => 'jpg'
                        );
                        $iphone_big_thumb = $this->DealUser->Deal->getImageUrl('Deal', $deal_users[$i]['Deal']['Attachment'][0], $image_options);
                        $deal_users[$i]['Deal']['iphone_big_thumb'] = $iphone_big_thumb;
                        $image_options = array(
                            'dimension' => 'iphone_small_thumb',
                            'class' => '',
                            'alt' => $deal_users[$i]['Deal']['name'],
                            'title' => $deal_users[$i]['Deal']['name'],
                            'type' => 'jpg'
                        );
                        $iphone_small_thumb = $this->DealUser->Deal->getImageUrl('Deal', $deal_users[$i]['Deal']['Attachment'][0], $image_options);
                        $deal_users[$i]['Deal']['iphone_small_thumb'] = $iphone_small_thumb;
                    }
                }
                $this->view = 'Json';
                $this->set('json', (empty($this->viewVars['iphone_response'])) ? $deal_users : $this->viewVars['iphone_response']);
            }
            // For iPhone App code -->
            
        }
        if (!empty($this->request->params['named']['deal_id'])) {
            $deal = $this->DealUser->Deal->_getDealInfo($this->request->params['named']['deal_id']);
            $this->set('deal_info', $deal);
        }
    }
    public function update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data['DealUser'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $dealUserIds = array();
            $dealusercoupon_code = array();
            if (!empty($this->request->data['DealUserCoupon']) && $actionid) {
                foreach($this->request->data['DealUserCoupon'] as $dealusercoupon_id => $code) {
                    if (!empty($code['unique_coupon_code'])) {
                        $dealusercoupon_code[$dealusercoupon_id] = $code['unique_coupon_code'];
                    }
                }
                if (!empty($dealusercoupon_code)) {
                    if ($actionid == ConstMoreAction::Used) {
                        foreach($dealusercoupon_code as $id => $code) {
                            $conditions = array();
                            $conditions['DealUserCoupon.id'] = $id;
                            $conditions['DealUserCoupon.unique_coupon_code'] = $code;
                            $conditions['DealUserCoupon.is_used'] = 0;
                            $get_deal_user_coupons = $this->DealUser->DealUserCoupon->find('first', array(
                                'conditions' => $conditions,
                                'recursive' => - 1
                            ));
                            if (!empty($get_deal_user_coupons)) {
                                $DealUserCoupons = array();
                                $DealUserCoupons['id'] = $id;
                                $DealUserCoupons['is_used'] = '1';
                                $this->DealUser->DealUserCoupon->save($DealUserCoupons);
                            }
                        }
                    } else if ($actionid == ConstMoreAction::NotUsed) {
                        foreach($dealusercoupon_code as $id => $code) {
                            $conditions = array();
                            $conditions['DealUserCoupon.id'] = $id;
                            $conditions['DealUserCoupon.unique_coupon_code'] = $code;
                            $conditions['DealUserCoupon.is_used'] = 1;
                            $get_deal_user_coupons = $this->DealUser->DealUserCoupon->find('first', array(
                                'conditions' => $conditions,
                                'recursive' => - 1
                            ));
                            if (!empty($get_deal_user_coupons)) {
                                $DealUserCoupons = array();
                                $DealUserCoupons['id'] = $id;
                                $DealUserCoupons['is_used'] = '0';
                                $this->DealUser->DealUserCoupon->save($DealUserCoupons);
                            }
                        }
                    }
                }
            }
            foreach($this->request->data['DealUser'] as $dealuser_id => $is_checked) {
                if ($is_checked['id']) {
                    $dealUserIds[] = $dealuser_id;
                }
            }
            $DealUserCoupons = array();
            $get_deal_user_coupons = array();
            if ($actionid && !empty($dealUserIds)) {
                if ($actionid == ConstMoreAction::Used) {
                    unset($dealUserIds['0']);
                    foreach($dealUserIds as $dealUserId) {
                        $get_deal_user_coupons = $this->DealUser->DealUserCoupon->find('all', array(
                            'conditions' => array(
                                'DealUserCoupon.deal_user_id' => $dealUserId
                            ) ,
                            'recursive' => - 1
                        ));
                        foreach($get_deal_user_coupons as $get_deal_user_coupon) {
                            if (!empty($get_deal_user_coupon)) {
                                $DealUserCoupons['id'] = $get_deal_user_coupon['DealUserCoupon']['id'];
                                $DealUserCoupons['is_used'] = '1';
                                $this->DealUser->DealUserCoupon->save($DealUserCoupons);
                            }
                        }
                    }
                    $this->Session->setFlash(__l('Checked users status has been changed') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::NotUsed) {
                    unset($dealUserIds['0']);
                    foreach($dealUserIds as $dealUserId) {
                        $get_deal_user_coupons = $this->DealUser->DealUserCoupon->find('all', array(
                            'conditions' => array(
                                'DealUserCoupon.deal_user_id' => $dealUserId
                            ) ,
                            'recursive' => - 1
                        ));
                        foreach($get_deal_user_coupons as $get_deal_user_coupon) {
                            if (!empty($get_deal_user_coupon)) {
                                $DealUserCoupons['id'] = $get_deal_user_coupon['DealUserCoupon']['id'];
                                $DealUserCoupons['is_used'] = '0';
                                $this->DealUser->DealUserCoupon->save($DealUserCoupons);
                            }
                        }
                    }
                    $this->Session->setFlash(__l('Checked users status has been changed') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::Delete) {
                    unset($dealUserIds['0']);
                    foreach($dealUserIds as $dealUserId) {
                        $this->DealUser->DealUserCoupon->deleteAll(array(
                            'DealUserCoupon.deal_user_id' => $dealUserId
                        ));
                        $this->DealUser->delete($dealUserId);
                    }
                    $this->Session->setFlash(__l('Deal User deleted') , 'default', null, 'success');
                }
            }
        }
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(Router::url('/', true) . $r);
        } else {
            $this->redirect($r);
        }
    }
    public function view($id = null)
    {
        $coupon_conditions = array();
        if (!empty($this->request->params['named']['coupon_id'])) {
            $coupon_conditions['DealUserCoupon.id'] = $this->request->params['named']['coupon_id'];
        }
        if (!empty($this->request->params['named']['filter_id'])) {
            $db = $this->DealUser->getDataSource();
            if (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'available') {
                $conditions['DealUser.quantity >'] = $db->expression('DealUser.deal_user_coupon_count');
                $coupon_conditions['DealUserCoupon.is_used'] = 0;
                $conditions['AND'] = array(
                    'OR' => array(
                        'Deal.is_anytime_deal' => 1,
                        'Deal.coupon_expiry_date >=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true)
                    ) ,
                );
            } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'used') {
                $coupon_conditions['DealUserCoupon.is_used'] = 1;
                $conditions['DealUser.deal_user_coupon_count !='] = 0;
            } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'expired') {
                $conditions['Deal.coupon_expiry_date <='] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
                $conditions['DealUser.is_repaid'] = 0;
            } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'gifted_deals') {
                $conditions['DealUser.is_gift'] = 1;
            } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'open') {
                $conditions['Deal.deal_status_id'] = ConstDealStatus::Open;
            } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'refunded') {
                $conditions['Deal.deal_status_id'] = ConstDealStatus::Refunded;
                $conditions['DealUser.is_repaid'] = 1;
            }
        }
        if ($this->Auth->user('user_type_id') == ConstUserTypes::Company) {
            $deal = $this->DealUser->find('first', array(
                'conditions' => array(
                    'DealUser.id' => $id
                ) ,
                'contain' => array(
                    'Deal'
                ) ,
                'recursive' => 0
            ));
            $company = $this->DealUser->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'recursive' => - 1
            ));
        }
        $conditions['DealUser.id'] = $id;
        if ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
            $conditions['OR'] = array(
                'DealUser.user_id' => $this->Auth->user('id') ,
                'DealUser.gift_email' => ($this->Auth->user('email')) ? $this->Auth->user('email') : '-'
            );
            if (!empty($company['Company']['user_id']) && !empty($deal['Deal']['company_id']) && ($company['Company']['id'] == $deal['Deal']['company_id'])) {
                unset($conditions['OR']);
            }
        }
        $dealUser = $this->DealUser->find('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                    )
                ) ,
                'DealUserCoupon' => array(
                    'conditions' => $coupon_conditions
                ) ,
                'Deal' => array(
                    'CompanyAddressesDeal',
                    'Company' => array(
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
                        ) ,
                        'Country' => array(
                            'fields' => array(
                                'Country.name'
                            )
                        )
                    ) ,
					'Attachment',
                    'fields' => array(
                        'Deal.is_redeem_at_all_branch_address',
                        'Deal.is_redeem_in_main_address',
                        'Deal.coupon_condition',
                        'Deal.coupon_expiry_date',
                        'Deal.coupon_start_date',
                        'Deal.name',
                        'Deal.id',
                        'Deal.is_anytime_deal',
                    ) ,
                ) ,
                'SubDeal'
            ) ,
        ));
        if (empty($dealUser)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'print') {
            $this->layout = 'print';
        }
        $this->set('dealUser', $dealUser);
    }
    public function user_deals($user_id = null)
    {
        if (!empty($this->request->params['named']['user_id'])) {
            $conditions = array(
                'DealUser.user_id' => $this->request->params['named']['user_id'],
                'DealUser.is_repaid' => 0,
            );
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'Deal' => array(
                    'Attachment' => array(
                        'fields' => array(
                            'Attachment.id',
                            'Attachment.dir',
                            'Attachment.filename',
                            'Attachment.width',
                            'Attachment.height'
                        )
                    ) ,
                    'fields' => array(
                        'Deal.name',
                        'Deal.slug',
                    ) ,
                ) ,
            )
        );
        $this->DealUser->recursive = 2;
        $user_deals = $this->paginate();
        $this->set('user_deals', $user_deals);
    }
    public function admin_index()
    {
        $this->disableCache();
        $this->pageTitle = __l('All Coupons');
        if (!empty($this->request->data['DealUser']['deal_name'])) {
            $this->request->params['named']['deal_name'] = $this->request->data['DealUser']['deal_name'];
        }
        if (!empty($this->request->data['DealUser']['coupon_code'])) {
            $this->request->params['named']['coupon_code'] = $this->request->data['DealUser']['coupon_code'];
        }
        if (!empty($this->request->data['DealUser']['filter_id'])) {
            $this->request->params['named']['filter_id'] = $this->request->data['DealUser']['filter_id'];
        }
        if (!empty($this->request->data['DealUser']['deal_id'])) {
            $this->request->params['named']['deal_id'] = $this->request->data['DealUser']['deal_id'];
        }
        $conditions = array();
        $coupon_find_id = array();
        $conditions['DealUser.is_repaid'] = 0;
        $is_show_coupon_code = 0;
        $param_string = '';
        if (!empty($this->request->data['DealUser'])) {
            $param_string.= !empty($this->request->params['named']['filter_id']) ? '/filter_id:' . $this->request->params['named']['filter_id'] : '';
            $param_string.= !empty($this->request->params['named']['deal_name']) ? '/deal_name:' . $this->request->params['named']['deal_name'] : '';
            $param_string.= !empty($this->request->params['named']['coupon_code']) ? '/coupon_code:' . $this->request->params['named']['coupon_code'] : '';
            $param_string.= !empty($this->request->params['named']['deal_id']) ? '/deal_id:' . $this->request->params['named']['deal_id'] : '';
        }
        if (isset($this->request->params['named']['deal_name'])) {
            $this->request->data['DealUser']['deal_name'] = $this->request->params['named']['deal_name'];
            $conditions['Deal.name'] = $this->request->params['named']['deal_name'];
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->params['named']['deal_name']);
        }
        if (!empty($this->request->params['named']['coupon_code'])) {
            $this->request->data['DealUser']['coupon_code'] = $this->request->params['named']['coupon_code'];
            $get_deal_user_id = $this->DealUser->DealUserCoupon->find('first', array(
                'conditions' => array(
                    'OR' => array(
                        'DealUserCoupon.coupon_code like' => $this->request->data['DealUser']['coupon_code'] . '%',
                        'DealUserCoupon.unique_coupon_code like' => $this->request->data['DealUser']['coupon_code'] . '%',
                    )
                ) ,
                'recursive' => - 1
            ));
            $conditions['DealUser.id'] = $get_deal_user_id['DealUserCoupon']['deal_user_id'];
            $coupon_find_id[] = $get_deal_user_id['DealUserCoupon']['id'];
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->params['named']['coupon_code']);
        }
        if (isset($this->request->params['named']['deal_id'])) {
            $conditions['Deal.id'] = $this->request->params['named']['deal_id'];
            $this->request->data['DealUser']['deal_id'] = $this->request->params['named']['deal_id'];
        }
        $db = $this->DealUser->getDataSource();
        $conditions['DealUser.is_canceled'] = 0;
        if (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'available') {
            $conditions['DealUser.quantity >'] = $db->expression('DealUser.deal_user_coupon_count');
            $conditions['Deal.deal_status_id'] = array(
                ConstDealStatus::Closed,
                ConstDealStatus::Tipped,
                ConstDealStatus::PaidToCompany
            );
            //$conditions['Deal.coupon_expiry_date >='] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
            $conditions['AND'] = array(
                'OR' => array(
                    'Deal.is_anytime_deal' => 1,
                    'Deal.coupon_expiry_date >=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true)
                ) ,
            );
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'used') {
            $conditions['DealUser.deal_user_coupon_count !='] = 0;
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'expired') {
            $conditions['Deal.coupon_expiry_date <='] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
            $conditions['DealUser.is_repaid'] = 0;
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'gifted_deals') {
            unset($conditions['DealUser.is_canceled']);
            unset($conditions['DealUser.is_repaid']);
            $conditions['DealUser.is_gift'] = 1;
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'open') {
            $conditions['Deal.deal_status_id'] = ConstDealStatus::Open;
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'canceled') {
            $conditions['DealUser.is_canceled'] = 1;
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'refunded') {
            $conditions['Deal.deal_status_id'] = ConstDealStatus::Refunded;
            $conditions['DealUser.is_repaid'] = 1;
        } elseif (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'all') {
            unset($conditions['DealUser.is_canceled']);
            unset($conditions['DealUser.is_repaid']);
        } else {
            unset($conditions['DealUser.is_repaid']);
        }
        if (!empty($this->request->params['named']['deal_name'])) {
            $this->request->data['DealUser']['deal_name'] = $this->request->params['named']['deal_name'];
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->params['named']['deal_name']);
            $conditions['Deal.name'] = $this->request->data['DealUser']['deal_name'];
        }
        if (!empty($this->request->params['named']['filter_id'])) {
            $this->request->data['DealUser']['filter_id'] = $this->request->params['named']['filter_id'];
        }
        // Citywise admin filter //
        $city_filter_id = $this->Session->read('city_filter_id');
        if (!empty($city_filter_id)) {
            $deal_cities = $this->DealUser->Deal->City->find('first', array(
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
            $conditions['Deal.id'] = $city_deal_id;
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'UserAvatar',
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                        'User.fb_user_id'
                    )
                ) ,
                'CharitiesDealUser' => array(
                    'Charity' => array(
                        'fields' => array(
                            'Charity.id',
                            'Charity.name',
                            'Charity.url'
                        )
                    ) ,
                    'fields' => array(
                        'CharitiesDealUser.amount',
                        'CharitiesDealUser.site_commission_amount',
                        'CharitiesDealUser.seller_commission_amount'
                    )
                ) ,
                'DealUserCoupon',
                'SubDeal' => array(
                    'fields' => array(
                        'SubDeal.id',
                        'SubDeal.name',
                        'SubDeal.discount_amount',
                    ) ,
                ) ,
                'City',
                'Deal' => array(
                    'fields' => array(
                        'Deal.name',
                        'Deal.slug'
                    ) ,
                    'Attachment' => array(
                        'fields' => array(
                            'Attachment.id',
                            'Attachment.dir',
                            'Attachment.filename',
                            'Attachment.mimetype',
                            'Attachment.filesize',
                            'Attachment.height',
                            'Attachment.width',
                        )
                    )
                )
            ) ,
            'order' => array(
                'DealUser.id' => 'desc'
            ) ,
            'recursive' => 2
        );
        if (!empty($this->request->params['named']['deal_id'])) {
            $check_deal = $this->DealUser->Deal->find('first', array(
                'conditions' => array(
                    'Deal.id' => $this->request->params['named']['deal_id'],
                    'Deal.deal_status_id' => array(
                        ConstDealStatus::Tipped,
                        ConstDealStatus::Closed,
                        ConstDealStatus::PaidToCompany,
                    ) ,
                ) ,
                'fields' => array(
                    'Deal.id',
                    'Deal.name',
                    'Deal.deal_status_id',
                ) ,
                'recursive' => - 1
            ));
            if (!empty($check_deal)) {
                $is_show_coupon_code = 1;
            }
        }
        $this->set('dealUsers', $this->paginate());
        // Citywise admin filter //
        $count_conditions = array();
        $available_conditions = array();
        if (!empty($city_filter_id)) {
            $deal_cities = $this->DealUser->Deal->City->find('first', array(
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
            $count_conditions['Deal.id'] = $city_deal_id;
        }
        //	Configure::write('debug',1);
        $available_conditions['DealUser.quantity >'] = $db->expression('DealUser.deal_user_coupon_count');
        $available_conditions['DealUser.is_repaid'] = 0;
        $available_conditions['DealUser.is_canceled'] = 0;
        $available_conditions['Deal.deal_status_id'] = array(
            ConstDealStatus::Closed,
            ConstDealStatus::Tipped,
            ConstDealStatus::PaidToCompany
        );
        $available_conditions['Deal.coupon_expiry_date >='] = _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true);
        $available = $this->DealUser->find('all', array(
            'conditions' => array_merge($available_conditions, $count_conditions) ,
            'fields' => array(
                'SUM(DealUser.quantity - DealUser.deal_user_coupon_count) as available_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('available', !empty($available[0][0]['available_count']) ? $available[0][0]['available_count'] : '0');
        $used = $this->DealUser->find('all', array(
            'conditions' => array_merge(array(
                'DealUser.deal_user_coupon_count !=' => 0,
                'DealUser.is_canceled' => 0,
                'DealUser.is_repaid' => 0
            ) , $count_conditions) ,
            'fields' => array(
                'SUM(DealUser.deal_user_coupon_count) as used_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('used', !empty($used[0][0]['used_count']) ? $used[0][0]['used_count'] : '0');
        $expired = $this->DealUser->find('all', array(
            'conditions' => array_merge(array(
                'Deal.coupon_expiry_date <=' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
                'DealUser.is_repaid' => 0,
                'DealUser.is_canceled' => 0,
            ) , $count_conditions) ,
            'fields' => array(
                'SUM(DealUser.quantity) as expired_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('expired', !empty($expired[0][0]['expired_count']) ? $expired[0][0]['expired_count'] : '0');
        $open = $this->DealUser->find('all', array(
            'conditions' => array_merge(array(
                'Deal.deal_status_id' => ConstDealStatus::Open,
                'DealUser.is_repaid' => 0,
                'DealUser.is_canceled' => 0,
            ) , $count_conditions) ,
            'fields' => array(
                'SUM(DealUser.quantity) as open_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('open', !empty($open[0][0]['open_count']) ? $open[0][0]['open_count'] : '0');
        $canceled = $this->DealUser->find('all', array(
            'conditions' => array_merge(array(
                'DealUser.is_canceled' => 1
            ) , $count_conditions) ,
            'fields' => array(
                'SUM(DealUser.quantity) as canceled_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('canceled', !empty($canceled[0][0]['canceled_count']) ? $canceled[0][0]['canceled_count'] : '0');
        $gifted_count_conditions['DealUser.is_gift'] = 1;
        $gifted_deals = $this->DealUser->find('all', array(
            'conditions' => $gifted_count_conditions,
            'fields' => array(
                'SUM(DealUser.quantity) as gifted_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('gifted_deals', !empty($gifted_deals[0][0]['gifted_count']) ? $gifted_deals[0][0]['gifted_count'] : '0');
        $refund = $this->DealUser->find('all', array(
            'conditions' => array_merge(array(
                'Deal.deal_status_id' => ConstDealStatus::Refunded,
                'DealUser.is_repaid' => 1
            ) , $count_conditions) ,
            'fields' => array(
                'SUM(DealUser.quantity) as refund_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('refunded', !empty($refund[0][0]['refund_count']) ? $refund[0][0]['refund_count'] : '0');
        $all = $this->DealUser->find('all', array(
            'conditions' => $count_conditions,
            'fields' => array(
                'SUM(DealUser.quantity) as all_count'
            ) ,
            'recursive' => 1
        ));
        $this->set('all', !empty($all[0][0]['all_count']) ? $all[0][0]['all_count'] : '0');
        $this->set('coupon_find_id', $coupon_find_id);
        $this->set('is_show_coupon_code', $is_show_coupon_code);
        $moreActions = $this->DealUser->moreActions;
        if (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == 'available')) {
            unset($moreActions[ConstMoreAction::NotUsed]); // NotUsed options will not show for 'available' filter
            
        } elseif (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == 'used')) {
            unset($moreActions[ConstMoreAction::Used]); // NotUsed options will not show for 'available' filter
            
        }
        $this->set(compact('moreActions'));
        $this->set('param_string', $param_string);
        $this->set('pageTitle', $this->pageTitle);
    }
    public function admin_update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data['DealUser'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $dealUserIds = array();
            foreach($this->request->data['DealUser'] as $dealuser_id => $is_checked) {
                if ($is_checked['id']) {
                    $dealUserIds[] = $dealuser_id;
                }
            }
            if ($actionid && !empty($dealUserIds)) {
                if ($actionid == ConstMoreAction::Used) {
                    unset($dealUserIds['0']);
                    foreach($dealUserIds as $dealUserId) {
                        $get_deal_user_coupons = $this->DealUser->DealUserCoupon->find('all', array(
                            'conditions' => array(
                                'DealUserCoupon.deal_user_id' => $dealUserId
                            ) ,
                            'recursive' => - 1
                        ));
                        foreach($get_deal_user_coupons as $get_deal_user_coupon) {
                            if (!empty($get_deal_user_coupon)) {
                                $DealUserCoupons['id'] = $get_deal_user_coupon['DealUserCoupon']['id'];
                                $DealUserCoupons['is_used'] = '1';
                                $this->DealUser->DealUserCoupon->save($DealUserCoupons);
                            }
                        }
                    }
                    $this->Session->setFlash(__l('Checked users status has been changed') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::NotUsed) {
                    foreach($dealUserIds as $dealUserId) {
                        $get_deal_user_coupons = $this->DealUser->DealUserCoupon->find('all', array(
                            'conditions' => array(
                                'DealUserCoupon.deal_user_id' => $dealUserId
                            ) ,
                            'recursive' => - 1
                        ));
                        foreach($get_deal_user_coupons as $get_deal_user_coupon) {
                            if (!empty($get_deal_user_coupon)) {
                                $DealUserCoupons['id'] = $get_deal_user_coupon['DealUserCoupon']['id'];
                                $DealUserCoupons['is_used'] = '0';
                                $this->DealUser->DealUserCoupon->save($DealUserCoupons);
                            }
                        }
                    }
                    $this->Session->setFlash(__l('Checked users status has been changed') , 'default', null, 'success');
                } else if ($actionid == ConstMoreAction::Delete) {
                    unset($dealUserIds['0']);
                    foreach($dealUserIds as $dealUserId) {
                        $this->DealUser->DealUserCoupon->deleteAll(array(
                            'DealUserCoupon.deal_user_id' => $dealUserId
                        ));
                        $this->DealUser->delete($dealUserId);
                    }
                    $this->Session->setFlash(__l('Deal User deleted') , 'default', null, 'success');
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
        if ($this->DealUser->delete($id)) {
            $this->DealUser->DealUserCoupon->deleteAll(array(
                'DealUserCoupon.deal_user_id' => $id
            ));
            $this->Session->setFlash(__l('Deal User deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function update_status($deal_user_id = null, $field = 'is_used')
    {
        $action = 1;
        $this->autoRender = false;
        if (!empty($deal_user_id)) {
            $DealUser = $this->DealUser->find('first', array(
                'conditions' => array(
                    'DealUser.id' => $deal_user_id
                ) ,
                'contain' => array(
                    'Deal',
                    'DealUserCoupon',
                ) ,
                'recursive' => 2
            ));
        }
        $user = $this->DealUser->Deal->User->Company->find('first', array(
            'conditions' => array(
                'Company.user_id' => $this->Auth->User('id')
            ) ,
            'fields' => array(
                'Company.id'
            ) ,
            'recursive' => - 1
        ));
        if ($DealUser['DealUser']['quantity'] > $DealUser['DealUser']['deal_user_coupon_count']) {
            $status = 1;
        } elseif ($DealUser['DealUser']['quantity'] == $DealUser['DealUser']['deal_user_coupon_count']) {
            $status = 0;
            if (($DealUser['Deal']['company_id'] != $user['Company']['id']) && !empty($user['Company']['id']) && $user['User']['user_type_id'] != ConstUserTypes::Company) {
                $action = 0;
            }
        } else {
            $status = 1;
        }
        if (!empty($action)) {
            $DealUserCoupon = array();
            foreach($DealUser['DealUserCoupon'] as $deal_user_coupon) {
                $DealUserCoupon['id'] = $deal_user_coupon['id'];
                $DealUserCoupon['is_used'] = $status;
                $this->DealUser->DealUserCoupon->save($DealUserCoupon);
            }
        }
        echo $action;
    }
    public function cancel_deal($id = null)
    {
        if (empty($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $dealuser = $this->DealUser->find('first', array(
            'conditions' => array(
                'DealUser.id' => $id,
                'DealUser.user_id' => $this->Auth->user('id') ,
                'Deal.deal_status_id' => ConstDealStatus::Open,
                'DealUser.is_canceled' => 0,
            ) ,
            'contain' => array(
                'Deal',
                'SubDeal',
                'User',
                'PaypalDocaptureLog',
                'AuthorizenetDocaptureLog',
                'PaypalTransactionLog'
            ) ,
            'recursive' => 0
        ));
        if (empty($dealuser)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        // SubDeal: If subdeal, setting subdeal array as Deal array, so all the cancel process remain for the sub and not the main //
        if (!empty($dealuser['Deal']['is_subdeal_available']) && !empty($dealuser['SubDeal'])) {
            $temp_deal = array();
            $temp_deal = $dealuser['Deal'];
            unset($dealuser['Deal']);
            $dealuser['Deal'] = $dealuser['SubDeal'];
        }
        $response = $this->DealUser->Deal->_refundDealAmountForCacel($dealuser);
        // SubDeal: Resetting the actual deal array //
        if (!empty($temp_deal)) {
            $dealuser['Deal'] = $temp_deal;
        }
        if (!is_array($response)) {
            $_data['DealUser']['id'] = $dealuser['DealUser']['id'];
            $_data['DealUser']['is_canceled'] = 1;
            $this->DealUser->save($_data);
            $this->DealUser->Deal->UpdateAll(array(
                'Deal.deal_user_count' => $dealuser['Deal']['deal_user_count'] - $dealuser['DealUser']['quantity']
            ) , array(
                'Deal.id' => $dealuser['Deal']['id']
            ));
            // SubDeal: Reducing the count for sub deal too //
            if (!empty($dealuser['Deal']['is_subdeal_available']) && !empty($dealuser['SubDeal'])) {
                $this->DealUser->Deal->UpdateAll(array(
                    'Deal.deal_user_count' => $dealuser['SubDeal']['deal_user_count'] - $dealuser['DealUser']['quantity']
                ) , array(
                    'Deal.id' => $dealuser['SubDeal']['id']
                ));
            }
            $this->Session->setFlash(__l('Deal canceled successfully.') , 'default', null, 'success');
        } else {
            $this->Session->setFlash(sprintf(__l('Gateway error: %s <br>Note: Due to security reasons, error message from gateway may not be verbose. Please double check your card number, security number and address details. Also, check if you have enough balance in your card.') , $response['message']) , 'default', null, 'error');
        }
        $this->redirect(array(
            'controller' => 'users',
            'action' => 'my_stuff',
            '#My_Purchases'
        ));
    }
}
?>