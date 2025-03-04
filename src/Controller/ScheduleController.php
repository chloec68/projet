<?php

namespace App\Controller;

use App\Repository\EstablishmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ScheduleController extends AbstractController
{
    #[Route('/schedule', name: 'app_schedule')]
     public function index(EstablishmentRepository $establishmentRepository):Response
        {   
           // pour afficher les horaires
        }

    #[Route('/schedule/update/{establishment}', name: 'app_schedule_update')]
    public function updateSchedule():Response
        {
            //pour modifier les horaires d'un établissement
        }

    #[Route('/schedule/delete/{establishment}', name: 'app_schedule_delete')]
    public function deleteSchedule():Response
    {
        //pour supprimer les horaires d'un établissement
    }

    #[Route('/schedule/create/{establishment}', name: 'app_schedule_create')]
    public function createSchedule():Response
    {
        //pour créer les horaires d'un établissement après suppression
    }
}