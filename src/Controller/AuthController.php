<?php


namespace App\Controller;


use App\Entity\User;
use App\Entity\Worker;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastLogin = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, [
            'email' => $lastLogin,
        ]);



        return $this->render('auth/login.html.twig',[
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles($user->getRole());
            $user->setCreated(new \DateTimeImmutable());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            if($user->getRole() === 'ROLE_WORKER')
            {
                $worker = new Worker();
                $worker->setUser($user);
                $em->persist($worker);
            }
            $em->flush();

            $this->addFlash('success', 'Registration successful!');

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'auth/registration.html.twig', array(
                'form' => $form->createView()
            )
        );
    }

}