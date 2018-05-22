<?php

namespace App\Controller;

use App\Entity\Skills;
use App\Entity\User;
use App\Entity\Worker;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function show(WorkerRepository $repository): Response
    {
        $user = $this->getUser();
        $worker = $repository->findOneBy(['user' => $user]);
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'worker' => $worker,
        ]);
    }

    /**
     * @Route("/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Profile updated!');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
