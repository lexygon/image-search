<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        return new Response("YAY !", 200);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function search(Request $request) : Response
    {
        $zipName = $this->get('image_service')->search($request);

        $rootDir = $this->get('kernel')->getProjectDir();
        $filePath = $rootDir."/public/zip/".$zipName;
        $response = new BinaryFileResponse($filePath);

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $zipName
        ));

        return $response;
    }
}