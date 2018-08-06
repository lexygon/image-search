<?php

namespace App\Service\Image;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ImageService
 * @package App\Service\Image
 */
class ImageService
{
    private $container;
    private $compressor;

    /**
     * ImageService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->compressor = $this->container->get('file_compressor');
        $this->processor = $this->container->get('image_processor');
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function search(Request $request)
    {
        $input = $this->fetchRequest($request);

        $images = [];
        $augmentedImages = [];


        foreach ($input as $params) {
            $query = $params['query'];
            $engineName = $params['source'];

            $foundImages =  $this->doSearch($query, $engineName);

            if ($this->hasAugmentation($params)) {
                $augmentedImages = array_merge($augmentedImages, $this->augment($params, $foundImages));
            } else {
                $augmentedImages = null;
            }

            $images = array_merge($images, $foundImages);

        }

        return $this->compressor->zipFiles($images, $augmentedImages);
    }

    /**
     * @param $params
     * @param $images
     * @return array
     * @throws \Exception
     */
    private function augment($params, $images)
    {
        $augmentedImages = [];

        $opts = $params['opts'];

        if (in_array('zoom', $opts)) {
            $zoom = true;
        } else {
            $zoom = false;
        }

        if (in_array('rotate', $opts)) {
            $rotate = true;
        } else {
            $rotate = false;
        }

        foreach ($images as $image) {
            $image = $this->processor->openImage($image);
            if ($zoom) {
                $image = $this->processor->zoom($image);
            }

            if ($rotate) {
                $image = $this->processor->rotate($image);
            }

            $augmentedImages[] = $this->processor->saveImage($image, 'jpeg');
        }
        return $augmentedImages;
    }

    /**
     * @param $engineName
     * @return object
     */
    private function getEngine($engineName)
    {
        $trueName = "search.".$engineName;
        try {
            return $this->container->get($trueName);
        } catch (ServiceNotFoundException $e) {
            throw new BadRequestHttpException('Bad Source', null, 400);
        }
    }

    /**
     * @param $query
     * @param $engineName
     * @return mixed
     * @throws \Exception
     */
    private function doSearch($query, $engineName)
    {
        $engine = $this->getEngine($engineName);

        $data = $engine->search($query);
        $imageLinks = $engine->prepare($data);

        return $engine->getImages($imageLinks);
    }

    /**
     * @param array $params
     * @return bool
     */
    private function hasAugmentation(array $params) : bool
    {
        return array_key_exists('opts', $params);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    private function fetchRequest(Request $request)
    {
        if ($content = $request->getContent()) {
            $params = json_decode($content, true);
            return $params;
        } else {
            throw new BadRequestHttpException('Wrong Data', null, 400);
        }
    }
}