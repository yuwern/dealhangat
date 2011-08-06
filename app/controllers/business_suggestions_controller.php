<?php
class BusinessSuggestionsController extends AppController
{
    public $name = 'BusinessSuggestions';
    public $permanentCacheAction = array(
        'add' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true
        )
    );
    public function add()
    {
        $this->pageTitle = __l('Suggest a Business');
        $this->BusinessSuggestion->create();
        if (!empty($this->request->data)) {
            if ($this->Auth->user('id')) {
                $this->request->data['BusinessSuggestion']['user_id'] = $this->Auth->user('id');
            }
            if ($this->BusinessSuggestion->save($this->request->data)) {
                $this->Session->setFlash(__l('Suggestion has been sent') , 'default', null, 'success');
                $this->redirect(array(
                    'controller' => 'deals',
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Suggestion could not be sent. Please, try again.') , 'default', null, 'error');
            }
        }
        if ($this->Auth->user('id')) {
            $this->request->data['BusinessSuggestion']['email'] = $this->Auth->user('email');
        }
    }
    public function admin_index()
    {
        $this->pageTitle = __l('Business Suggestions');
        $conditions = array();
        $this->paginate = array(
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.user_type_id',
                        'User.username',
                        'User.id',
                    ) ,
                    'UserAvatar'
                ) ,
            ) ,
            'recursive' => 1,
            'order' => 'BusinessSuggestion.id desc'
        );
        $this->set('businessSuggestions', $this->paginate());
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $businessSuggestion = $this->BusinessSuggestion->find('first', array(
            'conditions' => array(
                'BusinessSuggestion.id' => $id,
            ) ,
            'recursive' => - 1
        ));
        if (!empty($businessSuggestion['BusinessSuggestion']['id']) && $this->BusinessSuggestion->delete($businessSuggestion['BusinessSuggestion']['id'])) {
            $this->Session->setFlash(__l('Business suggestion deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>