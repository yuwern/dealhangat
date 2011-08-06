<?php
class UserEmploymentsController extends AppController
{
    public $name = 'UserEmployments';
    public function admin_index()
    {
        $this->pageTitle = __l('Employments');
        $this->UserEmployment->recursive = 0;
        $this->set('userEmployments', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Employment');
        $this->UserEmployment->create();
        if (!empty($this->request->data)) {
            if ($this->UserEmployment->save($this->request->data)) {
                $this->Session->setFlash(__l('Employment has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Employment could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Employment');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->UserEmployment->save($this->request->data)) {
                $this->Session->setFlash(__l('Employment has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Employment could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->UserEmployment->read(null, $id);
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
        if ($this->UserEmployment->delete($id)) {
            $this->Session->setFlash(__l('Employment deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>