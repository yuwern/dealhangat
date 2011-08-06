<?php
class MailChimpListsController extends AppController
{
    public $name = 'MailChimpLists';
    public function admin_index()
    {
        $this->pageTitle = __l('Mail Chimp Lists');
        $this->MailChimpList->recursive = 0;
        $this->set('mailChimpLists', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Mail Chimp List');
        if (!empty($this->request->data)) {
            $this->MailChimpList->create();
            if ($this->MailChimpList->save($this->request->data)) {
                $this->Session->setFlash(__l('Mail Chimp List has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Mail Chimp List could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
        $cities = $this->MailChimpList->City->find('list');
        $this->set(compact('cities'));
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Mail Chimp List');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->MailChimpList->save($this->request->data)) {
                $this->Session->setFlash(__l('Mail Chimp List has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Mail Chimp List could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->MailChimpList->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $this->pageTitle.= ' - ' . $this->request->data['MailChimpList']['id'];
        $cities = $this->MailChimpList->City->find('list');
        $this->set(compact('cities'));
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->MailChimpList->delete($id)) {
            $this->Session->setFlash(__l('Mail Chimp List deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>