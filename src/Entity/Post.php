<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    public const NUM_ITEMS = 10;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="255",
     *     minMessage="Title must contain at least {{ limit }} characters",
     *     maxMessage="Title should contain no more than {{ limit }} characters",
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     minMessage="Content must contain at least {{limit}} characters",
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isPublished;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="posts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"publishedAt": "DESC"})
     * @Assert\NotNull()
     */
    private $comments;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var Tag[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="3")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="post", orphanRemoval=true, cascade={"persist"})
     * @Assert\NotNull()
     */
    private $likes;

    /**
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->isPublished = true;
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->likes = new ArrayCollection();
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
     * @return Post
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $category
     *
     * @return Post
     */
    public function setCategory($category): self
    {
        $this->category = $category;

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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Post
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Post
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool $isPublished
     *
     * @return Post
     */
    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Post
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment|null $comment
     */
    public function addComment(?Comment $comment): void
    {
        $comment->setPost($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    /**
     * @param Comment $comment
     *
     * @return Post
     */
    public function removeComment(Comment $comment): self
    {
        $comment->setPost(null);
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     *
     * @return Post
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param Tag|null ...$tags
     */
    public function addTag(?Tag ...$tags): void
    {
        foreach ($tags as $tag) {
            if (!$this->tags->contains($tag)) {
                $this->tags->add($tag);
            }
        }
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @return Collection
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @param Like|null $like
     */
    public function addLike(?Like $like): void
    {
        $like->setPost($this);
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }
    }

    /**
     * @param Like $like
     *
     * @return Post
     */
    public function removeLike(?Like $like): self
    {
        $like->setPost(null);
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Post
     */
    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }
}
