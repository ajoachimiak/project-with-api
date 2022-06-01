<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/category/add", name="app_category_add")
     */
    public function add(Request $request): Response
    {
        $categoryForm = $this->createForm(CategoryType::class);

        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            try {
                $entityManager = $this->doctrine->getManager();
                $categoryCode = $categoryForm->get('code')->getData();

                $entityCategory = new Category();
                $entityCategory->setCode($categoryCode);
                $entityCategory->setCreationDate(new \DateTime());
                $entityCategory->setUpdateDate(new \DateTime());

                $entityManager->persist($entityCategory);
                $entityManager->flush();

                $this->addFlash('success', "The $categoryCode category has been added");

                return $this->redirectToRoute('app_category_list');
            }
            catch (\Exception $exception) {
                $this->addFlash('error', 'An unexpected error has occurred! '.$exception->getMessage());
            }
        }

        return $this->render('category/add.html.twig', [
            'categoryForm' => $categoryForm->createView()
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="app_category_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $entityManager = $this->doctrine->getManager();
        $entityCategory = $entityManager->getRepository(Category::class)->find($id);

        if(is_null($entityCategory)) {
            return $this->redirectToRoute('app_category_list');
        }

        $categoryForm = $this->createForm(CategoryType::class, $entityCategory);

        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            try {
                $categoryCode = $categoryForm->get('code')->getData();
                $entityCategory->setCode($categoryCode);
                $entityCategory->setUpdateDate(new \DateTime());

                $entityManager->persist($entityCategory);
                $entityManager->flush();

                $this->addFlash('success', "Successfully edited category $categoryCode");

                return $this->redirectToRoute('app_category_list');
            }
            catch (\Exception $exception) {
                $this->addFlash('error', 'An unexpected error has occurred! '.$exception->getMessage());
            }
        }

        return $this->render('category/edit.html.twig', [
            'categoryForm' => $categoryForm->createView()
        ]);
    }
}
