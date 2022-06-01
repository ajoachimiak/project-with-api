<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, [
                'label_attr' => ['class' => 'font-weight-bold mr-2'],
                'data' => (array_key_exists('product', $options['data'])) ? $options['data']['product']->getName(): null
            ])
            ->add('price', MoneyType::class, [
                'label_attr' => ['class' => 'font-weight-bold mr-2'],
                'data' => (array_key_exists('product', $options['data'])) ? $options['data']['product']->getPrice(): null
            ])
            ->add('category', ChoiceType::class, [
                'label_attr' => ['class' => 'font-weight-bold mr-2'],
                'multiple' => true,
                'choices' => $this->getCategoryNames($options['data']['categories']),
                'data' => $this->getSelectedCategories($options['data'])
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'form-control btn btn-success w-25 mt-2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }

    /**
     * @param array $categories
     * @return array
     */
    private function getCategoryNames(array $categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $result[$category->getCode()] = $category->getId();
        }
        return $result;
    }

    /**
     * @param $options
     * @return array
     */
    private function getSelectedCategories($options): array
    {
        $result = [];
        if(array_key_exists('product', $options)) {
            /** @var Product $product */
            $product = $options['product'];
            foreach ($product->getCategory() as $category) {
                $result[] = $category->getId();
            }
        }
        return $result;
    }
}
