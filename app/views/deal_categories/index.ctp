<?php
if (!empty($dealCategories)):
$i = 0;
foreach ($dealCategories as $dealCategory):
	$class='';
	$i++;
	if($i==7){
		$i = 1;
	}
	if(!empty($this->params['named']['category'])){
		if($this->params['named']['category']==$dealCategory['DealCategory']['id']){
			$class.='active';
		}
	}
?>
	<li>
		<?php  echo $this->Html->link(__l($dealCategory['DealCategory']['name']), array('controller' => 'deals', 'action' => 'index', 'category'=>$dealCategory['DealCategory']['id']), array('class'=>$class.' con'.$i, 'title' => __l($dealCategory['DealCategory']['name'])));?>
	</li>
<?php
    endforeach;
endif;
?>