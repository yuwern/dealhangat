<?php
class DealCoupon extends AppModel
{
    public $name = 'DealCoupon';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'Deal' => array(
            'className' => 'Deal',
            'foreignKey' => 'deal_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }
}
?>