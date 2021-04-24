<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class SercurityController extends AbstractController
{
  /**
    * @Route("/inscription", name="sercurity_registration")
     */
     public function registration(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder ) {
          $user = new User();
           $form = $this->createForm(RegistrationType::class, $user);
            $form->handleRequest($request);
             if($form->isSubmitted() && $form->isValid()) {
                $hash = $encoder->encodePassword($user,$user->getPassword());
                 $user->setPassword($hash);
                  $em->persist($user);
                   $em->flush();

                }
                return $this->rederictToRoute('security_login');
}
/**
  * @Route("/connexion",name="security_login")
 */
 public function login(AuthenticationUtils $authenticationUtils) { 
     // get the login error if there is one
      $error = $authenticationUtils->getLastAuthenticationError();
       // last username entered by the user 
       $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('sercurity/login.html.twig', ['lastUsername'=>$lastUsername,'error' => $error]);
 }
 /**
   * @Route("/deconnexion",name="security_logout") 
   */
   public function logout() { }
}