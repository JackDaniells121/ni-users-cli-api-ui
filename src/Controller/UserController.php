<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Utils\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserManager $userManager
    )
    {}

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    // Alternate new user form
    #[Route('/user/add', name: 'user_add')]
    public function add(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $this->userManager->saveUser($user);

            return $this->redirectToRoute('app_user');
        }

        return $this->renderForm('user/new.html.twig', [
            'form' => $form,
        ]);
    }

    // REST API endpoint
    #[Route('/user/create', name: 'user_create')]
    public function create(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {

            $name = $request->get('name');
            $surname =$request->get('surname');
            $email = $request->get('email');
            $pesel = $request->get('pesel');
            $skills = $request->get('skills');

            // TODO add bad http request codes = change validation from exceptions to response

            $this->userManager->saveData(name: $name, surname: $surname, email: $email, pesel: $pesel, skills: $skills, source: 'REST');

            return new Response('User has been created ', Response::HTTP_OK);
        }

        return new Response('Bad request, only POST accepteds ', Response::HTTP_BAD_REQUEST);
    }
}
