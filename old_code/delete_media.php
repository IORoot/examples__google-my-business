<?php

class delete_media
{

    private $client;

    private $sourceURL;

    private $gmb_accounts;
    private $gmb_locations;
    private $gmb_media;

    private $results;

    public function set_client($client)
    {
        $this->client = $client;
    }

    public function set_sourceURL($sourceURL)
    {
        $this->sourceURL = $sourceURL;
    }

    public function run()
    {
        if (!$this->is_valid()){return false;};
        $this->remove_via_gmb_service();
    }

    public function get_results()
    {
        return $this->results;
    }
    
    private function is_valid()
    {
        if (empty($this->client)){ return false; }
        return true;
    }


    private function remove_via_gmb_service()
    {
        $this->get_gmb_service();
        $this->get_gmb_accounts();
        $this->get_gmb_locations();
        $this->get_gmb_media();
        $this->delete_gmb_latest_media();
        
    }

    private function get_gmb_service()
    {
        $this->GMB = new \Google_Service_MyBusiness($this->client);
    }

    private function get_gmb_accounts()
    {
        $this->gmb_accounts = $this->GMB->accounts->listAccounts();
    }

    private function get_gmb_locations()
    {
        $this->gmb_locations = $this->GMB->accounts_locations->listAccountsLocations($this->gmb_accounts[0]->getName());
    }

    private function get_gmb_media()
    {
        $this->gmb_media = $this->GMB->accounts_locations_media->listAccountsLocationsMedia($this->gmb_locations[0]->getName());
    }

    private function delete_gmb_latest_media()
    {
        $all_items = $this->gmb_media->getMediaItems();

        $newest_item = $all_items[0]->getName();
        $sourceURL = $all_items[0]->getSourceUrl();

        if ($sourceURL == $this->sourceURL)
        {
            $this->results = $this->GMB->accounts_locations_media->delete($newest_item);
        }
        
    }

}