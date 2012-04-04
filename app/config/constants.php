<?php
class ConstPaymentStatus
{
    const Success = 'Success';
    const Refund = 'Refund';
    const Cancel = 'Cancel';
    const Pending = 'Pending';
}
class ConstDealStatus
{
    const Upcoming = 1;
    const Open = 2;
    const Canceled = 3;
    const Expired = 4;
    const Tipped = 5;
    const Closed = 6;
    const Refunded = 7;
    const PaidToCompany = 8;
    const PendingApproval = 9;
    const Rejected = 10;
    const Draft = 11;
    const Delete = 12; //Not available in table. only for coding purpose
    const SubDeal = 13; //Not available in table. only for coding purpose
}
class ConstTopicType
{
    const DealTalk = 1;
    const CityTalk = 2;
    const GlobalTalk = 3;
}
class ConstUserTypes
{
    const Admin = 1;
    const User = 2;
    const Company = 3;
}
class ConstUserIds
{
    const Admin = 1;
}
class ConstAttachment
{
    const Page = 2;
    const UserAvatar = 1;
    const Deal = 0;
}
class ConstFriendRequestStatus
{
    const Pending = 1;
    const Approved = 2;
    const Reject = 3;
}
class ConstMoreAction
{
    const Inactive = 1;
    const Active = 2;
    const Delete = 3;
    const OpenID = 4;
    const Export = 5;
    const EnableCompanyProfile = 6;
    const Used = 7;
    const DisableCompanyProfile = 8;
    const Online = 9;
    const Offline = 10;
    const FaceBook = 11;
    const DeductAmountFromWallet = 12;
    const NotUsed = 13;
    const UnSubscripe = 14;
    const Checked = 15;
    const Unchecked = 16;
    const Approved = 17;
    const Disapproved = 18;
    const Yahoo = 19;
    const Gmail = 20;
    const Twitter = 21;
    const Foursquare = 22;
}
class ConstUserFriendStatus
{
    const Pending = 1;
    const Approved = 2;
    const Rejected = 3;
}
// setting for one way and two way friendships
class ConstUserFriendType
{
    const IsTwoWay = true;
}
// Banned ips types
class ConstBannedTypes
{
    const SingleIPOrHostName = 1;
    const IPRange = 2;
    const RefererBlock = 3;
}
// Banned ips durations
class ConstBannedDurations
{
    const Permanent = 1;
    const Days = 2;
    const Weeks = 3;
}
class ConstReferralRule
{
    const Referral = 1;
    const Referred = 2;
    const BuyedFirst = 3;
    const BuyedSecond = 4;
}
class ConstWithdrawalStatus
{
    const Pending = 1;
    const Approved = 2;
    const Rejected = 3;
    const Failed = 4;
    const Success = 5;
}
class ConstAffiliateCashWithdrawalStatus
{
    const Pending = 1;
    const Approved = 2;
    const Rejected = 3;
    const Failed = 4;
    const Success = 5;
}
class ConstCommsisionType
{
   const Amount = 'amount';
   const Percentage = 'percentage';
}

