<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Animal;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Form\AnimalType;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;

class AnimalController extends AbstractController
{

    public function validarEmail($email){
        
        $validator = Validation::createValidator();
        $errores = $validator->validate($email, [
            new Email()
        ]);

        if(count($errores) != 0){
            echo "El email no se ha validado correctamente";
        }else{
            echo "El email se ha validado correctamente";
        }

        die();
    }

    public function __construct(private ManagerRegistry $doctrine) {}

    public function crearAnimal(Request $request){
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->doctrine->getManager();
            $em->persist($animal);
            $em->flush();
            
            // Sesopm flash
            $session = new Session();
            $session->getFlashBag()->add('message', 'Animal creado');

            return $this->redirectToRoute('crear_animal');
        }

        return $this->render('animal/crear-animal.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function index(): Response
    {

        $em = $this->doctrine->getManager();

        $animal_repo = $this->doctrine->getRepository(Animal::class);

        $animales = $animal_repo->findAll();

        $animal = $animal_repo->findBy([
            'raza' => 'Titi'
        ], [
            'id' => 'DESC'
        ]);

        //var_dump($animal);
        
        //Query Builder
        $qb = $animal_repo->createQueryBuilder('a')
                          //->andWhere("a.raza = :raza")
                          //->setParameter('raza', 'Americano')
                          ->orderBy('a.id', 'DESC')
                          ->getQuery();
        $resulset = $qb->execute();

        //var_dump($resulset);

        //DQL
        $dql = "SELECT a FROM App\Entity\Animal a ORDER BY a.id DESC";
        $query = $em->createQuery($dql);
        $resulset = $query->execute();

        //var_dump($resulset);

        //Repositorio
        $animals = $animal_repo->getAnimalsOrderId('DESC');

        var_dump($animals);


        return $this->render('animal/index.html.twig', [
            'controller_name' => 'AnimalController',
            'animales' => $animales
        ]);
    }

    public function save(){
        
        $entityManager = $this->doctrine->getManager();

        $animal = new Animal();
        $animal->setTipo('Mono');
        $animal->setColor('Verde');
        $animal->setRaza('Titi');

        $entityManager->persist($animal);
        $entityManager->flush();

        return new Response('El animal guardado tiene el id: '.$animal->getId());
        
    }

    public function animal(Animal $animal){

        /*
        $animal_repo = $this->doctrine->getRepository(Animal::class);

        $animal = $animal_repo->find($id);
        */
        if(!$animal){
            $message = 'El animal no existe';
        }else{
            $message = 'Tu animal elegido es: '.$animal->getTipo().' - '.$animal->getRaza();
        }

        return new Response($message);
    }

    public function update($id){
        $em =  $this->doctrine->getManager();

        $animal_repo = $em->getRepository(Animal::class);

        $animal = $animal_repo->find($id);

        if(!$animal){
            $message = 'El animal no existe en la bbdd';
        }else{
            $animal->setTipo("Perro $id");
            $animal->setColor('rojo');

            $em->persist($animal);
            $em->flush();

            $message = 'Has actualizado el animal '.$animal->getId();
        }

        return new Response($message);
    }

    public function delete(Animal $animal){
        $em =  $this->doctrine->getManager();
        
        if($animal && is_object($animal)){
            $em->remove($animal);
            $em->flush();

            $message = "Animal borrado correctamente";
        }else{
            $message = "Animal no encontrado";
        }
        return new Response($message);
    }

}
