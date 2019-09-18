<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CheckoutType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/checkout", name="product_checkout")
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function checkout(Request $request, ProductRepository $productRepository, \Swift_Mailer $mailer)
    {
        $total = 0;
        $form = $this->createForm(CheckoutType::class);

        $form->handleRequest($request);

        $cart = $this->session->get("Cart", array());

        $Products = array();

        foreach ($cart as $id => $product) {
            array_push($Products, ["Amount" => $product["Amount"], "Product" => $productRepository->find($id)]);

            $total += $product["Amount"] * $productRepository->find($id)->getPrice();
        }

        $this->session->set("Cart", $cart);


        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $message = (new \Swift_Message('Confirmation Mail'))
                ->setFrom('1033788@mborijnland.nl')
                ->setReplyTo('1033788@mborijnland.nl')
                ->setTo($formData["Email"])
                ->setBody(
                    $this->renderView(
                        'email/checkout.html.twig',
                        ["Name" => $formData["Name"], "Products" => $Products]
                    ),
                    'text/html'
                );

//            var_dump($message);
//
//            die();

            $mailer->send($message);

            $this->session->set("Cart", array());

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/checkout.html.twig', [
            "submitForm" => $form->createView(),
            "Products" => $Products,
            "total" => $total
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/{id}/remove", name="product_remove", methods={"GET", "POST"})
     */
    public function removeAction(Product $product)
    {
        $id = $product->getId();
        // check the cart
        $cart = $this->session->get("Cart", array());
        // if it doesn't exist redirect to cart index page. end
        // check if the $id already exists in it.
        if( isset($cart[$id]) ) {
            $cart[$id]["Amount"]--;
            if ($cart[$id]["Amount"] < 1) {
                unset($cart[$id]);
            }
        } else {
            return $this->redirect( $this->generateUrl('product_index') );
        }
        $this->session->set("Cart", $cart);
        return $this->redirect( $this->generateUrl('product_index') );
    }

    /**
     * @Route("/{id}/add", name="product_addtocart", methods={"GET", "POST"})
     */
    public function Add(Product $product, ProductRepository $productRepository)
    {
        $id = $product->getId();
        $cart = $this->session->get("Cart", array());

        $total = 0;

        if (isset($cart[$id])) {
            $cart[$id]["Amount"]++;
        } else {
            $cart[$id]["Amount"] = 1;
        }
        $this->session->set("Cart", $cart);

        $Products = array();

        foreach ($cart as $id => $Product) {
            array_push($Products, ["Amount" => $Product["Amount"], "Product" => $productRepository->find($id)]);

            $total += $Product["Amount"] * $productRepository->find($id)->getPrice();
        }

        return $this->render('product/addtocart.html.twig', [
            'Products' => $Products,
            'total' => $total
        ]);
    }
}
