<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Entity\Messages;
use App\Entity\Project;
use App\Form\MessagesType;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\ApplyService;
use App\Service\MessagesService;
use App\Service\CV;
use App\Service\SkillsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/", name="project_index", methods="GET")
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', ['projects' => $projectRepository->findBy(['user' => $this->getUser()])]);
    }

    /**
     * @Route("/new", name="project_new", methods="GET|POST")
     */
    public function new(Request $request, SkillsService $skillsService): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $project->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $skillsService->proceedSkills($project, $em);

            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods="GET")
     */
    public function show(Project $project, ApplyService $applyService): Response
    {
        $this->denyAccessUnlessGranted('is_project_author', $project);
        $applies = $applyService->groupApplies($project->getApplies());
        return $this->render('project/show.html.twig', ['project' => $project, 'applies' => $applies]);
    }

    /**
     * @Route("/{project}/apply/{id}", name="apply_show", methods="GET|POST")
     */
    public function apply(Request $request, Apply $apply, MessagesService $messagesService, ApplyService $applyService): Response
    {
        $this->denyAccessUnlessGranted('view_apply', $apply);

        if(!is_null($request->get('status'))) {
            $applyService->changeStatus($apply, $request->get('status'));
            return $this->redirectToRoute('apply_show', ['id' => $apply->getId(), 'project' => $apply->getProject()->getId()]);
        }

        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messagesService->saveMessage($message, $this->getUser(), $apply);
            return $this->redirectToRoute('apply_show', ['id' => $apply->getId(), 'project' => $apply->getProject()->getId()]);
        }

        return $this->render('project/apply.html.twig', [
            'apply' => $apply,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cv/{id}", name="apply_cv_show", methods="GET")
     */
    public function getCV(Apply $apply, CV $CV): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted('view_apply_cv', $apply);
        return $CV->show(
            $this->getParameter('cv_directory').'/'.$apply->getWorker()->getCv(),
            $apply->getWorker()->getUser()->getFullName().'.pdf'
        );
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods="GET|POST")
     */
    public function edit(Request $request, Project $project, SkillsService $skillsService): Response
    {
        $this->denyAccessUnlessGranted('is_project_author', $project);
        $oldSkills = $project->getSkillsString(null);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($oldSkills !== $project->getSkillsString(null)) {
                $skillsService->proceedSkills($project, $em);
            }

            $em->flush();

            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods="DELETE")
     */
    public function delete(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted('is_project_author',$project);
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
