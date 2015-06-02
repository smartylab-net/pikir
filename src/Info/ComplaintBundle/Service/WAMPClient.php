<?php

namespace Info\ComplaintBundle\Service;

use Thruway\ClientSession;
use Thruway\Logging\Logger;
use Thruway\Peer\Client;
use Thruway\Transport\AbstractTransport;
use Thruway\Transport\PawlTransportProvider;

class WAMPClient {

    private $realm;
    private $url;
    private $prefix;

    public function __construct($logger, $prefix, $realm, $url) {
        Logger::set($logger);
        $this->realm = $realm;
        $this->url = $url;
        $this->prefix = $prefix;
    }

    /**
     * @param $topicName
     * @param $arguments
     * @return bool
     * @throws \Exception
     */
    public function publish($topicName, $arguments)
    {
        $result = false;
        $client = new Client($this->realm);
        $client->addTransportProvider(new PawlTransportProvider($this->url));
        $client->setAttemptRetry(false);

        $client->on('open', function (ClientSession $session, AbstractTransport $transport) use ($topicName, $arguments, &$result) {

            $session->publish($topicName, $arguments, [], ["acknowledge" => true])->then(
                function () use ($transport, $session, &$result) {
                    $result = true;
                    $session->close();
                }, function ($error) use ($transport, $session, &$result) {
                $result = false;
                $session->close();
            }
            );
        });

        $client->start();
        return $result;
    }
}