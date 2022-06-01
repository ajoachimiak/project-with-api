<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/product/add", name="app_product_add")
     */
    public function add(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        $entityCategories = $entityManager->getRepository(Category::class)->findAll();
        $productForm = $this->createForm(ProductType::class, ['categories' => $entityCategories]);

        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid()) {

            try{
                $productName = $productForm->get('name')->getData();
                $selectedCategories = $entityManager->getRepository(Category::class)->findBy(['id' => $productForm->get('category')->getData()]);

                $entityProduct = new Product();
                $entityProduct->setName($productName);
                $entityProduct->setPrice($productForm->get('price')->getData());
                foreach ($selectedCategories as $selectedCategory) {
                    $entityProduct->addCategory($selectedCategory);
                }

                $entityProduct->setCreationDate(new \DateTime());
                $entityProduct->setUpdateDate(new \DateTime());

                $entityManager->persist($entityProduct);
                $entityManager->flush();

                $this->addFlash('success', "The $productName product has been added");

                return $this->redirectToRoute('app_product_list');
            }
            catch (\Exception $exception) {
                $this->addFlash('error', 'An unexpected error has occurred! '.$exception->getMessage());
            }
        }

        return $this->render('product/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="app_product_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $entityManager = $this->doctrine->getManager();
        $entityProduct = $entityManager->getRepository(Product::class)->find($id);

        if(is_null($entityProduct)) {
            return $this->redirectToRoute('app_product_list');
        }

        $entityCategories = $entityManager->getRepository(Category::class)->findAll();
        $productForm = $this->createForm(ProductType::class, [
            'categories' => $entityCategories,
            'product' => $entityProduct
        ]);

        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid()) {

            try{
                $productName = $productForm->get('name')->getData();
                $formSelectedCategory = $productForm->get('category')->getData();

                foreach ($entityProduct->getCategory() as $productSelectedCategory) {
                    if(!in_array($productSelectedCategory->getId(), $formSelectedCategory)) {
                        $entityProduct->removeCategory($productSelectedCategory);
                    }
                }

                $selectedCategories = $entityManager->getRepository(Category::class)->findBy(
                    ['id' => $formSelectedCategory]);

                foreach ($selectedCategories as $selectedCategory) {
                    $entityProduct->addCategory($selectedCategory);
                }

                $entityProduct->setName($productName);
                $entityProduct->setPrice($productForm->get('price')->getData());
                $entityProduct->setUpdateDate(new \DateTime());

                $entityManager->persist($entityProduct);
                $entityManager->flush();

                $this->addFlash('success', "Successfully edited product $productName");

                return $this->redirectToRoute('app_product_list');
            }
            catch (\Exception $exception) {
                $this->addFlash('error', 'An unexpected error has occurred! '.$exception->getMessage());
            }
        }

        return $this->render('product/edit.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="app_product_delete")
     */
    public function delete($id): Response
    {
        $entityManager = $this->doctrine->getManager();
        $entityProduct = $entityManager->getRepository(Product::class)->find($id);

        if(is_null($entityProduct)) {
            return $this->redirectToRoute('app_product_list');
        }

        try{
            $productName = $entityProduct->getName();

            $entityManager->remove($entityProduct);
            $entityManager->flush();

            $this->addFlash('success', "$productName product has been successfully removed");
        }
        catch (\Exception $exception) {
            $this->addFlash('error', 'An unexpected error has occurred! '.$exception->getMessage());
        }

        return $this->redirectToRoute('app_product_list');
    }
}
