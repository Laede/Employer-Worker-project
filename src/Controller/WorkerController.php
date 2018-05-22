<?php

namespace App\Controller;

use App\Entity\Worker;
use App\Form\WorkerType;
use App\Repository\WorkerRepository;
use App\Service\CV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/worker")
 */
class WorkerController extends Controller
{

    /**
     * @Route("/", name="worker_edit", methods="GET|POST")
     */
    public function edit(Request $request, WorkerRepository $repository, CV $cvUploader): Response
    {
        $user = $this->getUser();
        $worker = $repository->findOneBy(['user' => $user]);

        $oldFile = $worker->getCv();
        if($oldFile) {
            $worker->setCv(
                new File($this->getParameter('cv_directory').'/'.$worker->getCv())
            );
        }
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($file = $worker->getCV()) {
                $fileName = $cvUploader->upload($file);
                if($oldFile) {
                    unlink($this->getParameter('cv_directory').'/'.$oldFile);
                }
                $worker->setCv($fileName);
            } else {
                $worker->setCv($oldFile);
            }

            $this->getDoctrine()->getManager()
                ->flush();

            $this->addFlash('success', 'Profile updated!');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('worker/edit.html.twig', [
            'worker' => $worker,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/my-cv", name="my_cv", methods="GET|POST")
     */
    public function my_cv(CV $CV, WorkerRepository $repository)
    {
        $user = $this->getUser();
        $worker = $repository->findOneBy(['user' => $user]);

        if($worker && $worker->getCv()) {
            $CV->show(
                $this->getParameter('cv_directory').'/'.$worker->getCv(),
                $user->getFullName().'.pdf'
            );
        } else {
            throw $this->createNotFoundException('CV Not Found');
        }
    }


}
