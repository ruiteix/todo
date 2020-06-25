<?php

/**
 * (c) Adrien PIERRARD
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Show the Users list.
     *
     * @return Response
     *
     * @Route("/users", name="user_list")
     */
    public function usersList(): Response
    {
        $users = $this
            ->getDoctrine()
            ->getRepository('App:User')
            ->findAll()
        ;

        return $this->render(
            'user/list.html.twig',
            ['users' => $users]
        );
    }

    /**
     * Add a User
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     *
     * @Route("/users/create", name="user_create")
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($password);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur a bien été ajouté."
            );

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Update User information.
     *
     * @param User                         $user
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     *
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editUser(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "L'utilisateur a bien été modifié"
            );

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/edit.html.twig',
            ['form' => $form->createView(), 'user' => $user]
        );
    }
}
