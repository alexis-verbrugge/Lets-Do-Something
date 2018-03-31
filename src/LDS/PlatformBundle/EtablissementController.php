<?php

namespace LDS\PlatformBundle\Controller;

use LDS\PlatformBundle\Entity\Etablissement;
use LDS\PlatformBundle\Entity\Professionnel;
use LDS\PlatformBundle\Entity\Particulier;
use LDS\PlatformBundle\Entity\CommentaireEtablissement;
use LDS\PlatformBundle\Entity\VillesFranceFree;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\Datetime;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * EtablissementController
 *
 * @author Boutaljante, Ruiz Ramirez, Herve, Verbrugge
 *
 *Ce controller gère tout ce qui se rapporte aux etablissements et plus particulierement à la recherche.
 *Il y'a possibilité de chercher un etablissement a partir d'une ville ou encore a partir de son nom.
 *Si la géolocalisation est activée alors, les meilleurs etablissments (meilleurs coeff) apparaissant,
 * ils sont max 6. Il y'a possibilité pour rechercher un etablissemrnt de chosir des tags, une ville, un code postal
 * une distance (1->50km). Une fois les critères remplis il y'a possibilité de les affichés sous forme de liste ou de carte
 * Google Map (markers pour représenter les etablissements).
 *
 */

class EtablissementController extends Controller {

  /**
  * Meilleurs etablissements
  *
  * Fonction qui renvoi sur la page d'accueil du site, elle permet d'avoir 6 des meilleurs etablissements
  * à proximité de la personne sur le site si elle a activé la géolocalisation.
  * Une barre de recherche permet aussi à l'utilisateur de trouver un etablissement à partir de son nom et d'une ville
  *
  *
  **/

    public function meilleursEtablissementsAction(Request $request) {
      $etablissement = new Etablissement();
      $session = $this->get('session');

      //on recupere la localisation de la personne connectée
      $maLatitude=$session->get('ma_lat');
      $maLongitude=$session->get('ma_long');
      $listeEtablissement ="";
      $listeMeilleurEtablissement="";

      //on fait appel au service  qui permet de calculer la distance a partir de la lat et de la long
      $calculLongitude = $this->container->get('lds_platform.calculLongitude');

      //on fixe la distance max à 50km pour les meilleurs etablissements
      $distance= 50;
      $em = $this->getDoctrine()->getManager();

        //creation du formulaire pour rechercher a partir du nom de l'etablissement
         $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $etablissement);
         $formBuilder
        ->add('nomEtablissement',    TextType::class, array('required' => false))
        ->add('ville',   TextType::class, array('required' => false))
        ->add('chercher',  SubmitType::class);
        $form = $formBuilder->getForm();

    $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
    $repository2 = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');


      //lorsque l'on appui sur le bouton submit
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
       
      $listeEtablissement = $repository->trouverParNom($etablissement->getNomEtablissement());

      //on recupere la localisation de la ville si on en a rentré une 
      $ville= new VillesFranceFree();
      if ($etablissement->getVille()!='') {
        $ville = $repository2->findOneByVilleNom($etablissement->getVille());

        if ($ville!=null) {
        $maLatitude=$ville->getVilleLatitudeDeg();
        $maLongitude=$ville->getVilleLongitudeDeg();
        }
      }

  //on appel la fonction pour trouver les etablissement correspondant a la recherche lancée
  $listeEtablissement=$calculLongitude->calculDistance($distance, $maLatitude, $maLongitude, $listeEtablissement, true);

      //on recupere toutes les latitudes et longitude de ces etablissements
      $long=array();
      $lat=array();
      $i=0;
      foreach ($listeEtablissement as $e) {
          $long[$i]=$e->getLongitude();
          $lat[$i]=$e->getLatitude();
          $siret[$i]=$e->getSiret();
          $i++;
      }

      if (ceil(count($listeEtablissement))==0) {
        $pas_etablissement="Aucun établissement avec vos critères !";
      }

