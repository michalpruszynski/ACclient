<?php

namespace App\Controller;

use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
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
    public function editProduct(int $id, Request $request) {

        // getting data to fill form to know which one currently you're updading
        $preClient = new CurlHttpClient();
        $preFetch = $preClient->request('GET', 'http://api/api/products/'.$id.'.json');
        $preContent = $preFetch->toArray();

        $form = $this->createForm(ProductType::class, $preContent);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $requestData = $form->getData();

            $requestJson = json_encode($requestData, JSON_THROW_ON_ERROR);

            $this->client->request('PUT', 'http://api/api/products/'.$id.'', [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'body' => $requestJson
            ]);

            $this->addFlash('success', 'Edycja przebiegła pomyślnie');
            return $this->redirectToRoute('index');
        }
        
        return $this->render('product/edit_product.html.twig', [
            'form' => $form->createView(),            
        ]);
    }

    #[Route('/add/', name: 'add_single_product')]
    public function addNewProduct(Request $request) {

        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $requestData = $form->getData();

            $requestJson = json_encode($requestData, JSON_THROW_ON_ERROR);

            $this->client->request('POST', 'http://api/api/products', [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'body' => $requestJson
            ]);

            $this->addFlash('success', 'Produkt dodany pomyślnie');
            return $this->redirectToRoute('index');
        }
        
        return $this->render('product/edit_product.html.twig', [
            'form' => $form->createView(),            
        ]);
    }

    #[Route('/product/delete/{id}', name: 'delete_product')]
    public function deleteProduct(int $id, Request $request) {

        $this->client->request('DELETE', 'http://api/api/products/'.$id.'', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'body' => [
                'id' => $id
            ]
        ]);

        $this->addFlash('success', 'Produkt usunięty pomyślnie');
        return $this->redirectToRoute('index');
    }
}
