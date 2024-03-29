<?php
class TempContact extends AppModel
{
    public $name = 'TempContact';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ) ,
        'ContactUser' => array(
            'className' => 'User',
            'foreignKey' => 'contact_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->add_friend_options = array(
            '0' => __l('No Action') ,
            '1' => __l('Add as friend')
        );
        $this->exist_friend_options = array(
            '0' => __l('No Action') ,
            '1' => __l('Remove')
        );
        $this->invite_friend_options = array(
            '0' => __l('No Action') ,
            '1' => __l('Invite')
        );
    }
}
?>