<?php

namespace Wotoog\ClubBundle\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;
use Wotoog\BlogBundle\Entity\Blog as Blog;


use Doctrine\ORM\Mapping as ORM;

/**
 * Club
 */
class Club
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $picture;

    /**
     * @var string
     */
    private $cover;

    /**
     * @var array
     */
    private $extension;

    private $blog;

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
     * Set name
     *
     * @param string $name
     * @return Club
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Club
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set visibility
     *
     * @param string $visibility
     * @return Club
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return string 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /** Upload cover and picture
     * @param $args
     */
    public function upload($args){
        if(null === $this->picture)
            return;
        $dir = 'files/';
        $fs = new Filesystem();
        $fs->mkdir($dir);
        $pictureFileName = 'profil-' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16) . time() . '.' . $this->picture->getClientOriginalExtension();
        $coverFileName = 'cover-' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16) . time() . '.' . $this->cover->getClientOriginalExtension();
        $this->picture->move($dir, $pictureFileName);
        $this->cover->move($dir, $coverFileName);
        $this->picture = $dir.'/' . $pictureFileName;
        $this->cover = $dir.'/' . $coverFileName;
        $args->getEntityManager()->flush();
    }


    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $cover
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
    }

    /**
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set theme
     *
     * @param string $theme
     * @return Club
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string 
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set extension
     *
     * @param array $extension
     * @return Club
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return array 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    public function getBlog(){
        return $this->blog;
    }

    public function __construct(){
        $this->blog = new Blog();
        $this->blog->setVisibility(Blog::$visibility_public);
        $this->blog->setDescription("Voici mon blog");
    }

    public function setDeleted(){
        $this->deleted = true;
    }

    public function getDeleted(){
        return $this->deleted;
    }

    public function getClassName(){
        return get_class($this);
    }

}