      $this->get('session')->set('etablissement', $listeEtablissement);
      $this->get('session')->set('lat', $lat);
      $this->get('session')->set('long', $long);

      return $this->redirectToRoute('lds_platform_trouver_etablissement', array('id' => 1, 'page' => 1));
    }

        $listeMeilleurEtablissement=$repository->findAll(array('valide' => true));
        $listeMeilleurEtablissement=$calculLongitude->calculDistance($distance, $maLatitude, $maLongitude, $listeMeilleurEtablissement, false);

          return $this->render('LDSPlatformBundle:Etablissement:meilleursEtablissements.html.twig', array(
      'listeEtablissement' => $listeMeilleurEtablissement,
      'form' => $form->createView(),
    ));

    }

  /**
  * Details etablissement
  *
  * Fonction qui permet d'afficher les details d'un etablissemnt a partir de son siret (ses infos, ses events, ses commentaires)
  *
  *
  **/

    public function detailAction(Request $request,$siret, $id) {
      $em = $this->getDoctrine()->getManager();
      $etablissement = $em
      ->getRepository('LDSPlatformBundle:Etablissement')
      ->findOneBySiret($siret);


      if ($etablissement==null) {
        return $this->render('LDSPlatformBundle:Erreurs:ErreurEtablissement.html.twig', array(
          'siret' => $siret
          ));
      } else {
      $listeEvenement=$em
      ->getRepository('LDSPlatformBundle:Evenement')
      ->findByEtablissement($etablissement, array('nom' => 'asc'));
      $listeCommentaire=$em
      ->getRepository('LDSPlatformBundle:CommentaireEtablissement')
      ->findByEtablissement($etablissement , array('date' => 'desc'));
      }

      //gestion de l'etoile favori
      $repository=$em->getRepository('LDSPlatformBundle:FavoriEtablissement');
      $login='';
      $session = $this->get('session');
      $login=$session->get('compte');
      //Pour remplir le commentaire
      $particulier = $em
      ->getRepository('LDSPlatformBundle:Particulier')
      ->findOneByLogin($login);
      //verifi si l'utilisateur est connecté et à déjà l'etablissement en favori
      $estFavori=true;
      if ($login!=null) {
        $favori=$repository->findBy(array('siret'=> $siret, 'loginParticulier'=> $login));
        $estConnecte=true;
        if ($count_favori=count($favori)==0){
          $estFavori = false;
        }
        else{
          $estFavori=true;
        }
      }
      else {
        $estConnecte=false;
      }

      //formulaire pour laisser un commentaire
      $commentaire=new CommentaireEtablissement();
      $form = $this->get('form.factory')->createBuilder(FormType::class, $commentaire)
      ->add('commentaire',   TextareaType::class)
      ->add('note',    ChoiceType::class, array(
      'choices' => array(
        '5' => '5',
        '4' => '4',
        '3' => '3',
        '2' => '2',
        '1' => '1',
      )))
      ->add('Laisser un commentaire',      SubmitType::class)
      ->getForm()
      ;

      //On recup le commentaire
      if ($request->isMethod('POST')) {
        $form->handleRequest($request);
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          //Prise en compte du vote pour la moyenne
          //maj nb de vote
          $etablissement_maj=$em->getRepository('LDSPlatformBundle:Etablissement')->findOneBySiret($siret);
          $nbvote=$etablissement_maj->getNombreNote()+1;
          $etablissement_maj->setNombreNote($nbvote);
          //maj moy
          $moy=($etablissement_maj->getMoyenne()+$commentaire->getNote())/$etablissement_maj->getNombreNote();
          $etablissement_maj->setMoyenne($moy);
          //maj coeff
          $coeff=$etablissement_maj->getNombreNote()*$etablissement_maj->getMoyenne();
          $etablissement_maj->setCoefficient($coeff);
          //insert dans la table commentaire
          $commentaire->setDate(new \Datetime());
          $commentaire->setEtablissement($etablissement);
          $commentaire->setParticulier($particulier);
          $em->persist($commentaire);
          $em->flush();
          return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 1));
        }
      }


       return $this->render('LDSPlatformBundle:Etablissement:detail.html.twig', array(
      'etablissement' => $etablissement,
      'listeEvenement' => $listeEvenement,
      'listeCommentaire' => $listeCommentaire,
      'id' => $id,
      'estFavori' => $estFavori,
      'estConnecte' => $estConnecte,
      'form' => $form->createView()
    ));
    }

  /**
  * Details evenement
  *
  * Fonction qui permet d'afficher les details d'un evenement a partir de son id
  *
  *
  **/
    public function detailEvenementAction($id, $id2) {
      $em = $this->getDoctrine()->getManager();
      $evenement = $em
      ->getRepository('LDSPlatformBundle:Evenement')
      ->findOneById($id);

      if ($evenement==null) {
        return $this->render('LDSPlatformBundle:Erreurs:ErreurEvenement.html.twig', array(
          'id' => $id
          ));
      }

      //gestion de l'etoile favori
      $repository=$em->getRepository('LDSPlatformBundle:FavoriEvenement');
      $login='';
      $session = $this->get('session');
      $login=$session->get('compte');
      //
      $estFavori=true;
      if($login!=null){
        $favori=$repository->findBy(array('idEvenement'=> $id, 'loginParticulier'=> $login));
        $estConnecte=true;
        if ($count_favori=count($favori)==0){
          $estFavori = false;
        }
        else{
          $estFavori=true;
        }
      }
      else {
        $estConnecte=false;
      }

      return $this->render('LDSPlatformBundle:Etablissement:detailEVenement.html.twig', array(
      'evenement' => $evenement,
      'id' => $id2,
      'estFavori' => $estFavori,
      'estConnecte' => $estConnecte));
    }


  /**
  * Trouver etablissement
  *
  * Fonction qui permet de chercher un etablissement en choisissant une distance, des tags, une ville ou un code postal (optionnel)
  *
  *
  **/
    public function trouverEtablissementAction($id,$page=1, Request $request) {
    
    $session = $this->get('session');
    $etablissement= new Etablissement();
    $listeEtablissement=$session->get('etablissement');

    //recupere la lat, la long et le siret des etablissments recherchés precedemment
    $lat=$session->get('lat');
    $long=$session->get('long');
    $siret=$session->get('siret');

    //booleen pour savoir si on doit se servir de la geolocalisation ou de la ville entree manuellement
    $choix_manuel=false;
    $calculLongitude = $this->container->get('lds_platform.calculLongitude');

    //lat et long de l'user connecte
    $maLatitude=$session->get('ma_lat');
    $maLongitude=$session->get('ma_long');

    //messages d'erreur du formulaire
    $erreur_ville="";
    $ville_multiple="";
    $pas_etablissement="";
    $erreur=false;

    $tab_distance=array();


    //on recupere la liste des tags valides 
    $em = $this->getDoctrine()->getManager();
    $listeTag =$em->getRepository('LDSPlatformBundle:Tag')->trouverTagValide();
    $tab_tag = array();
    $i=0;
    foreach ($listeTag as $tag) {
      $tab_tag[$i]=$tag->getNom();
      $i++;
    }

    //creation du formaulaire de la recherche 
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $etablissement);
    $formBuilder
    ->add('tags', EntityType::class, array(
      'class'        => 'LDSPlatformBundle:Tag',
      'choice_label' => 'nom',
      'multiple'     => true,
      'expanded' => true,))
    ->add('codePostal',    TextType::class, array('required' => false))
    ->add('ville',   TextType::class, array('required' => false))
    ->add('distance', RangeType::class, array(
    'attr' => array(
        'min' => 1,
        'max' => 50
    ), 'mapped' => false,))->add('chercher',  SubmitType::class);
    $form = $formBuilder->getForm();


    $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
    $repository2 = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');

    // si il n'y a pas de variable de session -> on selectionne tous les etblissements a moins de 25km
     if ($listeEtablissement==null) {
       $listeEtablissement = $repository->findAll(array('valide' => true));
       $listeEtablissement=$calculLongitude->calculDistance(25, $maLatitude, $maLongitude, $listeEtablissement, true);
     }
   
    //a la soumission du formulaire 
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

     $ville= new VillesFranceFree();

     //si l'user ne rentre qu'un nom de ville
      if ($etablissement->getVille()!='' && $etablissement->getCodePostal()=='') {
        $ville = $repository2->findByVilleNom($etablissement->getVille());

        //on verifie que le code postal est valide
        if (count($ville)>1) {
          $ville_multiple="Il y'a plusieurs ville avec ce nom veuillez preciser un code postal";
          $erreur=true;
        }
        $ville = $repository2->findOneByVilleNom($etablissement->getVille());
        $choix_manuel=true;
      }

      // si l'user ne rentre qu'un code postal
      if ($etablissement->getCodePostal()!='' && $etablissement->getVille()=='') {
        $ville = $repository2->findByVilleCodePostal($etablissement->getCodePostal());
        //si il y'a plus d'une ville qui correspond a ce code
         if (count($ville)>1) {
          $ville_multiple="Il y'a plusieurs ville avec ce code postal veuillez preciser un nom de ville";
          $erreur=true;
        }
          $ville = $repository2->findOneByVilleCodePostal($etablissement->getCodePostal());
          $choix_manuel=true;       
      }

      //si l'user choisi un code postal et une ville
      if ($etablissement->getVille()!='' && $etablissement->getCodePostal()!='') {
        $ville = $repository2->findOneBy(array('villeNomReel' => $etablissement->getVille(),'villeCodePostal' => $etablissement->getCodePostal(),));
        $choix_manuel=true;
      }

      //si il n'y pas de ville en focntion de ce qui a ete chois precedemment alors le bool erreur passe a true
      if ($ville==null) {
        $erreur=true;
        $erreur_ville="La ville ou le code postal indiqués ne sont pas corrects";
      //sinon si choix manuel est  vrai alors on recupere la let et la long de la ville du formulaire
      } else if ($choix_manuel==true) {
        $maLatitude=$ville->getVilleLatitudeDeg();
        $maLongitude=$ville->getVilleLongitudeDeg();
      }

      //si erreur est faux alors on peut continuer
      if ($erreur == false) {
      $listeEtablissement = $repository->findByValide(true);
      $distance_choisie= $form['distance']->getData();

      $count_tag=ceil(count($etablissement->getTags()));

      //on recupere tous les etablissement ayant au moins tous les tags choisis
      if (ceil(count($etablissement->getTags()))!=0) {
        $listeTags=$etablissement->getTags();
        for ($i=0; $i<$count_tag; $i++) {
          $tmp = array();
          $compteur=0;
          foreach($listeEtablissement as $e) {
            foreach ($e->getTags() as $tag) {
              if ($listeTags[$i]==$tag) {
                $tmp[$compteur]=$e;
                $compteur++;
              }
            }
          }
          $listeEtablissement=$tmp;
        }
      }

//appel de la fonction pour recuperer tous les etablissements en fonction de la distance choisie
 $listeEtablissement=$calculLongitude->calculDistance($distance_choisie, $maLatitude, $maLongitude, $listeEtablissement, true);
 

      $long=array();
      $lat=array();
      $i=0;

      //on recupere dans tes tableaux toutes les infos des etablissements recuperes 
      foreach ($listeEtablissement as $e) {
          $long[$i]=$e->getLongitude();
          $lat[$i]=$e->getLatitude();
          $siret[$i]=$e->getSiret();
          $i++;
      }

      if (ceil(count($listeEtablissement))==0) {
        $pas_etablissement="Aucun établissement avec vos critères !";
      }

      //on passe aux variables de session les tableaux remplis
      $this->get('session')->set('etablissement', $listeEtablissement);
      $this->get('session')->set('lat', $lat);
      $this->get('session')->set('long', $long);
      $this->get('session')->set('siret', $siret);

      //on remet page a 1 car il faur que nous retombions sur la bonne page en cas de reinitialisation de la recehrche
      $page=1;
      }
    }
     
    //si l'id est 2 alors nous chargeons la carte google map.
    if ($id==2) {
      return $this->render('LDSPlatformBundle:Etablissement:carte.html.twig', array(
      'form' => $form->createView(),
      'listeEtablissement' => $listeEtablissement,
      'lati' => $lat,
      'lngi' => $long,
      'siret' => $siret,
      'pas_etablissement' => $pas_etablissement,
      'maLat' => $maLatitude,
      'maLong' => $maLongitude,
      'choix_manuel' => $choix_manuel,
      'erreur_ville' => $erreur_ville,
      'ville_multiple' => $ville_multiple,
    ));
    }

    //on met en place la pagination (6 etablissments par page)
     $pagination = array('page' => $page,'route' => 'lds_platform_trouver_etablissement',
      'pages_count' => ceil(count($listeEtablissement)/6),
      'route_params' => array('id' => $id));

    //on charge les bons etablissements en fonction de la page choisie en argument
    $first=($page-1) * 6;
    $tmp=array();
    $count=0;
    for ($i=$first; $i<($first+6); $i++) {
      if ($i>=count($listeEtablissement)) {
        break;
      }
      $tmp[$count]=$listeEtablissement[$i];
      $count++;
    }
    $listeEtablissement=$tmp;

    // par defaut on retourne la liste des etablissemnrts
    return $this->render('LDSPlatformBundle:Etablissement:chercherEtablissement.html.twig', array(
      'form' => $form->createView(),
      'listeEtablissement' => $listeEtablissement,
      'pas_etablissement' => $pas_etablissement,
      'tab_distance' => $tab_distance,
      'pagination' => $pagination,
      'erreur_ville' => $erreur_ville,
      'ville_multiple' => $ville_multiple,
    ));
    }


    public function trouverEvenementAction($id,$page, Request $request) {
    $session = $this->get('session');
    $etablissement= new Etablissement();
    $listeEtablissement="";
    $listeEvenement=$session->get('evenement');
    $lat=$session->get('lat_event');
    $long=$session->get('long_event');
    $choix_manuel=false;
    $calculLongitude = $this->container->get('lds_platform.calculLongitude');

    $maLatitude=$session->get('ma_lat');
    $maLongitude=$session->get('ma_long');

    $id_event=$session->get('id_event');
    $pas_evenement="";
    $tab_distance=array();
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $etablissement);
    $em = $this->getDoctrine()->getManager();
    $listeTag =$em
    ->getRepository('LDSPlatformBundle:Tag')->trouverTagValide();
    // On ajoute les champs de l'entité que l'on veut à notre formulaire
    $tab_tag = array();
    $i=0;
    foreach ($listeTag as $tag) {
      $tab_tag[$i]=$tag->getNom();
      $i++;
    }
    $formBuilder
    ->add('tags', EntityType::class, array(
      'class'        => 'LDSPlatformBundle:Tag',
      'choice_label' => 'nom',
      'multiple'     => true,
      'expanded' => true,))
    ->add('codePostal',    TextType::class, array('required' => false))
    ->add('ville',   TextType::class, array('required' => false))
    ->add('distance', RangeType::class, array(
    'attr' => array(
        'min' => 1,
        'max' => 50
    ), 'mapped' => false,))->add('chercher',  SubmitType::class);

    $form = $formBuilder->getForm();

    $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
    $repository2 = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');

     if ($listeEtablissement==null) {
       $listeEtablissement = $repository->findAll();
       $listeEtablissement=$calculLongitude->calculDistance(25, $maLatitude, $maLongitude, $listeEtablissement, true);
     }

   
    
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
       $em = $this->getDoctrine()->getManager();

      $listeEtablissement = $repository->findAll();
      $e='';

      $ville= new VillesFranceFree();
      if ($etablissement->getVille()!='' && $etablissement->getCodePostal()=='') {
        $ville = $repository2->findOneByVilleNom($etablissement->getVille());
        $maLatitude=$ville->getVilleLatitudeDeg();
        $maLongitude=$ville->getVilleLongitudeDeg();
        $choix_manuel=true;
      }

      if ($etablissement->getCodePostal()!='' && $etablissement->getVille()=='') {
        $ville = $repository2->findOneByVilleCodePostal($etablissement->getCodePostal());
        $maLatitude=$ville->getVilleLatitudeDeg();
        $maLongitude=$ville->getVilleLongitudeDeg();
        $choix_manuel=true;       
      }

      if ($etablissement->getVille()!='' && $etablissement->getCodePostal()!='') {
        $ville = $repository->findBy(array(
                                                        'ville' => $etablissement->getVille(),
                                                        'codePostal' => $etablissement->getCodePostal(),));
        $maLatitude=$ville->getVilleLatitudeDeg();
        $maLongitude=$ville->getVilleLongitudeDeg();
        $choix_manuel=true;
      }


      $distance_choisie= $form['distance']->getData();
      $count_tag=ceil(count($etablissement->getTags()));

      if (ceil(count($etablissement->getTags()))!=0) {
        $listeTags=$etablissement->getTags();
        for ($i=0; $i<$count_tag; $i++) {
          $tmp = array();
          $compteur=0;
          foreach($listeEtablissement as $e) {
            foreach ($e->getTags() as $tag) {
              if ($listeTags[$i]==$tag) {
                $tmp[$compteur]=$e;
                $compteur++;
              }
            }
          }
          $listeEtablissement=$tmp;
        }
      }

