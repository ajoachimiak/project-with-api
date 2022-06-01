<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryListController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/category/list", name="app_category_list")
     */
    public function index(): Response
    {
        $entityManager = $this->doctrine->getManager();
        $entityCategories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('category_list/index.html.twig', [
            'entityCategories' => $entityCategories
        ]);
    }
}
