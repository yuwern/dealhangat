<?php
class MailChimpList extends AppModel
{
    public $name = 'MailChimpList';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = array(
            'city_id' => array(
                'rule2' => array(
                    'rule' => 'isUnique',
                    'message' => __l('City list already exist')
                ) ,
                'rule1' => array(
                    'rule' => 'notempty',
                    'message' => __l('Required')
                )
            ) ,
            'list_id' => array(
                'rule2' => array(
                    'rule' => 'isUnique',
                    'message' => __l('list already exist')
                ) ,
                'rule1' => array(
                    'rule' => 'notempty',
                    'message' => __l('Required')
                )
            )
        );
    }
}
?>