<?php

namespace Maith\Common\UsersBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Maith\Common\UsersBundle\Entity\GroupRole;
use Maith\Common\UsersBundle\Form\GroupRoleType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * GroupRole controller.
 *
 */
class GroupRoleController extends Controller
{

    /**
     * Lists all GroupRole entities.
     *
	 * @Secure(roles="ROLE_VIEW_PERMISSION_GROUPS")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MaithCommonUsersBundle:GroupRole')->findAll();

        return $this->render('MaithCommonUsersBundle:GroupRole:index.html.twig', array(
            'entities' => $entities,
            'activemenu' => 'users',
            'activesubmenu' => 'usersgrouproles',
        ));
    }
    /**
     * Creates a new GroupRole entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_PERMISSION_GROUPS")
     */
    public function createAction(Request $request)
    {
        $entity = new GroupRole();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notif-success', 'Grupo de roles creado con exito');
            return $this->redirect($this->generateUrl('grouprole_edit', array('id' => $entity->getId())));
        }else{
            $this->get('session')->getFlashBag()->add('notif-error', 'A ocurrido un error con el formulario. Revisa los campos.');
        }

        return $this->render('MaithCommonUsersBundle:GroupRole:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'usersgrouproles',
        ));
    }

    /**
     * Creates a form to create a GroupRole entity.
     *
     * @param GroupRole $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(GroupRole $entity)
    {
        $form = $this->createForm(new GroupRoleType(), $entity, array(
            'action' => $this->generateUrl('grouprole_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new GroupRole entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_PERMISSION_GROUPS")
     */
    public function newAction()
    {
        $entity = new GroupRole();
        $form   = $this->createCreateForm($entity);

        return $this->render('MaithCommonUsersBundle:GroupRole:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'usersgrouproles',
        ));
    }

    /**
     * Finds and displays a GroupRole entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_PERMISSION_GROUPS1")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MaithCommonUsersBundle:GroupRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GroupRole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MaithCommonUsersBundle:GroupRole:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'usersgrouproles',
        ));
    }

    /**
     * Displays a form to edit an existing GroupRole entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_PERMISSION_GROUPS")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MaithCommonUsersBundle:GroupRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GroupRole entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MaithCommonUsersBundle:GroupRole:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'usersgrouproles',
        ));
    }

    /**
    * Creates a form to edit a GroupRole entity.
    *
    * @param GroupRole $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(GroupRole $entity)
    {
        $form = $this->createForm(new GroupRoleType(), $entity, array(
            'action' => $this->generateUrl('grouprole_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing GroupRole entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_PERMISSION_GROUPS")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MaithCommonUsersBundle:GroupRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GroupRole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            foreach($entity->getUsers() as $user)
            {
              $userRoles = array();
              foreach($user->getUserGroups() as $userGroup)
              {
                foreach($userGroup->getGroupRoles() as $role)
                {
                  $userRoles[] = $role;
                }
              }
              $user->setRoles($userRoles);
              $em->persist($user);
              //die;
              $em->flush();
            }
            $this->get('session')->getFlashBag()->add('notif-success', 'Grupo de roles actualizado con exito');
            return $this->redirect($this->generateUrl('grouprole_edit', array('id' => $id)));
        }else{
            $this->get('session')->getFlashBag()->add('notif-error', 'A ocurrido un error con el formulario. Revisa los campos.'.implode(',',$form->getErrors()));
        }

        return $this->render('MaithCommonUsersBundle:GroupRole:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'usersgrouproles',
        ));
    }
    /**
     * Deletes a GroupRole entity.
     *
	 * @Secure(roles="ROLE_REMOVE_PERMISSION_GROUPS")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MaithCommonUsersBundle:GroupRole')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find GroupRole entity.');
            }
            foreach($entity->getUsers() as $user)
            {
              $userRoles = array();
              foreach($user->getUserGroups() as $userGroup)
              {
                foreach($userGroup->getGroupRoles() as $role)
                {
                  $userRoles[] = $role;
                }
              }
              $user->setRoles($userRoles);
              $em->persist($user);
              //die;
              $em->flush();
            }
            $em->remove($entity);
            $em->flush();
            
			$this->get('session')->getFlashBag()->add('notif-success', 'Grupo de roles eliminado con exito');
        }

        return $this->redirect($this->generateUrl('grouprole'));
    }

    /**
     * Creates a form to delete a GroupRole entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('grouprole_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
