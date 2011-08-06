<?php
class AffiliateCashWithdrawal extends AppModel
{
    public $name = 'AffiliateCashWithdrawal';
    //$validate set in __construct for multi-language support
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ) ,
        'AffiliateCashWithdrawalStatus' => array(
            'className' => 'AffiliateCashWithdrawalStatus',
            'foreignKey' => 'affiliate_cash_withdrawal_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ) ,
        'PaymentGateway' => array(
            'className' => 'PaymentGateway',
            'foreignKey' => 'payment_gateway_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        )
    );
    public $hasOne = array(
        'PaypalTransactionLog' => array(
            'className' => 'PaypalTransactionLog',
            'foreignKey' => 'affiliate_cash_withdrawal_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ) ,
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = array(
            'user_id' => array(
                'rule' => 'numeric',
                'allowEmpty' => false,
                'message' => __l('Required')
            ) ,
            'amount' => array(
                'rule2' => array(
                    'rule' => 'numeric',
                    'message' => __l('Should be numeric')
                ) ,
                'rule1' => array(
                    'rule' => 'notempty',
                    'message' => __l('Required')
                )
            )
        );
        $this->moreActions = array(
            ConstAffiliateCashWithdrawalStatus::Pending => __l('Pending') ,
            ConstAffiliateCashWithdrawalStatus::Approved => __l('Approve (Pay to user)') ,
            ConstAffiliateCashWithdrawalStatus::Rejected => __l('Rejected') ,
        );
    }
    function _checkAmount($amount)
    {
        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->data[$this->name]['user_id']
            ) ,
            'fields' => array(
                'User.commission_line_amount',
                'User.user_type_id',
            ) ,
            'recursive' => - 1
        ));
        $user_available_balance = $user['User']['commission_line_amount'];
        if ($user_available_balance < $amount) {
            $this->validationErrors['amount'] = __l('Given amount is greater than your commission amount');
        }
        if ($user['User']['user_type_id'] == ConstUserTypes::User) {
            if ($amount < Configure::read('affiliate.payment_threshold_for_threshold_limit_reach')) {
                $this->validationErrors['amount'] = __l('Given amount is less than withdraw limit');
            }
        }
        return false;
    }
    function _transferAmount($user_type_id = '', $userCashWithdrawalsIds = array())
    {
        App::import('Model', 'PaypalTransactionLog');
        $this->PaypalTransactionLog = new PaypalTransactionLog();
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'Paypal');
        $this->Paypal = new PaypalComponent($collection);
        $flash_message = '';
        $conditions['AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id'] = ConstAffiliateCashWithdrawalStatus::Pending;
        if (!empty($userCashWithdrawalsIds)) {
            $conditions['AffiliateCashWithdrawal.id'] = $userCashWithdrawalsIds;
        } elseif ($user_type_id == ConstUserTypes::User) {
            $conditions['User.user_type_id'] = ConstUserTypes::User;
        }
        $userCashWithdrawals = $this->find('all', array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'UserProfile' => array(
                        'fields' => array(
                            'UserProfile.paypal_account'
                        )
                    ) ,
                    'fields' => array(
                        'User.username',
                        'User.available_balance_amount',
                        'User.blocked_amount',
                        'User.commission_line_amount',
                        'User.commission_withdraw_request_amount',
                        'User.commission_paid_amount'
                    )
                ) ,
            ) ,
            'recursive' => 2
        ));
        if (!empty($userCashWithdrawals)) {
            $paymentGateway = $this->User->Transaction->PaymentGateway->find('first', array(
                'conditions' => array(
                    'PaymentGateway.id ' => ConstPaymentGateways::PayPal
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
            foreach($userCashWithdrawals as $userCashWithdrawal) {
                if (!empty($userCashWithdrawal) && !empty($userCashWithdrawal['User']['UserProfile']['paypal_account']) && $userCashWithdrawal['User']['commission_withdraw_request_amount'] >= $userCashWithdrawal['AffiliateCashWithdrawal']['amount']) {
                    $affilate_transaction_fee_enabled = Configure::read('affiliate.site_commission_amount');
                    if (!empty($affilate_transaction_fee_enabled)) {
                        if (Configure::read('affiliate.site_commission_type') == 'percentage') {
                            $commission_amount = ($userCashWithdrawal['AffiliateCashWithdrawal']['amount'] * Configure::read('affiliate.site_commission_amount') / 100);
                        } else {
                            $commission_amount = Configure::read('affiliate.site_commission_amount');
                        }
                        $amount = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'] - $commission_amount;
                        $userCashWithdrawal['AffiliateCashWithdrawal']['commission_amount'] = $commission_amount;
                        $this->save($userCashWithdrawal);
                    } else {
                        $amount = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'];
                    }
                    $this->PaypalTransactionLog->create();
                    // Currency Conversion Process //
                    $get_conversion_val = $this->getConversionCurrency();
                    $get_conversion = $this->_convertAmount($amount);
                    $this->data['PaypalTransactionLog']['currency_id'] = $get_conversion_val['CurrencyConversion']['currency_id'];
                    $this->data['PaypalTransactionLog']['converted_currency_id'] = $get_conversion_val['CurrencyConversion']['converted_currency_id'];
                    $this->data['PaypalTransactionLog']['orginal_amount'] = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'];
                    $this->data['PaypalTransactionLog']['rate'] = $get_conversion_val['CurrencyConversion']['rate'];
                    $this->data['PaypalTransactionLog']['user_id'] = $userCashWithdrawal['AffiliateCashWithdrawal']['user_id'];
                    $this->data['PaypalTransactionLog']['currency_type'] = $get_conversion['currency_code'];
                    $this->data['PaypalTransactionLog']['is_mass_pay'] = 1;
                    $this->data['PaypalTransactionLog']['mass_pay_status'] = 'PENDING';
                    if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                        foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                            if ($paymentGatewaySetting['key'] == 'payee_account') {
                                $this->data['PaypalTransactionLog']['payer_email'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                                break;
                            }
                        }
                    }
                    $this->data['PaypalTransactionLog']['receiver_email'] = $userCashWithdrawal['User']['UserProfile']['paypal_account'];
                    $this->data['PaypalTransactionLog']['ip'] = $_SERVER['REMOTE_ADDR'];
                    $this->PaypalTransactionLog->save($this->data, false);
                    $paypal_log_list[$userCashWithdrawal['AffiliateCashWithdrawal']['id']] = $paypal_transaction_list[] = $paypal_transaction_id = $this->PaypalTransactionLog->getLastInsertId();
                    $userCashWithdrawal_list[] = $userCashWithdrawal['AffiliateCashWithdrawal']['id'];
                    $reciever_info[] = array(
                        'receiverEmail' => $userCashWithdrawal['User']['UserProfile']['paypal_account'],
                        'amount' => $get_conversion['amount'],
                        'uniqueID' => 'affiliate-' . $userCashWithdrawal['AffiliateCashWithdrawal']['id'],
                        'note' => 'Amount Received from ' . Configure::read('site.name') ,
                    );
                }
            }
            if (!empty($userCashWithdrawal_list)) {
                if (!empty($paymentGateway['PaymentGatewaySetting'])) {
                    foreach($paymentGateway['PaymentGatewaySetting'] as $paymentGatewaySetting) {
                        if ($paymentGatewaySetting['key'] == 'masspay_API_UserName') {
                            $sender_info['API_UserName'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                        }
                        if ($paymentGatewaySetting['key'] == 'masspay_API_Password') {
                            $sender_info['API_Password'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                        }
                        if ($paymentGatewaySetting['key'] == 'masspay_API_Signature') {
                            $sender_info['API_Signature'] = $paymentGateway['PaymentGateway']['is_test_mode'] ? $paymentGatewaySetting['test_mode_value'] : $paymentGatewaySetting['live_mode_value'];
                        }
                    }
                }
                $notify_url = Router::url(array(
                    'controller' => 'affiliate_cash_withdrawals',
                    'action' => 'process_masspay_ipn',
                    'admin' => false
                ) , true);
                $paypal_response = $this->Paypal->massPay($sender_info, $reciever_info, $notify_url, 'Your Payment Has been Sent', $paymentGateway['PaymentGateway']['is_test_mode'], $get_conversion['currency_code']);
                $this->PaypalTransactionLog->updateAll(array(
                    'PaypalTransactionLog.masspay_response' => '\'' . serialize($paypal_response) . '\'',
                    'PaypalTransactionLog.affiliate_cash_withdrawal_id' => $userCashWithdrawal['AffiliateCashWithdrawal']['id'],
                ) , array(
                    'PaypalTransactionLog.id' => $paypal_transaction_list
                ));
                $return['paypal_log_list'] = $paypal_log_list;
                $return['paypal_response'] = $paypal_response;
                return $return;
            }
        }
    }
    function affiliate_masspay_ipn_process($userCashWithdrawal_id, $userCashWithdrawal_response)
    {
        App::import('Model', 'PaypalTransactionLog');
        $this->PaypalTransactionLog = new PaypalTransactionLog();
        $userCashWithdrawal = $this->find('first', array(
            'conditions' => array(
                'AffiliateCashWithdrawal.id' => $userCashWithdrawal_id,
                'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Approved,
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
                        'PaypalTransactionLog.affiliate_cash_withdrawal_id',
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
        if (!empty($userCashWithdrawal)) {
            if ($userCashWithdrawal_response['status'] == 'Completed') {
                $data['Transaction']['user_id'] = ConstUserIds::Admin;
                $data['Transaction']['foreign_id'] = $userCashWithdrawal['AffiliateCashWithdrawal']['user_id'];
                $data['Transaction']['class'] = 'SecondUser';
                $data['Transaction']['amount'] = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'];
                $data['Transaction']['payment_gateway_id'] = ConstPaymentGateways::PayPal;
                $data['Transaction']['description'] = 'Payment Success';
                $data['Transaction']['gateway_fees'] = $userCashWithdrawal_response['mc_fee'];
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateAddFundToAffiliate;
                // Currency Conversion Changes //
                $data['Transaction']['currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['currency_id'];
                $data['Transaction']['converted_currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['converted_currency_id'];
                $data['Transaction']['converted_amount'] = $userCashWithdrawal_response['mc_gross'];
                $data['Transaction']['rate'] = $userCashWithdrawal['PaypalTransactionLog']['rate'];
                $transaction_id = $this->User->Transaction->log($data);
                $data = array();
                $data['Transaction']['user_id'] = $userCashWithdrawal['AffiliateCashWithdrawal']['user_id'];
                $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                $data['Transaction']['class'] = 'SecondUser';
                $data['Transaction']['amount'] = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'];
                $data['Transaction']['payment_gateway_id'] = ConstPaymentGateways::PayPal;
                $data['Transaction']['description'] = 'Payment Success';
                $data['Transaction']['gateway_fees'] = $userCashWithdrawal_response['mc_fee'];
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateAcceptCashWithdrawRequest;
                // Currency Conversion Changes //
                $data['Transaction']['currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['currency_id'];
                $data['Transaction']['converted_currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['converted_currency_id'];
                $data['Transaction']['converted_amount'] = $userCashWithdrawal_response['mc_gross'];
                $data['Transaction']['rate'] = $userCashWithdrawal['PaypalTransactionLog']['rate'];
                $transaction_to_user = $this->User->Transaction->log($data);
                $this->User->updateAll(array(
                    'User.commission_paid_amount' => 'User.commission_paid_amount +' . $userCashWithdrawal['AffiliateCashWithdrawal']['amount']
                ) , array(
                    'User.id' => $userCashWithdrawal['AffiliateCashWithdrawal']['user_id']
                ));
                $this->updateAll(array(
                    'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Success
                ) , array(
                    'AffiliateCashWithdrawal.id' => $userCashWithdrawal_id
                ));
            } else {
                //Failed,Returned,Reversed,Unclaimed
                $data['Transaction']['user_id'] = ConstUserIds::Admin;
                $data['Transaction']['foreign_id'] = $userCashWithdrawal['AffiliateCashWithdrawal']['user_id'];
                $data['Transaction']['class'] = 'SecondUser';
                $data['Transaction']['amount'] = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'];
                $data['Transaction']['description'] = 'User cash withdrawal request failed from paypal';
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateFailedWithdrawalRequest;
                $this->User->Transaction->log($data);
                $data = array();
                $data['Transaction']['user_id'] = $userCashWithdrawal['AffiliateCashWithdrawal']['user_id'];
                $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                $data['Transaction']['class'] = 'SecondUser';
                $data['Transaction']['amount'] = $userCashWithdrawal['AffiliateCashWithdrawal']['amount'];
                $data['Transaction']['description'] = 'User cash withdrawal request failed from paypal';
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::AffliateFailedWithdrawalRequestRefundToUser;
                $this->User->Transaction->log($data);;
                $transaction_to_user = $this->User->Transaction->log($data);
                $this->User->Transaction->log($data);
                $this->User->updateAll(array(
                    'User.commission_line_amount' => 'User.commission_line_amount +' . $userCashWithdrawal['AffiliateCashWithdrawal']['amount']
                ) , array(
                    'User.id' => $userCashWithdrawal['AffiliateCashWithdrawal']['user_id']
                ));
                $this->updateAll(array(
                    'AffiliateCashWithdrawal.affiliate_cash_withdrawal_status_id' => ConstAffiliateCashWithdrawalStatus::Failed
                ) , array(
                    'AffiliateCashWithdrawal.id' => $userCashWithdrawal_id
                ));
            }
            $this->User->updateAll(array(
                'User.commission_withdraw_request_amount' => 'User.commission_withdraw_request_amount -' . $userCashWithdrawal['AffiliateCashWithdrawal']['amount']
            ) , array(
                'User.id' => $userCashWithdrawal['AffiliateCashWithdrawal']['user_id']
            ));
            $log_data = array();
            $log_data['PaypalTransactionLog']['id'] = $userCashWithdrawal['PaypalTransactionLog']['id'];
            $log_data['PaypalTransactionLog']['user_id'] = $userCashWithdrawal['AffiliateCashWithdrawal']['user_id'];
            $log_data['PaypalTransactionLog']['is_mass_pay'] = 1;
            $log_data['PaypalTransactionLog']['transaction_id'] = $transaction_id;
            $log_data['PaypalTransactionLog']['receiver_email'] = $userCashWithdrawal_response['receiver_email'];
            $log_data['PaypalTransactionLog']['txn_id'] = $userCashWithdrawal_response['masspay_txn_id'];
            $log_data['PaypalTransactionLog']['paypal_response'] = strtoupper($userCashWithdrawal_response['status']);
            $log_data['PaypalTransactionLog']['mass_pay_status'] = strtoupper($userCashWithdrawal_response['status']);
            $log_data['PaypalTransactionLog']['mc_currency'] = $userCashWithdrawal_response['mc_currency'];
            $log_data['PaypalTransactionLog']['mc_gross'] = $userCashWithdrawal_response['mc_gross'];
            $log_data['PaypalTransactionLog']['ip'] = '\'' . $_SERVER['REMOTE_ADDR'] . '\'';
            $log_data['PaypalTransactionLog']['mc_fee'] = $userCashWithdrawal_response['mc_fee'];
            $this->PaypalTransactionLog->save($log_data);
        }
    }
}
?>