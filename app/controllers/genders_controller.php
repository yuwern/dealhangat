<?php
class GendersController extends AppController
{
    public $name = 'Genders';
    public function admin_index()
    {
        $this->pageTitle = __l('Genders');
        $this->Gender->recursive = 0;
        $this->paginate = array(
            'order' => array(
                'Gender.id' => 'desc'
            )
        );
        $this->set('genders', $this->paginate());
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Gender');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            if ($this->Gender->save($this->request->data)) {
                $this->Session->setFlash(__l('Gender has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Gender could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->Gender->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
    }
}
?>