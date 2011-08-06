<?php
class UserCashWithdrawalsController extends AppController
{
    public $name = 'UserCashWithdrawals';
    public $components = array(
        'Paypal'
    );
    public $permanentCacheAction = array(
        'index' => array(
            'is_user_specific_url' => true
        ) ,
        'add' => array(
            'is_user_specific_url' => true
        )
    );
    public function index()
    {
        $payment_options = $this->UserCashWithdrawal->getGatewayTypes('is_enable_for_add_to_wallet');
        if (empty($payment_options[ConstPaymentGateways::Wallet])) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!Configure::read('company.is_user_can_withdraw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::Company) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!Configure::read('user.is_user_can_with_draw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::User) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle = __l('Withdraw Fund Request');
        $this->paginate = array(
            'conditions' => array(
                'UserCashWithdrawal.user_id' => $this->Auth->user('id') ,
            ) ,
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                    )
                ) ,
                'WithdrawalStatus' => array(
                    'fields' => array(
                        'WithdrawalStatus.name',
                        'WithdrawalStatus.id'
                    )
                )
            ) ,
            'order' => array(
                'UserCashWithdrawal.id' => 'desc'
            ) ,
            'recursive' => 0
        );
        $userProfile = $this->UserCashWithdrawal->User->UserProfile->find('first', array(
            'conditions' => array(
                'UserProfile.user_id' => $this->Auth->User('id')
            ) ,
            'fields' => array(
                'UserProfile.paypal_account'
            ) ,
            'recursive' => - 1
        ));
        $this->set('userProfile', $userProfile);
        $this->request->data['UserCashWithdrawal']['user_id'] = $this->Auth->user('id');
        $this->set('userCashWithdrawals', $this->paginate());
    }
    public function add()
    {
        $payment_options = $this->UserCashWithdrawal->getGatewayTypes('is_enable_for_add_to_wallet');
        if (empty($payment_options[ConstPaymentGateways::Wallet])) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!Configure::read('company.is_user_can_withdraw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::Company) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!Configure::read('user.is_user_can_with_draw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::User) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle = __l('Add Fund Withdraw');
        if (!empty($this->request->data)) {
            $this->UserCashWithdrawal->set($this->request->data);
            $this->UserCashWithdrawal->_checkAmount($this->request->data['UserCashWithdrawal']['amount']);
            if ($this->UserCashWithdrawal->validates()) {
                $this->request->data['UserCashWithdrawal']['withdrawal_status_id'] = ConstWithdrawalStatus::Pending;
                $this->UserCashWithdrawal->create();
                if ($this->UserCashWithdrawal->save($this->request->data)) {
                    // Updating transaction during intital withdraw request by user.
                    $data['Transaction']['user_id'] = $this->request->data['UserCashWithdrawal']['user_id'];
                    $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                    $data['Transaction']['class'] = 'SecondUser';
                    $data['Transaction']['amount'] = $this->request->data['UserCashWithdrawal']['amount'];
                    $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::UserWithdrawalRequest;
                    $this->UserCashWithdrawal->User->Transaction->log($data);
                    $this->UserCashWithdrawal->User->updateAll(array(
                        'User.available_balance_amount' => 'User.available_balance_amount -' . $this->request->data['UserCashWithdrawal']['amount']
                    ) , array(
                        'User.id' => $this->request->data['UserCashWithdrawal']['user_id']
                    ));
                    $this->UserCashWithdrawal->User->updateAll(array(
                        'User.blocked_amount' => 'User.blocked_amount +' . $this->request->data['UserCashWithdrawal']['amount']
                    ) , array(
                        'User.id' => $this->request->data['UserCashWithdrawal']['user_id']
                    ));
                    $this->Session->setFlash('Withdraw fund request has been added', 'default', null, 'success');
                    if ($this->RequestHandler->isAjax()) {
                        $this->autoRender = false;
                    } else {
                        $this->redirect(array(
                            'action' => 'index'
                        ));
                    }
                } else {
                    $this->Session->setFlash('Withdraw fund request could not be added. Please, try again.', 'default', null, 'error');
                }
            } else {
                $this->Session->setFlash('Withdraw fund request could not be added. Please, try again.', 'default', null, 'error');
            }
        }
        $userProfile = $this->UserCashWithdrawal->User->UserProfile->find('first', array(
            'conditions' => array(
                'UserProfile.user_id' => $this->Auth->User('id')
            ) ,
            'fields' => array(
                'UserProfile.paypal_account'
            ) ,
            'recursive' => - 1
        ));
        if (empty($userProfile['UserProfile']['paypal_account'])) {
            if ($this->Auth->User('user_type_id') == ConstUserTypes::Company) {
                $company = $this->UserCashWithdrawal->User->Company->find('first', array(
                    'conditions' => array(
                        'Company.user_id' => $this->Auth->User('id')
                    ) ,
                    'fields' => array(
                        'Company.id'
                    ) ,
                    'recursive' => - 1
                ));
                $this->redirect(array(
                    'controller' => 'companies',
                    'action' => 'edit',
                    $company['Company']['id']
                ));
            } else {
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'my_stuff'
                ));
            }
        }
        $this->request->data['UserCashWithdrawal']['user_id'] = $this->Auth->user('id');
    }
    public function admin_index()
    {
        $title = '';
        $conditions = array();
        $this->_redirectGET2Named(array(
            'filter_id',
            'q'
        ));
        $this->pageTitle = __l('Withdraw Fund Requests');
        if (isset($this->request->params['named']['filter_id'])) {
            $this->request->data['UserCashWithdrawal']['filter_id'] = $this->request->params['named']['filter_id'];
        }
        if (!empty($this->request->data['UserCashWithdrawal']['filter_id']) && $this->request->data['UserCashWithdrawal']['filter_id'] != 'all') {
            $conditions['UserCashWithdrawal.withdrawal_status_id'] = $this->request->data['UserCashWithdrawal']['filter_id'];
            $status = $this->UserCashWithdrawal->WithdrawalStatus->find('first', array(
                'conditions' => array(
                    'WithdrawalStatus.id' => $this->request->data['UserCashWithdrawal']['filter_id'],
                ) ,
                'fields' => array(
                    'WithdrawalStatus.name'
                ) ,
                'recursive' => - 1
            ));
            $title = $status['WithdrawalStatus']['name'];
        }
        if (!empty($title)) {
            $this->pageTitle = sprintf(__l(' %s Withdrawal Requests') , $title);
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(UserCashWithdrawal.created) <= '] = 0;
            $this->pageTitle.= __l(' - Requested today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(UserCashWithdrawal.created) <= '] = 7;
            $this->pageTitle.= __l(' - Requested in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(UserCashWithdrawal.created) <= '] = 30;
            $this->pageTitle.= __l(' - Requested in this month');
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
                        'User.fb_user_id',
                    )
                ) ,
                'WithdrawalStatus' => array(
                    'fields' => array(
                        'WithdrawalStatus.name',
                        'WithdrawalStatus.id',
                    )
                )
            ) ,
            'order' => array(
                'UserCashWithdrawal.id' => 'desc'
            ) ,
            'recursive' => 1,
        );
        $withdrawalStatuses = $this->UserCashWithdrawal->WithdrawalStatus->find('all', array(
            'recursive' => - 1
        ));
        $this->set('withdrawalStatuses', $withdrawalStatuses);
        $moreActions = $this->UserCashWithdrawal->moreActions;
        if (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == ConstWithdrawalStatus::Pending)) {
            unset($moreActions[ConstWithdrawalStatus::Pending]);
        }
        $this->set(compact('moreActions'));
        $this->set('userCashWithdrawals', $this->paginate());
        $this->set('approved', $this->UserCashWithdrawal->find('count', array(
            'conditions' => array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Approved,
            ) ,
            'recursive' => - 1
        )));
        $this->set('success', $this->UserCashWithdrawal->find('count', array(
            'conditions' => array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Success,
            ) ,
            'recursive' => - 1
        )));
        $this->set('failed', $this->UserCashWithdrawal->find('count', array(
            'conditions' => array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Failed,
            ) ,
            'recursive' => - 1
        )));
        $this->set('pending', $this->UserCashWithdrawal->find('count', array(
            'conditions' => array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Pending,
            ) ,
            'recursive' => - 1
        )));
        $this->set('rejected', $this->UserCashWithdrawal->find('count', array(
            'conditions' => array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Rejected,
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
        if ($this->UserCashWithdrawal->delete($id)) {
            $this->Session->setFlash(__l('Withdraw fund request deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_update()
    {
        $this->loadModel('PaypalTransactionLog');
        if (!empty($this->request->data['UserCashWithdrawal'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $userCashWithdrawalIds = array();
            foreach($this->request->data['UserCashWithdrawal'] as $userCashWithdrawal_id => $is_checked) {
                if ($is_checked['id']) {
                    $userCashWithdrawalIds[] = $userCashWithdrawal_id;
                }
            }
            if ($actionid && !empty($userCashWithdrawalIds)) {
                if ($actionid == ConstWithdrawalStatus::Approved) {
                    $status = $this->UserCashWithdrawal->_transferAmount('admin_update', $userCashWithdrawalIds);
                    if (strtoupper($status['paypal_response']['ACK']) == 'SUCCESS') {
                        foreach($userCashWithdrawalIds as $userCashWithdrawalId) {
                            $cash_withdraw = $this->UserCashWithdrawal->find('first', array(
                                'conditions' => array(
                                    'UserCashWithdrawal.id' => $userCashWithdrawalId
                                ) ,
                                'recursive' => - 1
                            ));
                            if (!empty($userCashWithdrawalId) && !empty($cash_withdraw)) {
                                $data['Transaction']['user_id'] = ConstUserIds::Admin;
                                $data['Transaction']['foreign_id'] = $cash_withdraw['UserCashWithdrawal']['user_id'];
                                $data['Transaction']['class'] = 'SecondUser';
                                $data['Transaction']['amount'] = $cash_withdraw['UserCashWithdrawal']['amount'];
                                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AdminApprovedWithdrawalRequest;
                                $this->UserCashWithdrawal->User->Transaction->log($data);
                                $transaction_id = $this->UserCashWithdrawal->User->Transaction->getLastInsertId();
                                $data = array();
                                $data['Transaction']['user_id'] = $cash_withdraw['UserCashWithdrawal']['user_id'];
                                $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                                $data['Transaction']['class'] = 'SecondUser';
                                $data['Transaction']['amount'] = $cash_withdraw['UserCashWithdrawal']['amount'];
                                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AmountApprovedForUserCashWithdrawalRequest;
                                $this->UserCashWithdrawal->User->Transaction->log($data);
                                // update log transaction id
                                $paypal_log_array = array();
                                $paypal_log_array['PaypalTransactionLog']['id'] = $status['paypal_log_list'][$userCashWithdrawalId];
                                $paypal_log_array['PaypalTransactionLog']['transaction_id'] = $transaction_id;
                                $this->PaypalTransactionLog->save($paypal_log_array);
                                // update status
                                $user_cash_data = array();
                                $user_cash_data['UserCashWithdrawal']['id'] = $userCashWithdrawalId;
                                $user_cash_data['UserCashWithdrawal']['withdrawal_status_id'] = ConstWithdrawalStatus::Approved;
                                $this->UserCashWithdrawal->save($user_cash_data);
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
                        'controller' => 'user_cash_withdrawals',
                        'action' => 'index'
                    ));
                } else if ($actionid == ConstWithdrawalStatus::Pending) {
                    $this->UserCashWithdrawal->updateAll(array(
                        'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Pending
                    ) , array(
                        'UserCashWithdrawal.id' => $userCashWithdrawalIds
                    ));
                    $this->Session->setFlash(__l('Checked requests have been moved to pending status') , 'default', null, 'success');
                } else if ($actionid == ConstWithdrawalStatus::Rejected) {
                    // Need to Refund the Money to User
                    $canceled_withdraw_requests = $this->UserCashWithdrawal->find('all', array(
                        'conditions' => array(
                            'UserCashWithdrawal.id' => $userCashWithdrawalIds
                        ) ,
                        'fields' => array(
                            'UserCashWithdrawal.id',
                            'UserCashWithdrawal.user_id',
                            'UserCashWithdrawal.amount',
                        ) ,
                        'recursive' => 1
                    ));
                    // Updating user balance
                    foreach($canceled_withdraw_requests as $canceled_withdraw_request) {
                        // Updating transactions
                        if (!empty($canceled_withdraw_request)) {
                            $data['Transaction']['user_id'] = ConstUserIds::Admin;
                            $data['Transaction']['foreign_id'] = $canceled_withdraw_request['UserCashWithdrawal']['user_id'];
                            $data['Transaction']['class'] = 'SecondUser';
                            $data['Transaction']['amount'] = $canceled_withdraw_request['UserCashWithdrawal']['amount'];
                            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AdminRejecetedWithdrawalRequest;
                            $this->UserCashWithdrawal->User->Transaction->log($data);
                            $data = array();
                            $data['Transaction']['user_id'] = $canceled_withdraw_request['UserCashWithdrawal']['user_id'];
                            $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                            $data['Transaction']['class'] = 'SecondUser';
                            $data['Transaction']['amount'] = $canceled_withdraw_request['UserCashWithdrawal']['amount'];
                            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AmountRefundedForRejectedWithdrawalRequest;
                            $this->UserCashWithdrawal->User->Transaction->log($data);
                        }
                        // Addding to user's Available Balance
                        $this->UserCashWithdrawal->User->updateAll(array(
                            'User.available_balance_amount' => 'User.available_balance_amount +' . $canceled_withdraw_request['UserCashWithdrawal']['amount']
                        ) , array(
                            'User.id' => $canceled_withdraw_request['UserCashWithdrawal']['user_id']
                        ));
                        // Deducting user's Available Balance
                        $this->UserCashWithdrawal->User->updateAll(array(
                            'User.blocked_amount' => 'User.blocked_amount -' . $canceled_withdraw_request['UserCashWithdrawal']['amount']
                        ) , array(
                            'User.id' => $canceled_withdraw_request['UserCashWithdrawal']['user_id']
                        ));
                        $this->UserCashWithdrawal->updateAll(array(
                            'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Rejected
                        ) , array(
                            'UserCashWithdrawal.id' => $canceled_withdraw_request['UserCashWithdrawal']['id']
                        ));
                    }
                    //
                    $this->Session->setFlash(__l('Checked requests have been moved to rejected status, Refunded  Money to Wallet') , 'default', null, 'success');
                }
            }
        }
        $this->redirect(array(
            'controller' => 'user_cash_withdrawals',
            'action' => 'index'
        ));
    }
    function user_masspay_ipn_process($userCashWithdrawal_id, $userCashWithdrawal_response)
    {
        $userCashWithdrawal = $this->UserCashWithdrawal->find('first', array(
            'conditions' => array(
                'UserCashWithdrawal.id' => $userCashWithdrawal_id,
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Approved,
            ) ,
            'contain' => array(
                'User' => array(
                    'UserProfile' => array(
                        'fields' => array(
                            'UserProfile.paypal_account'
                        )
                    ) ,
                    'fields' => array(
                        'User.username',
                        'User.available_balance_amount'
                    )
                ) ,
                'PaypalTransactionLog' => array(
                    'fields' => array(
                        'PaypalTransactionLog.id',
                        'PaypalTransactionLog.user_id',
                        'PaypalTransactionLog.transaction_id',
                        'PaypalTransactionLog.user_cash_withdrawal_id',
                        'PaypalTransactionLog.currency_id',
                        'PaypalTransactionLog.converted_currency_id',
                        'PaypalTransactionLog.orginal_amount',
                        'PaypalTransactionLog.rate',
                        'PaypalTransactionLog.masspay_response',
                    )
                ) ,
            ) ,
            'recursive' => 1
        ));
        if ($userCashWithdrawal_response['status'] == 'Completed') {
            $data['Transaction']['user_id'] = ConstUserIds::Admin;
            $data['Transaction']['foreign_id'] = $userCashWithdrawal['UserCashWithdrawal']['user_id'];
            $data['Transaction']['class'] = 'SecondUser';
            $data['Transaction']['amount'] = $userCashWithdrawal['UserCashWithdrawal']['amount'];
            $data['Transaction']['payment_gateway_id'] = ConstPaymentGateways::PayPalAuth;
            $data['Transaction']['gateway_fees'] = $userCashWithdrawal_response['mc_fee'];
            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::UserCashWithdrawalAmount;
            // Currency Conversion Changes //
            $data['Transaction']['currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['currency_id'];
            $data['Transaction']['converted_currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['converted_currency_id'];
            $data['Transaction']['converted_amount'] = $userCashWithdrawal_response['mc_gross'];
            $data['Transaction']['rate'] = $userCashWithdrawal['PaypalTransactionLog']['rate'];
            $transaction_id = $this->UserCashWithdrawal->User->Transaction->log($data);
            $data = array();
            $data['Transaction']['user_id'] = $userCashWithdrawal['UserCashWithdrawal']['user_id'];
            $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
            $data['Transaction']['class'] = 'SecondUser';
            $data['Transaction']['amount'] = $userCashWithdrawal['UserCashWithdrawal']['amount'];
            $data['Transaction']['payment_gateway_id'] = ConstPaymentGateways::PayPalAuth;
            $data['Transaction']['gateway_fees'] = $userCashWithdrawal_response['mc_fee'];
            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AcceptCashWithdrawRequest;
            // Currency Conversion Changes //
            $data['Transaction']['currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['currency_id'];
            $data['Transaction']['converted_currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['converted_currency_id'];
            $data['Transaction']['converted_amount'] = $userCashWithdrawal_response['mc_gross'];
            $data['Transaction']['rate'] = $userCashWithdrawal['PaypalTransactionLog']['rate'];
            $transaction_to_user = $this->UserCashWithdrawal->User->Transaction->log($data);
            $this->UserCashWithdrawal->updateAll(array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Success
            ) , array(
                'UserCashWithdrawal.id' => $userCashWithdrawal_id
            ));
        } else {
            //Failed,Returned,Reversed,Unclaimed
            $data['Transaction']['user_id'] = ConstUserIds::Admin;
            $data['Transaction']['foreign_id'] = $userCashWithdrawal['UserCashWithdrawal']['user_id'];
            $data['Transaction']['class'] = 'SecondUser';
            $data['Transaction']['amount'] = $userCashWithdrawal['UserCashWithdrawal']['amount'];
            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::FailedWithdrawalRequest;
            $this->UserCashWithdrawal->User->Transaction->log($data);
            $data = array();
            $data['Transaction']['user_id'] = $userCashWithdrawal['UserCashWithdrawal']['user_id'];
            $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
            $data['Transaction']['class'] = 'SecondUser';
            $data['Transaction']['amount'] = $userCashWithdrawal['UserCashWithdrawal']['amount'];
            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::FailedWithdrawalRequestRefundToUser;
            $this->UserCashWithdrawal->User->Transaction->log($data);
            $this->UserCashWithdrawal->User->updateAll(array(
                'User.available_balance_amount' => 'User.available_balance_amount +' . $userCashWithdrawal['UserCashWithdrawal']['amount']
            ) , array(
                'User.id' => $userCashWithdrawal['UserCashWithdrawal']['user_id']
            ));
            $this->UserCashWithdrawal->updateAll(array(
                'UserCashWithdrawal.withdrawal_status_id' => ConstWithdrawalStatus::Failed
            ) , array(
                'UserCashWithdrawal.id' => $userCashWithdrawal_id
            ));
        }
        $this->UserCashWithdrawal->User->updateAll(array(
            'User.blocked_amount' => 'User.blocked_amount -' . $userCashWithdrawal['UserCashWithdrawal']['amount']
        ) , array(
            'User.id' => $userCashWithdrawal['UserCashWithdrawal']['user_id']
        ));
        $this->UserCashWithdrawal->PaypalTransactionLog->updateAll(array(
            'PaypalTransactionLog.transaction_id' => $transaction_id,
            'PaypalTransactionLog.receiver_email' => '\'' . $userCashWithdrawal_response['receiver_email'] . '\'',
            'PaypalTransactionLog.txn_id' => '\'' . $userCashWithdrawal_response['masspay_txn_id'] . '\'',
            'PaypalTransactionLog.paypal_response' => '\'' . strtoupper($userCashWithdrawal_response['status']) . '\'',
            'PaypalTransactionLog.mass_pay_status' => '\'' . strtoupper($userCashWithdrawal_response['status']) . '\'',
            'PaypalTransactionLog.mc_currency' => '\'' . $userCashWithdrawal_response['mc_currency'] . '\'',
            'PaypalTransactionLog.mc_gross' => $userCashWithdrawal_response['mc_gross'],
            'PaypalTransactionLog.ip' => '\'' . $this->RequestHandler->getClientIP() . '\'',
            'PaypalTransactionLog.mc_fee' => $userCashWithdrawal_response['mc_fee'],
        ) , array(
            'PaypalTransactionLog.id' => $userCashWithdrawal['PaypalTransactionLog']['id']
        ));
    }
    public function process_masspay_ipn()
    {
        $ipn_data = $_POST;
        if (!empty($ipn_data)) {
            $processed_data['payer_id'] = $ipn_data['payer_id'];
            $processed_data['payment_date'] = $ipn_data['payment_date'];
            $processed_data['charset'] = $ipn_data['charset'];
            $processed_data['notify_version'] = $ipn_data['notify_version'];
            $processed_data['payer_status'] = $ipn_data['payer_status'];
            $processed_data['verify_sign'] = $ipn_data['verify_sign'];
            $processed_data['last_name'] = $ipn_data['last_name'];
            $processed_data['first_name'] = $ipn_data['first_name'];
            $processed_data['payer_email'] = $ipn_data['payer_email'];
            $processed_data['payer_business_name'] = $ipn_data['payer_business_name'];
            $payment_count = 0;
            for ($i = 1; !empty($ipn_data["receiver_email_$i"]); $i++) {
                $payment_count++;
            }
            for ($i = 1; $i <= $payment_count; $i++) {
                $user_defined = explode('-', $ipn_data["unique_id_$i"]);
                $unique_id = $user_defined[0];
                $withdrawal_type = 'user';
                if (count($user_defined) == 2) {
                    $unique_id = $user_defined[1];
                    $withdrawal_type = $user_defined[0];
                }
                $processed_data['UserCashWithdrawal'][$unique_id] = array(
                    'receiver_email' => $ipn_data["receiver_email_$i"],
                    'masspay_txn_id' => $ipn_data["masspay_txn_id_$i"],
                    'status' => $ipn_data["status_$i"],
                    'mc_currency' => $ipn_data["mc_currency_$i"],
                    'payment_gross' => $ipn_data["payment_gross_$i"],
                    'mc_gross' => $ipn_data["mc_gross_$i"],
                    'mc_fee' => $ipn_data["mc_fee_$i"],
                    'withdrawal_type' => $withdrawal_type
                );
            }
            if ($processed_data); {
                foreach($processed_data['UserCashWithdrawal'] as $userCashWithdrawal_id => $userCashWithdrawal_response) {
                    switch ($userCashWithdrawal_response['withdrawal_type']) {
                    case 'user':
                        $this->user_masspay_ipn_process($userCashWithdrawal_id, $userCashWithdrawal_response);
                        break;

                    case 'affiliate':
                        $this->loadModel('AffiliateCashWithdrawal');
                        $this->AffiliateCashWithdrawal->affiliate_masspay_ipn_process($userCashWithdrawal_id, $userCashWithdrawal_response);
                        break;

                    case 'charity':
                        $this->loadModel('CharityCashWithdrawal');
                        $this->CharityCashWithdrawal->charity_masspay_ipn_process($userCashWithdrawal_id, $userCashWithdrawal_response);
                        break;
                    }
                }
            }
        }
        exit;
    }
}
?>