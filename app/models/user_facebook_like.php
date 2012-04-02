<?php
class UserFacebookLike extends AppModel
{
    public $name = 'UserFacebookLike';
    public $displayField = 'id';
    //$validate set in __construct for multi-language support
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'Deal' => array(
            'className' => 'Deal',
            'foreignKey' => 'deal_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => false
        ) ,
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => false
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->alidate = array(
            'deal_id' => array(
                'rule1' => array(
                    'rule' => 'numeric',
                    'allowEmpty' => true,
                    'message' => __l('Enter the Deal id') ,
                ) ,
            ) ,
            'user_id' => array(
                'rule1' => array(
                    'rule' => 'numeric',
                    'allowEmpty' => true,
                    'message' => __l('Enter the User_id') ,
                ) ,
            ) ,
        );
    }
}
