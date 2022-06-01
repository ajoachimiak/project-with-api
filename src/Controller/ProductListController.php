<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductListController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("", name="app_product_list")
     */
    public function index(): Response
    {
        $entityManager = $this->doctrine->getManager();
        $entityProducts = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product_list/index.html.twig', [
            'entityProducts' => $entityProducts
        ]);
    }
}
