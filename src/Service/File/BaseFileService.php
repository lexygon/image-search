<?php

namespace App\Service\File;


use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseFileService
 * @package App\Service\File
 */
abstract class BaseFileService
{
    protected $container;
    protected $ROOT_DIR;

    /**
     * BaseFileService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->ROOT_DIR = $this->container->get('kernel')->getProjectDir();
    }

    /**
     * @param $file
     * @return string
     */
    public function getFileExtension($file): string
    {
        $finfo = new \finfo(FILEINFO_MIME);
        $mime = explode('; ', $finfo->buffer($file))[0];
        return explode('/', $mime)[1];
    }

    /**
     * @return string
     */
    protected function getOriginalFilesPath(): string
    {
        $path = $this->container->getParameter('ORIGINAL_FILE_PATH');
        return $this->getTempFilesDir().$path;
    }

    /**
     * @return string
     */
    protected function getAugmentedFilesPath(): string
    {
        $path = $this->container->getParameter('AUGMENTED_FILE_PATH');
        return $this->getTempFilesDir().$path;
    }

    /**
     * @return string
     */
    protected function getTempFilesDir(): string
    {
        $path = $this->container->getParameter('TEMP_DIR');
        return $this->ROOT_DIR."/public".$path;
    }

    /**
     * @param null $file
     * @param null $extension
     * @return string
     * @throws \Exception
     */
    protected function generateOriginalFileName($file = null, $extension = null): string
    {
        if (!is_null($file) && is_null($extension)) {
            $extension = $this->getFileExtension($file);
        } else if (is_null($file) && is_null($extension)) {
            throw new \Exception("You have to provide either 'file' or 'extension' parameters.");
        }

        $dir = $this->getOriginalFilesPath();
        return $this->generateFilePath($dir, $extension);
    }

    /**
     * @param null $file
     * @param null $extension
     * @return string
     * @throws \Exception
     */
    protected function generateAugmentedFileName($file = null, $extension = null): string
    {
        if (!is_null($file) && is_null($extension)) {
            $extension = $this->getFileExtension($file);
        } else if (is_null($file) && is_null($extension)) {
            throw new \Exception("You have to provide either 'file' or 'extension' parameters.");
        }

        $dir = $this->getAugmentedFilesPath();
        return $this->generateFilePath($dir, $extension);
    }

    /**
     * @param $dir
     * @param $extension
     * @return string
     */
    protected function generateFilePath($dir, $extension): string
    {
        $fileName = $this->generateRandomFileName();

        // $tempDir = $this->getTempFilesDir();

        return "{$dir}{$fileName}.{$extension}";
    }

    /**
     * @param null $extension
     * @return string
     */
    protected function generateRandomFileName($extension = null): string
    {
        $md5 = md5(rand(0,100000)+strtotime(time()));

        if ($extension) {
            return $md5 . ".{$extension}";
        } else {
            return $md5;
        }
    }

}