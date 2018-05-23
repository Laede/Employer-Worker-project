<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Entity\Project;
use App\Form\WorkerType;
use App\Repository\ProjectRepository;
use App\Service\CV;
use App\Service\WorkerService;
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
    private $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    /**
     * @Route("/edit/", name="worker_edit", methods="GET|POST")
     */
    public function edit(Request $request, CV $cvUploader): Response
    {
        $user = $this->getUser();
        $worker = $this->workerService->getWorker($user);

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
    public function my_cv(CV $CV)
    {
        $user = $this->getUser();
        $worker = $this->workerService->getWorker($user);

        if($worker && $worker->getCv()) {
            $CV->show(
                $this->getParameter('cv_directory').'/'.$worker->getCv(),
                $user->getFullName().'.pdf'
            );
        } else {
            throw $this->createNotFoundException('CV Not Found');
        }
    }

    /**
     * @Route("/projects/", name="worker_projects", methods="GET")
     */
    public function projects(ProjectRepository $projectRepository): Response
    {
        if(!$this->workerService->cvUploaded($this->getUser())) {
            $this->addFlash('warning', 'Please upload your CV and select your skills first!');
            return $this->redirectToRoute('worker_edit');
        }

        return $this->render('project/index.html.twig', ['projects' => $projectRepository->findAvailable()]);
    }

    /**
     * @Route("/history/", name="worker_history", methods="GET")
     */
    public function history(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();

        if(!$this->workerService->cvUploaded($user)) {
            $this->addFlash('warning', 'Please upload your CV and select your skills first!');
            return $this->redirectToRoute('worker_edit');
        }

        return $this->render('project/index.html.twig', ['projects' => $projectRepository->findApplied($this->workerService->getWorker($user))]);
    }


    /**
     * @Route("/projects/{id}", name="worker_project_show", methods="GET")
     */
    public function showProject(Project $project): Response
    {
        if(!$this->workerService->cvUploaded($this->getUser())) {
            $this->addFlash('warning', 'Please upload your CV and select your skills first!');
            return $this->redirectToRoute('worker_edit');
        }

        return $this->render('project/show.html.twig', ['project' => $project]);
    }


    /**
     * @Route("/projects/{id}/apply", name="worker_project_apply", methods="GET")
     */
    public function applyProject(Project $project): Response
    {
        $this->denyAccessUnlessGranted('project_not_applied_yet', $project, 'Already applied');
        $this->denyAccessUnlessGranted('project_not_expired', $project, 'Project expired');
        $this->denyAccessUnlessGranted('project_not_full', $project, 'Project full');

        $user = $this->getUser();

        if(!$this->workerService->cvUploaded($user)) {
            $this->addFlash('warning', 'Please upload your CV and select your skills first!');
            return $this->redirectToRoute('worker_edit');
        }

        $apply = new Apply();
        $apply->setWorker($this->workerService->getWorker($user));
        $apply->setProject($project);
        $apply->setStatus('new');

        $em = $this->getDoctrine()->getManager();
        $em->persist($apply);
        $em->flush();

        $this->addFlash('success', 'Successfully applied! Now you can write a message.');

        return $this->redirectToRoute('worker_project_show',['id' => $project->getId()]);
    }




}
