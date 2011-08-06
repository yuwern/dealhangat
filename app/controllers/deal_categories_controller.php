<?php
class DealCategoriesController extends AppController
{
    public $name = 'DealCategories';
    public function admin_index()
    {
        $this->_redirectGET2Named(array(
            'q'
        ));
        $this->pageTitle = __l('Deal Subscription Categories');
        $this->DealCategory->recursive = 0;
        if (isset($this->request->params['named']['q'])) {
            $this->request->data['DealCategory']['q'] = $this->request->params['named']['q'];
            $this->pageTitle.= sprintf(__l(' - Search - %s') , $this->request->params['named']['q']);
        }
        $this->paginate = array(
            'order' => array(
                'DealCategory.id' => 'desc'
            ) ,
        );
        if (isset($this->request->data['DealCategory']['q'])) {
            $this->paginate = array_merge($this->paginate, array(
                'search' => $this->request->params['named']['q']
            ));
        }
        $this->set('dealCategories', $this->paginate());
        $moreActions = $this->DealCategory->moreActions;
        $this->set(compact('moreActions'));
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Deal Subscription  Category');
        if (!empty($this->request->data)) {
            $this->DealCategory->create();
            if ($this->DealCategory->save($this->request->data)) {
                $this->Session->setFlash(__l('Deal subscription category has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Deal subscription category could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
        $subscriptions = $this->DealCategory->Subscription->find('list');
        $this->set(compact('subscriptions'));
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Deal Subscription Category');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->DealCategory->save($this->request->data)) {
                $this->Session->setFlash(__l('Deal subscription category has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Deal subscription category could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->DealCategory->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $this->pageTitle.= ' - ' . $this->request->data['DealCategory']['name'];
        $subscriptions = $this->DealCategory->Subscription->find('list');
        $this->set(compact('subscriptions'));
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->DealCategory->delete($id)) {
            $this->Session->setFlash(__l('Deal subscription category deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_update()
    {
        $this->autoRender = false;
        if (!empty($this->request->data[$this->modelClass])) {
            $r = $this->request->data[$this->modelClass]['r'];
            $actionid = $this->request->data[$this->modelClass]['more_action_id'];
            unset($this->request->data[$this->modelClass]['r']);
            unset($this->request->data[$this->modelClass]['more_action_id']);
            $selectedIds = array();
            foreach($this->request->data[$this->modelClass] as $primary_key_id => $is_checked) {
                if ($is_checked['id']) {
                    $selectedIds[] = $primary_key_id;
                }
            }
            if ($actionid && !empty($selectedIds)) {
                if ($actionid == ConstMoreAction::Delete) {
                    $this->{$this->modelClass}->deleteAll(array(
                        $this->modelClass . '.id' => $selectedIds
                    ));
                    $this->Session->setFlash(__l('Checked deal subscription categories has been deleted') , 'default', null, 'success');
                }
            }
        }
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(Router::url('/', true) . $r);
        } else {
            $this->redirect($r);
        }
    }
}
?>