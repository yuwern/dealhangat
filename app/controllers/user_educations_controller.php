<?php
class UserEducationsController extends AppController
{
    public $name = 'UserEducations';
    public function admin_index()
    {
        $this->pageTitle = __l('Educations');
        $this->UserEducation->recursive = 0;
        $this->set('userEducations', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Education');
        $this->UserEducation->create();
        if (!empty($this->request->data)) {
            if ($this->UserEducation->save($this->request->data)) {
                $this->Session->setFlash(__l('Education has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Education could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Education');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->UserEducation->save($this->request->data)) {
                $this->Session->setFlash(__l('Education has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Education could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->UserEducation->read(null, $id);
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
        if ($this->UserEducation->delete($id)) {
            $this->Session->setFlash(__l('Education deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>