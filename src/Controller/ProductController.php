<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    

    #[Route('/product/{id}', name: 'product', requirements:[ 'id' => '\d+'])]
    public function index(int $id, ProductRepository $prodRepository): Response
    {
        $product = $prodRepository->find($id);
        return $this->render('product/product.html.twig', [
            'prod' => $product,
        ]);
    }


    #[Route("/cat/{id}",name: "category", requirements: [ 'id' => '\d+'])]
    public function category(int $id, ProductRepository $prodRepository, CategoryRepository $catRepository): Response
    {
        $products = $prodRepository->findByCategoryId($id);
        $cat = $catRepository->find($id);
        
        if(!$products)
           throw new NotFoundHttpException();

        return $this->render('product/productCat.html.twig',[
            'products' => $products,
            'cname' => $cat->getName()
        ]);
    }



    public function getCategories(CategoryRepository $catRepository): Response
    {
        $cats = $catRepository->findAll();
        $response =  $this->render('layout/_cats.html.twig', [
            'categories' => $cats]);

        $response->setPublic();
        $response->setMaxAge(600);

        return $response;
    }
}