class ConstAffiliateStatus
{
   const Pending = 1;
   const Canceled = 2;
   const PipeLine = 3;
   const Completed = 4;
}
class ConstAffiliateCommissionType
{
   const Percentage = 1;
   const Amount = 2;
}
class ConstAffiliateRequests
{
   const Pending = 0;
   const Accepted = 1;
   const Rejected = 2;
}
class ConstMyGiftStatus
{
    const Pending = 'Pending';
    const Success = 'Not Yet Redeemed';
    const ToCredit = 'Redeemed By Recipient';
}
class ConstReferralOption
{
    const GrouponLikeRefer = "Refer and Get Amount";
    const XRefer = "Refer and Get Refund/Get Amount";
    const Disabled = "Disable Referral";
}
class ConstReferralRefundType
{
    const RefundDealAmount = "Refund Deal Amount";
    const RefundParticularAmount = "Refund Particular Amount";
}
class ConstTransactionTypes
{
    const AddedToWallet = 1;
    const BuyDeal = 2;
    const DealGift = 3;
    const GiftSent = 4;
    const GiftReceived = 5;
    const ReferralAmount = 6;
    const PaidDealAmountToCompany = 7;
    const UserCashWithdrawalAmount = 8;
    const DealBoughtRefund = 9;
    const DealGiftRefund = 10;
    const ReferralAmountPaid = 11;
    const ReceivedDealPurchasedAmount = 12;
    const AcceptCashWithdrawRequest = 13;
    const DeductedAmountForOfflineCompany = 14;
    const DealBoughtCancel = 15;
    const DealGiftCancel = 16;
    const UserWithdrawalRequest = 17;
    const AdminApprovedWithdrawalRequest = 18;
    const AdminRejecetedWithdrawalRequest = 19;
    const FailedWithdrawalRequest = 20;
    const AmountRefundedForRejectedWithdrawalRequest = 21;
    const AmountApprovedForUserCashWithdrawalRequest = 22;
    const FailedWithdrawalRequestRefundToUser = 24;
	const AddFundToWallet = 25;
	const DeductFundFromWallet = 26;
	const PartallyAmountTakenForDealPurchase = 28;
	const PartallyAmountTakenForGiftPurchase = 29;
	const AffliateUserWithdrawalRequest = 30;
    const AffliateAdminApprovedWithdrawalRequest = 31;
    const AffliateAdminRejecetedWithdrawalRequest = 32;
    const AffliateFailedWithdrawalRequest = 33;
	const AffliateAmountRefundedForRejectedWithdrawalRequest = 34;
    const AffliateAmountApprovedForUserCashWithdrawalRequest = 35;
    const AffliateFailedWithdrawalRequestRefundToUser = 36;
	const AffliateAddFundToAffiliate = 37;
    const AffliateAcceptCashWithdrawRequest = 38;
	const CharityFailedWithdrawalRequest = 40;
	const CharityFailedWithdrawalRequestRefundToUser = 41;
	const CharityAcceptCashWithdrawRequest = 42;
	const CharityAdminApprovedWithdrawalRequest = 43;
    const CharityAdminRejecetedWithdrawalRequest = 44;
    const AmountTakenForCharity = 45;
	const CharityAddFundToCharity = 47;
	const AddFundForSignup = 48;
	const AddFundForFeacbookShare = 49;		
}
// Setting for privacy setting
class ConstPrivacySetting
{
    const EveryOne = 1;
    const Users = 2;
    const Friends = 3;
    const Nobody = 4;
}
class ConstPaymentGateways
{
    const Wallet = 1;
    const CreditCard = 2;
    const PayPalAuth = 3;	
    const AuthorizeNet = 4;
	const PagSeguro = 5;
	// affiliate setting 
	const PayPal = 3;
}
class ConstPaymentGatewayFilterOptions
{
    const Active = 1;
    const Inactive = 2;
    const TestMode = 3;
    const LiveMode = 4;
}
class ConstPaymentGatewayMoreActions
{
    const Activate = 1;
    const Deactivate = 2;
    const MakeTestMode = 3;
    const MakeLiveMode = 4;
    const Delete = 5;
}
class ConstCharityWhoWillPay
{
    const Admin = 'Admin';
    const CompanyUser = 'Company User';
    const AdminCompanyUser  = 'Admin and Company User';    
}
class ConstCharityWhoWillChoose
{
    const CompanyUser = 'Company User';
    const Buyer = 'Buyer';   
}
class ConstCharityCashWithdrawalStatus
{
    const Pending = 1;
    const Failed = 2;
    const Success = 3;
	const Approved = 4;
	const Rejected = 5;
	
}
class ConstProfileImage
{
    const Twitter = 1;
	const Facebook = 2;
	const Upload = 3;
}
class ConstCurrencies
{
    const USD = 1;
}
class ConstShare
{
    const Twitter = 1;
	const Facebook = 2;
}
/*
date_default_timezone_set('Asia/Calcutta');

Configure::write('Config.language', 'spa');
setlocale (LC_TIME, 'es');
*/
/*
** to do move to settings table
*/
$config['sitemap']['models'] = array(
    'Deal',
	'Topic'
);
$config['sitemap']['models'] = array(
    'Deal' => array(
        'conditions' => array(
            'deal_status_id' => array(
				ConstDealStatus::Open,
				ConstDealStatus::Tipped
			),
        ),
		'contain' => array(
			'Deal',
			'CitiesDeal' => array(
				'City' => array(
					'fields' => array(
						'City.id',
						'City.name',
						'City.slug',
					)
				)
			)
		),
		'fields'=>array(
			'slug',
			'id',
		)
    ),
	'Topic'
);
?>