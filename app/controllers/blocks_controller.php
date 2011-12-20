<?php
class BlocksController extends AppController
{
    public $helpers = array(
        'Cache',
        'Html',
        'Form'
    );
    public function beforeFilter()
    {
        parent::beforeFilter();
    }
    public function admin_add()
    {
        if ($this->request->is('post')) {
          if ($this->Block->save($this->request->data)) {
            $this->Session->setFlash('Your block has been saved.');
            $this->redirect(array('action' => 'index'));
          }
        }
    }
    public function admin_edit($id = null)
    {
        $this->Block->id = $id;
        if ($this->request->is('get')) {
            $this->request->data = $this->Block->read();
        } else {
            if ($this->Block->save($this->request->data)) {
                $this->Session->setFlash('Your block has been updated.');
                $this->redirect(array('action' => 'index'));
            }
        }

    }
    public function admin_index()
    {
        $this->set('blocks', $this->Block->find('all'));
    }
    public function admin_delete($id)
    {

        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->Block->delete($id)) {
            $this->Session->setFlash(__l('Block Deleted Successfully') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index',
                $cancelled
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }

        // 
        // 
        // if (!$this->request->is('post')) {
        //     throw new MethodNotAllowedException();
        // }
        // if ($this->Block->delete($id)) {
        //     $this->Session->setFlash('The block with id: ' . $id . ' has been deleted.');
        //     $this->redirect(array('action' => 'admin_index'));
        // }
        // 
    }
    public function admin_view($id = null)
    {
        $this->Block->id = $id;
        $this->set('block', $this->Block->read());
    }
}
