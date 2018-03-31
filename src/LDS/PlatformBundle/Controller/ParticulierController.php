<?php

namespace LDS\PlatformBundle\Controller;

use LDS\PlatformBundle\Entity\Etablissement;
use LDS\PlatformBundle\Entity\FicheSoiree;
use LDS\PlatformBundle\Entity\ParticipantsSoiree;
use LDS\PlatformBundle\Entity\MessageSoiree;
use LDS\PlatformBundle\Entity\Evenement;
use LDS\PlatformBundle\Entity\FavoriEtablissement;
use LDS\PlatformBundle\Entity\FavoriEvenement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ParticulierController extends Controller
{

  public function accueilAction() {

  }

  public function ajouterEtablissementFavoriAction(Request $request, $siret) {
	//On recupere l'id de la personne connecté
	//$repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Particulier');
   $login='';
   $session = $this->get('session');
   $login=$session->get('compte');
    //On cree une entité
   $favori = new FavoriEtablissement();
   $favori->setLoginParticulier($login);
   $favori->setSiret($siret);
    //On insert dans la BD
   $em = $this->getDoctrine()->getManager();
   $em->persist($favori);
   $em->flush();


   return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 1));

 }

 public function supprimerEtablissementFavoriAction(Request $request, $siret){
	//On recupere l'id de la personne connecté
   $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:FavoriEtablissement');
   $login='';
   $session = $this->get('session');
   $login=$session->get('compte');
    //Recup la ligne concerné
   $favori=$repository->findOneBy(array('siret'=> $siret, 'loginParticulier'=> $login));
    //On la supprime
   $em = $this->getDoctrine()->getManager();
   $em->remove($favori);
   $em->flush();

   return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 1));
 }

 public function ajouterEvenementFavoriAction(Request $request, $id) {
   $login='';
   $session = $this->get('session');
   $login=$session->get('compte');
    //On cree une entité
   $favori = new FavoriEvenement();
   $favori->setLoginParticulier($login);
   $favori->setIdEvenement($id);
    //On insert dans la BD
   $em = $this->getDoctrine()->getManager();
   $em->persist($favori);
   $em->flush();

   return $this->redirectToRoute('lds_platform_evenement_detail',array('id' => $id, 'id2' => 1));

 }	

 public function supprimerEvenementFavoriAction(Request $request, $id) {
	//On recupere l'id de la personne connecté
   $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:FavoriEvenement');
   $login='';
   $session = $this->get('session');
   $login=$session->get('compte');
    //Recup la ligne concerné
   $favori=$repository->findOneBy(array('idEvenement'=> $id, 'loginParticulier'=> $login));
    //On la supprime
   $em = $this->getDoctrine()->getManager();
   $em->remove($favori);
   $em->flush();

   return $this->redirectToRoute('lds_platform_evenement_detail',array('id' => $id, 'id2' => 1));
 }

 public function listeEtablissementFavoriAction() {
   $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:FavoriEtablissement');
   $repositorybis = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
   $compteur=0;
   $login='';
   $session = $this->get('session');
   $login=$session->get('compte');
    //Recup la ligne concerné
   $favori=$repository->findByLoginParticulier($login);
   $etablissement=array();
   foreach($favori as $fav) {
     $tmp=$repositorybis->findOneBySiret($fav->getSiret());
     $etablissement[$compteur] = $tmp;
     $compteur++;
   }

   return $this->render('LDSPlatformBundle:Particulier:listeFavoriEtablissement.html.twig', array(
    'listeFavori' => $etablissement,
  ));

 }

 public function listeEvenementFavoriAction() {
   $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:FavoriEvenement');
   $repositorybis = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Evenement');
   $compteur=0;
   $login='';
   $session = $this->get('session');
   $login=$session->get('compte');
    //Recup la ligne concerné
   $favori=$repository->findByLoginParticulier($login);
   $evenement=array();
   foreach($favori as $fav) {
     $tmp=$repositorybis->findOneById($fav->getIdEvenement());
     $evenement[$compteur] = $tmp;
     $compteur++;
   }

   return $this->render('LDSPlatformBundle:Particulier:listeFavoriEvenement.html.twig', array(
    'listeFavori' => $evenement,
  ));
 }



 public function creerFicheSoireeAction(Request $request, $siret) {
	//formulaire pour laisser un commentaire
  $ficheSoiree=new FicheSoiree();
  $participant=new ParticipantsSoiree();
  $form = $this->get('form.factory')->createBuilder(FormType::class, $ficheSoiree)
  ->add('nom',   TextType::class)
  ->add('description',   TextAreaType::class)
  ->add('date',    DateType::class)
  ->add('heure_debut', TimeType::class)
  ->add('heure_fin' , TimeType::class)
  ->add('creer',      SubmitType::class)
  ->getForm()
  ;

      //On recup le commentaire
  if ($request->isMethod('POST')) {
    $form->handleRequest($request);
    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
          //ajout de l'etablissement
      $etablissement=$em->getRepository('LDSPlatformBundle:Etablissement')->findOneBySiret($siret);
      $ficheSoiree->setEtablissement($etablissement);
      $session = $this->get('session');
      $login=$session->get('compte');
      $admin=$em->getRepository('LDSPlatformBundle:Particulier')->findOneByLogin($login);
      $ficheSoiree->setAdmin($admin);
      $em->persist($ficheSoiree);
      $em->flush();
      $participant->setLogin($admin->getLogin());
      $participant->setIdSoiree($ficheSoiree->getId());
      $participant->setParticipe(true);
      $em->persist($participant);
      $em->flush();

      return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 1));
    }
  }

  return $this->render('LDSPlatformBundle:Soiree:creerFicheSoiree.html.twig', array(
   'form' => $form->createView()
 ));

}

