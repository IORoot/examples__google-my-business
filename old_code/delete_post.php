<?php

namespace ex\exporter\gmb;

class delete_post
{

    private $client; 
    private $summary;

    private $gmb_accounts;
    private $gmb_locations;
    private $gmb_media;

    private $results;


    public function set_client($client)
    {
        $this->client = $client;
    }

    public function set_summary($summary)
    {
        $this->summary = $summary;
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
        $this->get_gmb_localPosts();
        $this->delete_gmb_latest_localPost();
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

    private function get_gmb_localPosts()
    {
        $this->gmb_media = $this->GMB->accounts_locations_localPosts->listAccountsLocationsLocalPosts($this->gmb_locations[0]->getName());
    }

    private function delete_gmb_latest_localPost()
    {
        $all_posts = $this->gmb_media->getLocalPosts();

        $newest_post = $all_posts[0]->getName();
        $summary = $all_posts[0]->getSummary();

        if ($summary == $this->summary)
        {
            $this->results = $this->GMB->accounts_locations_localPosts->delete($newest_post);
        }
    }

}