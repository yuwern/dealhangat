<?php
class PrivacyTypesController extends AppController
{
    public $name = 'PrivacyTypes';
    public function admin_index()
    {
        $this->pageTitle = __l('Privacy Types');
        $this->PrivacyType->recursive = 0;
        $this->paginate = array(
            'order' => array(
                'PrivacyType.id' => 'desc'
            )
        );
        $this->set('privacyTypes', $this->paginate());
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Privacy Type');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->PrivacyType->save($this->request->data)) {
                $this->Session->setFlash(__l('Privacy Type has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Privacy Type could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->PrivacyType->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
    }
}
?>