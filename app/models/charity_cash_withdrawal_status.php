<?php
class CharityCashWithdrawalStatus extends AppModel
{
    public $name = 'CharityCashWithdrawalStatus';
    public $displayField = 'name';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $hasMany = array(
        'CharityCashWithdrawal' => array(
            'className' => 'CharityCashWithdrawal',
            'foreignKey' => 'charity_cash_withdrawal_status_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }
}
?>