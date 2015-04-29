<?php

/**
 * This file contains the class ApiConnector
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

use Birke\Mediawiki\Api\MediawikiApiClient;
use Birke\Mediawiki\Api\Session;

use \Monolog\Logger;

/**
 * Wrapper around common MediaWiki API client to do specific functions
 *
 * @author birkeg
 */
class ApiConnector
{
    
    /**
     *
     * @var \Birke\Mediawiki\Api\Session
     */
    protected $session;
    
    /**
     *
     * @var MediawikiApiClient
     */
    protected $client;
    
    /**
     *
     * @var \Monolog\Logger
     */
    protected $logger;
    
    /**
     * How many list items can be retrieved from the API with one "query" action
     * @var int 
     */
    public $apiLimit = 500;
    
    /**
     * Cache for the last stabble revision id for each page
     * @var array
     */
    protected $stableRevIdsForTitle;


    public function __construct(Session $session, Logger $logger)
    {
        $this->session = $session;
        $this->client = $session->getClient();
        $this->logger = $logger;
    }
    
    /**
     * Get HTML 
     * @param string $title
     * @param boolean $latestStableVersion
     * @return string
     */
    public function downloadPageText($title, $latestStableVersion = true)
    {
        $query = array (
            'action' => 'query',
            'prop' => 'revisions',
            'rvprop' => 'content',
            'format' => 'json'
        );

        // if we find a stable id , let's use it
        $normalizedTitle = str_replace(' ', '_', $title);
        $latestStableRevId = $latestStableVersion ? $this->getLastestStableRevId($normalizedTitle) : 0;
        if ($latestStableRevId) {
            $query ['revids'] = (string) $latestStableRevId;
        } else {
            $query ['titles'] = $title;
        }

        $data = $this->getDataFromClient($query);
        // convert pageid => page date to array
        $pages = array_values($data['query']['pages']);
        // return full text (*) of first revision of first page
        if (!empty($pages[0]['revisions'][0]['*'])) {
            return $pages[0]['revisions'][0]['*'];
        } else {
            return "";
        }
    }
    
    /**
     * 
     * @param string $title
     * @return int
     */
    protected function getLastestStableRevId($title)
    {
        if (!is_null($this->stableRevIdsForTitle)) {
            $this->initializeRevisionIdCache();
        }
        return isset($this->stableRevIdsForTitle[$title]) ? $this->stableRevIdsForTitle[$title] : 0;
    }
    
    protected function initializeRevisionIdCache()
    {
        
        // i found no better way to get the stable version of a page;
        // 'stable' => '1' does not work in api query
        $this->stableRevIdsForTitle = array();
        $query = array(
            'action'=>'query',
            'list'=>'oldreviewedpages|reviewedpages',
            'format'=>'json',
            'rplimit' => $this->apiLimit,
            'orlimit' => $this->apiLimit
        );
        
        $data = $this->getDataFromClient($query);
        foreach ($data['query'] as $pagelist) {
            foreach ($pagelist as $page) {
                $normalizedTitle = str_replace(' ', '_', $page['title']);
                $this->stableRevIdsForTitle[$normalizedTitle] = $page['stable_revid'];
            }
        }
    }
    
    /**
     * Send a request to the API and get the data back as array
     * 
     * @param array $query
     * @return array
     */
    protected function getDataFromClient($query)
    {
        $url = $this->client->getBaseUrl();
        $req = $this->client->get($url, array(), array('query' => $query));
        $response = $req->send();
        return $response->json();
    }
    
    
    public function getSectionsForTitle($title)
    {
        $cmd = $this->client->getCommand('parse', array(
                'prop' => 'sections|displaytitle',
                'page' => $title
            ));
        $data = $cmd->execute();
        if (empty($data['parse'])) {
            // Probably page does not exist
            return array();
        }
        return $data['parse']['sections'];
    }
    
    public function editPage($title, $content, $summary)
    {
        $query = array(
            'action' => 'edit',
            'title' => $title,
            'text' => $content,
            'summary' => $summary,
            'bot' => "true",
            'format' => 'json',
            'token' => $this->session->getEditToken()
        );
        $url = $this->client->getBaseUrl();
        $req = $this->client->post($url, array(), $query);
        $response = $req->send();
        $data = $response->json();
        return $data['edit']['result'];
    }
    
    /**
     * Call Wiki API to get array of all titles in a category
     * 
     * @param string $categoryName Category name without "Category:" prefix
     * @param int $continue Contine API query at offset
     * @return array 
     */
    public function getAllPageTitlesInCategory($categoryName, $continue = null)
    {
        $query = array(
            'action' => 'query',
            'list' => 'categorymembers',
            'cmtitle' => 'Category:'.$categoryName,
            'cmtype' => 'page',
            'format' => 'json',
        );
        if ($continue) {
            $query['cmcontinue'] = $continue;
        }
        
        $url = $this->client->getBaseUrl();
        $req = $this->client->get($url, array(), array('query' => $query));
        $response = $req->send();
        $data = $response->json();
        
        $titles = array();
        foreach ($data['query']['categorymembers'] as $page) {
            $titles[] = $page['title'];
        }
        if (!empty($data['query-continue']['categorymembers'])) {
            $titles = array_merge($titles, $this->getAllPageTitlesInCategory(
                $categoryName,
                $data['query-continue']['categorymembers']['cmcontinue']
            ));
        }
        return $titles;
    }
}