public function rejoindreGroupeSoireeAction($id) {
	$em = $this->getDoctrine()->getManager();
	$repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:ParticipantsSoiree');
	$session = $this->get('session');
  $login=$session->get('compte');
  $participant=$repository->findOneBy(array('idSoiree' => $id, 'login' => $login));
  $participant->setParticipe(true);
  $em->flush();
  return $this->redirectToRoute('lds_platform_particulier_liste_groupe_soiree');
}

public function refuserGroupeSoireeAction($id) {
	$em = $this->getDoctrine()->getManager();
	$repository = $em->getRepository('LDSPlatformBundle:ParticipantsSoiree');
	$session = $this->get('session');
  $login=$session->get('compte');
  $participant=$repository->findOneBy(array('idSoiree' => $id, 'login' => $login));
  $em->remove($participant);
  $em->flush();

  return $this->redirectToRoute('lds_platform_particulier_liste_groupe_soiree');

}

public function listeGroupeSoireeAction() {
  $em= $this->getDoctrine()->getManager();
	$repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:FicheSoiree');
	$repositorybis = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:ParticipantsSoiree');
	$repositoryter = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Particulier');
	$compteur=0;
	$login='';
	$session = $this->get('session');
  $login=$session->get('compte');
  $particulier=$repositoryter->findByLogin($login);
    //Recup participe
  $participant=$repositorybis->findBy(array('login' => $login, 'participe' => true));
  $participe = array();
  foreach($participant as $par) {
   $tmp=$repository->findOneById($par->getIdSoiree());
   $participe[$compteur] = $tmp;
   $compteur++;
 }

 $admin=$repository->findByAdmin($particulier);

 return $this->render('LDSPlatformBundle:Soiree:listeFicheSoiree.html.twig', array(
  'listeParticipe' => $participe,
  'listeAdmin' => $admin
));

}

public function invitationsGroupeSoireeAction() {
	$repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:ParticipantsSoiree');
	$repositorybis = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:FicheSoiree');
	$compteur=0;
	$session = $this->get('session');
  $login=$session->get('compte');
  $invitationEnAttente = $repository->findBy(array('login' => $login, 'participe' => 0));
  $invitation = array();
  foreach($invitationEnAttente as $invit) {
   $tmp=$repositorybis->findOneById($invit->getIdSoiree());
   $invitation[$compteur] = $tmp;
   $compteur++;
 }

 return $this->render('LDSPlatformBundle:Soiree:listeInvitations.html.twig', array(
  'listeInvitation' => $invitation,
));
}

