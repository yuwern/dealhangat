<?php
class CharityCashWithdrawal extends AppModel
{
    public $name = 'CharityCashWithdrawal';
    //$validate set in __construct for multi-language support
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'Charity' => array(
            'className' => 'Charity',
            'foreignKey' => 'charity_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ) ,
        'CharityCashWithdrawalStatus' => array(
            'className' => 'CharityCashWithdrawalStatus',
            'foreignKey' => 'charity_cash_withdrawal_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        )
    );
    public $hasOne = array(
        'PaypalTransactionLog' => array(
            'className' => 'PaypalTransactionLog',
            'foreignKey' => 'charity_cash_withdrawal_id',
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
            'charity_id' => array(
                'rule' => 'numeric',
                'allowEmpty' => false,
                'message' => __l('Required')
            ) ,
            'amount' => array(
                'rule3' => array(
                    'rule' => '_checkAmount',
                    'message' => __l('should be less than or equal to available amount') ,
                    'allowEmpty' => false
                ) ,
                'rule2' => array(
                    'rule' => 'numeric',
                    'message' => __l('Should be numeric') ,
                    'allowEmpty' => false
                ) ,
                'rule1' => array(
                    'rule' => 'notempty',
                    'message' => __l('Required')
                )
            ) ,
            'charity_cash_withdrawal_status_id' => array(
                'rule' => 'numeric',
                'allowEmpty' => false,
                'message' => __l('Required')
            )
        );
        $this->moreActions = array(
            ConstCharityCashWithdrawalStatus::Pending => __l('Pending') ,
            ConstCharityCashWithdrawalStatus::Approved => __l('Approve (Pay to user)') ,
            ConstCharityCashWithdrawalStatus::Rejected => __l('Rejected') ,
        );
    }
    function _checkAmount()
    {
        $charity = $this->Charity->find('first', array(
            'conditions' => array(
                'Charity.id' => $this->data['CharityCashWithdrawal']['charity_id']
            ) ,
            'fields' => array(
                'Charity.available_amount',
            ) ,
            'recursive' => - 1
        ));
        $charity_transaction_fee_enabled = Configure::read('charity.site_commission_amount');
        if (!empty($charity_transaction_fee_enabled)) {
            if (Configure::read('charity.site_commission_type') == 'percentage') {
                $this->data['CharityCashWithdrawal']['commission_amount'] = ($this->data['CharityCashWithdrawal']['amount'] * Configure::read('charity.site_commission_amount') / 100);
            } else {
                $this->data['CharityCashWithdrawal']['commission_amount'] = Configure::read('charity.site_commission_amount');
            }
        }
        $charity_available_balance = $charity['Charity']['available_amount'];
        $amount = $this->data['CharityCashWithdrawal']['amount'] + $this->data['CharityCashWithdrawal']['commission_amount'];
        if ($charity_available_balance < $amount) {
            return false;
        }
        return true;
    }
    function _transferAmount($user_type_id = '', $charityCashWithdrawalsIds = array())
    {
        App::import('Model', 'PaypalTransactionLog');
        $this->PaypalTransactionLog = new PaypalTransactionLog();
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'Paypal');
        $this->Paypal = new PaypalComponent($collection);
        $flash_message = '';
        $conditions['CharityCashWithdrawal.charity_cash_withdrawal_status_id'] = ConstCharityCashWithdrawalStatus::Pending;
        if (!empty($charityCashWithdrawalsIds)) {
            $conditions['CharityCashWithdrawal.id'] = $charityCashWithdrawalsIds;
        }
        $charityCashWithdrawals = $this->find('all', array(
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
            'recursive' => 2
        ));
        if (!empty($charityCashWithdrawals)) {
            App::import('Model', 'PaymentGateway');
            $this->PaymentGateway = new PaymentGateway();
            $paymentGateway = $this->PaymentGateway->find('first', array(
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
            foreach($charityCashWithdrawals as $charityCashWithdrawal) {
                if (!empty($charityCashWithdrawal) && !empty($charityCashWithdrawal['Charity']['paypal_email']) && $charityCashWithdrawal['Charity']['withdraw_request_amount'] >= $charityCashWithdrawal['CharityCashWithdrawal']['amount']) {
                    $affilate_transaction_fee_enabled = Configure::read('charity.site_commission_amount');
                    if (!empty($affilate_transaction_fee_enabled)) {
                        if (Configure::read('charity.site_commission_type') == 'percentage') {
                            $commission_amount = ($charityCashWithdrawal['CharityCashWithdrawal']['amount'] * Configure::read('charity.site_commission_amount') / 100);
                        } else {
                            $commission_amount = Configure::read('charity.site_commission_amount');
                        }
                        $amount = $charityCashWithdrawal['CharityCashWithdrawal']['amount'] - $commission_amount;
                        $charityCashWithdrawal['CharityCashWithdrawal']['commission_amount'] = $commission_amount;
                        $this->save($charityCashWithdrawal);
                    } else {
                        $amount = $charityCashWithdrawal['CharityCashWithdrawal']['amount'];
                    }
                    $this->PaypalTransactionLog->create();
                    // Currency Conversion Process //
                    $get_conversion_val = $this->getConversionCurrency();
                    $get_conversion = $this->_convertAmount($amount);
                    $this->data['PaypalTransactionLog']['currency_id'] = $get_conversion_val['CurrencyConversion']['currency_id'];
                    $this->data['PaypalTransactionLog']['converted_currency_id'] = $get_conversion_val['CurrencyConversion']['converted_currency_id'];
                    $this->data['PaypalTransactionLog']['orginal_amount'] = $charityCashWithdrawal['CharityCashWithdrawal']['amount'];
                    $this->data['PaypalTransactionLog']['rate'] = $get_conversion_val['CurrencyConversion']['rate'];
                    $this->data['PaypalTransactionLog']['user_id'] = $charityCashWithdrawal['CharityCashWithdrawal']['charity_id'];
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
                    $this->data['PaypalTransactionLog']['receiver_email'] = $charityCashWithdrawal['Charity']['paypal_email'];
                    $this->data['PaypalTransactionLog']['ip'] = $_SERVER['REMOTE_ADDR'];
                    $this->PaypalTransactionLog->save($this->data, false);
                    $paypal_log_list[$charityCashWithdrawal['CharityCashWithdrawal']['id']] = $paypal_transaction_list[] = $paypal_transaction_id = $this->PaypalTransactionLog->getLastInsertId();
                    $charityCashWithdrawal_list[] = $charityCashWithdrawal['CharityCashWithdrawal']['id'];
                    $reciever_info[] = array(
                        'receiverEmail' => $charityCashWithdrawal['Charity']['paypal_email'],
                        'amount' => $get_conversion['amount'],
                        'uniqueID' => 'charity-' . $charityCashWithdrawal['CharityCashWithdrawal']['id'],
                        'note' => 'Amount Received from ' . Configure::read('site.name') ,
                    );
                }
            }
            if (!empty($charityCashWithdrawal_list)) {
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
                    'controller' => 'user_cash_withdrawals',
                    'action' => 'process_masspay_ipn',
                    'admin' => false
                ) , true);
                $paypal_response = $this->Paypal->massPay($sender_info, $reciever_info, $notify_url, 'Your Payment Has been Sent', $paymentGateway['PaymentGateway']['is_test_mode'], $get_conversion['currency_code']);
                $this->PaypalTransactionLog->updateAll(array(
                    'PaypalTransactionLog.masspay_response' => '\'' . serialize($paypal_response) . '\'',
                    'PaypalTransactionLog.charity_cash_withdrawal_id' => $charityCashWithdrawal['CharityCashWithdrawal']['id'],
                ) , array(
                    'PaypalTransactionLog.id' => $paypal_transaction_list
                ));
                $return['paypal_log_list'] = $paypal_log_list;
                $return['paypal_response'] = $paypal_response;
                return $return;
            }
        }
    }
    function charity_masspay_ipn_process($userCashWithdrawal_id, $userCashWithdrawal_response)
    {
        App::import('Model', 'PaypalTransactionLog');
        $this->PaypalTransactionLog = new PaypalTransactionLog();
        App::import('Model', 'Transaction');
        $this->Transaction = new Transaction();
        $userCashWithdrawal = $this->find('first', array(
            'conditions' => array(
                'CharityCashWithdrawal.id' => $userCashWithdrawal_id,
                'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Approved,
            ) ,
            'contain' => array(
                'Charity' => array(
                    'fields' => array(
                        'Charity.name',
                        'Charity.available_amount'
                    )
                ) ,
                'PaypalTransactionLog' => array(
                    'fields' => array(
                        'PaypalTransactionLog.id',
                        'PaypalTransactionLog.user_id',
                        'PaypalTransactionLog.transaction_id',
                        'PaypalTransactionLog.charity_cash_withdrawal_id',
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
                $data['Transaction']['foreign_id'] = $userCashWithdrawal['CharityCashWithdrawal']['charity_id'];
                $data['Transaction']['class'] = 'Charity';
                $data['Transaction']['amount'] = $userCashWithdrawal['CharityCashWithdrawal']['amount'];
                $data['Transaction']['payment_gateway_id'] = ConstPaymentGateways::PayPal;
                $data['Transaction']['description'] = 'Payment Success';
                $data['Transaction']['gateway_fees'] = $userCashWithdrawal_response['mc_fee'];
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::CharityAddFundToCharity;
                // Currency Conversion Changes //
                $data['Transaction']['currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['currency_id'];
                $data['Transaction']['converted_currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['converted_currency_id'];
                $data['Transaction']['converted_amount'] = $userCashWithdrawal_response['mc_gross'];
                $data['Transaction']['rate'] = $userCashWithdrawal['PaypalTransactionLog']['rate'];
                $transaction_id = $this->Transaction->log($data);
                $data = array();
                $data['Transaction']['user_id'] = $userCashWithdrawal['CharityCashWithdrawal']['charity_id'];
                $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                $data['Transaction']['class'] = 'Charity';
                $data['Transaction']['amount'] = $userCashWithdrawal['CharityCashWithdrawal']['amount'];
                $data['Transaction']['payment_gateway_id'] = ConstPaymentGateways::PayPal;
                $data['Transaction']['description'] = 'Payment Success';
                $data['Transaction']['gateway_fees'] = $userCashWithdrawal_response['mc_fee'];
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::CharityAcceptCashWithdrawRequest;
                // Currency Conversion Changes //
                $data['Transaction']['currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['currency_id'];
                $data['Transaction']['converted_currency_id'] = $userCashWithdrawal['PaypalTransactionLog']['converted_currency_id'];
                $data['Transaction']['converted_amount'] = $userCashWithdrawal_response['mc_gross'];
                $data['Transaction']['rate'] = $userCashWithdrawal['PaypalTransactionLog']['rate'];
                $transaction_to_user = $this->Transaction->log($data);
                $this->Charity->updateAll(array(
                    'Charity.total_amount' => 'Charity.paid_amount +' . $userCashWithdrawal['CharityCashWithdrawal']['amount']
                ) , array(
                    'Charity.id' => $userCashWithdrawal['CharityCashWithdrawal']['charity_id']
                ));
                $this->updateAll(array(
                    'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Success
                ) , array(
                    'CharityCashWithdrawal.id' => $userCashWithdrawal_id
                ));
            } else {
                //Failed,Returned,Reversed,Unclaimed
                $data['Transaction']['user_id'] = ConstUserIds::Admin;
                $data['Transaction']['foreign_id'] = $userCashWithdrawal['CharityCashWithdrawal']['charity_id'];
                $data['Transaction']['class'] = 'Charity';
                $data['Transaction']['amount'] = $userCashWithdrawal['CharityCashWithdrawal']['amount'];
                $data['Transaction']['description'] = 'Charity cash withdrawal request failed from paypal';
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::CharityFailedWithdrawalRequest;
                $this->Transaction->log($data);
                $data = array();
                $data['Transaction']['user_id'] = $userCashWithdrawal['CharityCashWithdrawal']['charity_id'];
                $data['Transaction']['foreign_id'] = ConstUserIds::Admin;
                $data['Transaction']['class'] = 'Charity';
                $data['Transaction']['amount'] = $userCashWithdrawal['CharityCashWithdrawal']['amount'];
                $data['Transaction']['description'] = 'Charity cash withdrawal request failed from paypal';
                $data['Transaction']['transaction_type_id'] = ConstTransactionTypes::CharityFailedWithdrawalRequestRefundToUser;
                $this->Transaction->log($data);;
                $transaction_to_user = $this->Charity->Transaction->log($data);
                $this->Charity->Transaction->log($data);
                $this->Charity->updateAll(array(
                    'Charity.available_amount' => 'Charity.available_amount +' . $userCashWithdrawal['CharityCashWithdrawal']['amount']
                ) , array(
                    'Charity.id' => $userCashWithdrawal['CharityCashWithdrawal']['charity_id']
                ));
                $this->updateAll(array(
                    'CharityCashWithdrawal.charity_cash_withdrawal_status_id' => ConstCharityCashWithdrawalStatus::Failed
                ) , array(
                    'CharityCashWithdrawal.id' => $userCashWithdrawal_id
                ));
            }
            $this->Charity->updateAll(array(
                'Charity.withdraw_request_amount' => 'Charity.withdraw_request_amount -' . $userCashWithdrawal['CharityCashWithdrawal']['amount']
            ) , array(
                'Charity.id' => $userCashWithdrawal['CharityCashWithdrawal']['charity_id']
            ));
            $log_data = array();
            $log_data['PaypalTransactionLog']['id'] = $userCashWithdrawal['PaypalTransactionLog']['id'];
            $log_data['PaypalTransactionLog']['user_id'] = $userCashWithdrawal['CharityCashWithdrawal']['charity_id'];
            $log_data['PaypalTransactionLog']['is_mass_pay'] = 1;
            $log_data['PaypalTransactionLog']['transaction_id'] = $transaction_id;
            $log_data['PaypalTransactionLog']['receiver_email'] = $userCashWithdrawal_response['receiver_email'];
            $log_data['PaypalTransactionLog']['txn_id'] = $userCashWithdrawal_response['masspay_txn_id'];
            $log_data['PaypalTransactionLog']['paypal_response'] = strtoupper($userCashWithdrawal_response['status']);
            $log_data['PaypalTransactionLog']['mass_pay_status'] = strtoupper($userCashWithdrawal_response['status']);
            $log_data['PaypalTransactionLog']['mc_currency'] = $userCashWithdrawal_response['mc_currency'];
            $log_data['PaypalTransactionLog']['mc_gross'] = $userCashWithdrawal_response['mc_gross'];
            $log_data['PaypalTransactionLog']['mc_fee'] = $userCashWithdrawal_response['mc_fee'];
            $log_data['PaypalTransactionLog']['ip'] = '\'' . $_SERVER['REMOTE_ADDR'] . '\'';
            $this->PaypalTransactionLog->save($log_data);
        }
    }
}
?>
