<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\Type\TagsInputType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Choose a Image file',
                'required' => false,

                ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'span12'],
                'label' => 'post.title',
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'span10 ckeditor'],
                'label' => 'post.content',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'span12'],
                'label' => 'post.category',
            ])
            ->add('tags', TagsInputType::class, [
                'attr' => ['class' => 'span12'],
                'required' => false,
                'label' => 'post.tags',
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-inverse'],
                'label' => 'post.button_save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
