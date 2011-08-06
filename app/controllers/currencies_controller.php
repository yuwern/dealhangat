<?php
class CurrenciesController extends AppController
{
    public $name = 'Currencies';
    function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'CurrencyConversion'
        );
        parent::beforeFilter();
    }
    public function admin_index()
    {
        $this->pageTitle = __l('Currencies');
        $this->_redirectGET2Named(array(
            'q',
        ));
        $this->paginate = array(
            'order' => array(
                'Currency.id' => 'desc'
            )
        );
        if (isset($this->request->params['named']['q']) && !empty($this->request->params['named']['q'])) {
            $this->paginate = array_merge($this->paginate, array(
                'search' => $this->request->params['named']['q']
            ));
            $this->request->data['Currency']['q'] = $this->request->params['named']['q'];
        }
        $this->Currency->recursive = 0;
        $this->set('currencies', $this->paginate());
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Currency');
        $this->Currency->create();
        if (!empty($this->request->data)) {
            if ($this->Currency->save($this->request->data)) {
                $this->Session->setFlash(__l('Currency has been added') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Currency could not be added. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data['Currency']['is_enabled'] = 1;
        }
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Currency');
        if (!empty($this->request->data['Currency']['id'])) {
            $id = $this->request->data['Currency']['id'];
        }
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $currency = $this->Currency->find('first', array(
            'conditions' => array(
                'Currency.id' => $id
            ) ,
            'recursive' => - 1
        ));
        if (!empty($this->request->data)) {
            if ($this->Currency->save($this->request->data)) {
                if (!empty($this->request->data['CurrencyConversion'])) {
                    foreach($this->request->data['CurrencyConversion'] as $currencyConversion) {
                        if (!empty($currencyConversion['rate'])) {
                            $_convertion = array();
                            $_convertion['CurrencyConversion'] = $currencyConversion;
                            $_convertion['CurrencyConversion']['currency_id'] = $this->request->data['Currency']['id'];
                            $this->Currency->CurrencyConversion->create();
                            $this->Currency->CurrencyConversion->save($_convertion);
                        }
                    }
                }
                $this->Session->setFlash(__l('Currency has been updated') , 'default', null, 'success');
                $this->redirect(array(
                    'action' => 'index'
                ));
            } else {
                $this->Session->setFlash(__l('Currency could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $currency;
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $currencyList = $this->Currency->find('list', array(
            /*'conditions' => array(
                'Currency.is_paypal_supported' => 1
            ) ,*/
            'order' => array(
                'Currency.code' => 'asc'
            ) ,
            'recursive' => - 1
        ));
        $currencies = array();
        $i = 0;
        foreach($currencyList as $currency_conversion_id => $code) {
            $currencyConversion = $this->Currency->CurrencyConversion->find('first', array(
                'conditions' => array(
                    'CurrencyConversion.currency_id' => $currency['Currency']['id'],
                    'CurrencyConversion.converted_currency_id' => $currency_conversion_id
                ) ,
                'recursive' => - 1
            ));
            if (!empty($currencyConversion)) {
                $currencies[$i]['id'] = $currencyConversion['CurrencyConversion']['id'];
                $currencies[$i]['rate'] = $currencyConversion['CurrencyConversion']['rate'];
            } else {
                $currencies[$i]['id'] = '';
                $currencies[$i]['rate'] = '';
			}
            $currencies[$i]['converted_currency_id'] = $currency_conversion_id;
            $currencies[$i]['code'] = $code;
            $i++;
        }
        $this->set('currencies', $currencies);
        $this->pageTitle.= ' - ' . $this->request->data['Currency']['name'];
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->Currency->delete($id)) {
            $this->Session->setFlash(__l('Currency deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    function admin_update_status()
    {
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'cron');
        $this->Cron = new CronComponent($collection);
        $this->Cron->currency_convertion(1);
        $this->redirect(array(
            'controller' => 'currencies',
            'action' => 'index'
        ));
    }
}
?>