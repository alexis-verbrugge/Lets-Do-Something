<?php

namespace LDS\PlatformBundle\Controller;

use LDS\PlatformBundle\Entity\Etablissement;
use LDS\PlatformBundle\Entity\Professionnel;
use LDS\PlatformBundle\Entity\Evenement;
use LDS\PlatformBundle\Entity\MessageAdmin;
use LDS\PlatformBundle\Entity\CommentaireEtablissement;
use LDS\PlatformBundle\Entity\Particulier;
use LDS\PlatformBundle\Entity\Tag;
use LDS\PlatformBundle\Entity\VillesFranceFree;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * AdminController
 *
 * @author Boutaljante, Ruiz Ramirez, Herve, Verbrugge
 *
 * Ce controller a pour but de gerer les action de l'administrateur du site LDS
 * Il a la possibilité de voir la liste des messages envoyés par les membres du site, il peut y répondre ou les supprimer
 * Il a aussi la possibilité de voir toutes les demandes d'etablissement, de les valider ou de les refuser
 * Meme chose pour les evenements ajoutés pour un etablissement
 * Il peut voir la liste de tous les particuliers ainsi que les commentaires délivrés, en fonction 
 * de cela il peut alors les bannir directement du site
 *
 */

/**
*
**/

class AdminController extends Controller {

  /**
  * Accueil
  *
  * Fonction permettant d'afficher l'accueil de l'interface administrateur
  *
  **/
  public function accueilAction() {
     return $this->render('LDSPlatformBundle:Admin:accueil.html.twig');
  }

  /**
  * Liste Message
  *
  * Fonction permettant a l'admin de recuperer la liste des messages qui lui sont envoyés
  *
  **/
	public function listeMessageAction($page =1, Request $request) {
    $aucun_message='';
    $repository = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('LDSPlatformBundle:MessageAdmin');

    $this->get('session')->remove('contenu_mail');

    $listeMessage = $repository->trouverMessageAdmin($page, 5);
    $pas_message = $repository->countMessage();
    $count = ceil(count($listeMessage));

    //on affiche un message d'indication s'il n'y a aucun message
    if ($pas_message==true) {
      $aucun_message="Vous n'avez aucun message";
    }

    $defaultData = array('message' => 'Type your message here');
    $form = $this->createFormBuilder($defaultData)
        ->add('message', TextareaType::class)
        ->add('envoyer', SubmitType::class)
        ->getForm();


     //mise en place d'un objet paginator pour mieux agencer la liste
     $pagination = array('page' => $page,'route' => 'lds_platform_admin_liste_message',
      'pages_count' => ceil(count($listeMessage) / 5),
      'route_params' => array());

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $mail=$this->get('session')->get('email_reponse');
         $data = $form->getData();
         $message = (new \Swift_Message("Let's Do Something: Reponse à votre question"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($mail) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
        'LDSPlatformBundle:Email:EmailReponseAdmin.html.twig',
        array('data' => $data['message'],)
      ),
      'text/html'
    );
     $this->get('mailer')->send($message);  

      return $this->redirectToRoute('lds_platform_admin_liste_message', array('page' => 1));
      }

