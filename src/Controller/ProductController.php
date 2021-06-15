<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CartItemType;
use App\Form\ProductType;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\CartManagment;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    
    // Product details
    #[Route('/product/{id}-{name}', name: 'product', requirements:[ 'id' => '\d+'])]
    public function index(Product $product, Request $request, CartManagment $cartMngmt): Response
    {
        $form = $this->createForm(CartItemType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $item = $form->getData();
            $cartMngmt->addItemToCart($item, $product);
            return $this->redirectToRoute('cart');
        }

        return $this->render('product/product.html.twig', [
            'prod' => $product,
            'form' => $form->createView()
        ]);
    }

    // Adding new product
    #[Route('/product/add', name:"add_prod")]
    public function add(Request $request, SluggerInterface $slugger ): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->get('image')->getData();

            if($image)
            {
                $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($name);
                $new = $safe.'_'.uniqid().'.'.$image->guessExtension();

                try
                {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $new
                    );
                }
                catch(FileException $e)
                {
                    throw new NotFoundHttpException();
                }

                $product->setImageFileName($new);

            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product',['id' => $product->getId(), 'name' => $product->getName()]);
        }
        
        return $this->render('product/newProduct.html.twig',[
            'form' => $form->createView(),
            'product' => $product
        ]);

    }


    // Editing product
    #[Route('/product/edit/{id}', name:"edit_prod", requirements:[ 'id' => '\d+'])]
    public function edit(int $id, Request $request, SluggerInterface $slugger ): Response
    {
       $em = $this->getDoctrine()->getManager();
       $product = $em->getRepository(Product::class)->find($id);

        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->get('image')->getData();

            if($image)
            {
                $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($name);
                $new = $safe.'_'.uniqid().'.'.$image->guessExtension();

                try
                {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $new
                    );
                }
                catch(FileException $e)
                {
                    throw new NotFoundHttpException();
                }

                $product->setImageFileName($new);

            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product',['id' => $product->getId(),'name' => $product->getName()]);
        }
        
        return $this->render('product/editProduct.html.twig',[
            'form' => $form->createView(),
            'product' => $product
        ]);

    }

    // Delete product
    #[Route("/product/delete/{id}", name: "delete_prod", requirements: ['id' => '\d+'])]
    public function delete(Request $request, Product $product, Filesystem $fs, LoggerInterface $log)
    {

    

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('token'))) {
            $em= $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();

           $path = $this->getParameter('images_directory').'/'.$product->getImageFileName();
           if($fs->exists($path))
           {
               $log->info("ZNAJDUJE PLIK");
               $fs->remove($path);
           }
            return $this->redirectToRoute('category', ['id' => $product->getCategory()->getId(), 'catname' => $product->getCategory()->getName()]);
        }

        return $this->render('product/deleteProduct.html.twig', [
            'product' => $product
        ]);

    }


    // Show products from specific category
    #[Route("/cat/{id}-{catname}/page/{!pg?1}-{ord?}",name: "category", requirements: [ 'id' => '\d+', 'pg' => '\d+', 'ord' => '[a-z]{2}'])]
    public function category(int $id, int $pg, $ord, Request $request,  ProductRepository $prodRepository, CategoryRepository $catRepository, LoggerInterface $logger): Response
    {
        
        $cat = $catRepository->find($id);

        if(!$cat)
           throw new NotFoundHttpException();

        if($pg < 1)
            throw new NotFoundHttpException();

        // Filtering
        $form = $this->createForm(SearchType::class, options:[
            'method' => 'GET'
        ]);
        $form->handleRequest($request);
        $filter = "";
        if($form->isSubmitted() && $form->isValid())
        {
            $filter = $form->get('searchString')->getData();
            $pg = 1; // When filtering always start on page 1
        }

        //$logger->info("FILTER ".$filter);

        // Pagination
        $len = $this->getParameter('product_page_count');
        $productsPag = $prodRepository->findByCategoryId($id, (($pg-1) * $len), $len, $ord, $filter);
        $maxpages = ceil($productsPag->count() / $len);

        //$logger->info("MAXPAGES ".$productsPag->count());

        if($maxpages < $pg && $maxpages > 0)
            throw new NotFoundHttpException();

        return $this->render('product/productCat.html.twig',[
            'products' => $productsPag,
            'cat' => $cat,
            'page' => $pg,
            'maxpages' => $maxpages,
            'order' => $ord,
            'form' => $form->createView()
        ]);
    }

}
