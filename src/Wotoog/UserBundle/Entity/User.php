<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 22/05/14
 * Time: 16:32
 */

namespace Wotoog\UserBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Wotoog\BlogBundle\WotoogBlogBundle;
use Wotoog\BlogBundle\Entity\Blog as Blog;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;

/**
 * @ORM\Entity(repositoryClass="Wotoog\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Wotoog\ClubBundle\Entity\Club")
     */
    protected $clubs;

    /**
     * @ORM\OneToOne(targetEntity="Wotoog\BlogBundle\Entity\Blog", cascade={"persist"})
     */
    protected $blog;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $cover;


    public function __construct()
    {
        parent::__construct();
        $this->clubs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->blog = new Blog();
        $this->blog->setVisibility(Blog::$visibility_public);
        $this->blog->setDescription("Voici mon blog");
        // your own logic
    }

    /**
     * Add club
     * @param Wotoog\ClubBundle\Entity\Club
     */
    public function addClub(\Wotoog\ClubBundle\Entity\Club $club)
    {
        $this->clubs[] = $club;
    }

    /**
     * Remove club
     * @param Wotoog\ClubBundle\Entity\Club
     */
    public function removeClub(\Wotoog\ClubBundle\Entity\Club $club)
    {
        // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
        $this->clubs->removeElement($club);
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getClubs()
    {
        return $this->clubs;
    }

    public function getBlog(){
        return $this->blog;
    }

    public function getName(){
        return strtoupper($this->username);
    }

    public function getClassName(){
        return get_class($this);
    }

    public function getPicture(){
        return $this->picture;
    }

    public function setPicture($picture){
        $this->picture = $picture;
    }

    public function getCover(){
        return $this->cover;
    }

    public function setCover($cover){
        $this->cover = $cover;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = ucfirst(strtolower($first_name));
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = ucfirst(strtolower($last_name));
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @ORM\PostPersist()
     * Upload cover and picture
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

    public function getDescription(){
        return $this->first_name. ' '. $this->last_name;
    }
}