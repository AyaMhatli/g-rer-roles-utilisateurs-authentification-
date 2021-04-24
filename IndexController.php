<?php 

namespace App\Controller;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\CategorySearch; 
use App\Form\CategorySearchType;
use App\Entity\PropretySearch;
use App\Form\PropretySearchType;
use App\Form\PriceSearchType;
use App\Entity\PriceSearch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Response; 
Use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController {
     /**
       *@Route("/",name="article_list")
        */ 
        public function home(Request $request)
         { 
          $propretySearch = new PropretySearch();
          $form = $this->createForm(PropretySearchType::class,$propretySearch);
          $form->handleRequest($request);
          $articles= [];
          if($form->isSubmitted() && $form->isValid()) {
            $nom = $propretySearch->getNom();
            if ($nom!=""){
               $articles= $this->getDoctrine()->getRepository(Article::class)->findBy(['nom' => $nom] );
            }
            else{
            $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
  }}
  return $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
}

     /**
       * @Route("/article/save") 
          */ 
          public function save()
           {
              $entityManager = $this->getDoctrine()->getManager(); 
              $article = new Article();
               $article->setNom('Article 3'); 
               $article->setPrix(1900); 
               $entityManager->persist($article); 
               $entityManager->flush();
                return new Response('Article enregisté avec id '.$article->getId());
               }

             /** 
              * @IsGranted("ROLE_EDITOR") 
              * @Route("/article/new", name="new_article") 
              * Method({"GET", "POST"}) 
              */
               public function new(Request $request) { 
                 $article = new Article(); $form = $this->createForm(ArticleType::class,$article);
                 $form->handleRequest($request);
                  if($form->isSubmitted() && $form->isValid())
                   { 
                     $article = $form->getData();
                      $entityManager = $this->getDoctrine()->getManager();
                       $entityManager->persist($article);
                        $entityManager->flush();
                         return $this->redirectToRoute('article_list');
                        } 
                        return $this->render('articles/new.html.twig',['form' => $form->createView()]);
    }
  /** 
   * @Route("/article/{id}", name="article_show") 
   */ 
  public function show($id) { 
    $article = $this->getDoctrine()->getRepository(Article::class) ->find($id); 
    return $this->render('articles/show.html.twig', array('article' => $article));
   }

   /** 
    * @IsGranted("ROLE_EDITOR") 
    * @Route("/article/edit/{id}", name="edit_article") 
    * Method({"GET", "POST"}) 
    */
     public function edit(Request $request, $id) { 
       $article = new Article(); 
       $article = $this->getDoctrine()->getRepository(Article::class)->find($id); 
       $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request); 
        if($form->isSubmitted() && $form->isValid()) {
           $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush(); 
            return $this->redirectToRoute('article_list');
           }
            return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
 } 

/** 
 * @IsGranted("ROLE_EDITOR") 
 * @Route("/article/delete/{id}",name="delete_article") 
 * @Method({"DELETE"})
  */
   public function delete(Request $request, $id) 
   {
      $article = $this->getDoctrine()->getRepository(Article::class)->find($id); 
      $entityManager = $this->getDoctrine()->getManager(); 
      $entityManager->remove($article);
       $entityManager->flush();
        $response = new Response();
         $response->send();
          return $this->redirectToRoute('article_list'); 
        }

        /** 
         * @Route("/category/newCat", name="new_category") 
         * Method({"GET", "POST"}) 
         */
         public function newCategory(Request $request) {
            $category = new Category();
             $form = $this->createForm(CategoryType::class,$category); 
             $form->handleRequest($request); 
          if($form->isSubmitted() && $form->isValid()) { 
            $article = $form->getData(); 
            $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($category); 
             $entityManager->flush();
             } 
             return $this->render('articles/newCategory.html.twig',['form'=>$form->createView()]);
}
 /**
   * @Route("/art_cat/", name="article_par_cat")
   * Method({"GET", "POST"}) 
   */
  public function articlesParCategorie(Request $request) 
  {
     $categorySearch = new CategorySearch(); 
     $form = $this->createForm(CategorySearchType::class,$categorySearch); 
     $form->handleRequest($request);
      $articles= [];
      if($form->isSubmitted() && $form->isValid()) 
      {
         $category = $categorySearch->getCategory();
          
         if ($category!="")
           $articles= $category->getArticles(); 
          
           else 
           $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
           }
            return $this->render('articles/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]);
           }

  /**
    * @Route("/art_prix/", name="article_par_prix") 
    * Method({"GET"}) 
    */ 
    public function articlesParPrix(Request $request)
     {
        $priceSearch = new PriceSearch();
         $form = $this->createForm(PriceSearchType::class,$priceSearch); 
         $form->handleRequest($request);
          $articles= [];
            if($form->isSubmitted() && $form->isValid()) 
            {
               $minPrice = $priceSearch->getMinPrice(); 
               $maxPrice = $priceSearch->getMaxPrice();
                $articles= $this->getDoctrine()-> getRepository(Article::class)->findByPriceRange($minPrice,$maxPrice);
               }
                return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);

              }
            
            }
