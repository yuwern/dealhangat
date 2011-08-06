<?php
class CharityCashWithdrawalsController extends AppController
{
    public $name = 'CharityCashWithdrawals';
    public $components = array(
        'Paypal'
    );
    public $uses = array(
        'CharityCashWithdrawal',
        'PaypalTransactionLog'
    );
    public function beforeFilter()
    {
        if (!Configure::read('charity.is_enabled') && $this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
            throw new NotFoundException(__l('Invalid request'));
        }
        parent::beforeFilter();
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Charity Cash Withdrawal');
        if (!empty($this->request->data)) {
            $charity_transaction_fee_enabled = Configure::read('charity.site_commission_amount');
            if (!empty($charity_transaction_fee_enabled)) {
                if (Configure::read('charity.site_commission_type') == 'percentage') {
                    $this->request->data['CharityCashWithdrawal']['commission_amount'] = ($this->request->data['CharityCashWithdrawal']['amount'] * Configure::read('charity.site_commission_amount') / 100);
                } else {
                    $this->request->data['CharityCashWithdrawal']['commission_amount'] = Configure::read('charity.site_commission_amount');
                }
            }
            $this->CharityCashWithdrawal->set($this->request->data);
            if ($this->CharityCashWithdrawal->validates()) {
                $this->request->data['CharityCashWithdrawal']['charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Pending;
                $this->request->data['CharityCashWithdrawal']['amount'] = $this->request->data['CharityCashWithdrawal']['amount'] + $this->request->data['CharityCashWithdrawal']['commission_amount'];
                if ($this->CharityCashWithdrawal->save($this->request->data, false)) {
                    $amount = $this->request->data['CharityCashWithdrawal']['amount'] + $this->request->data['CharityCashWithdrawal']['commission_amount'];
                    $this->CharityCashWithdrawal->Charity->updateAll(array(
                        'Charity.available_amount' => 'Charity.available_amount -' . $amount
                    ) , array(
                        'Charity.id' => $this->request->data['CharityCashWithdrawal']['charity_id']
                    )); //
                    $this->CharityCashWithdrawal->Charity->updateAll(array(
                        'Charity.withdraw_request_amount' => 'Charity.withdraw_request_amount +' . $amount
                    ) , array(
                        'Charity.id' => $this->request->data['CharityCashWithdrawal']['charity_id']
                    ));
                    $this->Session->setFlash('Charity cash withdrawal request has been added', 'default', null, 'success');
                    if ($this->RequestHandler->isAjax()) {
                        $this->autoRender = false;
                    } else {
                        $this->redirect(array(
                            'action' => 'index',
                        ));
                    }
                } else {
                    $this->Session->setFlash('Charity cash withdrawal request could not be added. Please, try again.', 'default', null, 'error');
                }
                $this->Session->setFlash('Charity cash withdrawal request has been added', 'default', null, 'success');
            } else {
                $this->Session->setFlash('Charity cash withdrawal request could not be added. Please, try again.', 'default', null, 'error');
            }
        }
        $charities = $this->CharityCashWithdrawal->Charity->find('all', array(
            'conditions' => array(
                'Charity.is_active' => 1
            ) ,
            'recursive' => - 1
        ));
        $charity = array();
        foreach($charities as & $eachcharity) {
            $charity[$eachcharity['Charity']['id']] = $eachcharity['Charity']['name'] . ' (' . $eachcharity['Charity']['available_amount'] . ') ';
        }
        $this->set('charities', $charity);
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
            $this->request->data['CharityCashWithdrawal']['filter_id'] = $this->request->params['named']['filter_id'];
        }
        if (!empty($this->request->data['CharityCashWithdrawal']['filter_id']) && $this->request->data['CharityCashWithdrawal']['filter_id'] != 'all') {
            $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = $this->request->data['CharityCashWithdrawal']['filter_id'];
            $status = $this->CharityCashWithdrawal->CharityCashWithdrawalStatus->find('first', array(
                'conditions' => array(
                    'CharityCashWithdrawalStatus.id' => $this->request->data['CharityCashWithdrawal']['filter_id'],
                ) ,
                'fields' => array(
                    'CharityCashWithdrawalStatus.name'
                ) ,
                'recursive' => - 1
            ));
            $title = $status['CharityCashWithdrawalStatus']['name'];
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'day') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(CharityCashWithdrawal.created) <= '] = 0;
            $this->pageTitle.= __l(' - Requested today');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'week') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(CharityCashWithdrawal.created) <= '] = 7;
            $this->pageTitle.= __l(' - Requested in this week');
        }
        if (isset($this->request->params['named']['stat']) && $this->request->params['named']['stat'] == 'month') {
            $conditions['TO_DAYS(NOW()) - TO_DAYS(CharityCashWithdrawal.created) <= '] = 30;
            $this->pageTitle.= __l(' - Requested in this month');
        }
        if (!empty($this->request->data['CharityCashWithdrawal']['filter_id'])) {
            switch ($this->request->data['CharityCashWithdrawal']['filter_id']) {
            case ConstCharityCashWithdrawalStatus::Pending:
                $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Pending;
                $this->pageTitle.= __l(' - Pending');
                break;

            case ConstCharityCashWithdrawalStatus::Approved:
                $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Approved;
                $this->pageTitle.= __l(' - Accepted');
                break;

            case ConstCharityCashWithdrawalStatus::Rejected:
                $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Rejected;
                $this->pageTitle.= __l(' - Rejected');
                break;

            case ConstCharityCashWithdrawalStatus::Failed:
                $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Failed;
                $this->pageTitle.= __l(' - Payment Failure');
                break;

            case ConstCharityCashWithdrawalStatus::Success:
                $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Success;
                $this->pageTitle.= __l(' - Paid');
                break;
            }
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'Charity',
                'CharityCashWithdrawalStatus' => array(
                    'fields' => array(
                        'CharityCashWithdrawalStatus.name',
                        'CharityCashWithdrawalStatus.id',
                    )
                )
            ) ,
            'order' => array(
                'CharityCashWithdrawal.id' => 'desc'
            ) ,
            'recursive' => 1,
        );
        $CharityCashWithdrawalStatuses = $this->CharityCashWithdrawal->CharityCashWithdrawalStatus->find('all', array(
            'recursive' => - 1
        ));
        $this->set('CharityCashWithdrawalStatuses', $CharityCashWithdrawalStatuses);
        $moreActions = $this->CharityCashWithdrawal->moreActions;
        if (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == ConstCharityCashWithdrawalStatus::Pending)) {
            unset($moreActions[ConstCharityCashWithdrawalStatus::Pending]);
        }
        $this->set(compact('moreActions'));
        $this->set('charityCashWithdrawals', $this->paginate());
        $this->set('approved', $this->CharityCashWithdrawal->find('count', array(
            'conditions' => array(
                'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Approved,
            ) ,
            'recursive' => - 1
        )));
        $this->set('success', $this->CharityCashWithdrawal->find('count', array(
            'conditions' => array(
                'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Success,
            ) ,
            'recursive' => - 1
        )));
        $this->set('failed', $this->CharityCashWithdrawal->find('count', array(
            'conditions' => array(
                'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Failed,
            ) ,
            'recursive' => - 1
        )));
        $this->set('pending', $this->CharityCashWithdrawal->find('count', array(
            'conditions' => array(
                'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Pending,
            ) ,
            'recursive' => - 1
        )));
        $this->set('rejected', $this->CharityCashWithdrawal->find('count', array(
            'conditions' => array(
                'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Rejected,
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
        $charities = $this->CharityCashWithdrawal->find('first', array(
            'conditions' => array(
                'CharityCashWithdrawal.id' => $id
            ) ,
            'recursive' => - 1
        ));
        if ($this->CharityCashWithdrawal->delete($id)) {
            $amount = $charities['CharityCashWithdrawal']['amount'] + $charities['CharityCashWithdrawal']['commission_amount'];
            $this->CharityCashWithdrawal->Charity->updateAll(array(
                'Charity.available_amount' => 'Charity.available_amount +' . $amount,
            ) , array(
                'Charity.id' => $charities['CharityCashWithdrawal']['charity_id']
            ));
            $this->CharityCashWithdrawal->Charity->updateAll(array(
                'Charity.withdraw_request_amount' => 'Charity.withdraw_request_amount -' . $amount
            ) , array(
                'Charity.id' => $charities['CharityCashWithdrawal']['charity_id']
            ));
            $this->Session->setFlash(__l('Charity Cash Withdrawal deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_update()
    {
        App::import('Model', 'Transaction');
        $this->Transaction = new Transaction();
        if (!empty($this->request->data['CharityCashWithdrawal'])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $userCashWithdrawalIds = array();
            foreach($this->request->data['CharityCashWithdrawal'] as $userCashWithdrawal_id => $is_checked) {
                if ($is_checked['id']) {
                    $userCashWithdrawalIds[] = $userCashWithdrawal_id;
                }
            }
            if ($actionid && !empty($userCashWithdrawalIds)) {
                if ($actionid == ConstCharityCashWithdrawalStatus::Approved) {
                    $status = $this->CharityCashWithdrawal->_transferAmount('admin_update', $userCashWithdrawalIds);
                    if (strtoupper($status['paypal_response']['ACK']) == 'SUCCESS') {
                        foreach($userCashWithdrawalIds as $userCashWithdrawalId) {
                            $cash_withdraw = $this->CharityCashWithdrawal->find('first', array(
                                'conditions' => array(
                                    'CharityCashWithdrawal.id' => $userCashWithdrawalId
                                ) ,
                                'recursive' => - 1
                            ));
                            if (!empty($userCashWithdrawalId) && !empty($cash_withdraw)) {
                                $data['Transaction']['user_id'] = ConstUserIds::Admin;
                                $data['Transaction']['foreign_id'] = $cash_withdraw['CharityCashWithdrawal']['charity_id'];
                                $data['Transaction']['class'] = 'Charity';
                                $data['Transaction']['amount'] = $cash_withdraw['CharityCashWithdrawal']['amount'];
                                $data['Transaction']['description'] = 'Charity amount withdrawal approved by admin';
                                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::CharityAdminApprovedWithdrawalRequest;
                                $this->Transaction->log($data);
                                $transaction_id = $this->Transaction->getLastInsertId();
                                // update log transaction id
                                $paypal_log_array = array();
                                $paypal_log_array['PaypalTransactionLog']['id'] = $status['paypal_log_list'][$userCashWithdrawalId];
                                $paypal_log_array['PaypalTransactionLog']['transaction_id'] = $transaction_id;
                                $this->loadModel('PaypalTransactionLog');
                                $this->PaypalTransactionLog->save($paypal_log_array);
                                // update status
                                $user_cash_data = array();
                                $user_cash_data['CharityCashWithdrawal']['id'] = $userCashWithdrawalId;
                                $user_cash_data['CharityCashWithdrawal']['charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Approved;
                                $this->CharityCashWithdrawal->save($user_cash_data, false);
                            }
                        }
                        $messageType = 'success';
                        $flash_message = __l('Mass payment request is submitted in Paypal. Charity will be paid once process completed.');
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
                        'controller' => 'charity_cash_withdrawals',
                        'action' => 'index'
                    ));
                } else if ($actionid == ConstCharityCashWithdrawalStatus::Pending) {
                    $this->CharityCashWithdrawal->updateAll(array(
                        'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Pending
                    ) , array(
                        'CharityCashWithdrawal.id' => $userCashWithdrawalIds
                    ));
                    $this->Session->setFlash(__l('Checked requests have been moved to pending status') , 'default', null, 'success');
                } else if ($actionid == ConstCharityCashWithdrawalStatus::Rejected) {
                    // Need to Refund the Money to Charity
                    $canceled_withdraw_requests = $this->CharityCashWithdrawal->find('all', array(
                        'conditions' => array(
                            'CharityCashWithdrawal.id' => $userCashWithdrawalIds
                        ) ,
                        'fields' => array(
                            'CharityCashWithdrawal.id',
                            'CharityCashWithdrawal.charity_id',
                            'CharityCashWithdrawal.amount',
                        ) ,
                        'recursive' => 1
                    ));
                    // Updating user balance
                    foreach($canceled_withdraw_requests as $canceled_withdraw_request) {
                        // Updating transactions
                        if (!empty($canceled_withdraw_request)) {
                            $data['Transaction']['user_id'] = ConstUserIds::Admin;
                            $data['Transaction']['foreign_id'] = $canceled_withdraw_request['CharityCashWithdrawal']['charity_id'];
                            $data['Transaction']['class'] = 'Charity';
                            $data['Transaction']['amount'] = $canceled_withdraw_request['CharityCashWithdrawal']['amount'];
                            $data['Transaction']['description'] = 'Charity request charity commission amount withdrawal rejected by admin';
                            $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::CharityAdminRejecetedWithdrawalRequest;
                            $this->Transaction->log($data);
                        }
                        // Addding to user's Available Balance
                        $this->CharityCashWithdrawal->Charity->updateAll(array(
                            'Charity.available_amount' => 'Charity.available_amount +' . $canceled_withdraw_request['CharityCashWithdrawal']['amount']
                        ) , array(
                            'Charity.id' => $canceled_withdraw_request['CharityCashWithdrawal']['charity_id']
                        ));
                        // Deducting user's Available Balance
                        $this->CharityCashWithdrawal->Charity->updateAll(array(
                            'Charity.withdraw_request_amount' => 'Charity.withdraw_request_amount -' . $canceled_withdraw_request['CharityCashWithdrawal']['amount']
                        ) , array(
                            'Charity.id' => $canceled_withdraw_request['CharityCashWithdrawal']['charity_id']
                        ));
                        $this->CharityCashWithdrawal->updateAll(array(
                            'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Rejected
                        ) , array(
                            'CharityCashWithdrawal.id' => $canceled_withdraw_request['CharityCashWithdrawal']['id']
                        ));
                    }
                    //
                    $this->Session->setFlash(__l('Checked requests have been moved to rejected status, Amount sent back tot the users.') , 'default', null, 'success');
                }
            }
        }
        $this->redirect(array(
            'controller' => 'charity_cash_withdrawals',
            'action' => 'index'
        ));
    }
}
?>