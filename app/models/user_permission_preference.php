<?php
class UserPermissionPreference extends AppModel
{
    public $name = 'UserPermissionPreference';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }
    function getUserPrivacySettings($user_id)
    {
        $privacy = $this->find('first', array(
            'conditions' => array(
                'UserPermissionPreference.user_id' => $user_id
            ) ,
            'recursive' => - 1
        ));
        return $privacy;
    }
}
?>