$listeEtablissement=$calculLongitude->calculDistance($distance_choisie, $maLatitude, $maLongitude, $listeEtablissement, true);

   $repository3 = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LDSPlatformBundle:Evenement');

  $compteur=0;
  $tmp=array();
  $tmp2=array();
  foreach($listeEtablissement as $e) {
        $tmp=$repository3->findByEtablissement($e);
        foreach ($tmp as $event) {
          $tmp2[$compteur]=$event;
          $compteur++;
        }
      }
      $listeEvenement=$tmp2;
      $long=array();
      $lat=array();
      $i=0;
      foreach ($listeEvenement as $e) {
          $long[$i]=$e->getEtablissement()->getLongitude();
          $lat[$i]=$e->getEtablissement()->getLatitude();
          $id_event[$i]=$e->getId();
          $i++;
      }
      if (ceil(count($listeEvenement))==0) {
        $pas_evenement="Aucun evenement avec vos critères !";
      }
      $this->get('session')->set('evenement', $listeEvenement);
      $this->get('session')->set('lat_event', $lat);
      $this->get('session')->set('long_event', $long);
      $this->get('session')->set('id_event', $id_event);
    }


    if ($id==2) {
      return $this->render('LDSPlatformBundle:Etablissement:carteEvenement.html.twig', array(
      'form' => $form->createView(),
      'listeEvenement' => $listeEvenement,
      'lati' => $lat,
      'lngi' => $long,
      'id_event' => $id_event,
      'pas_evenement' => $pas_evenement,
      'maLat' => $maLatitude,
      'maLong' => $maLongitude,
      'choix_manuel' => $choix_manuel,
    ));
    }

    $pagination = array('page' => $page,'route' => 'lds_platform_trouver_evenement',
      'pages_count' => ceil(count($listeEtablissement)/6),
      'route_params' => array('id' => $id));


    $first=($page-1) * 6;
    $tmp=array();
    $count=0;
    for ($i=$first; $i<($first+6); $i++) {
      if ($i>=count($listeEtablissement)) {
        break;
      }
      $tmp[$count]=$listeEtablissement[$i];
      $count++;
    }

    $listeEtablissement=$tmp;

    return $this->render('LDSPlatformBundle:Etablissement:chercherEvenement.html.twig', array(
      'form' => $form->createView(),
      'listeEvenement' => $listeEvenement,
      'pas_evenement' => $pas_evenement,
      'tab_distance' => $tab_distance,
      'pagination' => $pagination,
    ));
    }
}