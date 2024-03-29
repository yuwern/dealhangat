<?php
class Currency extends AppModel
{
    public $name = 'Currency';
    public $displayField = 'code';
    //$validate set in __construct for multi-language support
    public $hasMany = array(
        'CurrencyConversion' => array(
            'className' => 'CurrencyConversion',
            'foreignKey' => 'currency_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = array(
            'name' => array(
                'rule' => 'notempty',
                'allowEmpty' => false,
                'message' => __l('Required')
            ) ,
            'code' => array(
                'rule' => 'notempty',
                'allowEmpty' => false,
                'message' => __l('Required')
            ) ,
            'symbol' => array(
                'rule' => 'notempty',
                'allowEmpty' => false,
                'message' => __l('Required')
            )
        );
    }
    function cacheCurrency($is_supported = null)
    {
        $conditions = array();
        $conditions['Currency.is_enabled'] = 1;
        if (!empty($is_supported)) {
            $conditions['Currency.is_paypal_supported'] = 1;
        }
        $currencies = $this->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'Currency.id',
                'Currency.name',
                'Currency.code',
                'Currency.symbol',
                'Currency.dec_point',
                'Currency.thousands_sep',
                'Currency.is_paypal_supported',
            ) ,
            'order' => array(
                'Currency.id' => 'ASC'
            ) ,
            'recursive' => 1
        ));
        if (!empty($is_supported)) {
            Cache::write('site_supported_currencies', $currencies);
        } else {
            Cache::write('site_currencies', $currencies);
        }
        return $currencies;
    }
    function afterSave()
    {
        Cache::delete('site_currencies');
        Cache::delete('site_paypal_conversion_currency');
        Cache::delete('site_paypal_conversion_currency_rate');
        Cache::delete('site_supported_currencies');
        Cache::delete('site_authorizenet_conversion_currency');
    }
    function afterDelete()
    {
        Cache::delete('site_currencies');
        Cache::delete('site_paypal_conversion_currency');
        Cache::delete('site_paypal_conversion_currency_rate');
        Cache::delete('site_supported_currencies');
        Cache::delete('site_authorizenet_conversion_currency');
    }
    function _retrieveCurrencies()
    {
        //Check whther we have already cached the currencies this session...
        //...we haven't, so load utility classes needed
        App::import('HttpSocket');
        App::import('Xml');
        //Create an http socket
        $http = new HttpSocket();
        //And retrieve rates as an XML object
        $currencies = Xml::build('http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml');
        //Convert XML to array
        $currencies = Set::reverse($currencies);
        //Filter down to just the rates
        $currencies = $currencies['Envelope']['Cube']['Cube']['Cube'];
        //Create an array to hold the rates
        $currencyList = array();
        //European Central bank gives us everything against Euro so add this manually
        $currencyList['EUR'] = 1;
        //Now iterate through and add the rates provided
        foreach($currencies as $currency) {
            $currencyList[$currency['@currency']] = $currency['@rate'];
        }
        //Return rates array from session
        //return $this->Session->read('Currencies');

        return $currencyList;
    }
    function table($base = 'EUR', $decimals = 2)
    {
        //Create array to holds rates
        $rateTable = array();
        //Get rate table array
        $rates = $this->_retrieveCurrencies();
        //Iterate throught each rate converting it against $base
        foreach($rates as $key => $value) {
            $rateTable[$key] = number_format(1 / $rates[$base] * $rates[$key], $decimals);
        }
        //Return result array
        return $rateTable;
    }
    function convert_rate($rates = array() , $base = 'EUR', $decimals = 2)
    {
        //Create array to holds rates
        $rateTable = array();
        //Get rate table array
        if (empty($rates)) {
            $rates = $this->_retrieveCurrencies();
        }
        //Iterate throught each rate converting it against $base
        foreach($rates as $key => $value) {
            $rateTable[$key] = number_format(1 / $rates[$base] * $rates[$key], $decimals);
        }
        //Return result array
        return $rateTable;
    }
    function rate_convertion()
    {
        $currencyLists = $this->find('all', array(
            'recursive' => - 1
        ));
        $supported_currencyLists = $this->find('list', array(
            'fields' => array(
                'Currency.id',
                'Currency.code',
            ) ,
            'recursive' => - 1
        ));
        $currency_convertions = $this->CurrencyConversion->find('all', array(
            'recursive' => - 1
        ));
        $rate_lists = $this->_retrieveCurrencies();
        foreach($currencyLists as $currencyList) {
            $rates = $this->convert_rate($rate_lists, $currencyList['Currency']['code'], 2);
            foreach($supported_currencyLists as $id => $code) {
                $data = array();
                foreach($currency_convertions as $currency_convertion) {
                    if (($currency_convertion['CurrencyConversion']['currency_id'] == $currencyList['Currency']['id']) && ($currency_convertion['CurrencyConversion']['converted_currency_id'] == $id)) {
                        $data['CurrencyConversion']['id'] = $currency_convertion['CurrencyConversion']['id'];
                    }
                }
                if (empty($data)) {
                    $this->CurrencyConversion->create();
                }
                if (!empty($rates[$code])) {
                    $data['CurrencyConversion']['rate'] = $rates[$code];
                    $data['CurrencyConversion']['converted_currency_id'] = $id;
                    $data['CurrencyConversion']['currency_id'] = $currencyList['Currency']['id'];
                    $this->CurrencyConversion->save($data);
                }
            }
        }
    }
}
?>