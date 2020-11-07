<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Event\RegisterUserEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
    * @Route("/inscription", name="registration")
    */
    public function register(Request $request, 
        EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, 
        EventDispatcherInterface $dispatcher
    ) {
        $user = new User();

        $registrationForm = $this->createForm(RegistrationType::class, $user);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $activationToken = md5(uniqid());
            $user->setActivationToken($activationToken);

            $manager->persist($user);
            $manager->flush();

            $event = new RegisterUserEvent($user);
        
            $dispatcher->dispatch($event, RegisterUserEvent::NAME);
            
            return $this->redirectToRoute('home');
        }

        return $this->render('register/registration.html.twig', [
            'registrationForm' => $registrationForm->createView()
        ]);
    }

    /**
     * @Route("/activation/{activationToken}", name="activation")
     */
    public function activateAccount($activationToken, 
        UserRepository $userRepository, EntityManagerInterface $manager,
        Session $session
    ) {
        $user = $userRepository->findOneBy(['activationToken' => $activationToken]);

        if (!$user) {
            throw new HttpException(404, 'Cette page n\'éxiste pas');
        }

        $user->setActivationToken('');
        $manager->persist($user);
        $manager->flush();

        return $this->render('register/accountIsActivate.html.twig', [
            'user' => $user
        ]);
    }
}