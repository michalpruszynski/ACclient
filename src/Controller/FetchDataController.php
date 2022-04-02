<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchDataController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/show/in-stock/', name: 'fetch_in_stock')]
    public function inStock()
    {
        $singleResponse = $this->client->request(
            'GET',
            'http://api:80/api/products.json?page=1&amount[gt]=0'
        );
        $singleContent = $singleResponse->getContent();
        $singleContent = $singleResponse->toArray();

        return $this->render('/index.html.twig', [
            'content' => $singleContent,
        ]);
        
    }

    #[Route('/show/out-of-stock/', name: 'fetch_out_of_stock')]
    public function outOfStock()
    {
        $singleResponse = $this->client->request(
            'GET',
            'http://api:80/api/products.json?page=1&amount[lt]=1'
        );
        $singleContent = $singleResponse->getContent();
        $singleContent = $singleResponse->toArray();

        return $this->render('/index.html.twig', [
            'content' => $singleContent,
        ]);
        
    }

    #[Route('/show/more-than/', name: 'fetch_more_than')]
    public function moreThan()
    {
        $singleResponse = $this->client->request(
            'GET',
            'http://api:80/api/products.json?page=1&amount[gte]=5'
        );
        $singleContent = $singleResponse->getContent();
        $singleContent = $singleResponse->toArray();

        return $this->render('/index.html.twig', [
            'content' => $singleContent,
        ]);
       
    }
}
