<?php

namespace App\Service\File;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ImageProcessor
 * @package App\Service\Image
 */
class ImageProcessor extends BaseFileService
{

    private $createFunctionMap = [
        'png' => 'imagecreatefrompng',
        'jpeg' => 'imagecreatefromjpeg',
        'jpg' => 'imagecreatefromjpeg',
        'bmp' => 'imagecreatefrombmp',
        'gif' => 'imagecreatefromgif',
        'plain' => 'imagecreatefromjpeg'
    ];

    private $saveFunctionMap = [
        'png' => "imagepng",
        'jpeg' => "imagejpeg",
        'jpg' => "imagejpeg",
        'plain' => "imagejpeg",
        'bmp' => "imagebmp",
        'gif' => "imagegif",

    ];

    /**
     * @param string $fileName
     * @return mixed
     */
    public function openImage(string $fileName)
    {

        $extension = $this->getFileExtension($fileName);

        try {
            $functionName = $this->createFunctionMap[$extension];
            return $functionName($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Bad Image Type', null, 400);
        }
    }

    /**
     * @param $image
     * @param $extension
     * @return string
     * @throws \Exception
     */
    public function saveImage($image, $extension) : string
    {
        if ($extension === 'plain') {
            $extension = 'jpeg';
        }

        $functionName = $this->saveFunctionMap[$extension];
        $newFileName = $this->generateAugmentedFileName(null, $extension);
        $functionName($image, $newFileName);
        return $newFileName;
    }

    /**
     * @param $image
     * @return resource
     */
    public function rotate($image)
    {
        return imagerotate($image, $this->randomAngle(), 0);
    }

    /**
     * @param $image
     * @return resource
     */
    public function zoom($image)
    {

        $zoom = rand(11, 30) / 10;  // random zoom ratio between 1.1x - 3x
        $width = imagesx($image);
        $height = imagesy($image);

        $new_width = $width / $zoom;
        $new_height = $height / $zoom;

        $srcx = $width - $new_width;
        $srcy = $height - $new_height;

        $newImage = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($newImage, $image, 0, 0, $srcx, $srcy, $width, $height, $width, $height);
        return $newImage;
    }

    /**
     * @return mixed
     */
    private function randomAngle()
    {
        $angles = [90, 180, 270];
        return $angles[array_rand($angles)];
    }

}