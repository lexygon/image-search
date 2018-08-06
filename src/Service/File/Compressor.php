<?php

namespace App\Service\File;

use ZipArchive;

/**
 * Class Compressor
 * @package App\Service\File
 */
class Compressor extends BaseFileService
{

    /**
     * @param array $files
     * @param null $augmentedFiles
     * @return string
     */
    public function zipFiles(array $files, $augmentedFiles = null)
    {
        $zip = new ZipArchive();
        $zipName = $this->generateRandomFileName('zip');

        $filePath = $this->ROOT_DIR."/public/zip/".$zipName;

        $zip->open($filePath, ZipArchive::CREATE);

        if ($augmentedFiles) {
            foreach ($augmentedFiles as $file) {
                $fileName = $this->prepareInnerZipDirectory($file, true);
                $zip->addFile($file, $fileName);
            }
        }

        foreach ($files as $file) {
            $fileName = $this->prepareInnerZipDirectory($file);
            $zip->addFile($file, $fileName);
        }

        $zip->close();

        $this->deleteTempFiles($files, $augmentedFiles);

        return $zipName;
    }

    /**
     * @param string $fileName
     * @param bool $augmented
     * @return string
     */
    private function prepareInnerZipDirectory(string $fileName, bool $augmented=false) : string
    {
        $splitted = explode('/', $fileName);
        $trueName = end($splitted);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($augmented) {
            return "augmented/{$trueName}.{$extension}";
        } else {
            return "{$trueName}";
        }
    }

    /**
     * @param array $files
     * @param null $augmentedFiles
     */
    private function deleteTempFiles(array $files, $augmentedFiles = null): void
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }

        if ($augmentedFiles) {
            foreach ($augmentedFiles as $file) {
                $this->deleteFile($file);
            }
        }
    }

    /**
     * @param string $fileName
     * @return bool
     */
    private function deleteFile(string $fileName) : bool
    {
        if (file_exists($fileName)) {
            unlink($fileName);
            return true;
        } else {
            return false;
        }
    }

}