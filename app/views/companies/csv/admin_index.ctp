<?php
$i = 0;
do {
    $company->paginate = array(
        'conditions' => $conditions,
        'offset' => $i,
		'order' => array(
			'Company.id' => 'desc'
		) ,
        'recursive' => 1
    );
    if(!empty($q)){
        $company->paginate['search'] = $q;
    }
    $Companies = $company->paginate();
    if (!empty($Companies)) {
        $data = array();
        foreach($Companies as $Company) {
			$address = !empty($Company['City']['name']) ? $Company['City']['name'].', ' : '';
			$address.= !empty($Company['State']['name']) ? $Company['State']['name'].', ' : '';
			$address.= !empty($Company['Country']['name']) ? $Company['Country']['name'].', ' : '';
	        $data[]['Company'] = array(
				__l('Name') => $Company['Company']['name'],
				__l('Address') => !empty($address) ? $address: '',
				__l('Email') => $Company['User']['email'],
				__l('User') => $Company['User']['email'],
				__l('URL') => $Company['Company']['url'],
				__l('Profile Enabled') => $this->Html->cBool($Company['Company']['is_company_profile_enabled'], false),
				__l('Available Balance Amount') => $Company['User']['available_balance_amount'],
          	);
        }
        if (!$i) {
            $this->Csv->addGrid($data);
        } else {
            $this->Csv->addGrid($data, false);
        }
    }
    $i+= 20;
}
while (!empty($Companies));
echo $this->Csv->render(true);
?>