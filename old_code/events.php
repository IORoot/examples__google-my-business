<?php

namespace ex\exporter\gmb;

class events
{

    use \ex\debug;

    private $options;

    private $data;

    private $results;

    private $client;

    private $service;

    public function set_options($options)
    {
        $this->options = $options;
    }

    public function set_data($data)
    {
        $this->data = $data;
    }

    public function set_client($client)
    {
        $this->client = $client;
    }

    /**
     * Get a new gmb Object.
     * 
     * Services are called through queries to service specific objects. 
     * These are created by constructing the service object, and passing an 
     * instance of Google_Client to it. Google_Client contains the IO, authentication 
     * and other classes required by the service to function, and the service informs 
     * the client which scopes it uses to provide a default when authenticating a user.
     */
    public function run()
    {
        if ($this->isDisabled()){ return; }

        $this->parse_moustaches();
        $this->parse_datetimes();

        $this->build_startDate();
        $this->build_startTime();
        $this->build_endDate();
        $this->build_endTime();
        $this->build_schedule();
        $this->build_event();

        $this->build_mediaItem();

        $this->build_CTA();

        $this->build_localPost();

        $this->create_localPost();
    }
    

    
    public function get_result()
    {
        return $this->results;
    }


    //  ┌─────────────────────────────────────────────────────────────────────────┐
    //  │                                                                         │░
    //  │                                                                         │░
    //  │                                 PRIVATE                                 │░
    //  │                                                                         │░
    //  │                                                                         │░
    //  └─────────────────────────────────────────────────────────────────────────┘░
    //   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░

    /**
     * parse_moustaches
     * 
     * Substitute any moustaches for real values.
     * Split into two parts {{post_type:field}}
     * Post_type = post, meta, image
     * Field = Any found field.
     *
     * @return void
     */
    private function parse_moustaches()
    {
        foreach($this->data as $posttype => $post)
        {
            $parse = new \ex\parse\replace_moustaches_in_array($post, $this->options);
            $this->options = $parse->get_results();
        }
    }

    private function parse_datetimes()
    {
        $this->start_datetime = explode(',', $this->options['start_datetime']);
        $this->end_datetime = explode(',', $this->options['end_datetime']);
    }

    /**
     * build_event
     * 
     * Generate a Event object.
     *
     * @return void
     */
    private function build_event()
    {
        $this->event = new \Google_Service_MyBusiness_LocalPostEvent();
        $this->event->setTitle($this->options['title']);
        $this->event->setSchedule($this->schedule);
    }

    private function build_schedule()
    {
        $this->schedule = new \Google_Service_MyBusiness_TimeInterval();
        $this->schedule->setStartDate($this->startdate);
        $this->schedule->setStartTime($this->starttime);
        $this->schedule->setEndDate($this->enddate);
        $this->schedule->setEndTime($this->endtime);
    }

    private function build_startDate()
    {
        $this->startdate = new \Google_Service_MyBusiness_Date();
        $this->startdate->setYear((int)$this->start_datetime[0]);
        $this->startdate->setMonth((int)$this->start_datetime[1]);
        $this->startdate->setDay((int)$this->start_datetime[2]);
    }

    private function build_startTime()
    {
        $this->starttime = new \Google_Service_MyBusiness_TimeOfDay();
        $this->starttime->setHours((int)$this->start_datetime[3]);
        $this->starttime->setMinutes((int)$this->start_datetime[4]);
        $this->starttime->setSeconds((int)$this->start_datetime[5]);
        $this->starttime->setNanos(0);
    }

    private function build_endDate()
    {
        $this->enddate = new \Google_Service_MyBusiness_Date();
        $this->enddate->setYear((int)$this->end_datetime[0]);
        $this->enddate->setMonth((int)$this->end_datetime[1]);
        $this->enddate->setDay((int)$this->end_datetime[2]);
    }

    private function build_endTime()
    {
        $this->endtime = new \Google_Service_MyBusiness_TimeOfDay();
        $this->endtime->setHours((int)$this->end_datetime[3]);
        $this->endtime->setMinutes((int)$this->end_datetime[4]);
        $this->endtime->setSeconds((int)$this->end_datetime[5]);
        $this->endtime->setNanos(0);
    }

    /**
     * build_CTA
     * 
     * Generate a CTA object.
     *
     * @return void
     */
    private function build_CTA()
    {
        $this->CTA = new \Google_Service_MyBusiness_CallToAction();
        $this->CTA->setActionType($this->options['button_action_type']);
        $this->CTA->setUrl($this->options['button_url']);
    }

    /**
     * build_mediaItem
     * 
     * Generate a media object
     *
     * @return void
     */
    private function build_mediaItem()
    {
        $media = new upload_media();
        $media->set_options($this->options);
        $media->set_client($this->client);
        $this->media = $media->run();
    }

    /**
     * build_localPost
     * 
     * Generate a localPost object using the
     * CTA and mediaItem.
     *
     * @return void
     */
    private function build_localPost()
    {
        $this->localPost = new \Google_Service_MyBusiness_LocalPost();
        $this->localPost->setSummary(substr($this->options['summary'],0,1500));
        $this->localPost->setLanguageCode('en-GB');
        $this->localPost->setEvent($this->event);
        $this->localPost->setCallToAction($this->CTA);
        $this->localPost->setMedia($this->media);
    }

    /**
     * Each API provides resources and methods, usually in a chain. These can be 
     * accessed from the service object in the form $service->resource->method(args). 
     * Most method require some arguments, then accept a final parameter of an array 
     * containing optional parameters.
     */
    private function create_localPost()
    {
        $this->service = new \Google_Service_MyBusiness($this->client);

        try {
            $this->results = $this->service->accounts_locations_localPosts->create(
                $this->options['locationid'],
                $this->localPost
            );

            $this->debug('export', $this->results);
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

    private function isDisabled()
    {
        if ($this->options['enabled'] == false)
        {
            return true;
        }
        return false;
    }

}