public function detailFicheSoireeAction(Request $request, $id) {
	$em = $this->getDoctrine()->getManager();
  $soiree = $em->getRepository('LDSPlatformBundle:FicheSoiree')->findOneById($id);
  $listeParticipants=$em->getRepository('LDSPlatformBundle:ParticipantsSoiree')->findBy(array('idSoiree' => $id, 'participe' => true));
  $listeInvites=$em->getRepository('LDSPlatformBundle:ParticipantsSoiree')->findBy(array('idSoiree' => $id, 'participe' => false));
  $participants = array();
  $invites= array();
  $compteur=0;
  foreach($listeParticipants as $partizer) {
   $tmp=$em->getRepository('LDSPlatformBundle:Particulier')->findOneByLogin($partizer->getLogin());
   $participants[$compteur] = $tmp;
   $compteur++;
 }
  $compteur=0;
   foreach($listeInvites as $invitzer) {
   $tmp=$em->getRepository('LDSPlatformBundle:Particulier')->findOneByLogin($invitzer->getLogin());
   $invites[$compteur] = $tmp;
   $compteur++;
 }

 $messages=$em->getRepository('LDSPlatformBundle:MessageSoiree')->findByFicheSoiree($soiree);
 $organisateur=$soiree->getAdmin()->getLogin();
    //mess err
 $loginIntrouvable=$this->get('session')->get('login_introuvable');

    //Formulaire invitation
 $participant=new ParticipantsSoiree();
 $invitation = $this->get('form.factory')->createBuilder(FormType::class, $participant)
 ->add('login',   TextType::class)
 ->add('inviter',    SubmitType::class)
 ->getForm();

 if ($request->isMethod('POST')) {
  $invitation->handleRequest($request);
  if ($invitation->isValid()) {

    $em = $this->getDoctrine()->getManager();
    $login=$participant->getLogin();
    $p=$em->getRepository('LDSPlatformBundle:Particulier')->findOneByLogin($login);
    if(count($p)==0) {
     $this->get('session')->set('login_introuvable', 'login introuvable !');
     return $this->redirectToRoute('lds_platform_particulier_detail_fiche_soiree',array('id' => $id,));
   }
   $participantExistant=$em->getRepository('LDSPlatformBundle:ParticipantsSoiree')->findOneBy(
    array('login' => $login, 'idSoiree' => $id));
   if(count($participantExistant)>0) {
    $this->get('session')->set('login_introuvable', 'membre déja invité !');
     return $this->redirectToRoute('lds_platform_particulier_detail_fiche_soiree',array('id' => $id,));
   }
  

          //ajout de participe
   $participant->setIdSoiree($id);
   $participant->setParticipe(false);
   $em->persist($participant);
   $em->flush();
   return $this->redirectToRoute('lds_platform_particulier_detail_fiche_soiree',array(
    'id' => $id,));
 
 }
}


    //Formulaire message
$message=new MessageSoiree();
$form = $this->get('form.factory')->createBuilder(FormType::class, $message)
->add('commentaire',   TextareaType::class)
->add('envoyer',    SubmitType::class)
->getForm()
;
if ($request->isMethod('POST')) {
  $form->handleRequest($request);
  if ($form->isValid()) {
    $em = $this->getDoctrine()->getManager();
          //ajout de date particulier et fiche soiree
    $session = $this->get('session');
    $login=$session->get('compte');
    $particuzer=$em->getRepository('LDSPlatformBundle:Particulier')->findOneByLogin($login);
    $message->setDate(new \Datetime());
    $message->setParticulier($particuzer);
    $message->setFicheSoiree($soiree);
    $em->persist($message);
    $em->flush();
    return $this->redirectToRoute('lds_platform_particulier_detail_fiche_soiree',array('id' => $id));
  }
}
 $this->get('session')->remove('login_introuvable');
return $this->render('LDSPlatformBundle:Soiree:detailFicheSoiree.html.twig', array(
  'organisateur' => $organisateur,
  'idzer' => 1,
  'soiree' => $soiree,
  'listeParticipe' => $participants,
  'listeInvite' => $invites,
  'listeMessage' => $messages,
  'invitation' => $invitation->createView(),
  'form' => $form->createView(),
  'loginIntrouvable' => $loginIntrouvable
));


}

public function ajouterMessageGroupeSoireeAction() {

}

public function modifierInfoPersonnellesAction() {

}

public function supprimerCommentaireAction($id, $redirection) {
  $em = $this->getDoctrine()->getManager();
  $commentaire=$monCommentaire=$em
      ->getRepository('LDSPlatformBundle:CommentaireEtablissement')
      ->findOneById($id);
 $particulier=$commentaire->getParticulier();
  $etablissement_maj=$commentaire->getEtablissement();
  $siret=$etablissement_maj->getSiret();
          $nbvote=$etablissement_maj->getNombreNote()-1;
          $etablissement_maj->setNombreNote($nbvote);
          //maj moy
          $moy=($etablissement_maj->getMoyenne()*($nbvote+1)-$commentaire->getNote())/$etablissement_maj->getNombreNote();
          $etablissement_maj->setMoyenne($moy);
          //maj coeff
          $coeff=$etablissement_maj->getNombreNote()*$etablissement_maj->getMoyenne();
          $etablissement_maj->setCoefficient($coeff);
  $em->remove($commentaire);
  $em->flush();
  if ($redirection==0) {
   return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 1));
  }
  return $this->redirectToRoute('lds_platform_admin_detail_particulier',array('login' => $particulier));
}

}