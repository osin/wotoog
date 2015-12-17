<?php

namespace Wotoog\BlogBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\AsseticBundle\Exception\InvalidBundleException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Wotoog\BlogBundle\Entity\Post;
use Wotoog\BlogBundle\Form\PostType;
use Doctrine\Common\Collections\Criteria;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    private $numberOfPostToShow = 5;

    /**
     * Lists recent Post entities for user blog
     *
     */
    public function indexAction($blog_id, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $loggedUser = $this->container->get('security.context')->getToken()->getUser();;
        $blogRepository = $em->getRepository('WotoogBlogBundle:Blog');
        $blog = $blogRepository->find($blog_id);
        $author = $blogRepository->getOwner($blog);
        $posts = $em->getRepository('WotoogBlogBundle:Post')->getRecentPosts($blog, $this->numberOfPostToShow, $page);
        $nbPages = ceil($em->getRepository('WotoogBlogBundle:Post')->count($blog)/$this->numberOfPostToShow);

        $canEdit = (is_a($loggedUser, 'Wotoog\UserBundle\Entity\User')) ? $blogRepository->hasAdminRights($loggedUser, $blog) : false;

        return $this->render('WotoogBlogBundle:Post:index.html.twig', array(
            'posts'     => $posts,
            'author'     => $author,
            'blog'      => $blog,
            'canEdit'   => $canEdit,
            'page' => $page,
            'nbPages' => $nbPages
        ));
    }

    /**
     * Creates a new Post entity.
     *
     */
    public function createAction(Request $request)
    {
        $loggedUser = $this->container->get('security.context')->getToken()->getUser();;
        if(!is_a($loggedUser, 'Wotoog\UserBundle\Entity\User'))
            throw new \AuthenticationCredentialsNotFoundException();

        $post = new Post();
        $blog_id = $request->get('blog_id');

        $em = $this->getDoctrine()->getManager();
        $blogRepository = $em->getRepository('WotoogBlogBundle:Blog');

        $blog = $blogRepository->find($blog_id);
        if(!$blog)
            throw new createNotFoundException('Unable to find Blog');

        if(!$blogRepository->hasAdminRights($loggedUser, $blog))
            throw new AccessDeniedException("You do not have autorisation to create post in this blog");

        $action = $this->generateUrl('wotoog_post_create', array('blog_id' => $blog_id));
        $form = $this->createForm(new PostType(), $post, array(
            'action' => $action,
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));
        $form->handleRequest($request);

        $post->setBlog($blog);
        if ($form->isValid()) {
            $em->persist($post);
            $em->flush();
            $returnRoute = $this->generateUrl('wotoog_post_show', array('blog_id' => $blog_id, 'id' => $post->getId()));
            return $this->redirect($returnRoute);
        }else{
            return $this->render('WotoogBlogBundle:Post:update.html.twig', array(
                'entity' => $post,
                'form'   => $form->createView(),
                'blog' => $blog
            ));
        }
    }

    /**
     * Displays a form to create a new Post entity.
     *
     */
    public function newAction($blog_id)
    {
        $loggedUser = $this->container->get('security.context')->getToken()->getUser();
        if(!is_a($loggedUser, 'Wotoog\UserBundle\Entity\User'))
            throw new AuthenticationCredentialsNotFoundException();

        $blogRepository = $this->getDoctrine()->getManager()->getRepository('WotoogBlogBundle:Blog');
        $blog = $blogRepository->find($blog_id);
        if(!$blog){
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
        $entity = new Post();
        $author = $blogRepository->getOwner($blog);
        $action = $this->generateUrl('wotoog_post_new', array('blog_id' => $blog->getId()));
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $action,
            'method' => 'POST',
        ));

        $categories = $this->getDoctrine()->getManager()->getRepository('WotoogBlogBundle:Blog')->getCategories($blog);
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $this->render('WotoogBlogBundle:Post:update.html.twig', array(
            'author' => $author,
            'entity' => $entity,
            'form'   => $form->createView(),
            'blog' => $blog,
            'categories' => $categories
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction($blog_id, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepostory = $em->getRepository('WotoogBlogBundle:Post');
        $post = $postRepostory->find($id);
//        $nextPost = $postRepostory->getNext();
//        $previoustPost = $postRepostory->getPrevious();

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $loggedUser = $this->container->get('security.context')->getToken()->getUser();

        $blogRepository = $em->getRepository('WotoogBlogBundle:Blog');
        $blog = $blogRepository->find($blog_id);
        $author = $blogRepository->getOwner($blog);

        $canEdit = (is_a($loggedUser, 'Wotoog\UserBundle\Entity\User')) ? $blogRepository->hasAdminRights($loggedUser, $blog) : false;

        $viewParameters = array(
            'author' => $author,
            'post'      => $post,
            'canEdit'   => $canEdit,
            'blog' => $blog,
        );
        return $this->render('WotoogBlogBundle:Post:show.html.twig', $viewParameters);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction($blog_id, $id)
    {

        $loggedUser = $this->container->get('security.context')->getToken()->getUser();
        if(!is_a($loggedUser, 'Wotoog\UserBundle\Entity\User')){
            throw new AuthenticationCredentialsNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $blogRepository = $em->getRepository('WotoogBlogBundle:Blog');

        $blog = $blogRepository->find($blog_id);
        if(!$blog)
            throw new createNotFoundException('Unable to find Blog');

        if(!$blogRepository->hasAdminRights($loggedUser, $blog))
            throw new AccessDeniedException("You do not have autorisation to create post in this blog");

        $author = $blogRepository->getOwner($blog);
        $postRepository = $em->getRepository('WotoogBlogBundle:Post');
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $editForm = $this->createEditForm($blog_id, $post);
        return $this->render('WotoogBlogBundle:Post:update.html.twig', array(
            'author' => $author,
            'entity' => $post,
            'form'   => $editForm->createView(),
            'blog'  => $post->getBlog(),
        ));

    }

    /**
     * Creates a form to edit a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($blog_id, $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('wotoog_post_update', array('blog_id' => $blog_id, 'id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));
        $form->remove('category');
        return $form;
    }

    /**
     * Edits an existing Post entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $loggedUser = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('WotoogBlogBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $blogRepository = $em->getRepository('WotoogBlogBundle:Blog');
        $blog = $post->getBlog();
        if(!$blog)
            throw new createNotFoundException('Unable to find Blog');

        if(!$blogRepository->hasAdminRights($loggedUser, $blog))
            throw new AccessDeniedException("You do not have autorisation to create post in this blog");

        $editForm = $this->createEditForm($post->getBlog()->getId(), $post);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('wotoog_post_edit', array('blog_id' => $blog->getId(), 'id' => $id)));
        }

        return $this->render('WotoogBlogBundle:Post:edit.html.twig', array(
            'entity'      => $post,
            'edit_form'   => $editForm->createView(),
        ));
    }
}
