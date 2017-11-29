<?php

namespace App\Services;

use PubNub\PubNub;
use PubNub\Enums\PNStatusCategory;
use PubNub\Callbacks\SubscribeCallback;
use PubNub\PNConfiguration;
use PubNub\Exceptions\PubNubUnsubscribeException;	
use Config;
 
class CloudFoundryStatsService extends SubscribeCallback {
	private $result = '';

	public function getResult() {
		return $this->result;
	}

	public function status($pubnub, $status) {
        if ($status->getCategory() === PNStatusCategory::PNUnexpectedDisconnectCategory) {
            // This event happens when radio / connectivity is lost
        } else if ($status->getCategory() === PNStatusCategory::PNConnectedCategory) {
            // Connect event. You can do stuff like publish, and know you'll get it
            // Or just use the connected event to confirm you are subscribed for
            // UI / internal notifications, etc
        } else if ($status->getCategory() === PNStatusCategory::PNDecryptionErrorCategory) {
            // Handle message decryption error. Probably client configured to
            // encrypt messages and on live data feed it received plain text.
        }
    }
 
    public function message($pubnub, $message) {
    	$this->result = [
            'received_at' => time(),
            'message' => $message->getMessage(),
        ];

    	throw new PubNubUnsubscribeException();
    }
 
    public function presence($pubnub, $presence) {
        // handle incoming presence data
    }

    public function stats() {
         
		$pnconf = new PNConfiguration();
		$pubnub = new PubNub($pnconf);
		$pnconf->setSubscribeKey(Config::get('dashboard.pubnub.subscribe_key'));
		$pnconf->setPublishKey(Config::get('dashboard.pubnub.publish_key'));
		 
		$pubnub->addListener($this);
		 
		// Subscribe to a channel, this is not async.
		$pubnub->subscribe()
		    ->channels("cloud_foundry_8")
		    ->execute();

		return $this->result;
	}
}
