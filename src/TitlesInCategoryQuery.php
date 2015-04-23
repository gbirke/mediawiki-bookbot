<?php

/**
 * This file contains the class TitlesInCategoryQuery
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Tocbot;

use Birke\Mediawiki\Api\MediawikiApiClient;

/**
 * Call Wiki API to get array of all titles in a category
 *
 * @author birkeg
 */
class TitlesInCategoryQuery {
    
    /**
     *
     * @var MediawikiApiClient
     */
    protected $client;
    
    function __construct(MediawikiApiClient $client) {
        $this->client = $client;
    }

    /**
     * Call Wiki API to get array of all titles in a category
     * 
     * @param string $categoryName Category name without "Category:" prefix
     * @param int $continue
     * @return array 
     */
    public function getAllTitles($categoryName, $continue = null) {
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
        foreach($data['query']['categorymembers'] as $page) {
            $titles[] = $page['title'];
        }
        if (!empty($data['query-continue']['categorymembers'])) {
            $titles = array_merge($titles, $this->getAllBookTitles($categoryName, 
                    $data['query-continue']['categorymembers']['cmcontinue']));
        }
        return $titles;
    }
}