    return $this->render('LDSPlatformBundle:Admin:listeMessage.html.twig', array(
     'listeMessage' => $listeMessage,
     'aucun_message' => $aucun_message,
     'pagination' => $pagination,
     'count' => $count,
     'form' => $form->createView(),

   ));
  }

	/**
  * Liste Demande Etablissement
  *
  * Fonction permettant a l'admin de voir la liste des demandes d'etablissement
  *
  **/
  public function listeDemandeEtablissementAction($page = 1) {
    $aucun_etablissement='';
    $repository = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('LDSPlatformBundle:Etablissement');

    $listeEtablissement = $repository->trouverEtablissementNonValide($page, 5);
    $pas_etablissement = $repository->countEtablissementNonValide();
    $count = ceil(count($listeEtablissement));

    if ($pas_etablissement==true) {
      $aucun_etablissement="Aucune demande d'etablissements";
    }
     //mise en place d'un objet paginator pour mieux agencer la liste
     $pagination = array('page' => $page,'route' => 'lds_platform_admin_liste_demande_etablissement',
      'pages_count' => ceil(count($listeEtablissement) / 5),
      'route_params' => array());

    return $this->render('LDSPlatformBundle:Admin:listeDemandeEtablissement.html.twig', array(
     'listeEtablissement' => $listeEtablissement,
     'aucun_etablissement' => $aucun_etablissement,
     'pagination' => $pagination,
     'count' => $count,
   ));
  }


  /**
  * Liste Demande Tag
  *
  * fonction qui permet a l'admin de voir toutes les demandes de tags
  *
  **/
  public function listeDemandeTagAction($page =1) {

    $aucun_tag='';

    $repository = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('LDSPlatformBundle:Tag');
    $listeTag = $repository->trouverTagInvalide($page,5);

    $pas_tag = $repository->count_tag();
    $count = ceil(count($listeTag));

    if ($pas_tag==true) {
      $aucun_tag="Aucune demande de tags";
    }

    $pagination = array('page' => $page,'route' => 'lds_platform_admin_liste_demande_tag',
      'pages_count' => ceil(count($listeTag) / 5),
      'route_params' => array());

    return $this->render('LDSPlatformBundle:Admin:listeDemandeTag.html.twig', array(
     'listeTag' => $listeTag,
     'aucun_tag' => $aucun_tag,
     'pagination' => $pagination,
     'count' => $count,
   ));
  }

  /**
  * Liste Demande Evenement
  *
  * fonction qui permet a l'admin de voir toutes les demandes pour des evenemeets
  *
  **/
  public function listeDemandeEvenementAction($page =1) {

    $aucun_evenement='';

    $repository = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('LDSPlatformBundle:Evenement');

    $listeEvenement = $repository->trouverEvenementInvalide($page,5);
    $pas_evenement = ceil(count($listeEvenement));

      if ($pas_evenement==0) {
        $aucun_evenement="Aucune demande d'evenements";
      }

      $pagination = array('page' => $page,'route' => 'lds_platform_admin_liste_demande_evenement',
        'pages_count' => ceil(count($listeEvenement) / 5),
        'route_params' => array());

      return $this->render('LDSPlatformBundle:Admin:listeDemandeEvenement.html.twig', array(
        'listeEvenement' => $listeEvenement,
        'aucun_evenement' => $aucun_evenement,
        'pagination' => $pagination,
        'count' => $pas_evenement,
      ));
    }

  /**
  * Accepter Evenement
  *
  * fonction qui permet a l'admin d'accepter et d'envoyer la confirmation email
  *
  **/
    public function accepterEvenementAction($id) {
       $em = $this->getDoctrine()->getManager();
      $evenement = $em->getRepository(Evenement::class)->findOneById($id);

      //si on ne trouve pas cet evenement on creer une exception
      if (!$evenement) {
        throw $this->createNotFoundException(
          "Pas d'evenements avec ".$id
        );
      }

      //l'evenement devient valide (booleen)
      $evenement->setValide(true);
      $em->flush();

       //creation du message avec son titre
     $message = (new \Swift_Message("Let's Do Something: Acceptation de votre evenement"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($evenement->getEtablissement()->getEmail()) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
        'LDSPlatformBundle:Email:EmailAccepterEvenement.html.twig',
        array('evenement' => $evenement,)
      ),
      'text/html'
    );

     $this->get('mailer')->send($message);      

       return $this->redirectToRoute('lds_platform_admin_liste_demande_evenement', array('page' => 1));
    }


  /**
  * Refuser Evenement
  *
  * fonction qui permet a l'admin de refuser un etablissement et d'annoncer la mauvaise nouvelle par mail
  *
  **/
    public function refuserEvenementAction($id) {
     $em = $this->getDoctrine()->getManager();
      $evenement = $em->getRepository(Evenement::class)->findOneById($id);

      if (!$evenement) {
        throw $this->createNotFoundException(
          "Pas d'evenements avec ".$id
        );
      }
    $em->remove($evenement);
    $em->flush();

     //creation du message avec son titre
     $message = (new \Swift_Message("Let's Do Something: Refus de votre Evenement"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($evenement->getEtablissement()->getEmail()) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
        'LDSPlatformBundle:Email:EmailRefuserEvenement.html.twig',
        array('evenement' => $evenement,)
      ),
      'text/html'
    );

     $this->get('mailer')->send($message);      
      return $this->redirectToRoute('lds_platform_admin_liste_demande_evenement', array('page' => 1));
    }


  /**
  * Liste Etablissement
  *
  * fonction qui permet a l'admin de consuleter la liste de tous les etablissements
  **/
    public function listeEtablissementAction($page=1, Request $request) {

    $session = $this->get('session');
    $etablissement= new Etablissement();
    $data='';
    $ville_valide=true;

    $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
    $repository2 = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');

      $listeEtablissement=$repository->trouverParNomPagination($data, $page, 8);


      $pagination = array('page' => $page,'route' => 'lds_platform_admin_etablissement',
        'pages_count' => ceil(abs(count($listeEtablissement) / 8)),
        'route_params' => array());

         $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $etablissement);
         $formBuilder
    ->add('nomEtablissement',    TextType::class, array('required' => false))
    ->add('ville',   TextType::class, array('required' => false))
    ->add('chercher',  SubmitType::class);

    $form = $formBuilder->getForm();



     if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();


     $ville=null;
      if ($etablissement->getVille()!='') {
        $ville = $repository2->findOneByVilleNom($etablissement->getVille());
        if ($ville==null) {
          $ville_valide=false;
        }
      }

  $listeEtablissement=$repository->trouverParNomPagination($etablissement->getNomEtablissement(), $page, 8);

