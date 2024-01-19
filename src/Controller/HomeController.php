<?php

namespace App\Controller;

use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(Environment $twig, ConferenceRepository $conferenceRepository): Response
    {

        return new Response($twig->render('home/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }
}
