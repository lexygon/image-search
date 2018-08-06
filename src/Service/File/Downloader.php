<?php

namespace App\Service\File;

/**
 * Class Downloader
 * @package App\Service\File
 */
class Downloader extends BaseFileService
{

    /**
     * @param string $url
     * @return string
     * @throws \Exception
     */
    public function download(string $url) : string
    {
        $file = file_get_contents($url);

        $path = $this->generateOriginalFileName($file);

        file_put_contents($path, fopen($url, 'r'));

        return $path;
    }
}