<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{

    
    #[Route('/addcat', name: 'addcat')]
    public function add(Request $request): Response
    {
        $cat = new Category();

        $form = $this->createFormBuilder($cat)
                ->add('name',TextType::class)
                ->add('description', TextareaType::class)
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


    #[Route('/editcat/{id}', name: 'editcat', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $man = $this->getDoctrine()->getManager();
        $cat = $man->getRepository(Category::class)->find($id);

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
            'cname' => $cat->getName()
        ]);
    }


    #[Route('/delcat/{id}', name: 'delcat', requirements: ['id' => '\d+'])]
    public function delete(int $id, Request $request): Response
    {
        $man = $this->getDoctrine()->getManager();
        $cat = $man->getRepository(Category::class)->find($id);

        if(!$cat)
            throw new NotFoundHttpException();

        $form = $this->createFormBuilder($cat)
                ->add('Delete', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted())
        {

            $man->remove($cat);
            $man->flush();

            return $this->redirectToRoute("home");

        }

        return $this->render('category/delete.html.twig', [
            'form' => $form->createView(),
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
