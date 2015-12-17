<?php

namespace Wotoog\ClubBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wotoog\ClubBundle\Entity\Club;
use Wotoog\ClubBundle\Form\ClubType;

/**
 * Club controller.
 *
 */
class ClubController extends Controller
{

    /**
     * Lists all Club entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WotoogClubBundle:Club')->findAll();

        return $this->render('WotoogClubBundle:Club:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Club entity.
     *
     */
    public function createAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof \Wotoog\UserBundle\Entity\User) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $club = new Club();
        $form = $this->createCreateForm($club);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // traitement sur les images ici
            $user->addClub($club);
            $em = $this->getDoctrine()->getManager();
            $em->persist($club);
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('wotoog_club_show', array('id' => $club->getId())));
        }

        return $this->render('WotoogClubBundle:Club:new.html.twig', array(
            'entity' => $club,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Club entity.
    *
    * @param Club $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Club $entity)
    {
        $form = $this->createForm(new ClubType(), $entity, array(
            'action' => $this->generateUrl('wotoog_club_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Club entity.
     *
     */
    public function newAction()
    {
        $entity = new Club();
        $form   = $this->createCreateForm($entity);

        return $this->render('WotoogClubBundle:Club:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Club entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $club = $em->getRepository('WotoogClubBundle:Club')->find($id);
        if (!$club) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }

        $loggedUser = $this->container->get('security.context')->getToken()->getUser();;
//        @todo: changer la manière dont ce controle est réalisé (ne doit pas se baser sur le blog)
        $blogRepository = $em->getRepository('WotoogBlogBundle:Blog');
        if(is_a($loggedUser, 'Wotoog\UserBundle\Entity\User'))
            $canEdit = $blogRepository->hasAdminRights($loggedUser, $club->getBlog());
        else
            $canEdit = false;

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WotoogClubBundle:Club:show.html.twig', array(
            'canEdit' => $canEdit,
            'author'    => $club,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Club entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WotoogClubBundle:Club')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WotoogClubBundle:Club:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Club entity.
    *
    * @param Club $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Club $entity)
    {
        $form = $this->createForm(new ClubType(), $entity, array(
            'action' => $this->generateUrl('wotoog_club_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Club entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WotoogClubBundle:Club')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('wotoog_club_edit', array('id' => $id)));
        }

        return $this->render('WotoogClubBundle:Club:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Club entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WotoogClubBundle:Club')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Club entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('wotoog_club'));
    }

    /**
     * Creates a form to delete a Club entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('wotoog_club_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function registerAction()
    {
        return $this->render('WotoogClubBundle:Register:index.html.twig');
    }
}
