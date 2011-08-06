<?php
class UserCashWithdrawal extends AppModel
{
    public $name = 'UserCashWithdrawal';
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
        'WithdrawalStatus' => array(
            'className' => 'WithdrawalStatus',
            'foreignKey' => 'withdrawal_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => true,
            'counterScope' => ''
        ) ,
    );
    public $hasOne = array(
        'PaypalTransactionLog' => array(
            'className' => 'PaypalTransactionLog',
            'foreignKey' => 'user_cash_withdrawal_id',
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
            'withdrawal_status_id' => array(
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
            ConstWithdrawalStatus::Pending => __l('Pending') ,
            ConstWithdrawalStatus::Approved => __l('Approve (Pay to user)') ,
            ConstWithdrawalStatus::Rejected => __l('Rejected') ,
        );
    }
    function _checkAmount($amount)
    {
        $user_available_balance = $this->User->checkUserBalance($this->data[$this->name]['user_id']);
        if ($user_available_balance < $amount) {
            $this->validationErrors['amount'] = __l('Given amount is greater than wallet amount');
        }
        if ($this->data[$this->name]['user_type_id'] == ConstUserTypes::User) {
            if (($amount < Configure::read('user.minimum_withdraw_amount')) || ($amount > Configure::read('user.maximum_withdraw_amount'))) {
                $this->validationErrors['amount'] = sprintf(__l('Given amount should lies from  %s%s to %s%s') , Configure::read('site.currency') , Configure::read('user.minimum_withdraw_amount') , Configure::read('site.currency') , Configure::read('user.maximum_withdraw_amount'));
            }
        } else if ($this->data[$this->name]['user_type_id'] == ConstUserTypes::Company) {
            if (($amount < Configure::read('company.minimum_withdraw_amount')) || ($amount > Configure::read('company.maximum_withdraw_amount'))) {
                $this->validationErrors['amount'] = sprintf(__l('Given amount should lies from  %s%s to %s%s') , Configure::read('site.currency') , Configure::read('company.minimum_withdraw_amount') , Configure::read('site.currency') , Configure::read('company.maximum_withdraw_amount'));
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
        $conditions['UserCashWithdrawal.withdrawal_status_id'] = ConstWithdrawalStatus::Pending;
        if (!empty($userCashWithdrawalsIds)) {
            $conditions['UserCashWithdrawal.id'] = $userCashWithdrawalsIds;
        } elseif ($user_type_id == ConstUserTypes::User) {
            $conditions['User.user_type_id'] = ConstUserTypes::User;
        } elseif ($user_type_id == ConstUserTypes::Company) {
            $conditions['User.user_type_id'] = ConstUserTypes::Company;
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
                        'User.blocked_amount'
                    )
                ) ,
            ) ,
            'recursive' => 2
        ));
        if (!empty($userCashWithdrawals)) {
            $paymentGateway = $this->User->Transaction->PaymentGateway->find('first', array(
                'conditions' => array(
                    'PaymentGateway.id ' => ConstPaymentGateways::PayPalAuth
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
                if (!empty($userCashWithdrawal) && !empty($userCashWithdrawal['User']['UserProfile']['paypal_account']) && $userCashWithdrawal['User']['blocked_amount'] >= $userCashWithdrawal['UserCashWithdrawal']['amount']) {
                    // Currency Conversion Process //
                    $get_conversion_val = $this->getConversionCurrency();
                    $get_conversion = $this->_convertAmount($userCashWithdrawal['UserCashWithdrawal']['amount']);
                    $this->data['PaypalTransactionLog']['currency_id'] = $get_conversion_val['CurrencyConversion']['currency_id'];
                    $this->data['PaypalTransactionLog']['converted_currency_id'] = $get_conversion_val['CurrencyConversion']['converted_currency_id'];
                    $this->data['PaypalTransactionLog']['orginal_amount'] = $userCashWithdrawal['UserCashWithdrawal']['amount'];
                    $this->data['PaypalTransactionLog']['rate'] = $get_conversion_val['CurrencyConversion']['rate'];
                    $this->data['PaypalTransactionLog']['user_id'] = $userCashWithdrawal['UserCashWithdrawal']['user_id'];
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
                    $this->data['PaypalTransactionLog']['user_cash_withdrawal_id'] = $userCashWithdrawal['UserCashWithdrawal']['id'];
                    $this->data['PaypalTransactionLog']['ip'] = $_SERVER['REMOTE_ADDR'];
                    $this->PaypalTransactionLog->save($this->data, false);
                    $paypal_log_list[$userCashWithdrawal['UserCashWithdrawal']['id']] = $paypal_transaction_list[] = $paypal_transaction_id = $this->PaypalTransactionLog->getLastInsertId();
                    $userCashWithdrawal_list[] = $userCashWithdrawal['UserCashWithdrawal']['id'];
                    $reciever_info[] = array(
                        'receiverEmail' => $userCashWithdrawal['User']['UserProfile']['paypal_account'],
                        'amount' => $get_conversion['amount'],
                        'uniqueID' => $userCashWithdrawal['UserCashWithdrawal']['id'],
                        'transaction_log' => $paypal_transaction_id,
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
                    'controller' => 'user_cash_withdrawals',
                    'action' => 'process_masspay_ipn',
                    'admin' => false
                ) , true);
                $paypal_response = $this->Paypal->massPay($sender_info, $reciever_info, $notify_url, 'Your Payment Has been Sent', $paymentGateway['PaymentGateway']['is_test_mode'], $get_conversion['currency_code']);
                $this->PaypalTransactionLog->updateAll(array(
                    'PaypalTransactionLog.masspay_response' => '\'' . serialize($paypal_response) . '\'',
                    'PaypalTransactionLog.user_cash_withdrawal_id' => $userCashWithdrawal['UserCashWithdrawal']['id'],
                ) , array(
                    'PaypalTransactionLog.id' => $paypal_transaction_list
                ));
                $return['paypal_log_list'] = $paypal_log_list;
                $return['paypal_response'] = $paypal_response;
                return $return;
            }
        }
    }
}
?>