/*  $tmp=array();
  $count=0;
  if ($ville_valide) {
    foreach($listeEtablissement as $e) {
      if ($e->getVille()==$ville) {
        $tmp[$count]=$e;
        $count++;
      }
    }
    $listeEtablissement=$tmp;
  }*/

  $pagination = array('page' => $page,'route' => 'lds_platform_admin_etablissement',
        'pages_count' => ceil(abs(count($listeEtablissement) / 8)),
        'route_params' => array());
    }

          return $this->render('LDSPlatformBundle:Admin:listeEtablissement.html.twig', array(
      'pagination' => $pagination,
      'listeEtablissement' => $listeEtablissement,
      'form' => $form->createView(),
    ));

    }


    public function supprimerEtablissementAction($siret) {
      $em=$this
      ->getDoctrine()
      ->getManager();
      $repository = $em->getRepository('LDSPlatformBundle:Etablissement');
      $etablissement=$repository->findOneBySiret($siret);
      $em->remove($etablissement);
      $em->flush();

       //creation du message avec son titre
     $message = (new \Swift_Message("Let's Do Something: Suppression de votre etablissement"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($etablissement->getEmail()) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
        'LDSPlatformBundle:Email:EmailSupprimerEtablissement.html.twig',
        array('etablissement' => $etablissement,)
      ),
      'text/html'
    );

     $this->get('mailer')->send($message);      

       return $this->redirectToRoute('lds_platform_admin_etablissement', array('page' => 1));
    }


  /**
  * Refuser Evenement
  *
  * fonction qui permet a l'admin de consulter la liste de tous les comptes particuliers
  **/
    public function listeParticulierAction($page =1) {

      $repository = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('LDSPlatformBundle:Particulier');

      $listeParticulier = $repository->trouverParticulier($page,5);
      $count =  ceil(count($listeParticulier));
      $pagination = array('page' => $page,'route' => 'lds_platform_admin_liste_particulier',
        'pages_count' => ceil(abs(count($listeParticulier) / 5)),
        'route_params' => array());

      return $this->render('LDSPlatformBundle:Admin:listeParticulier.html.twig', array(
       'listeParticulier' => $listeParticulier,
       'pagination' => $pagination,
       'count' => $count,
     ));
}

  /**
  * Detail Particulier
  *
  * fonction qui permet a l'admin de consulter les details concernant un membre (commentaires, infos de base)
  **/
    public function detailParticulierAction($login) {
      $aucun_commentaire='';
      $em = $this->getDoctrine()->getManager();
      $particulier = $em->getRepository(Particulier::class)->findOneByLogin($login);

      if (!$particulier) {
        throw $this->createNotFoundException(
          "Pas de particulier ".$login
        );
      }

      $em2 = $this->getDoctrine()->getManager();
      $listeCommentaire = $em2->getRepository(CommentaireEtablissement::class)->findByParticulier($particulier);
      $pas_commentaire = ceil(count($listeCommentaire));
      if ($pas_commentaire==0) {
        $aucun_commentaire="Ce particulier n'a posté aucun commentaire";
      }
      return $this->render('LDSPlatformBundle:Admin:detailParticulier.html.twig', array(
       'particulier' => $particulier,
       'listeCommentaire' => $listeCommentaire,
       'aucun_commentaire' => $aucun_commentaire,
       'count' => $pas_commentaire,
     ));
    }

  /**
  * Bannir Particulier
  *
  * fonction qui permet a l'admin de bannir un particulier du site en faisant passer son etat a false 
  **/
    public function bannirParticulierAction($login) {
       $em = $this->getDoctrine()->getManager();
       $repository=$em->getRepository(Particulier::class);
       $particulier=$repository->findOneByLogin($login);
       $particulier->setValide(false);
       $em->flush();

        //creation du message avec son titre
     $message = (new \Swift_Message("Let's Do Something: Vous avez été banni !"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($particulier->getEmail()) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
        'LDSPlatformBundle:Email:EmailBannirParticulier.html.twig',
        array('particulier' => $particulier,)
      ),
      'text/html'
    );

     $this->get('mailer')->send($message);      
       return $this->redirectToRoute('lds_platform_admin_liste_particulier', array('page' => 1));


    }


    public function listeProfessionnelAction() {

    }


  /**
  * Accepter etablissement
  *
  * fonction qui perment a l'admin d'accepter un etablissement et d'envoyer un mail au demandeur
  **/
    public function accepterEtablissementAction ($siret) {
      $em = $this->getDoctrine()->getManager();
      $etablissement = $em->getRepository(Etablissement::class)->findOneBySiret($siret);

      if (!$etablissement) {
        throw $this->createNotFoundException(
          "Pas d'etablissements avec ".$siret
        );
      }

      $etablissement->setValide(true);
      $em->flush();


      $em2 = $this->getDoctrine()->getManager();
      $professionnel = $em2->getRepository(Professionnel::class)->findOneByLogin($etablissement->getProprietaire());

      if ($professionnel->getValide()==false) {
       $professionnel->setValide(true);
       $em->flush();
     }

     //creation du message avec son titre
     $message = (new \Swift_Message("Let's Do Something: Acceptation de votre etablissement"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($etablissement->getEmail()) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
        'LDSPlatformBundle:Email:EmailAccepterEtablissement.html.twig',
        array('proprietaire' => $professionnel,
         'etablissement' => $etablissement,)
      ),
      'text/html'
    );

     $this->get('mailer')->send($message);       

     return $this->redirectToRoute('lds_platform_admin_liste_demande_etablissement', array('page' => 1));
   }


  /**
  * Refuser etablissement
  *
  * fonction qui perment a l'admin de refuser un etablissement et d'envoyer un mail pour annoncer la mauvaise nouvelle 
  **/
   public function refuserEtablissementAction ($siret) {
    $em = $this->getDoctrine()->getManager();
    $etablissement = $em->getRepository(Etablissement::class)->findOneBySiret($siret);

    if (!$etablissement) {
      throw $this->createNotFoundException(
        "Pas d'etablissements avec ".$siret
      );
    }

    $em->remove($etablissement);
    $em->flush();

    $em2 = $this->getDoctrine()->getManager();
    $professionnel = $em2->getRepository(Professionnel::class)->findOneByLogin($etablissement->getProprietaire());

    if ($professionnel->getValide()==false) {
    	$em->remove($professionnel);
     $em->flush();
   }
   echo $etablissement->getEmail();
   $message = (new \Swift_Message("Let's Do Something: Refus de votre etablissement"))
   ->setFrom('admin@lds.com')
   ->setTo($etablissement->getEmail())
   ->setBody(
    $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
      'LDSPlatformBundle:Email:EmailRefuserEtablissement.html.twig',
      array('proprietaire' => $professionnel,
       'etablissement' => $etablissement,)
    ),
    'text/html'
  );

   $this->get('mailer')->send($message);

   return $this->redirectToRoute('lds_platform_admin_liste_demande_etablissement', array('page' => 1));
 }



  /**
  * Accepter Tag
  *
  * fonction qui perment a l'admin d'accepter un tag et d'envoyer un mail pour le prevenir 
  **/
 public function accepterTagAction ($id) {
  $em = $this->getDoctrine()->getManager();
  $tag = $em->getRepository(Tag::class)->findOneById($id);

  if (!$tag) {
    throw $this->createNotFoundException(
     "Pas de tag avec ".$id
   );
  }

  $tag->setValide(true);
  $em->flush();

  $em2 = $this->getDoctrine()->getManager();
  $professionnel = $em2->getRepository(Professionnel::class)->findOneByLogin($tag->getProprietaire());

  echo $professionnel->getEmail();
  $message = (new \Swift_Message("Let's Do Something: Acceptation de votre demande de tag"))
  ->setFrom('admin@lds.com')
  ->setTo($professionnel->getEmail())
  ->setBody(
    $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
      'LDSPlatformBundle:Email:EmailAccepterTag.html.twig',
      array('proprietaire' => $professionnel,
       'tag' => $tag,)
    ),
    'text/html'
  );

  $this->get('mailer')->send($message);

  return $this->redirectToRoute('lds_platform_admin_liste_demande_tag', array('page' => 1));
}



  /**
  * Refuser Tag
  *
  * fonction qui perment a l'admin de refuser un tag et de prevenir le demandeur
  **/
