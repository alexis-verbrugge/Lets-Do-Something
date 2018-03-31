<?php

namespace LDS\PlatformBundle\Controller;

use LDS\PlatformBundle\Entity\Etablissement;
use LDS\PlatformBundle\Entity\Professionnel;
use LDS\PlatformBundle\Entity\Particulier;
use LDS\PlatformBundle\Entity\VillesFranceFree;
use LDS\PlatformBundle\Entity\CommentaireEtablissement;
use LDS\PlatformBundle\Entity\FavoriEtablissement;
use LDS\PlatformBundle\Entity\FavoriEvenement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
      $this->get('session')->remove('lat_etablissement');
      $this->get('session')->remove('long_etablissement');

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
          $lat[$i]=$e->getLattitude();
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

    //si la geolocalisation a echoué on met par défaut sur Paris 
    if ($maLatitude==null ||$maLongitude==null) {
      $maLatitude=48.866667;
      $maLongitude=2.333333;
    }

        $listeMeilleurEtablissement=$repository->findBy(array('valide' => true));
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

    public function detailAction($siret, $id, Request $request) {

      $this->get('session')->remove('lat_etablissement');
      $this->get('session')->remove('long_etablissement');
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
      ->derniers_commentaires($etablissement);
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

      $monCommentaire=$em
      ->getRepository('LDSPlatformBundle:CommentaireEtablissement')
      ->findOneBy(array('particulier' => $particulier, 'etablissement' => $etablissement));
      
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
      ->getForm();

      //On recup le commentaire
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
          $moy=($etablissement_maj->getMoyenne()*($nbvote-1)+$commentaire->getNote())/$etablissement_maj->getNombreNote();
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
      'form' => $form->createView(),
      'monCommentaire' => $monCommentaire,
    ));
    }

  /**
  * Details evenement
  *
  * Fonction qui permet d'afficher les details d'un evenement a partir de son id
  *
  *
  **/
    public function detailEvenementAction($id, $id2, Request $request) {
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
    $liste_description=$session->get('liste_description');
    $listeNom=$session->get('liste_nom');
    $listePhoto=$session->get('listePhoto');

    $webPath = $this->get('kernel')->getRootDir().'/../web';

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
    $listeErreurs= [];
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
      //on ajoute une fonction pour classer les tags par ordre alphabetique
      'query_builder' => function () {
        return $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Tag')->createQueryBuilder('t')
        ->where('t.valide = true')
        ->orderBy('t.nom', 'ASC');
      },
      'choice_label' => 'nom',
      'multiple'     => true,
      'expanded' => true,))
    ->add('codePostal',    TextType::class, array('required' => false))
    ->add('ville',   TextType::class, array('required' => false))
    ->add('distance', RangeType::class,array(
    'attr' => array(
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'value' => 50,
        'oninput' => "res.value=parseInt(this.value)"
    ),
    'mapped' => false,))
    ->add('chercher',  SubmitType::class);
    $form = $formBuilder->getForm();


    $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
    $repository2 = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');

    // si il n'y a pas de variable de session -> on selectionne tous les etblissements a moins de 25km
     if ($listeEtablissement==null) {
       $listeEtablissement = $repository->findBy(array('valide' => true));
       $listeEtablissement=$calculLongitude->calculDistance(25, $maLatitude, $maLongitude, $listeEtablissement, true);
     }

     $count_etablissement=count($listeEtablissement);
   
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
          $listeErreurs['ville_multiple']="Il y'a plusieurs ville avec ce code postal veuillez preciser un nom de ville";
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
        $listeErreurs['erreur_ville']="La ville ou le code postal indiqués ne sont pas corrects";
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
 $count_etablissement=count($listeEtablissement);
 

      $long=array();
      $lat=array();
      $liste_description=array();
      $listePhoto=array();
      $listeNom=array();
      $i=0;

      //on recupere dans tes tableaux toutes les infos des etablissements recuperes 
      foreach ($listeEtablissement as $e) {
          $long[$i]=$e->getLongitude();
          $lat[$i]=$e->getLattitude();
          $siret[$i]=$e->getSiret();
          $listeNom[$i]=$e->getNomEtablissement();
          $liste_description[$i]=$e->getDescription();
          $listePhoto[$i]=$e->getPhoto1();
          $i++;
      }

      if (ceil(count($listeEtablissement))==0) {
        $listeErreurs['pas_etablissement']="Aucun établissement avec vos critères !";
      }

      //on passe aux variables de session les tableaux remplis
      $this->get('session')->set('etablissement', $listeEtablissement);
      $this->get('session')->set('lat', $lat);
      $this->get('session')->set('long', $long);
      $this->get('session')->set('siret', $siret);
      $this->get('session')->set('liste_nom', $listeNom);
      $this->get('session')->set('liste_description', $liste_description);
      $this->get('session')->set('listePhoto', $listePhoto);


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
      'listePhoto' => $listePhoto,
      'siret' => $siret,
      'listeErreurs' => $listeErreurs,
      'maLat' => $maLatitude,
      'maLong' => $maLongitude,
      'choix_manuel' => $choix_manuel,
      'erreur_ville' => $erreur_ville,
      'ville_multiple' => $ville_multiple,
      'listeNom' => $listeNom,
      'liste_description' => $liste_description,
      'webPath' => $webPath,

      
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
      'listeErreurs' => $listeErreurs,
      'tab_distance' => $tab_distance,
      'pagination' => $pagination,
      'erreur_ville' => $erreur_ville,
      'ville_multiple' => $ville_multiple,
      'count' => $count_etablissement,
    ));
    }


    public function trouverEvenementAction($id,$page, Request $request) {

    $session = $this->get('session');
    $etablissement= new Etablissement();
    $listeEtablissement=array();
    $listeEvenement=$session->get('evenement');
    $lat=$session->get('lat_event');
    $long=$session->get('long_event');
    $nom=$session->get('nom_event');
    $description=$session->get('description_event');
    $date_debut=$session->get('date_debut_event');
    $photos=$session->get('photo_event');

    $erreur=false;
    $choix_manuel=false;
    $calculLongitude = $this->container->get('lds_platform.calculLongitude');

    $maLatitude=$session->get('ma_lat'); 
    $maLongitude=$session->get('ma_long');

    $id_event=$session->get('id_event');
    $pas_evenement="";
    $listeErreurs= [];
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
      //on ajoute une fonction pour classer les tags par ordre alphabetique
      'query_builder' => function () {
        return $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Tag')->createQueryBuilder('t')
        ->where('t.valide = true')
        ->orderBy('t.nom', 'ASC');
      },
      'choice_label' => 'nom',
      'multiple'     => true,
      'expanded' => true,))
    ->add('codePostal',    TextType::class, array('required' => false))
    ->add('ville',   TextType::class, array('required' => false))
    ->add('distance', RangeType::class, array(
    'attr' => array(
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'oninput' => "res.value=parseInt(this.value)"
    ), 'mapped' => false,))->add('chercher',  SubmitType::class);

    $form = $formBuilder->getForm();

    $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
    $repository2 = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');

    if ($listeEvenement==null) {
      $listeEtablissement = $repository->findBy(array('valide' => true));
      $listeEtablissement=$calculLongitude->calculDistance(25, $maLatitude, $maLongitude, $listeEtablissement, true);

    }

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $e='';
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
          $listeErreurs['ville_multiple']="Il y'a plusieurs ville avec ce code postal veuillez preciser un nom de ville";
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
        $listeErreurs['erreur_ville']="La ville ou le code postal indiqués ne sont pas corrects";
      //sinon si choix manuel est  vrai alors on recupere la let et la long de la ville du formulaire
      } else if ($choix_manuel==true) {
        $maLatitude=$ville->getVilleLatitudeDeg();
        $maLongitude=$ville->getVilleLongitudeDeg();
      }

      if ($erreur == false) {
      $listeEtablissement = $repository->findByValide(true);
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
}
    }

    $repository3 = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LDSPlatformBundle:Evenement');

  $compteur=0;
  $tmp=array();
  $tmp2=array();
  foreach($listeEtablissement as $e) {
        $tmp=$repository3->findByEtablissement($e);
        $now=date('d-m-Y');
        foreach ($tmp as $event) {
          if ( (strtotime($event->getDateFin()->format('d-m-Y'))>=strtotime($now))  && $event->getValide()==true) {
          $tmp2[$compteur]=$event;
          $compteur++;
          }
        }
      }
      $listeEvenement=$tmp2;
      $long=array();
      $lat=array();
      $description=array();
      $nom=array();
      $date_debut=array();

      $i=0;
      foreach ($listeEvenement as $e) {
          $long[$i]=$e->getEtablissement()->getLongitude();
          $lat[$i]=$e->getEtablissement()->getLattitude();
          $id_event[$i]=$e->getId();
          $description[$i]=$e->getDescription();
          $nom[$i]=$e->getNom();
          $date_debut[$i]=$e->getDateDebut()->format('d-m-Y');
          $photos[$i]=$e->getPhoto1();
          $i++;
      }
      if (ceil(count($listeEvenement))==0) {
        $listeErreurs['pas_evenement']="Aucun evenement avec vos critères !";
      }

      $this->get('session')->set('evenement', $listeEvenement);
      $this->get('session')->set('lat_event', $lat);
      $this->get('session')->set('long_event', $long);
      $this->get('session')->set('id_event', $id_event);
      $this->get('session')->set('description_event', $description);
      $this->get('session')->set('nom_event', $nom);
      $this->get('session')->set('date_debut_event', $date_debut);
      $this->get('session')->set('photo_event', $photos);

      $count_evenement=count($listeEvenement);


    if ($id==2) {
      return $this->render('LDSPlatformBundle:Etablissement:carteEvenement.html.twig', array(
      'form' => $form->createView(),
      'listeEvenement' => $listeEvenement,
      'lati' => $lat,
      'lngi' => $long,
      'id_event' => $id_event,
      'listeErreurs' => $listeErreurs,
      'maLat' => $maLatitude,
      'maLong' => $maLongitude,
      'choix_manuel' => $choix_manuel,
      'description' => $description,
      'nom' => $nom,
      'date_debut' => $date_debut,
      'photos' => $photos,
    ));
    }

    $pagination = array('page' => $page,'route' => 'lds_platform_trouver_evenement',
      'pages_count' => ceil(count($listeEvenement)/6),
      'route_params' => array('id' => $id));


    $first=($page-1) * 6;
    $tmp=array();
    $count=0;
    for ($i=$first; $i<($first+6); $i++) {
      if ($i>=count($listeEvenement)) {
        break;
      }
      $tmp[$count]=$listeEvenement[$i];
      $count++;
    }
    
    $listeEvenement=$tmp;

    return $this->render('LDSPlatformBundle:Etablissement:chercherEvenement.html.twig', array(
      'form' => $form->createView(),
      'listeEvenement' => $listeEvenement,
      'listeErreurs' => $listeErreurs,
      'tab_distance' => $tab_distance,
      'pagination' => $pagination,
      'count' => $count_evenement,
    ));
    }
}