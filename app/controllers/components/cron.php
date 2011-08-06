<?php
class CronComponent extends Component
{
    var $controller;
    public function update_deal()
    {
        App::import('Model', 'Deal');
        $this->Deal = new Deal();
        App::import('Model', 'Subscription');
        $this->Subscription = new Subscription();
        App::import('Model', 'EmailTemplate');
        $this->EmailTemplate = new EmailTemplate();
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'Email');
        $this->Email = new EmailComponent($collection);
        App::import('Model', 'UserCashWithdrawal');
        $this->UserCashWithdrawal = new UserCashWithdrawal();
        $timeZone = Configure::read('site.timezone_offset');
        if (!empty($timeZone)) {
            date_default_timezone_set($timeZone);
        }
        require_once (LIBS . 'router.php');
        $this->Deal->_processOpenStatus();
        //change status of upcoming to open
        $this->Deal->updateAll(array(
            'Deal.deal_status_id' => ConstDealStatus::Open
        ) , array(
            'Deal.start_date <= ' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
            'Deal.deal_status_id' => ConstDealStatus::Upcoming,
        ));
        //send subscription mail
        $this->Deal->_sendSubscriptionMail();
        //update failure deals
        $this->Deal->updateAll(array(
            'Deal.deal_status_id' => ConstDealStatus::Expired
        ) , array(
            'Deal.end_date <= ' => _formatDate('Y-m-d H:i:s', date('Y-m-d H:i:s') , true) ,
            'Deal.is_anytime_deal' => 0,
            'Deal.deal_status_id' => ConstDealStatus::Open
        ));
        // Expired Tripped Deals To Closed With An Email To The Deal Owner With Deal User List
        $this->Deal->_closeDeals();
        //refund amount
        if (Configure::read('deal.is_auto_refund_enabled')) {
            $this->Deal->_refundDealAmount('cron');
        }
        //Automatic user cash with draw payment
        if (Configure::read('user.is_withdraw_request_amount_paid_automatic')) {
            $this->UserCashWithdrawal->_transferAmount(ConstUserTypes::User);
        }
        //Automatic company cash with draw payment for company
        if (Configure::read('user.company.is_withdraw_request_amount_paid_automatic')) {
            $this->UserCashWithdrawal->_transferAmount(ConstUserTypes::Company);
        }
        //Automatic pament to deals
        if (Configure::read('company.is_paid_to_company_automatic')) {
            $this->Deal->_payToCompany('cron');
        }
        // City wise deal count update
        $this->Deal->_updateCityDealCount();
        // For Affiliates ( //
        if (Configure::read('affiliate.is_enabled')) {
            App::import('Model', 'Affiliate');
            $this->Affiliate = new Affiliate();
            $this->Affiliate->update_affiliate_status();
        }
        // ) For affiliates //
        
    }
    function currency_convertion($is_manual_update = 0)
    {
		if(!empty($is_manual_update)){
			App::import('Model', 'Currency');
			$this->Currency = new Currency();	
			$this->Currency->rate_convertion();		
		}elseif(Configure::read('site.is_auto_currency_updation')) {
			App::import('Model', 'Currency');
			$this->Currency = new Currency();		
			$this->Currency->rate_convertion();
		}
    }
}
?>