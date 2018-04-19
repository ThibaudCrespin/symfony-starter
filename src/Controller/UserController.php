<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends Controller
{
    /**
     * @Route("/admin/user", name="user")
     */
    public function index($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        return $this->render('user/index.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/admin/user/success", name="user_success")
     * @Template()
     */
    public function success()
    {
        return array('message' => 'User successfully created.');
    }

    /**
     * @Route("/admin/user/new", name="user_new")
     * @Template()
     */
    public function new(Request $request, TranslatorInterface $translator)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, array(
                'label' => $translator->trans('form.username')
            ))
            ->add('password', PasswordType::class, array(
                'label' => $translator->trans('form.password')
            ))
            ->add('firstName', TextType::class, array(
                'label' => $translator->trans('form.firstName')
            ))
            ->add('lastName', TextType::class, array(
                'label' => $translator->trans('form.lastName')
            ))
            ->add('mail', EmailType::class, array(
                'label' => $translator->trans('form.mail')
            ))
            ->add('birth', BirthdayType::class, array(
                'label' => $translator->trans('form.birth')
            ))
            ->add('save', SubmitType::class, array(
                'label' => $translator->trans('form.save')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_success');
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/admin/users", name="user_list")
     * @Template()
     */
    public function list()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        if (!$users) {
            throw $this->createNotFoundException(
                'No users found'
            );
        }

        return array('users' => $users);
    }
}
