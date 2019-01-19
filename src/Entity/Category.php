<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="255",
     *     minMessage="Category name must contain at least {{ limit }} characters",
     *     maxMessage="Category name must contain no more than {{ limit }} characters",
     * )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="category")
     */
    private $posts;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subscription", mappedBy="category", orphanRemoval=true)
     */
    private $subscribers;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param $posts
     *
     * @return Category
     */
    public function setPosts($posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Category
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param $slug
     *
     * @return Category
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubscriber(): Collection
    {
        return $this->subscribers;
    }

    /**
     * @param Subscription $subscribers
     * @return Category
     */
    public function addSubscriber(Subscription $subscribers): self
    {
        if (!$this->subscribers->contains($subscribers)) {
            $this->subscribers[] = $subscribers;
            $subscribers->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Subscription $subscribers
     * @return Category
     */
    public function removeSubscriber(Subscription $subscribers): self
    {
        if ($this->subscribers->contains($subscribers)) {
            $this->subscribers->removeElement($subscribers);
            // set the owning side to null (unless already changed)
            if ($subscribers->getCategory() === $this) {
                $subscribers->setCategory(null);
            }
        }

        return $this;
    }
}
