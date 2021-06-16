<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\DeleteCategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{

    
    #[Route('/cat/add', name: 'addcat')]
    public function add(Request $request): Response
    {
        $cat = new Category();

        $form = $this->createFormBuilder($cat)
                ->add('name',TextType::class)
                ->add('description', TextareaType::class, ['required' => false])
                ->add('add', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $newCat = $form->getData();
            $man = $this->getDoctrine()->getManager();

            $man->persist($newCat);
            $man->flush();

            return $this->redirectToRoute("home");

        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/cat/edit/{id}', name: 'editcat', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $man = $this->getDoctrine()->getManager();
        $cat = $man->getRepository(Category::class)->find($id);
        $cname = $cat->getName();

        if(!$cat)
        throw new NotFoundHttpException();

        $form = $this->createFormBuilder($cat)
                ->add('name',TextType::class)
                ->add('description', TextareaType::class)
                ->add('edit', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $newCat = $form->getData();

            $cat->setName($newCat->getName());
            $cat->setDescription($newCat->getDescription());
            $man->flush();

            return $this->redirectToRoute("home");

        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'cname' => $cname
        ]);
    }


    #[Route('/cat/delete/{id}', name: 'delcat', requirements: ['id' => '\d+'])]
    public function delete(int $id, Request $request, CategoryRepository $catRepository, LoggerInterface $logger): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $catRepository->find($id);

        if(!$cat)
            throw new NotFoundHttpException();
        
        $form = $this->createForm(DeleteCategoryType::class, $cat,['notId' => $id]);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
              // Handle transfering products between categories when user decide to delete category with transfer
                $transferCat = $form->get('categories')->getData();

                if ($transferCat && $transferCat->getId() != $id)
                 $catRepository->transferProducts($cat, $transferCat);

                $em->remove($cat);
                $em->flush();
                return $this->redirectToRoute('home');
            }
        return $this->render('category/delete.html.twig', [
            'category' => $cat,
            'form' => $form->createView()
        ]);
    }
    

    // Rendering categories navigation bar
    // Using ESI Cache for performance https://symfony.com/doc/current/http_cache/esi.html#using-esi-in-symfony2 
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
