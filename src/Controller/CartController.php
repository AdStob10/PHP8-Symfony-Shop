<?php

namespace App\Controller;

use App\Service\CartManagment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CartType;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(Request $request,CartManagment $cartMngmt): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $cart = $cartMngmt->getCart();
        $form = $this->createForm(CartType::class, $cart);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $cartMngmt->saveCart($cart);

            return $this->redirectToRoute('cart');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

}
