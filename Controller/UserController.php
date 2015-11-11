<?php

namespace Maith\Common\UsersBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Maith\Common\UsersBundle\Entity\User;
use Maith\Common\UsersBundle\Form\UserType;
use Maith\Common\UsersBundle\Form\UserProfileType;
use Maith\Common\UsersBundle\Form\UserPasswordType;
use Maith\Common\UsersBundle\Form\UserEmailPasswordType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
	 * @Secure(roles="ROLE_VIEW_USERS")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MaithCommonUsersBundle:User')->findAll();

        return $this->render('MaithCommonUsersBundle:User:index.html.twig', array(
            'entities' => $entities, 
            'activemenu' => 'users',
            'activesubmenu' => 'users',
        ));
    }
    /**
     * Creates a new User entity.
     * @Secure(roles="ROLE_ADD_EDIT_USERS")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $form_data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $userManager = $this->container->get('fos_user.user_manager');
			$user = $userManager->createUser();
			$user->setUsername($form_data->getEmail());
			$user->setFullName($form_data->getFullName());
			$user->setEmail($form_data->getEmail());
			$user->setPlainPassword('NuevaPass1234');
			$user->setEnabled($form_data->isEnabled());
			$user->setUserGroups($form_data->getUserGroups());
			$token = sha1(uniqid(mt_rand(), true)); // Or whatever you prefer to generate a token
			$user->setConfirmationToken($token);
			$userRoles = array();
			foreach($form_data->getUserGroups() as $userGroup)
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
            //$mailer = $this->container->get('fos_user.mailer');
            //$mailer->sendConfirmationEmailMessage($user);
            /*
            $formFactory = $this->get('fos_user.registration.form.factory');
            $form = $formFactory->createForm();
            $form->setData($user); // created user object
            $event = new FormEvent($form, $request); // request of the Controller
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            */
            $this->get('session')->getFlashBag()->add('notif-success', 'Usuario creado con exito');
            return $this->redirect($this->generateUrl('user_edit', array('id' => $user->getId())));
        }
        else
        {
          $this->get('session')->getFlashBag()->add('notif-error', 'A ocurrido un error con el formulario. Revisa los campos.');
        }

        return $this->render('MaithCommonUsersBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'users',
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     * 
	 * @Secure(roles="ROLE_ADD_EDIT_USERS")
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return $this->render('MaithCommonUsersBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'users',
        ));
    }

    /**
     * Finds and displays a User entity.
     *
	 * 
     */
    public function showAction()
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		
        return $this->render('MaithCommonUsersBundle:User:show.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * Finds and displays a User entity.
     * 
     */
    public function editProfileAction(Request $request)
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		
		$form = $this->createForm(new UserProfileType(), $user, array(
            'method' => 'POST',
        ));
		if ('PUT' === $request->getMethod() || 'POST' === $request->getMethod()) {
		  $form->bind($request);
		  if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
			$em->flush();
            $response = new JsonResponse();
            $profileShow = $this->renderView('MaithCommonUsersBundle:User:profiledata.html.twig', array(
                'user' => $user
            ));
            $response->setData(array('result' => 'OK', 'viewshow' => $profileShow));
            return $response;
			//$userManager = $this->container->get('fos_user.user_manager');
			//$userManager->updateUser($user);
		  }
          else{
            $response = new JsonResponse();
            $view = $this->renderView('MaithCommonUsersBundle:User:profileedit.html.twig', array(
                'entity' => $user,
                'edit_form'   => $form->createView(),
            ));
            $response->setData(array('result' => 'ERROR', 'view' => $view));
            
            return $response;
          }
		}
		$response = new JsonResponse();
        $view = $this->renderView('MaithCommonUsersBundle:User:profileedit.html.twig', array(
                'entity' => $user,
                'edit_form'   => $form->createView(),
            ));
        $response->setData(array('result' => 'OK' , 'view' => $view));
        return $response;
    }

    
    /**
     * Finds and displays a User entity.
     * 
     */
    public function editEmailPasswordAction(Request $request)
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		
		$form = $this->createForm(new UserEmailPasswordType(), $user, array(
            'method' => 'POST',
        ));
		if ('PUT' === $request->getMethod() || 'POST' === $request->getMethod()) {
		  $form->bind($request);
		  if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
			$em->flush();
            $response = new JsonResponse();
            $profileShow = $this->renderView('MaithCommonUsersBundle:User:profiledata.html.twig', array(
                'user' => $user
            ));
            $response->setData(array('result' => 'OK', 'viewshow' => $profileShow));
            return $response;
			//$userManager = $this->container->get('fos_user.user_manager');
			//$userManager->updateUser($user);
		  }
          else{
            $response = new JsonResponse();
            $view = $this->renderView('MaithCommonUsersBundle:User:profileeditemailpassword.html.twig', array(
                'entity' => $user,
                'edit_form'   => $form->createView(),
            ));
            $response->setData(array('result' => 'ERROR', 'view' => $view));
            
            return $response;
          }
		}
		$response = new JsonResponse();
        $view = $this->renderView('MaithCommonUsersBundle:User:profileeditemailpassword.html.twig', array(
                'entity' => $user,
                'edit_form'   => $form->createView(),
            ));
        $response->setData(array('result' => 'OK' , 'view' => $view));
        return $response;
    }
    
    /**
     * Finds and displays a User entity.
     * 
     */
    public function editPasswordAction(Request $request)
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		
		$form = $this->createForm(new UserPasswordType(), $user, array(
            'method' => 'POST',
        ));
		if ('PUT' === $request->getMethod() || 'POST' === $request->getMethod()) {
		  $form->bind($request);
		  if ($form->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $response = new JsonResponse();
            $profileShow = $this->renderView('MaithCommonUsersBundle:User:profiledata.html.twig', array(
                'user' => $user
            ));
            $response->setData(array('result' => 'OK', 'viewshow' => $profileShow));
            return $response;
			//$userManager = $this->container->get('fos_user.user_manager');
			//$userManager->updateUser($user);
		  }
          else{
            $response = new JsonResponse();
            $view = $this->renderView('MaithCommonUsersBundle:User:profileeditpassword.html.twig', array(
                'entity' => $user,
                'edit_form'   => $form->createView(),
            ));
            $response->setData(array('result' => 'ERROR', 'view' => $view));
            
            return $response;
          }
		}
		$response = new JsonResponse();
        $view = $this->renderView('MaithCommonUsersBundle:User:profileeditpassword.html.twig', array(
                'entity' => $user,
                'edit_form'   => $form->createView(),
            ));
        $response->setData(array('result' => 'OK' , 'view' => $view));
        return $response;
    }
    
    /**
     * Displays a form to edit an existing User entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_USERS")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MaithCommonUsersBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MaithCommonUsersBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'users',
        ));
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
	 * @Secure(roles="ROLE_ADD_EDIT_USERS")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MaithCommonUsersBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
			$form_data = $editForm->getData();
			$userRoles = array();
			foreach($form_data->getUserGroups() as $userGroup)
			{
			  foreach($userGroup->getGroupRoles() as $role)
			  {
				$userRoles[] = $role;
			  }
			}
			$entity->setRoles($userRoles);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notif-success', 'Usuario editado con exito');
            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }else{
          $this->get('session')->getFlashBag()->add('notif-error', 'A ocurrido un error con el formulario. Revisa los campos.');
        }

        return $this->render('MaithCommonUsersBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'activemenu' => 'users',
            'activesubmenu' => 'users',
        ));
    }
    /**
     * Deletes a User entity.
     *
	 * @Secure(roles="ROLE_REMOVE_USERS")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MaithCommonUsersBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function blockUnblockAction($id, $status)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getId() != $id)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MaithCommonUsersBundle:User')->find($id);
            $message = "";
            if((int)$status == 0)
            {
                $message = "Usuario bloqueado con exito";
                $entity->setLocked(true);
            }
            else
            {
                $message = "Usuario desbloqueado con exito";
                $entity->setLocked(false);
            }
            
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($entity);
            $this->get('session')->getFlashBag()->add('notif-success', $message);
        }else{
          $this->get('session')->getFlashBag()->add('notif-error', 'No puede bloquearte a ti mismo');
        }
        
        return $this->redirect($this->generateUrl('user', array()));
    }
}
