<?php
class DealUserCouponsController extends AppController
{
    public $name = 'DealUserCoupons';
    public function update_status($deal_user_id = null, $field = 'is_used')
    {
        $action = 1;
        if (!empty($deal_user_id) && !empty($this->request->params['named']['coupon_id'])) {
            $DealUserCoupon = $this->DealUserCoupon->find('first', array(
                'conditions' => array(
                    'DealUserCoupon.id' => $this->request->params['named']['coupon_id'],
                    'DealUserCoupon.deal_user_id' => $deal_user_id
                ) ,
                'contain' => array(
                    'DealUser' => array(
                        'Deal'
                    ) ,
                ) ,
                'recursive' => 2
            ));
        }
        $user = $this->DealUserCoupon->DealUser->Deal->User->Company->find('first', array(
            'conditions' => array(
                'Company.user_id' => $this->Auth->User('id')
            ) ,
            'fields' => array(
                'Company.id'
            ) ,
            'recursive' => - 1
        ));
        if ($DealUserCoupon['DealUserCoupon'][$field] == 1) {
            $status = 0;
            if (($DealUserCoupon['DealUser']['Deal']['company_id'] != $user['Company']['id']) && !empty($user['Company']['id'])) {
                $action = 0;
            }
        } else {
            $status = 1;
        }
        if (!empty($action)) {
            $DealUserCoupon = array();
            $DealUserCoupon['id'] = $this->request->params['named']['coupon_id'];
            $DealUserCoupon['is_used'] = $status;
            $this->DealUserCoupon->save($DealUserCoupon);
        }
        if ($this->RequestHandler->prefers('json')) {
            $resonse = array(
                'status' => 0,
                'message' => __l('Success')
            );
            $this->view = 'Json';
            $this->set('json', (empty($this->viewVars['iphone_response'])) ? $resonse : $this->viewVars['iphone_response']);
        } else {
			if($this->layoutPath == 'touch')
			{
				$this->redirect(array(
					'controller' => 'deal_users',
					'action' => 'view',
					$deal_user_id,
					'admin' => false
				));
			} else {
				$this->autoRender = false;
				echo $action;
			}
        }
    }
    public function coupon_update_status($deal_user_id = null, $field = 'use')
    {
        $action = 1;
        if (!empty($deal_user_id) && !empty($this->request->params['named']['coupon_id'])) {
            $conditions = array();
            $conditions['DealUserCoupon.id'] = $this->request->params['named']['coupon_id'];
            $conditions['DealUserCoupon.deal_user_id'] = $deal_user_id;
            if ($field == 'use') {
                $conditions['DealUserCoupon.is_used'] = 0;
            } else {
                $conditions['DealUserCoupon.is_used'] = 1;
            }
            $code_type = (Configure::read('deal.deal_coupon_code_show_type') == 'top') ? 'unique_coupon_code' : 'coupon_code';
            $conditions['DealUserCoupon.' . $code_type] = $this->request->params['named']['code'];
            $DealUserCoupon = $this->DealUserCoupon->find('first', array(
                'conditions' => $conditions,
                'contain' => array(
                    'DealUser' => array(
                        'Deal'
                    ) ,
                ) ,
                'recursive' => 2
            ));
        }
        $status = ($field == 'use') ? 1 : 0;
        if (!empty($DealUserCoupon)) {
            $DealUserCoupon = array();
            $DealUserCoupon['id'] = $this->request->params['named']['coupon_id'];
            $DealUserCoupon['is_used'] = $status;
            $this->DealUserCoupon->save($DealUserCoupon);
            $this->autoRender = false;
            echo 'suceess';
        } else {
            $this->autoRender = false;
            echo 'fail';
        }
    }
    public function check_qr()
    {
        if (!empty($this->request->data)) {
            $coupon_code = $this->request->data['DealUserCoupon']['coupon_code'];
            $unique_coupon_code = $this->request->data['DealUserCoupon']['unique_coupon_code'];
        } else {
            $coupon_code = $this->request->params['pass'][0];
            $unique_coupon_code = $this->request->params['pass'][1];
        }
        if (is_null($coupon_code) || is_null($unique_coupon_code)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle = __l('Deal user coupon');
        $conditions['DealUserCoupon.coupon_code'] = $coupon_code;
        $conditions['DealUserCoupon.unique_coupon_code'] = $unique_coupon_code;
        $dealUserCoupon = $this->DealUserCoupon->find('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'DealUser' => array(
                    'User' => array(
                        'fields' => array(
                            'User.username'
                        ) ,
                    ) ,
                    'Deal' => array(
                        'Company' => array(
                            'fields' => array(
                                'Company.user_id'
                            ) ,
                        ) ,
                        'fields' => array(
                            'Deal.id',
                            'Deal.name',
                            'Deal.coupon_expiry_date',
                            'Deal.coupon_start_date'
                        ) ,
                    ) ,
                )
            ) ,
            'recursive' => 3
        ));
        if (empty($dealUserCoupon)) {
            $this->Session->setFlash(__l('Invalid coupon code') , 'default', null, 'error');
            $this->redirect(Router::url('/', true));
        }
        if (($this->Auth->user('user_type_id') == ConstUserTypes::User) || ($this->Auth->user('user_type_id') != ConstUserTypes::Admin) && ($this->Auth->user('id') != $dealUserCoupon['DealUser']['Deal']['Company']['user_id'])) {
            $this->Session->setFlash(__l('You have no authorized to view this page') , 'default', null, 'error');
            $this->redirect(Router::url('/', true));
        }
        if ($dealUserCoupon['DealUserCoupon']['is_used'] == 1) {
            $this->Session->setFlash(__l('This coupon used already') , 'default', null, 'error');
        }
        if (!empty($this->request->data)) {
            $deal_user_coupon_id = $dealUserCoupon['DealUserCoupon']['id'];
            $deal_update_data['DealUserCoupon']['is_used'] = $this->Auth->user('id');
            $deal_update_data['DealUserCoupon']['user_id'] = 1;
            $deal_update_data['DealUserCoupon']['id'] = $deal_user_coupon_id;
            if ($this->DealUserCoupon->save($deal_update_data)) {
                $dealUserCoupon['DealUserCoupon']['is_used'] = 1;
                $this->Session->setFlash(__l('Coupon used successfully') , 'default', null, 'success');
            }
        }
        $this->request->data['DealUserCoupon']['id'] = $coupon_code;
        $this->request->data['DealUserCoupon']['coupon_code'] = $coupon_code;
        $this->request->data['DealUserCoupon']['unique_coupon_code'] = $unique_coupon_code;
        $this->set('dealUserCoupon', $dealUserCoupon);
    }
}
?>