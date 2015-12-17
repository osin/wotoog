<?php

namespace Wotoog\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Wotoog\BlogBundle\Entity\Blog;
use Wotoog\BlogBundle\Form\BlogType;

/**
 * Blog controller.
 *
 */
class BlogController extends Controller
{
    /**
     * Displays a form to edit an existing Blog entity.
     *
     */
    public function editAction($id)
    {
        $blogRepository = $this->getDoctrine()->getManager()->getRepository('WotoogBlogBundle:Blog');
        $blog = $blogRepository->find($id);
        $author = $blogRepository->getOwner($blog);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $editForm = $this->createEditForm($blog);

        return $this->render('WotoogBlogBundle:Blog:edit.html.twig', array(
            'blog'      => $blog,
            'form'   => $editForm->createView(),
            'author'      => $author
        ));
    }

    /**
    * Creates a form to edit a Blog entity.
    *
    * @param Blog $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Blog $entity)
    {
        $form = $this->createForm(new BlogType(), $entity, array(
            'action' => $this->generateUrl('blog_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Blog entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $blogRepository = $this->getDoctrine()->getManager()->getRepository('WotoogBlogBundle:Blog');
        $blog = $blogRepository->find($id);
        $author = $blogRepository->getOwner($blog);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $editForm = $this->createEditForm($blog);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $blog->flush();

            return $this->redirect($this->generateUrl('blog_show', array('blog_id' => $id)));
        }


        return $this->render('WotoogBlogBundle:Blog:edit.html.twig', array(
            'blog'      => $blog,
            'form'   => $editForm->createView(),
            'author' => $author
        ));
    }
}
