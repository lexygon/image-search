<?php

namespace App\Service\SearchEngine;


use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FlickrSearchEngine
 * @package App\Service\SearchEngine
 */
class FlickrSearchEngine extends BaseSearchEngine
{
    private $API_URI = "https://api.flickr.com/services/rest/";
    private $API_KEY;
    private $client;
    private $RESULT_LIMIT;

    /**
     * FlickrSearchEngine constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->API_KEY = $this->container->getParameter('FLICKR_API_KEY');
        $this->RESULT_LIMIT = intval($this->container->getParameter('RESULT_LIMIT'));
        $this->client = new Client();

    }

    /**
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $ids = $this->doSearch($query);
        return $this->fetchImages($ids);
    }

    /**
     * @param string $query
     * @return array
     */
    private function doSearch(string $query)
    {
        $response = $this->client->get($this->API_URI, [
            'query' => [
                'api_key' => $this->API_KEY,
                'format' => 'json',
                'text' => $query,
                "nojsoncallback" => "1",
                "method" => "flickr.photos.search"
            ]
        ]);

        $photos = json_decode($response->getBody(), true)['photos']['photo'];
        $ids = array_column($photos, 'id');
        return $ids;
    }

    /**
     * @param array $ids
     * @return array
     */
    private function fetchImages(array $ids)
    {
        $data = [];
        $counter = 0;
        foreach ($ids as $id) {
            if ($counter === $this->RESULT_LIMIT) {
                break;
            }
            $response = $this->client->get($this->API_URI, [
                'query' => [
                    'api_key' => $this->API_KEY,
                    'format' => 'json',
                    'photo_id' => $id,
                    "nojsoncallback" => "1",
                    "method" => "flickr.photos.getSizes"
                ]
            ]);
            $data[] = json_decode($response->getBody(), true)['sizes']['size'];
            $counter += 1;
        }
        return $data;
    }

    /**
     * @param array $results
     * @return array
     */
    public function prepare(array $results): array
    {
        $data = [];

        foreach ($results as $result) {
            foreach ($result as $size) {
                if (in_array('Large', $size)) {
                    $data[] = $size['source'];
                    break;
                } else if (in_array('Medium', $size)) {
                    $data[] = $size['source'];
                    break;
                } else if (in_array('Small', $size)) {
                    $data[] = $size['source'];
                    break;
                }
            }
        }
        return $data;
    }
}