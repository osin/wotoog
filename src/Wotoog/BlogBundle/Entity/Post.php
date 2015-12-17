<?php

namespace Wotoog\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 */
class Post
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $content;

    /**
     * @var integer
     */
    private $blog;

    /**
     * @var string
     */
    private $category;

    /**
     * @var description
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tags;

    function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var boolean
     */
    private $deleted = false;

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
     * Set status
     * @param string $status
     */
    public function setVisibility($visibility){
        $this->visibility = $visibility;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Post
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    public function setBlog(\Wotoog\BlogBundle\Entity\Blog $blog){
        $this->blog = $blog;
        return $this;
    }

    public function getBlog(){
        return $this->blog;
    }

    public function setCreatedAtValue()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function setUpdatedAtValue(){
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function getTags(){
        return $this->tags;
    }

    public function addTag(\Wotoog\BlogBundle\Entity\Tag $tag){
        $this->tags[] = $tag;
    }

    public function setDeleted(){
        $this->deleted = true;
    }

    public function getDeleted(){
        return $this->deleted;
    }

    /**
     * Set description to content between firsts <p> tags </p>
     * @param $string
     * @param $tagname
     *
     * @return mixed
     */
    public function setDescription()
    {
        $pattern = "/<img(.*?)>/";
        $content = preg_replace($pattern, "", $this->content);//remove img

        $pattern = "/<p>(.*?)<\/p>/";
        preg_match_all($pattern, $content, $matches); //get text between <p>
        if(count($matches) && count($matches[0])){


            $lenght = 0;
            $description = "";
            foreach($matches[0] as $paragraph){
                if(strlen($paragraph) + $lenght < 480){
                    $lenght +=strlen($paragraph);
                    $description .= $paragraph;
                } //512 is the lenght of field description
            }
            $this->description = $description;
        }
        return $matches[1];
    }

    public function getFirstPicture(){
        $pattern = "/<img(.*?)>/";
        preg_match($pattern, $this->content, $match);
        if (count($match))
            return $match[0];
        else return "";
    }

    public function getDescription(){
        return $this->description;
    }
}
