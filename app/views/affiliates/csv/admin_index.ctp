<?php

$i = 0;
do {
    $affiliate->paginate = array(
        'conditions' => $conditions,
        'offset' => $i,
		'order' => array(
			'Affiliate.id' => 'desc'
		) ,
        'recursive' => 1
    );

 /*   if(!empty($q)){
        $affiliate->paginate['search'] = $q;
    } */
    $Affiliates = $affiliate->paginate();
    if (!empty($Affiliates)) {
        $data = array();
        if (!empty($Affiliates)) {
            foreach($Affiliates as $affiliate1) {
				//print_r($affiliate);
        	 
				if($affiliate1['Affiliate']['class'] == 'User'){
					$username=$this->Html->cText($affiliate1['User']['username'], false);
				} elseif(!empty($affiliate1['DealUser']['Deal']) && !empty($affiliate1['DealUser']['User'])){
					$username=$this->Html->cText($affiliate1['DealUser']['Deal']['name'], false);
					$username.='('.$this->Html->cText($affiliate1['DealUser']['User']['username'], false).')';
				} 
				else{
					$username='';
				}
                $data[]['Affiliate'] = array(
                    __l('Created') => $this->Html->cDateTimeHighlight($affiliate1['Affiliate']['created'], false),
                    __l('Affiliate User') => $affiliate1['AffiliateUser']['username'],
					__l('User').'/'. __l('Deal') => $username,                    
					__l('Type')=> $this->Html->cText($affiliate1['AffiliateType']['name'], false),
                    __l('Status')=> $this->Html->cText($affiliate1['AffiliateStatus']['name'], false),
                    __l('Commission')=> $this->Html->cFloat($affiliate1['Affiliate']['commission_amount'], false),
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
}
while (!empty($Affiliates));
echo $this->Csv->render(true);
?>