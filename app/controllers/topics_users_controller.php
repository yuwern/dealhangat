<?php
class TopicsUsersController extends AppController
{
    public $name = 'TopicsUsers';
    public function add($topic_id = null)
    {
        $this->request->data['TopicsUser']['topic_id'] = $topic_id;
        $this->request->data['TopicsUser']['user_id'] = $this->Auth->user('id');
        if (!empty($this->request->data)) {
            $this->TopicsUser->create();
            if ($this->TopicsUser->save($this->request->data)) {
                $this->Session->setFlash(__l('You are now following this topic') , 'default', null, 'success');
                $this->redirect(array(
                    'controller' => 'topic_discussions',
                    'action' => 'index',
                    $topic_id
                ));
            }
        }
    }
    public function delete($topic_id = null)
    {
        if (is_null($topic_id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->TopicsUser->deleteAll(array(
            'TopicsUser.topic_id' => $topic_id,
            'TopicsUser.user_id' => $this->Auth->user('id')
        ))) {
            $this->Session->setFlash(__l('You are no longer following this topic') , 'default', null, 'success');
            $this->redirect(array(
                'controller' => 'topic_discussions',
                'action' => 'index',
                $topic_id
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>