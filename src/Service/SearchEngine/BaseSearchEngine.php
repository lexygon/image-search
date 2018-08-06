<?php

namespace App\Service\SearchEngine;

use App\Service\File\Downloader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseSearchEngine
 * @package App\Service\SearchEngine
 */
abstract class BaseSearchEngine implements Searchable
{
    protected $container;
    protected $downloader;

    /**
     * BaseSearchEngine constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->downloader = $this->container->get('file_downloader');
    }

    /**
     * @param array $links
     * @return array
     * @throws \Exception
     */
    public function getImages(array $links): array
    {
        $files = [];
        foreach ($links as $link) {
            $files[] = $this->downloader->download($link);
        }
        return $files;
    }

}