<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IndexController extends AbstractController
{
    
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/', name: 'index')]
    public function fetchProducts()
    {
        $response = $this->client->request(
            'GET',
            'http://api:80/api/products.json'
        );
        $content = $response->getContent();
        // $content = '{"id":1, "name":"Produkt 1", ...}'
        $content = $response->toArray();
        // $content = ['id' => 2, 'name' => 'Produkt 2', ...]

        return $this->render('/index.html.twig', [
            'content' => $content,
        ]);
    }

    #[Route('/product/{id}', name: 'single_product')]
    public function singleProduct($id) 
    {
        $singleResponse = $this->client->request(
            'GET',
            'http://api:80/api/products/'.$id.'.json'
        );
        $singleContent = $singleResponse->getContent();
        // $singleContent = '{"id":1, "name":"Produkt 1", ...}'

        $singleContent = $singleResponse->toArray();
        // $singleContent = ['id' => 2, 'name' => 'Produkt 2', ...]

        return $this->render('product/product.html.twig', [
            'singleContent' => $singleContent,
        ]);
    }

    #[Route('/product/edit/{id}', name: 'edit_single_product')]
    public function editProduct($id) {

        
        return $this->render('product/edit_product.html.twig', [
            
        ]);
    }
}
