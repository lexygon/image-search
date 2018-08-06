<?php

namespace App\Service\SearchEngine;

use odannyc\GoogleImageSearch\ImageSearch;

/**
 * Class GoogleImageSearchEngine
 * @package App\Service\SearchEngine
 */
class GoogleImageSearchEngine extends BaseSearchEngine
{

    /**
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $apiKey = $this->container->getParameter('GOOGLE_API_KEY');
        $cx = $this->container->getParameter('GOOGLE_CX');

        ImageSearch::config()->apiKey($apiKey);
        ImageSearch::config()->cx($cx);

        return ImageSearch::search($query);
    }

    /**
     * @param array $results
     * @return array
     */
    public function prepare(array $results): array
    {
        $items = $results['items'];
        return array_slice(array_column($items, 'link'), 0 , $this->container->getParameter('RESULT_LIMIT'));
    }
}