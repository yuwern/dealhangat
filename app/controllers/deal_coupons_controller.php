<?php
class DealCouponsController extends AppController
{
    public $name = 'DealCoupons';
    public $permanentCacheAction = array(
        'index' => array(
            'is_user_specific_url' => true
        )
    );
    function index()
    {
        $this->pageTitle = __l('Deal Coupons');
        $this->DealCoupon->recursive = 0;
        if ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) {
            $conditions = array(
                'Deal.id' => $this->request->params['named']['deal_id']
            );
        } else {
            $company = $this->DealCoupon->Deal->Company->find('first', array(
                'conditions' => array(
                    'Company.user_id' => $this->Auth->user('id')
                ) ,
                'recursive' => - 1
            ));
            if (empty($company)) {
                throw new NotFoundException(__l('Invalid request'));
            }
            $conditions = array(
                'Deal.id' => $this->request->params['named']['deal_id'],
                'Deal.company_id' => $company['Company']['id']
            );
        }
        $deal = $this->DealCoupon->Deal->find('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'Deal.id',
                'Deal.name',
            ) ,
            'recursive' => - 1
        ));
        if (empty($deal)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->paginate = array(
            'conditions' => array(
                'DealCoupon.deal_id' => $this->request->params['named']['deal_id']
            ) ,
            'recursive' => - 1
        );
        $this->set('deal', $deal);
        $this->set('dealCoupons', $this->paginate());
    }
    function delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->DealCoupon->delete($id)) {
            $this->Session->setFlash(__l('Unused coupon deleted') , 'default', null, 'success');
            $this->redirect(array(
                'controller' => 'deals',
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    function admin_index()
    {
        $this->setAction('index');
    }
    function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->DealCoupon->delete($id)) {
            $this->Session->setFlash(__l('Unused coupon deleted') , 'default', null, 'success');
            $this->redirect(array(
                'controller' => 'deals',
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>