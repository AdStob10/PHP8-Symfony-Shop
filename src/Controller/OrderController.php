<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderManageType;
use App\Form\OrderStatusType;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\OrderManagment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends AbstractController
{

    // Get all user orders
    // Added sorting by date and status + pagination
    #[Route('/orders/{pg?1}-{ord?odd}/{sts?}', name: 'orders', requirements:[ 'ord' => '[a-z]{2,3}'])]
    public function index(int $pg, $ord, $sts, OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        if($pg < 1)
        throw new NotFoundHttpException();

        $len = $this->getParameter('order_page_count');
        $orders = $orderRepository->findUserOrders($this->getUser(), (($pg-1) * $len), $len, $ord, $sts);
        $maxpages = ceil($orders->count() / $len);

        if($maxpages < $pg && $maxpages > 0)
        throw new NotFoundHttpException();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'page' => $pg,
            'maxpages' => $maxpages,
            'order' => $ord,
            'status' => $sts
        ]);
    }

    // Creating new order
    #[Route('/order/create/{id}', name: 'create_order')]
    public function create(Request $request, Order $order, OrderManagment $orderManagment)
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        if($order->getStatus() != 0 || $order->getUser()->getId() != $this->getUser()->getId())
            return $this->redirectToRoute('home');

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form['acceptPolicy']->getData() == true)
        {
            // Using service to minimize code in controller method
            $order = $orderManagment->makeOrder($order);
            
            return $this->redirectToRoute('order',['id' => $order->getId()]);
        }

        return $this->render('order/createOrder.html.twig',[
            'form' => $form->createView()
        ]);
    }

    // Order page ( different version for different role )
    #[Route('/order/{id}', name: 'order', requirements:['id' => '\d+'])]
    public function details(int $id, OrderRepository $orderRepository, Request $request)
    {

        if($this->isGranted('ROLE_EMPLOYEE'))
        $order = $orderRepository->findOrderAsWorker($id);
        else
        $order = $orderRepository->findUserOrderWithItems($this->getUser(), $id);

        if(!$order)
            return $this->redirectToRoute('home');

        // Form type for changing order status
        $form = $this->createForm(OrderStatusType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $order = $form->getData();
            $em->persist($order);
            $em->flush();

        }

        $ct = Countries::getName($order->getClientCountry());

        return $this->render("order/details.html.twig",[
            'order' => $order,
            'clientCountry' => $ct,
            'form' => $form->createView()
        ]);
    }

    // Delete order action
    #[Route('/order/delete/{id}', name: 'delete_order', requirements:['id' => '\d+'])]
    public function delete(Order $order)
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        
        if($order->getUser()->getId() != $this->getUser()->getId())
        return $this->redirectToRoute('home');

        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute('orders');
    }

    // Orders view for employee
    // Added sorting by status and date + pagination
    // Form for changing order status
    #[Route('/order/manageorders/{pg?1}-{ord?odd}/{sts?}', name: 'manage_orders', requirements:[ 'ord' => '[a-z]{2,3}'])]
    public function manageOrders(int $pg, $ord, $sts,  Request $request,OrderRepository $orderRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        if($pg < 1)
        throw new NotFoundHttpException();

        $len = $this->getParameter('manage_page_count');


        $orders = $orderRepository->findOrdersToManage((($pg-1) * $len), $len, $ord,$sts);
        $maxpages = ceil($orders->count() / $len);

        if($maxpages < $pg && $maxpages > 0)
        throw new NotFoundHttpException();

        $form = $this->createForm(OrderManageType::class,['orders' => $orders]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //$arry = $form->getData();
            $em = $this->getDoctrine()->getManager();

            foreach($orders as $o)
            {
                 $em->persist($o);
            }
            $em->flush();
        }

        return $this->render('order/manageOrders.html.twig', [
            'orders' => $orders,
            'page' => $pg,
            'maxpages' => $maxpages,
            'order' => $ord,
            'status' => $sts,
            'form' => $form->createView()
        ]);
    }

    // Get bestselling books based on orders
    // Number of results - config parameter
    #[Route('/bestsellers',name:'bestsellers')]
    public function bestsellers(ProductRepository $productRepository)
    {
        $max = $this->getParameter('bestsellers_max');
        $products = $productRepository->findBestsellingProducts($max);

        return $this->render('order/bestsellers.html.twig',[
            'products' => $products,
            'max' => $max
        ]);
    }
}
