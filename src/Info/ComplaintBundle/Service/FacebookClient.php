<?php

namespace Info\ComplaintBundle\Service;

use Facebook\Exceptions\FacebookAuthorizationException;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookResponse;
use Info\ComplaintBundle\Entity\Complaint;
use Monolog\Logger;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class FacebookClient
{

    /** @var RouterInterface */
    private $router;
    /** @var Logger */
    private $logger;
    private $appId;
    private $appSecret;
    private $accessToken;
    private $pageId;
    private $appsecretProof;
    /** @var \Facebook\Facebook */
    private $fb;
    private $post_enabled;

    public function __construct($router, $logger, $appId, $appSecret, $accessToken, $pageId)
    {

        $this->router = $router;
        $this->logger = $logger;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->pageId = $pageId;
        $this->appsecretProof = hash_hmac('sha256', $this->accessToken, $this->appSecret);

        $this->fb = new Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'default_graph_version' => 'v2.0',
            'default_access_token' => $this->accessToken,
            'cookie' => false,
        ]);
    }

    public function post(Complaint $complaint)
    {
        if (!$this->post_enabled) {
            return false;
        }

        $link = $this->router->generate('info_complaint_complaint', array('id' => $complaint->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

        $linkData = [
            'link' => $link,
            'message' => sprintf("%s > %s \n%s", $complaint->getAuthor(), $complaint->getCompany()->getName(), $complaint->getText()),
            'appsecret_proof' => $this->appsecretProof,
        ];

        try {
            $pageAccessToken = $this->getPageAccessToken();
            $response = $this->fb->post('/' . $this->pageId . '/feed', $linkData, $pageAccessToken);
            $graphNode = $response->getGraphNode();
            $this->logger->debug('Posted with id: ' . $graphNode['id']);
            return true;
        } catch (FacebookResponseException $e) {
            $this->logger->error('Graph returned an error: ' . $e->getMessage());
        } catch (FacebookAuthorizationException $e) {
            $this->logger->error('Facebook authentification returned an error: ' . $e->getMessage());
        } catch (FacebookSDKException $e) {
            $this->logger->error('Facebook SDK returned an error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * @throws \Facebook\Exceptions\FacebookAuthorizationException
     * @return mixed
     */
    private function getPageAccessToken()
    {
        /** @var FacebookResponse $result */
        $response = $this->fb->get('/me/accounts');
        $result = $response->getDecodedBody();
        foreach ($result["data"] as $page) {
            if ($page["id"] == $this->pageId) {
                return $page["access_token"];
            }
        }
        throw new FacebookAuthorizationException();
    }

    /**
     * @return mixed
     */
    public function getPostEnabled()
    {
        return $this->post_enabled;
    }

    /**
     * @param mixed $post_enabled
     */
    public function setPostEnabled($post_enabled)
    {
        $this->post_enabled = $post_enabled;
    }
} 