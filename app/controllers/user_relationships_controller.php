<?php
class UserRelationshipsController extends AppController
{
    public $name = 'UserRelationships';
    public function admin_index()
    {
        $this->pageTitle = __l('Relationships');
        $this->UserRelationship->recursive = 0;
        $this->set('userRelationships', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Relationship');
        $this->UserRelationship->create();
        if (!empty($this->request->data)) {
            if ($this->UserRelationship->save($this->request->data)) {
                $this->Session->setFlash(__l('Relationship has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Relationship could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit  Relationship');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->UserRelationship->save($this->request->data)) {
                $this->Session->setFlash(__l('Relationship has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Relationship could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->UserRelationship->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->UserRelationship->delete($id)) {
            $this->Session->setFlash(__l('Relationship deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>