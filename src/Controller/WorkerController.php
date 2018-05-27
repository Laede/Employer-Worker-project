<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Entity\Messages;
use App\Entity\Project;
use App\Form\MessagesType;
use App\Form\WorkerType;
use App\Repository\ProjectRepository;
use App\Repository\SkillsRepository;
use App\Service\CV;
use App\Service\MessagesService;
use App\Service\SkillsService;
use App\Service\WorkerService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function edit(Request $request, CV $cvUploader, SkillsService $skillsService): Response
    {
        $user = $this->getUser();
        $worker = $this->workerService->getWorker($user);

        $oldFile = $worker->getCv();
        $oldSkills = $worker->getSkillsString(null);

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

            $em = $this->getDoctrine()->getManager();
            if($oldSkills !== $worker->getSkillsString(null)){
                $skillsService->proceedSkills($worker, $em);
            }

            $em->flush();

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
            return $CV->show(
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
    public function projects(ProjectRepository $projectRepository, Request $request, SkillsRepository $skillsRepository): Response
    {
        $user = $this->getUser();
        $skills = $skillsRepository->findBy([],['name'=>'asc']);
        $filters = $this->workerService->proceedFilters($request, $user);
        $projects = $projectRepository->findByFilters($filters);

        if(!$this->workerService->cvUploaded($user)) {
            $this->addFlash('warning', 'Please upload your CV and select your skills first!');
            return $this->redirectToRoute('worker_edit');
        }

        return $this->render('project/index.html.twig', ['projects' => $projects, 'filters' => $filters, 'skills' => $skills]);
    }

    /**
     * @Route("/history/", name="worker_history", methods="GET")
     */
    public function history(): Response
    {
        $user = $this->getUser();
        $worker = $this->workerService->getWorker($user);
        if(!$worker->getCv()) {
            $this->addFlash('warning', 'Please upload your CV and select your skills first!');
            return $this->redirectToRoute('worker_edit');
        }

        return $this->render('worker/history.html.twig', ['applies' => $worker->getApplies()]);
    }

    /**
     * @Route("/apply/{id}", name="worker_apply", methods="GET|POST")
     */
    public function apply(Request $request, Apply $apply, MessagesService $messagesService): Response
    {
        $this->denyAccessUnlessGranted('view_apply', $apply);

        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messagesService->saveMessage($message, $this->getUser(), $apply);
            return $this->redirectToRoute('worker_apply',['id' => $apply->getId()]);
        }

        return $this->render('project/show.html.twig', [
            'project' => $apply->getProject(),
            'apply' => $apply,
            'form' => $form->createView(),
        ]);
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
