<?php
class PaypalDocaptureLog extends AppModel
{
    public $name = 'PaypalDocaptureLog';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'DealUser' => array(
            'className' => 'DealUser',
            'foreignKey' => 'deal_user_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => ''
        )
    );
}
?>