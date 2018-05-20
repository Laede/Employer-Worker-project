<?php

namespace App\Controller;

use App\Entity\Worker;
use App\Form\WorkerType;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/worker")
 */
class WorkerController extends Controller
{

    /**
     * @Route("/", name="worker_show", methods="GET")
     */
    public function show(WorkerRepository $repository): Response
    {
        $worker = $repository->findOneBy(['user' => $this->getUser()]);
        return $this->render('worker/show.html.twig', ['worker' => $worker]);
    }

    /**
     * @Route("/{id}/edit", name="worker_edit", methods="GET|POST")
     */
    public function edit(Request $request, Worker $worker): Response
    {
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()
                ->flush();

            return $this->redirectToRoute('worker_edit', ['id' => $worker->getId()]);
        }

        return $this->render('worker/edit.html.twig', [
            'worker' => $worker,
            'form' => $form->createView(),
        ]);
    }


}
