<?php
class CharitiesController extends AppController
{
    public $name = 'Charities';
    public $permanentCacheAction = array(
        'view' => array(
            'is_public_url' => true,
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        if (!Configure::read('charity.is_enabled') && $this->Auth->user('user_type_id') != ConstUserTypes::Admin) {
            throw new NotFoundException(__l('Invalid request'));
        }
        parent::beforeFilter();
    }
    function view($slug = null)
    {
        $this->pageTitle = __l('Charity');
        if (is_null($slug)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $charity = $this->Charity->find('first', array(
            'conditions' => array(
                'Charity.slug = ' => $slug
            ) ,
        ));
        $this->pageTitle.= ' - ' . $charity['Charity']['name'];
        $this->set('charity', $charity);
    }
    public function admin_index()
    {
        $this->pageTitle = __l('Charities');
        $this->_redirectGET2Named(array(
            'q',
        ));
        $conditions = array();
        if (!empty($this->request->params['named']['charity_category_id'])) {
            $conditions['Charity.charity_category_id'] = $this->request->params['named']['charity_category_id'];
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => array(
                'Charity.id' => 'desc'
            )
        );
        if (isset($this->request->params['named']['q']) && !empty($this->request->params['named']['q'])) {
            $this->paginate = array_merge($this->paginate, array(
                'search' => $this->request->params['named']['q']
            ));
            $this->request->data['Charity']['q'] = $this->request->params['named']['q'];
        }
        $this->Charity->recursive = 0;
        $moreActions = $this->Charity->moreActions;
        $this->set(compact('moreActions'));
        $this->set('charities', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Charity');
        if (!empty($this->request->data)) {
            $this->Charity->create();
            if ($this->Charity->save($this->request->data)) {
                $this->Session->setFlash(__l('Charity has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Charity could not be added. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data['Charity']['is_active'] = 1;
        }
        $charityCategories = $this->Charity->CharityCategory->find('list');
        $this->set(compact('charityCategories'));
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Charity');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->Charity->save($this->request->data)) {
                $this->Session->setFlash(__l('Charity has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Charity could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->Charity->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $this->pageTitle.= ' - ' . $this->request->data['Charity']['name'];
        $charityCategories = $this->Charity->CharityCategory->find('list');
        $this->set(compact('charityCategories'));
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->Charity->delete($id)) {
            $this->Session->setFlash(__l('Charity deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    function admin_update()
    {
        if (!empty($this->request->data[$this->modelClass])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $ids = array();
            foreach($this->request->data[$this->modelClass] as $id => $is_checked) {
                if ($is_checked['id']) {
                    $ids[] = $id;
                }
            }
            if ($actionid && !empty($ids)) {
                switch ($actionid) {
                case ConstMoreAction::Active:
                    foreach($ids as $id) {
                        $this->{$this->modelClass}->updateAll(array(
                            $this->modelClass . '.is_active' => 1
                        ) , array(
                            $this->modelClass . '.id' => $id
                        ));
                    }
                    $this->Session->setFlash(__l('Checked charities has been marked as active') , 'default', null, 'success');
                    break;

                case ConstMoreAction::Inactive:
                    foreach($ids as $id) {
                        $this->{$this->modelClass}->updateAll(array(
                            $this->modelClass . '.is_active' => 0
                        ) , array(
                            $this->modelClass . '.id' => $id
                        ));
                    }
                    $this->Session->setFlash(__l('Checked charities has been marked as inactive') , 'default', null, 'success');
                    break;

                case ConstMoreAction::Delete:
                    foreach($ids as $id) {
                        $this->{$this->modelClass}->deleteAll(array(
                            $this->modelClass . '.id' => $id
                        ));
                    }
                    $this->Session->setFlash(__l('Checked charities has been deleted') , 'default', null, 'success');
                    break;
                }
            }
        }
        $this->redirect(Router::url('/', true) . $r);
    }
}
?>