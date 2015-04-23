<?php

/**
 * This file contains the class TocGenerator
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Tocbot;

use Birke\Mediawiki\Api\MediawikiApiClient;
use Birke\Mediawiki\Api\Session;

use \Monolog\Logger;

/**
 * Wrapper around common MediaWiki API client to function
 *
 * @author birkeg
 */
class ApiConnector {
    
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
    
    function __construct(Session $session, Logger $logger) {
        $this->session = $session;
        $this->client = $session->getClient();
        $this->logger = $logger;
        
   
        // i found no better way to get the stable version of a page;
        // 'stable' => '1' does not work in api query
        $this->stableRevIdsForTitle = array();
        $query = array(
        	'action'=>'query',
        	'list'=>'reviewedpages|oldreviewedpages',
        	'format'=>'json'
        );
        $url = $this->client->getBaseUrl();
        $req = $this->client->get($url, array(), array('query' => $query));
        $response = $req->send();
        $data = $response->json();
       
        foreach ($data['query'] as $qs){
        	foreach($qs as $q){
        	$title = str_replace(' ', '_',$q['title']);// see $title2 below
        	$stable_revid = $q['stable_revid'];
        	$this->stableRevIdsForTitle[$title] = $stable_revid;
			}
		}
	}
	public function downloadPageText($title) {
		$query = array (
				'action' => 'query',
				'prop' => 'revisions',
				'rvprop' => 'content',
				'format' => 'json' 
		);
		
		
		// if we find a stable id , let's use it
		// ignore inconsistent usage of underscores/whitespaces
		$title2 = str_replace ( ' ', '_', $title );
		if (isset ( $this->stableRevIdsForTitle [$title2] )) {
			$query ['revids'] = '' . $this->stableRevIdsForTitle [$title2];
		} else {
			$query ['titles'] = $title;
		}
		
		$url = $this->client->getBaseUrl ();
		$req = $this->client->get ( $url, array (), array (
				'query' => $query 
		) );
		$response = $req->send ();
		$data = $response->json ();
		
        $pages = array_values($data['query']['pages']);
        return $pages[0]['revisions'][0]['*'];
    }
    
    public function getSectionsForTitle($title) {
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
    
    public function editPage($title, $content, $summary) {
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
    
}
