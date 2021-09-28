<?php

namespace ex\exporter\gmb;

class upload_media
{

    use \ex\debug;

    private $media;

    private $client;

    private $service;



    public function set_options($options)
    {
        $this->options = $options;
    }

    public function set_client($client)
    {
        $this->client = $client;
    }

    public function run()
    {
        if ( !$this->are_options_valid() ) { return false; };
        
        $this->upload_image_via_curl();
        $this->process_result();
        return $this->media;
    }


    private function are_options_valid()
    {
        // no value.
        if (empty($this->options["media_source_url"])){ return false;}

        return true;
    }


    /**
     * upload_curl function
     * 
     * Use a REAL Image URL that's accessible from the web
     * and GMB can access.
     * Won't work with internal vagrant images.
     *
     * @return void
     */
    private function upload_image_via_curl()
    {

        try {

            $token = $this->client->getAccessToken();
            
            $data = array(
                'mediaFormat' => $this->options["media_type"],
                'locationAssociation' => array(
                    "category" => $this->options["media_category"]
                ),
                'sourceUrl' => $this->options["media_source_url"]
            );
            $json = json_encode($data);
            $url = 'https://mybusiness.googleapis.com/v4/'.$this->options["locationid"].'/media';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json',
                'Authorization: Bearer ' . $token['access_token'] 
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $this->upload = curl_exec($ch);
            curl_close ($ch);

            $this->debug('export', $this->upload);

        } 
        catch (\Google_Service_Exception $e) {
            $message = 'Caught \Google_Service_Exception: ' .  print_r($e->getMessage(), true) . "\n";
            $this->debug('export', $message);
            $this->results = false;
        }
        catch (\Google_Exception $e) {
            $message = 'Caught \Google_Exception: ' .  print_r($e->getMessage(), true) . "\n";
            $this->debug('export', $message);
            $this->results = false;
        }
        catch (\Exception $e) {
            $message = 'Caught \Exception: ' .  print_r($e->getMessage(), true) . "\n";
            $this->debug('export', $message);
            $this->results = false;
        }

    }



    private function process_result()
    {
        $this->upload = json_decode($this->upload);
        $this->media = new \Google_Service_MyBusiness_MediaItem();
        $this->media->setSourceUrl($this->upload->googleUrl);
        $this->media->setMediaFormat($this->upload->mediaFormat);
    }



}