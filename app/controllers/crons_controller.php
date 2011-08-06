<?php
class CronsController extends AppController
{
    public $name = 'Crons';
    public function update_deal()
    {
        $this->autoRender = false;
        App::import('Core', 'ComponentCollection');
        $collection = new ComponentCollection();
        App::import('Component', 'cron');
        $this->Cron = new CronComponent($collection);
        $this->Cron->update_deal();
    }
}
?>