<?php

namespace Wotoog\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 */
class Blog
{
    static $visibility_public = 'public';
    static $visibility_private = 'private';
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $posts;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var text
     */
    private $description;

    /**
     * Set visibility
     *
     * @param string $visibility
     * @return Blog
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return boolean 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    public function getPosts(){
        return $this->posts;
    }

    public function addPost(\Wotoog\BlogBundle\Entity\Post $post){
        $this->posts[] = $post;
    }

    public function removePost(\Wotoog\BlogBundle\Entity\Post $post){
        $this->posts->removeElement($post);
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function __construct(){
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
}