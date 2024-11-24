<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    
    //#[Route('/homes', name: 'app_home')]
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'hello' => 'Hola mundo con Symfony'
        ]);
    }

    public function animales($nombre, $apellidos)
    {
        $title = 'Bienvenidos sean todos';
        $animales = array('agaposni', 'gato', 'perro', 'tortuga');
        $pajaros = array(
            'tipo' => 'loro', 
            'color' => 'rojo', 
            'edad' => 4,  
            'raza' => 'chiguagua', );

        return $this->render('home/animales.html.twig', [
            'title' => $title,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'animales' => $animales,
            'pajaros' => $pajaros
        ]);
    }

    public function redirigir() {
        /*return $this->redirectToRoute('animales', [
            'nombre' => 'Felix',
            'apellidos' => '55'
        ]);*/

        return $this->redirect('https://www.youtube.com/watch?v=gZM1WQKwpl0');
    }

}
