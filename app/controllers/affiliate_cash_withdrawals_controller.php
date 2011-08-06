<?php
class AffiliateCashWithdrawalsController extends AppController
{
    public $name = 'AffiliateCashWithdrawals';
    public $components = array(
        'Paypal'
    );
    public $uses = array(
        'AffiliateCashWithdrawal',
        'PaypalTransactionLog'
    );
    public $permanentCacheAction = array(
        'index' => array(
            'is_user_specific_url' => true
        ) ,
        'add' => array(
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        if (!Configure::read('affiliate.is_enabled') && $this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
            throw new NotFoundException(__l('Invalid request'));
        }
        parent::beforeFilter();
    }
    public function index()
    {
        $this->pageTitle = __l('Affiliate Cash Withdrawal Request');
        $conditions = array();
        $conditions['AffiliateCashWithdrawal.user_id'] = $this->Auth->user('id');
        if (isset($this->request->params['named']['filter_id'])) {
            $this->request->data['AffiliateCashWithdrawal']['affiliate_cash_withdrawal_status_id'] = $this->request->params['named']['filter_id'];
        }
        if (!empty($this->request->data['AffiliateCashWithdrawal']['affiliate_cash_withdrawal_status_id'])) {
            switch ($this->request->data['AffiliateCashWithdrawal']['affiliate_cash_withdrawal_status_id']) {
            case ConstAffiliateCashWithdrawalStatus::Pending:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Pending;
                $this->pageTitle.= __l(' - Pending');
                break;

            case ConstAffiliateCashWithdrawalStatus::Approved:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Approved;
                $this->pageTitle.= __l(' - Accepted');
                break;

            case ConstAffiliateCashWithdrawalStatus::Rejected:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Rejected;
                $this->pageTitle.= __l(' - Rejected');
                break;

            case ConstAffiliateCashWithdrawalStatus::Failed:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Failed;
                $this->pageTitle.= __l(' - Payment Failure');
                break;

            case ConstAffiliateCashWithdrawalStatus::Success:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Success;
                $this->pageTitle.= __l(' - Paid');
                break;
            }
            $this->request->params['named']['filter_id'] = $this->request->data['Affiliate']['filter_id'];
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(AffiliateCashWithdrawal.created) <= '] = 0;
            $this->pageTitle.= __l(' -  today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(AffiliateCashWithdrawal.created) <= '] = 7;
            $this->pageTitle.= __l(' -  in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(AffiliateCashWithdrawal.created) <= '] = 30;
            $this->pageTitle.= __l(' -  in this month');
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.username'
                    )
                ) ,
                'AffiliateCashWithdrawalStatus' => array(
                    'fields' => array(
                        'AffiliateCashWithdrawalStatus.name',
                        'AffiliateCashWithdrawalStatus.id'
                    )
                )
            ) ,
            'order' => array(
                'AffiliateCashWithdrawal.id' => 'desc'
            ) ,
            'recursive' => 0
        );
        $user = $this->AffiliateCashWithdrawal->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->User('id') ,
            ) ,
            'contain' => array(
                'UserProfile' => array(
                    'fields' => array(
                        'UserProfile.paypal_account',
                    ) ,
                ) ,
            ) ,
            'recursive' => 0
        ));
        $this->set('user', $user);
        $this->request->data['AffiliateCashWithdrawal']['user_id'] = $this->Auth->user('id');
        $this->set('userCashWithdrawals', $this->paginate());
    }
    public function add()
    {
        $this->pageTitle = __l('Add Affiliate Cash Withdrawal');
        if (!empty($this->request->data)) {
            $affilate_transaction_fee_enabled = Configure::read('affiliate.site_commission_amount');
            if (!empty($affilate_transaction_fee_enabled)) {
                if (Configure::read('affiliate.site_commission_type') == 'percentage') {
                    $this->request->data['AffiliateCashWithdrawal']['commission_amount'] = ($this->request->data['AffiliateCashWithdrawal']['amount'] * Configure::read('affiliate.site_commission_amount') / 100);
                } else {
                    $this->request->data['AffiliateCashWithdrawal']['commission_amount'] = Configure::read('affiliate.site_commission_amount');
                }
            }
            $this->AffiliateCashWithdrawal->set($this->request->data);
            $this->AffiliateCashWithdrawal->_checkAmount($this->request->data['AffiliateCashWithdrawal']['amount']);
            if ($this->AffiliateCashWithdrawal->validates()) {
                $this->request->data['AffiliateCashWithdrawal']['affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Pending;
                $this->AffiliateCashWithdrawal->create();
                if ($this->AffiliateCashWithdrawal->save($this->request->data)) {
                    // Updating transaction during intital withdraw request by user.
                    $data['Transaction']['user_id'] = $this->request->data['AffiliateCashWithdrawal']['user_id'];
                    $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                    $data['Transaction']['class'] = 'SecondUser';
                    $data['Transaction']['amount'] = $this->request->data['AffiliateCashWithdrawal']['amount'];
                    $data['Transaction']['description'] = 'user cash withdrawal request from affliate commission';
                    $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateUserWithdrawalRequest;
                    $this->AffiliateCashWithdrawal->User->Transaction->log($data);
                    $this->AffiliateCashWithdrawal->User->updateAll(array(
                        'User.commission_line_amount' => 'User.commission_line_amount -' . $this->request->data['AffiliateCashWithdrawal']['amount']
                    ) , array(
                        'User.id' => $this->request->data['AffiliateCashWithdrawal']['user_id']
                    )); //
                    $this->AffiliateCashWithdrawal->User->updateAll(array(
                        'User.commission_withdraw_request_amount' => 'User.commission_withdraw_request_amount + ' . $this->request->data['AffiliateCashWithdrawal']['amount']
                    ) , array(
                        'User.id' => $this->request->data['AffiliateCashWithdrawal']['user_id']
                    ));
                    $this->Session->setFlash('Affiliate cash withdrawal request has been added', 'default', null, 'success');
                    if ($this->RequestHandler->isAjax()) {
                        $this->autoRender = false;
                    } else {
                        $this->redirect(array(
                            'action' => 'index',
                        ));
                    }
                } else {
                    $this->Session->setFlash('Affiliate cash withdrawal request could not be added. Please, try again.', 'default', null, 'error');
                }
            } else {
                $this->Session->setFlash('Affiliate cash withdrawal request could not be added. Please, try again.', 'default', null, 'error');
            }
        } else {
            $this->request->data['AffiliateCashWithdrawal']['user_id'] = $this->Auth->user('id');
        }
        $user = $this->AffiliateCashWithdrawal->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id')
            ) ,
            'recursive' => - 1
        ));
        $this->set('user', $user);
    }
    public function admin_index()
    {
        $title = '';
        $conditions = array();
        $this->_redirectGET2Named(array(
            'filter_id',
            'q'
        ));
        $this->pageTitle = __l('Withdraw Requests');
        if (isset($this->request->params['named']['filter_id'])) {
            $this->request->data['AffiliateCashWithdrawal']['filter_id'] = $this->request->params['named']['filter_id'];
        }
        if (!empty($this->request->data['AffiliateCashWithdrawal']['filter_id']) && $this->request->data['AffiliateCashWithdrawal']['filter_id'] != 'all') {
            $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = $this->request->data['AffiliateCashWithdrawal']['filter_id'];
            $status = $this->AffiliateCashWithdrawal->AffiliateCashWithdrawalStatus->find('first', array(
                'conditions' => array(
                    'AffiliateCashWithdrawalStatus.id' => $this->request->data['AffiliateCashWithdrawal']['filter_id'],
                ) ,
                'fields' => array(
                    'AffiliateCashWithdrawalStatus.name'
                ) ,
                'recursive' => - 1
            ));
            $title = $status['AffiliateCashWithdrawalStatus']['name'];
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(AffiliateCashWithdrawal.created) <= '] = 0;
            $this->pageTitle.= __l(' - Requested today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(AffiliateCashWithdrawal.created) <= '] = 7;
            $this->pageTitle.= __l(' - Requested in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(AffiliateCashWithdrawal.created) <= '] = 30;
            $this->pageTitle.= __l(' - Requested in this month');
        }
        if (!empty($this->request->data['AffiliateCashWithdrawal']['filter_id'])) {
            switch ($this->request->data['AffiliateCashWithdrawal']['filter_id']) {
            case ConstAffiliateCashWithdrawalStatus::Pending:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Pending;
                $this->pageTitle.= __l(' - Pending');
                break;

            case ConstAffiliateCashWithdrawalStatus::Approved:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Approved;
                $this->pageTitle.= __l(' - Accepted');
                break;

            case ConstAffiliateCashWithdrawalStatus::Rejected:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Rejected;
                $this->pageTitle.= __l(' - Rejected');
                break;

            case ConstAffiliateCashWithdrawalStatus::Failed:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Failed;
                $this->pageTitle.= __l(' - Payment Failure');
                break;

            case ConstAffiliateCashWithdrawalStatus::Success:
                $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Success;
                $this->pageTitle.= __l(' - Paid');
                break;
            }
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'UserAvatar',
                    'fields' => array(
                        'User.username',
                    )
                ) ,
                'AffiliateCashWithdrawalStatus' => array(
                    'fields' => array(
                        'AffiliateCashWithdrawalStatus.name',
                        'AffiliateCashWithdrawalStatus.id',
                    )
                )
            ) ,
            'order' => array(
                'AffiliateCashWithdrawal.id' => 'desc'
            ) ,
            'recursive' => 1,
        );
        $AffiliateCashWithdrawalStatuses = $this->AffiliateCashWithdrawal->AffiliateCashWithdrawalStatus->find('all', array(
            'recursive' => - 1
        ));
        $this->set('AffiliateCashWithdrawalStatuses', $AffiliateCashWithdrawalStatuses);
        $moreActions = $this->AffiliateCashWithdrawal->moreActions;
        if (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == ConstAffiliateCashWithdrawalStatus::Pending)) {
            unset($moreActions[ConstAffiliateCashWithdrawalStatus::Pending]);
        }
        $this->set(compact('moreActions'));
        $this->set('affiliateCashWithdrawals', $this->paginate());
        $this->set('approved', $this->AffiliateCashWithdrawal->find('count', array(
            'conditions' => array(
                'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Approved,
            ) ,
            'recursive' => - 1
        )));
        $this->set('success', $this->AffiliateCashWithdrawal->find('count', array(
            'conditions' => array(
                'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Success,
            ) ,
            'recursive' => - 1
        )));
        $this->set('failed', $this->AffiliateCashWithdrawal->find('count', array(
            'conditions' => array(
                'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Failed,
            ) ,
            'recursive' => - 1
        )));
        $this->set('pending', $this->AffiliateCashWithdrawal->find('count', array(
            'conditions' => array(
                'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Pending,
            ) ,
            'recursive' => - 1
        )));
        $this->set('rejected', $this->AffiliateCashWithdrawal->find('count', array(
            'conditions' => array(
                'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Rejected,
            ) ,
            'recursive' => - 1
        )));
        $this->set('pageTitle', $this->pageTitle);
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->AffiliateCashWithdrawal->delete($id)) {
            $this->Session->setFlash(__l('Affiliate Cash Withdrawal deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_update()
    {
        if (!empty($this->request->data['AffiliateCashWithdrawal'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $userCashWithdrawalIds = array();
            foreach($this->request->data['AffiliateCashWithdrawal'] as $userCashWithdrawal_id => $is_checked) {
                if ($is_checked['id']) {
                    $userCashWithdrawalIds[] = $userCashWithdrawal_id;
                }
            }
            if ($actionid && !empty($userCashWithdrawalIds)) {
                if ($actionid == ConstAffiliateCashWithdrawalStatus::Approved) {
                    $status = $this->AffiliateCashWithdrawal->_transferAmount('admin_update', $userCashWithdrawalIds);
                    if (strtoupper($status['paypal_response']['ACK']) == 'SUCCESS') {
                        foreach($userCashWithdrawalIds as $userCashWithdrawalId) {
                            $cash_withdraw = $this->AffiliateCashWithdrawal->find('first', array(
                                'conditions' => array(
                                    'AffiliateCashWithdrawal.id' => $userCashWithdrawalId
                                ) ,
                                'recursive' => - 1
                            ));
                            if (!empty($userCashWithdrawalId) && !empty($cash_withdraw)) {
                                $data['Transaction']['user_id'] = ConstUserIds::Admin;
                                $data['Transaction']['foreign_id'] = $cash_withdraw['AffiliateCashWithdrawal']['user_id'];
                                $data['Transaction']['class'] = 'SecondUser';
                                $data['Transaction']['amount'] = $cash_withdraw['AffiliateCashWithdrawal']['amount'];
                                $data['Transaction']['description'] = 'User request affiliate commission amount withdrawal approved by admin';
                                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateAdminApprovedWithdrawalRequest;
                                $this->AffiliateCashWithdrawal->User->Transaction->log($data);
                                $transaction_id = $this->AffiliateCashWithdrawal->User->Transaction->getLastInsertId();
                                $data = array();
                                $data['Transaction']['user_id'] = $cash_withdraw['AffiliateCashWithdrawal']['user_id'];
                                $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                                $data['Transaction']['class'] = 'SecondUser';
                                $data['Transaction']['amount'] = $cash_withdraw['AffiliateCashWithdrawal']['amount'];
                                $data['Transaction']['description'] = 'User request affiliate commission amount withdrawal approved by admin';
                                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateAmountApprovedForUserCashWithdrawalRequest;
                                $this->AffiliateCashWithdrawal->User->Transaction->log($data);
                                // update log transaction id
                                $paypal_log_array = array();
                                $paypal_log_array['PaypalTransactionLog']['id'] = $status['paypal_log_list'][$userCashWithdrawalId];
                                $paypal_log_array['PaypalTransactionLog']['transaction_id'] = $transaction_id;
                                $this->loadModel('PaypalTransactionLog');
                                $this->PaypalTransactionLog->save($paypal_log_array);
                                // update status
                                $user_cash_data = array();
                                $user_cash_data['AffiliateCashWithdrawal']['id'] = $userCashWithdrawalId;
                                $user_cash_data['AffiliateCashWithdrawal']['affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Approved;
                                $this->AffiliateCashWithdrawal->save($user_cash_data);
                            }
                        }
                        $messageType = 'success';
                        $flash_message = __l('Mass payment request is submitted in Paypal. User will be paid once process completed.');
                    } else {
                        $user_count = count($status['paypal_log_list']);
                        $flash_message = '';
                        for ($i = 0; $i < $user_count; $i++) {
                            if (!empty($status['paypal_response']['L_LONGMESSAGE' . $i])) {
                                $flash_message.= urldecode($status['paypal_response']['L_LONGMESSAGE' . $i]) . ' , ';
                            }
                        }
                        $messageType = 'error';
                        $flash_message.= __l(' Masspay not completed');
                    }
                    $this->Session->setFlash($flash_message, 'default', null, $messageType);
                    $this->redirect(array(
                        'controller' => 'affiliate_cash_withdrawals',
                        'action' => 'index'
                    ));
                } else if ($actionid == ConstAffiliateCashWithdrawalStatus::Pending) {
                    $this->AffiliateCashWithdrawal->updateAll(array(
                        'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Pending
                    ) , array(
                        'AffiliateCashWithdrawal.id' => $userCashWithdrawalIds
                    ));
                    $this->Session->setFlash(__l('Checked requests have been moved to pending status') , 'default', null, 'success');
                } else if ($actionid == ConstAffiliateCashWithdrawalStatus::Rejected) {
                    // Need to Refund the Money to User
                    $canceled_withdraw_requests = $this->AffiliateCashWithdrawal->find('all', array(
                        'conditions' => array(
                            'AffiliateCashWithdrawal.id' => $userCashWithdrawalIds
                        ) ,
                        'fields' => array(
                            'AffiliateCashWithdrawal.id',
                            'AffiliateCashWithdrawal.user_id',
                            'AffiliateCashWithdrawal.amount',
                        ) ,
                        'recursive' => 1
                    ));
                    // Updating user balance
                    foreach($canceled_withdraw_requests as $canceled_withdraw_request) {
                        // Updating transactions
                        if (!empty($canceled_withdraw_request)) {
                            $data['Transaction']['user_id'] = ConstUserIds::Admin;
                            $data['Transaction']['foreign_id'] = $canceled_withdraw_request['AffiliateCashWithdrawal']['user_id'];
                            $data['Transaction']['class'] = 'SecondUser';
                            $data['Transaction']['amount'] = $canceled_withdraw_request['AffiliateCashWithdrawal']['amount'];
                            $data['Transaction']['description'] = 'User request affiliate commission amount withdrawal rejected by admin';
                            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateAdminRejecetedWithdrawalRequest;
                            $this->AffiliateCashWithdrawal->User->Transaction->log($data);
                            $data = array();
                            $data['Transaction']['user_id'] = $canceled_withdraw_request['AffiliateCashWithdrawal']['user_id'];
                            $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                            $data['Transaction']['class'] = 'SecondUser';
                            $data['Transaction']['amount'] = $canceled_withdraw_request['AffiliateCashWithdrawal']['amount'];
                            $data['Transaction']['description'] = 'User request affiliate commission amount withdrawal rejected by admin';
                            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateAmountRefundedForRejectedWithdrawalRequest;
                            $this->AffiliateCashWithdrawal->User->Transaction->log($data);
                        }
                        // Addding to user's Available Balance
                        $this->AffiliateCashWithdrawal->User->updateAll(array(
                            'User.commission_line_amount' => 'User.commission_line_amount +' . $canceled_withdraw_request['AffiliateCashWithdrawal']['amount']
                        ) , array(
                            'User.id' => $canceled_withdraw_request['AffiliateCashWithdrawal']['user_id']
                        ));
                        // Deducting user's Available Balance
                        $this->AffiliateCashWithdrawal->User->updateAll(array(
                            'User.commission_withdraw_request_amount' => 'User.commission_withdraw_request_amount -' . $canceled_withdraw_request['AffiliateCashWithdrawal']['amount']
                        ) , array(
                            'User.id' => $canceled_withdraw_request['AffiliateCashWithdrawal']['user_id']
                        ));
                        $this->AffiliateCashWithdrawal->updateAll(array(
                            'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Rejected
                        ) , array(
                            'AffiliateCashWithdrawal.id' => $canceled_withdraw_request['AffiliateCashWithdrawal']['id']
                        ));
                    }
                    //
                    $this->Session->setFlash(__l('Checked requests have been moved to rejected status, Amount sent back tot the users.') , 'default', null, 'success');
                }
            }
        }
        $this->redirect(array(
            'controller' => 'affiliate_cash_withdrawals',
            'action' => 'index'
        ));
    }
}
?>