public function refuserTagAction ($id) {
  $em = $this->getDoctrine()->getManager();
  $tag = $em->getRepository(Tag::class)->findOneById($id);

  if (!$tag) {
    throw $this->createNotFoundException(
     "Pas de tag avec ".$id
   );
  }

  $em->remove($tag);
  $em->flush();

  $em2 = $this->getDoctrine()->getManager();
  $professionnel = $em2->getRepository(Professionnel::class)->findOneByLogin($tag->getProprietaire());

  echo $professionnel->getEmail();
  $message = (new \Swift_Message("Let's Do Something: Refus de votre demande de tag"))
  ->setFrom('admin@lds.com')
  ->setTo($professionnel->getEmail())
  ->setBody(
    $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
      'LDSPlatformBundle:Email:EmailRefuserTag.html.twig',
      array('proprietaire' => $professionnel,
       'tag' => $tag,)
    ),
    'text/html'
  );

  $this->get('mailer')->send($message);

  return $this->redirectToRoute('lds_platform_admin_liste_demande_tag', array('page' => 1));
}



  /**
  * Repondre Message
  *
  * fonction qui perment a l'admin de repondre a un message qui lui a ete envoyer et envoie celui ci au mail du demandeur 
  **/
public function repondreMessageAction($id) {
  $em = $this->getDoctrine()->getManager();
  $messageAdmin = $em->getRepository(MessageAdmin::class)->findOneById($id);
  $pro = $em->getRepository(Professionnel::class)->findOneByLogin($messageAdmin->getUser());
  $mail=$pro->getEmail();
  $contenu=$this->get('session')->get('contenu_mail');
  if ($contenu==null) {
    return $this->redirectToRoute('lds_platform_admin_repondre_message', array('id' => $id));
  } else {
         $message = (new \Swift_Message("Let's Do Something: Reponse à votre question"))
     ->setFrom('admin@lds.com') //adresse de l'evoyeur
     ->setTo($mail) //adresse du receveur
     ->setBody(
      //la vue qui est chargée (le contenu du mail)
      $this->renderView(
        'LDSPlatformBundle:Email:EmailReponseAdmin.html.twig',
        array('data' => $contenu,)
      ),
      'text/html'
    );
     $this->get('mailer')->send($message);
        $em->remove($messageAdmin);
        $em->flush();
    }

      return $this->redirectToRoute('lds_platform_admin_liste_message', array('page' => 1));
}

  /**
  * Effacer Message
  *
  * fonction qui perment a l'admin d'effacer un message sans y répondre
  **/
public function effacerMessageAction($id) {
  $em = $this->getDoctrine()->getManager();
  $messageAdmin = $em->getRepository(MessageAdmin::class)->findOneById($id);

  if (!$messageAdmin) {
    throw $this->createNotFoundException(
     "Pas de message avec ".$id
   );
  }
  $em->remove($messageAdmin);
  $em->flush();
  return $this->redirectToRoute('lds_platform_admin_liste_message', array('page' => 1));
}



}
