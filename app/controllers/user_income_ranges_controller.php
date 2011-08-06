<?php
class UserIncomeRangesController extends AppController
{
    public $name = 'UserIncomeRanges';
    public function admin_index()
    {
        $this->pageTitle = __l('Income Ranges');
        $this->UserIncomeRange->recursive = 0;
        $this->set('userIncomeRanges', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Income Range');
        $this->UserIncomeRange->create();
        if (!empty($this->request->data)) {
            if ($this->UserIncomeRange->save($this->request->data)) {
                $this->Session->setFlash(__l('Income Range has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Income Range could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Income Range');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->UserIncomeRange->save($this->request->data)) {
                $this->Session->setFlash(__l('Income Range has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Income Range could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->UserIncomeRange->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $this->pageTitle.= ' - ' . $this->request->data['UserIncomeRange']['id'];
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->UserIncomeRange->delete($id)) {
            $this->Session->setFlash(__l('Income Range deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>