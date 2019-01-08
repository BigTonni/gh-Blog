<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TagArrayToStringTransformer.
 */
class TagArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @var TagRepository
     */
    private $tags;

    /**
     * TagArrayToStringTransformer constructor.
     *
     * @param TagRepository $tags
     */
    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param mixed $tags
     *
     * @return string
     */
    public function transform($tags): string
    {
        /* @var Tag[] $tags */
        return implode(',', $tags);
    }

    /**
     * @param mixed $string
     *
     * @return array
     */
    public function reverseTransform($string): array
    {
        if ('' === $string || null === $string) {
            return [];
        }
        $names = array_filter(array_unique(array_map('trim', explode(',', $string))));

        $tags = $this->tags->findBy([
            'name' => $names,
        ]);
        $newNames = array_diff($names, $tags);
        foreach ($newNames as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tags[] = $tag;
        }

        return $tags;
    }
}
