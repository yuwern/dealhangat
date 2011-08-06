<?php
class CompanyAddressesController extends AppController
{
    public $name = 'CompanyAddresses';
    public $permanentCacheAction = array(
        'index' => array(
            'is_user_specific_url' => true
        ) ,
        'add' => array(
            'is_user_specific_url' => true
        ) ,
        'edit' => array(
            'is_user_specific_url' => true
        )
    );
    public function beforeFilter()
    {
        $this->Security->disabledFields = array(
            'City',
            'State',
            'CompanyAddress.latitude',
            'CompanyAddress.longitude',
            'Company.map_zoom_level'
        );
        parent::beforeFilter();
    }
    public function index()
    {
        $company = $this->CompanyAddress->Company->find('first', array(
            'conditions' => array(
                'Company.user_id' => $this->Auth->user('id')
            ) ,
            'fields' => array(
                'Company.id',
                'Company.name',
                'Company.slug',
                'Company.is_company_profile_enabled'
            ) ,
            'recursive' => - 1
        ));
        if (empty($company)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle = __l('Company Addresses');
        $this->CompanyAddress->recursive = 0;
        $this->paginate = array(
            'conditions' => array(
                'CompanyAddress.company_id = ' => $company['Company']['id']
            ) ,
        );
        $this->set('companyAddresses', $this->paginate());
        $this->set('company_id', $company['Company']['id']);
    }
    public function view($id = null)
    {
        $this->pageTitle = __l('Company Address');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $companyAddress = $this->CompanyAddress->find('first', array(
            'conditions' => array(
                'CompanyAddress.id = ' => $id
            ) ,
            'fields' => array(
                'CompanyAddress.id',
                'CompanyAddress.created',
                'CompanyAddress.modified',
                'CompanyAddress.address1',
                'CompanyAddress.address2',
                'CompanyAddress.company_id',
                'CompanyAddress.city_id',
                'CompanyAddress.state_id',
                'CompanyAddress.country_id',
                'CompanyAddress.phone',
                'CompanyAddress.zip',
                'CompanyAddress.url',
                'CompanyAddress.latitude',
                'CompanyAddress.longitude',
                'Company.id',
                'Company.created',
                'Company.modified',
                'Company.name',
                'Company.slug',
                'Company.address1',
                'Company.address2',
                'Company.email',
                'Company.user_id',
                'Company.city_id',
                'Company.state_id',
                'Company.country_id',
                'Company.phone',
                'Company.zip',
                'Company.url',
                'Company.deal_count',
                'Company.is_online_account',
                'Company.is_company_profile_enabled',
                'Company.company_profile',
                'Company.latitude',
                'Company.longitude',
                'City.id',
                'City.created',
                'City.modified',
                'City.country_id',
                'City.state_id',
                'City.name',
                'City.slug',
                'City.latitude',
                'City.longitude',
                'City.dma_id',
                'City.county',
                'City.code',
                'City.deal_count',
                'City.is_approved',
                'City.twitter_username',
                'City.twitter_password',
                'City.twitter_url',
                'City.facebook_url',
                'State.id',
                'State.country_id',
                'State.name',
                'State.code',
                'State.adm1code',
                'State.is_approved',
                'Country.id',
                'Country.name',
                'Country.fips104',
                'Country.iso2',
                'Country.iso3',
                'Country.ison',
                'Country.internet',
                'Country.capital',
                'Country.map_reference',
                'Country.nationality_singular',
                'Country.nationality_plural',
                'Country.currency',
                'Country.currency_code',
                'Country.population',
                'Country.title',
                'Country.comment',
                'Country.slug',
            ) ,
            'recursive' => 0,
        ));
        if (empty($companyAddress)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle.= ' - ' . $companyAddress['CompanyAddress']['id'];
        $this->set('companyAddress', $companyAddress);
    }
    public function add()
    { 
        $this->pageTitle = __l('Add Company Address');
		$this->CompanyAddress->create();
        if (!empty($this->request->data)) {
            $this->CompanyAddress->set($this->request->data);
            $this->CompanyAddress->State->set($this->request->data);
            $this->CompanyAddress->City->set($this->request->data);
            if ($this->CompanyAddress->validates() & $this->CompanyAddress->City->validates() & $this->CompanyAddress->State->validates()) {
                $this->request->data['CompanyAddress']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->CompanyAddress->City->findOrSaveAndGetId($this->request->data['City']['name']);
                $this->request->data['CompanyAddress']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->CompanyAddress->State->findOrSaveAndGetId($this->request->data['State']['name']);
                $this->CompanyAddress->save($this->request->data);
                $this->Session->setFlash(__l('Company Address has been added') , 'default', null, 'success');
                if ($this->RequestHandler->isAjax()) {
                    $this->setAction('index');
                } else {
                    $this->redirect(array(
                        'controller' => 'company_addresses',
                        'action' => 'index',
                        $this->request->data['CompanyAddress']['company_id'],
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Company Address could not be added. Please, try again.') , 'default', null, 'error');
            }
        }
        unset($this->CompanyAddress->Company->City->validate['City']);
        if (!empty($this->request->params['named']['company_id'])) {
            $this->request->data['CompanyAddress']['company_id'] = $this->request->params['named']['company_id'];
        }
        $countries = $this->CompanyAddress->Country->find('list');
        $states = $this->CompanyAddress->State->find('list');	
        $this->set(compact('countries'));
    }
    public function edit($id = null)
    {
        $this->pageTitle = __l('Edit Company Address');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            $this->CompanyAddress->set($this->request->data);
            $this->CompanyAddress->State->set($this->request->data);
            $this->CompanyAddress->City->set($this->request->data);
            if ($this->CompanyAddress->validates() & $this->CompanyAddress->City->validates() & $this->CompanyAddress->State->validates()) {
                $this->request->data['CompanyAddress']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->CompanyAddress->City->findOrSaveAndGetId($this->request->data['City']['name']);
                $this->request->data['CompanyAddress']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->CompanyAddress->State->findOrSaveAndGetId($this->request->data['State']['name']);
                $this->request->data['CompanyAddress']['id'] = $id;
				$this->CompanyAddress->save($this->request->data);
				
				$this->request->data['Company']['id'] = $this->request->data['CompanyAddress']['company_id'];
				$this->CompanyAddress->Company->save($this->request->data['Company']);
                $this->Session->setFlash(__l('Company Address has been updated') , 'default', null, 'success');
                if ($this->RequestHandler->isAjax()) {
                    $this->setAction('index');
                } else {
                    $this->redirect(array(
                        'controller' => 'company_addresses',
                        'action' => 'index',
                        $this->request->data['CompanyAddress']['company_id']
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Company Address could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            $this->request->data = $this->CompanyAddress->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        unset($this->CompanyAddress->Company->City->validate['City']);
        $this->pageTitle.= ' - ' . $this->request->data['CompanyAddress']['id'];
        $countries = $this->CompanyAddress->Country->find('list');
        $this->set(compact('countries'));
    }
    public function delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->CompanyAddress->delete($id)) {
            if ($this->RequestHandler->isAjax()) {
                echo 'deleted';
                exit;
            }
            $this->Session->setFlash(__l('Company Address deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
    public function admin_index()
    {
        $this->pageTitle = __l('Company Addresses');
        $this->CompanyAddress->recursive = 0;
        $this->set('companyAddresses', $this->paginate());
    }
    public function admin_view($id = null)
    {
        $this->pageTitle = __l('Company Address');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $companyAddress = $this->CompanyAddress->find('first', array(
            'conditions' => array(
                'CompanyAddress.id = ' => $id
            ) ,
            'fields' => array(
                'CompanyAddress.id',
                'CompanyAddress.created',
                'CompanyAddress.modified',
                'CompanyAddress.address1',
                'CompanyAddress.address2',
                'CompanyAddress.company_id',
                'CompanyAddress.city_id',
                'CompanyAddress.state_id',
                'CompanyAddress.country_id',
                'CompanyAddress.phone',
                'CompanyAddress.zip',
                'CompanyAddress.url',
                'CompanyAddress.latitude',
                'CompanyAddress.longitude',
                'Company.id',
                'Company.created',
                'Company.modified',
                'Company.name',
                'Company.slug',
                'Company.address1',
                'Company.address2',
                'Company.email',
                'Company.user_id',
                'Company.city_id',
                'Company.state_id',
                'Company.country_id',
                'Company.phone',
                'Company.zip',
                'Company.url',
                'Company.deal_count',
                'Company.is_online_account',
                'Company.is_company_profile_enabled',
                'Company.company_profile',
                'Company.latitude',
                'Company.longitude',
                'City.id',
                'City.created',
                'City.modified',
                'City.country_id',
                'City.state_id',
                'City.name',
                'City.slug',
                'City.latitude',
                'City.longitude',
                'City.dma_id',
                'City.county',
                'City.code',
                'City.deal_count',
                'City.is_approved',
                'City.twitter_username',
                'City.twitter_password',
                'City.twitter_url',
                'City.facebook_url',
                'State.id',
                'State.country_id',
                'State.name',
                'State.code',
                'State.adm1code',
                'State.is_approved',
                'Country.id',
                'Country.name',
                'Country.fips104',
                'Country.iso2',
                'Country.iso3',
                'Country.ison',
                'Country.internet',
                'Country.capital',
                'Country.map_reference',
                'Country.nationality_singular',
                'Country.nationality_plural',
                'Country.currency',
                'Country.currency_code',
                'Country.population',
                'Country.title',
                'Country.comment',
                'Country.slug',
            ) ,
            'recursive' => 0,
        ));
        if (empty($companyAddress)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        $this->pageTitle.= ' - ' . $companyAddress['CompanyAddress']['id'];
        $this->set('companyAddress', $companyAddress);
    }
    public function admin_add()
    {
        $this->pageTitle = __l('Add Company Address');
        if (!empty($this->request->data['CompanyAddress']['company_id'])) {
            $company = $this->CompanyAddress->Company->find('first', array(
                'conditions' => array(
                    'Company.id' => $this->request->data['CompanyAddress']['company_id']
                ) ,
                'recursive' => - 1
            ));
        } else {
            $company = $this->CompanyAddress->Company->find('first', array(
                'conditions' => array(
                    'Company.slug' => $this->request->params['named']['company']
                ) ,
                'recursive' => - 1
            ));
        }
        $this->pageTitle.= ' - ' . $company['Company']['name'];
        if (!empty($this->request->data)) {
            $this->CompanyAddress->set($this->request->data);
            $this->CompanyAddress->State->set($this->request->data);
            $this->CompanyAddress->City->set($this->request->data);
            unset($this->CompanyAddress->City->validate['City']);
            if ($this->CompanyAddress->validates() & $this->CompanyAddress->City->validates() & $this->CompanyAddress->State->validates()) {
                $this->request->data['CompanyAddress']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->CompanyAddress->City->findOrSaveAndGetId($this->request->data['City']['name']);
                $this->request->data['CompanyAddress']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->CompanyAddress->State->findOrSaveAndGetId($this->request->data['State']['name']);
                $this->CompanyAddress->create();
                $this->CompanyAddress->save($this->request->data);
                $this->Session->setFlash(__l('Company Address has been added') , 'default', null, 'success');
                if ($this->RequestHandler->isAjax()) {
                    $this->redirect(array(
                        'controller' => 'companies',
                        'action' => 'edit',
                        $this->request->data['CompanyAddress']['company_id']
                    ));
                } else {
                    $this->redirect(array(
                        'controller' => 'companies',
                        'action' => 'edit',
                        $this->request->data['CompanyAddress']['company_id']
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Company Address could not be added. Please, try again.') , 'default', null, 'error');
            }
        } else {
            unset($this->CompanyAddress->City->validate['City']);
        }
        if (!empty($this->request->params['named']['company'])) {
            $this->request->data['CompanyAddress']['company_id'] = $company['Company']['id'];
        }
        $countries = $this->CompanyAddress->Country->find('list');
        $this->set(compact('countries'));
    }
    public function admin_edit($id = null)
    {
        $this->pageTitle = __l('Edit Company Address');
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if (!empty($this->request->data)) {
            $this->CompanyAddress->set($this->request->data);
            $this->CompanyAddress->State->set($this->request->data);
            $this->CompanyAddress->City->set($this->request->data);
            unset($this->CompanyAddress->City->validate['City']);
            if ($this->CompanyAddress->validates() & $this->CompanyAddress->City->validates() & $this->CompanyAddress->State->validates()) {
                $this->request->data['CompanyAddress']['city_id'] = !empty($this->request->data['City']['id']) ? $this->request->data['City']['id'] : $this->CompanyAddress->City->findOrSaveAndGetId($this->request->data['City']['name']);
                $this->request->data['CompanyAddress']['state_id'] = !empty($this->request->data['State']['id']) ? $this->request->data['State']['id'] : $this->CompanyAddress->State->findOrSaveAndGetId($this->request->data['State']['name']);
                $this->request->data['CompanyAddress']['id'] = $id;
				$this->CompanyAddress->save($this->request->data);
				$this->request->data['Company']['id'] = $this->request->data['CompanyAddress']['company_id'];
				$this->CompanyAddress->Company->save($this->request->data['Company']);
                $this->Session->setFlash(__l('Company Address has been updated') , 'default', null, 'success');
                if ($this->RequestHandler->isAjax()) {
                    $this->redirect(array(
                        'controller' => 'companies',
                        'action' => 'edit',
                        $this->request->data['CompanyAddress']['company_id']
                    ));
                } else {
                    $this->redirect(array(
                        'controller' => 'companies',
                        'action' => 'edit',
                        $this->request->data['CompanyAddress']['company_id']
                    ));
                }
            } else {
                $this->Session->setFlash(__l('Company Address could not be updated. Please, try again.') , 'default', null, 'error');
            }
        } else {
            unset($this->CompanyAddress->City->validate['City']);
            $this->request->data = $this->CompanyAddress->read(null, $id);
            if (empty($this->request->data)) {
                throw new NotFoundException(__l('Invalid request'));
            }
        }
        $this->pageTitle.= ' - ' . $this->request->data['Company']['name'];
        $countries = $this->CompanyAddress->Country->find('list');
        $this->set(compact('countries'));
    }
    public function admin_delete($id = null)
    {
        if (is_null($id)) {
            throw new NotFoundException(__l('Invalid request'));
        }
        if ($this->CompanyAddress->delete($id)) {
            if ($this->RequestHandler->isAjax()) {
                echo 'deleted';
                exit;
            }
            $this->Session->setFlash(__l('Company Address deleted') , 'default', null, 'success');
            $this->redirect(array(
                'action' => 'index'
            ));
        } else {
            throw new NotFoundException(__l('Invalid request'));
        }
    }
}
?>