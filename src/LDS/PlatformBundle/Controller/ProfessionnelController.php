<?php

namespace LDS\PlatformBundle\Controller;

use LDS\PlatformBundle\Entity\Etablissement;
use LDS\PlatformBundle\Entity\Evenement;
use LDS\PlatformBundle\Entity\Tag;
use LDS\PlatformBundle\Entity\MessageAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * ProfessionnelController
 *
 * @author Boutaljante, Ruiz Ramirez, Herve, Verbrugge
 *
 * Ce controller gère tout ce qui se rapporte aux actions possibles du professionnel connecté. Il a la possibilité 
 * d'avoir une liste de tous ses etablissements. Pour chacuns de ces etablissements, il peut modifier leurs 
 * informations, ajouter un nouvel evenement, avoir un acces direct aux commentaires qui ont été émis.
 *
 */

class ProfessionnelController extends Controller {

	  public function accueilAction() {
    
  	  }


/**
  * Liste Etablissements
  *
  * Fonction qui permet au pro d'avoir un acces a la liste de tous ses etablissements et a son etablissement en 
  * attente. Un lien vers les details et vers la suppression de ceux ci sont disponibles.
  *
  *
  **/
	public function listeEtablissementAction() {
		$session = $this->get('session');
		$login=$session->get('compte');
		$repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Etablissement');
		$listeEtablissementNonValide = $repository->listeEtablissementNonValide($login);
		$listeEtablissementValide = $repository->listeEtablissementValide($login);

		return $this->render('LDSPlatformBundle:Pro:listeEtablissement.html.twig', array(
			'listeEtablissementNonValide' => $listeEtablissementNonValide,
			'listeEtablissementValide' => $listeEtablissementValide,
		));
	}

/**
  * Creer evenement
  *
  * Fonction qui permet au pro de creer un evenement pour l'un de ses etablissements.
  *
  **/
	public function creerEvenementAction($siret, Request $request) {
	 	//booleen pour prevenir d'une erreur
	    $event_programme=false;
	    $event_en_attente=false;

	    //messages d'erreur pour TWIG
	    $erreur_event='';
	    $erreur_attente='';
	    $listeErreurs = [];

		$evenement = new Evenement();
		$em = $this->getDoctrine()->getManager();
		$repository = $this
		->getDoctrine()
		->getManager()
		->getRepository('LDSPlatformBundle:Etablissement');

		$repository2 = $this
		->getDoctrine()
		->getManager()
		->getRepository('LDSPlatformBundle:Evenement');
		
		$etablissement = new Etablissement();
		$etablissement = $repository->findOneBySiret($siret);
		//on recupere la liste de tous les evenements de l'etablissement concerné
		$listeEvenement=$repository2->findBy(array('etablissement' => $etablissement));
		//on recupere la date du jour.
	    $now=date('d-m-Y');
        foreach ($listeEvenement as $e) {
        	//Si la date de fin d'un evenement > date d'ajd alors cela signifie qu'un evenement est deja programmé
			if ((strtotime($e->getDateFin()->format('d-m-Y'))>=strtotime($now))) {
				//le bool passe a faux
				$event_programme=true;
			}
			//si un evenement n'est pas valide alors il y'a deja un evnement en attente de validation
			if ($e->getValide()==false) {
				//le bool passe a vrai
				$event_en_attente=true;
			}
		}

		//Formulaire de creation de l'evenement
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $evenement);
		$formBuilder
		->add('nom',      TextType::class)
		->add('description',   TextareaType::class)
		->add('dateDebut',    DateType::class)
		->add('dateFin',    DateType::class)
		->add('heureDebut',    TimeType::class)
		->add('heureFin',    TimeType::class)
		->add('photo1', FileType::class)
		->add('photo2', FileType::class, array('required' => false))
		->add('photo3', FileType::class, array('required' => false))
		->add('creer',      SubmitType::class);
		$form = $formBuilder->getForm();

		//lorsque le formulaire est soumis:
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			//on verifie que les 2 booleen soient faux pour continuer
			if ($event_programme==false) {
				if ($event_en_attente==false) {
			$evenement->setEtablissement($etablissement);
			//la date de demande prend la date de l'instant ou le formualaire a ete envoyé
			//faciliation du tri pour l'admin
			$evenement->setDateDemande(new \DateTime('now'));
			$i=0;$get='';$set='';
			//POur les 4 photos on creer une image unique
			for ($i=1; $i<4; $i++) {
				$get='getPhoto'.$i;
				$set='setPhoto'.$i;
				$file = $evenement->$get();
				if ($file != '') {
					$fileName = md5(uniqid()).'.'.$file->guessExtension();
					$file->move(
						$this->getParameter('avatar_directory'),
						$fileName
					);
					$evenement->$set($fileName);
				}
			}
			//on ajoute le nouvel event dans la BD
			$em->persist($evenement);
			$em->flush();
			//on retourne sue les details de l'event modifié
			return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 2));
			//Craetion des messages pour TWIG en cas d'erreur.
			} else {
				$listeErreurs['erreur_attente']="Vous avez deja un evenement en attendre que celui soit validé par l'admin";
			}
			} else {
				$listeErreurs['erreur_event']="Vous avez déja un evenement en cours ! Veuillez attendre la fin de celui-ci pour en reproposer un autre";
			}
		}

		return $this->render('LDSPlatformBundle:Pro:creerEvenement.html.twig', array(
			'form' => $form->createView(),
			'listeErreurs' => $listeErreurs,
			'etablissement' => $etablissement,
		));

	}

	/**
  * Modifier informations etablissement
  *
  * Fonction qui permet au pro de modifier les informations d'un de ses etablissements
  *
  **/
	public function modifierInfosEtablissementAction($siret, Request $request) {
		$etablissement = new Etablissement();
		$em = $this->getDoctrine()->getManager();
		$etablissement =  $em->getRepository(Etablissement::class)->findOneBySiret($siret);
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $etablissement);
		$lat_ancienne=$etablissement->getLattitude();
		$long_ancienne=$etablissement->getLongitude();

    // On ajoute les champs de l'entité que l'on veut à notre formulaire
		$formBuilder
		->add('nomEtablissement',     TextType::class)
		->add('ville',   TextType::class)
		->add('codePostal',    TextType::class)
		->add('adresse', TextType::class)
		->add('telephone',    TextType::class)
		->add('joursOuverts', ChoiceType::class, array(
			'choices' => array(
				'lundi' => 'lundi',
				'mardi' => 'mardi',
				'mercredi' => 'mercredi',
				'jeudi' => 'jeudi',
				'vendredi' => 'vendredi',
				'samedi' => 'samedi',
				'dimanche' => 'dimanche',
			),'multiple'     => true,
			'expanded' => true,
			'mapped' => false))
		->add('heureOuvertureLundi', TimeType::class, array('required' => false))
		->add('heureFermetureLundi', TimeType::class, array('required' => false))
		->add('heureOuvertureMardi', TimeType::class, array('required' => false))
		->add('heureFermetureMardi', TimeType::class, array('required' => false))
		->add('heureOuvertureMercredi', TimeType::class, array('required' => false))
		->add('heureFermetureMercredi', TimeType::class, array('required' => false))
		->add('heureOuvertureJeudi', TimeType::class, array('required' => false))
		->add('heureFermetureJeudi', TimeType::class, array('required' => false))
		->add('heureOuvertureVendredi', TimeType::class, array('required' => false))
		->add('heureFermetureVendredi', TimeType::class, array('required' => false))
		->add('heureOuvertureSamedi', TimeType::class, array('required' => false))
		->add('heureFermetureSamedi', TimeType::class, array('required' => false))
		->add('heureOuvertureDimanche', TimeType::class, array('required' => false))
		->add('heureFermetureDimanche', TimeType::class, array('required' => false))
		->add('description', TextareaType::class)
		->add('tags', EntityType::class, array(
			'class'        => 'LDSPlatformBundle:Tag',
			//on ajoute une fonction pour classer les tags par ordre alphabetique
			'query_builder' => function () {
				return $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:Tag')->createQueryBuilder('t')
				->orderBy('t.nom', 'ASC');
			},
			'choice_label' => 'nom',
			'multiple'     => true,
			'expanded' => true,))
		->add('photo1', FileType::class, array('required' => false, 'data_class' => null))
		->add('photo2', FileType::class, array('required' => false, 'data_class' => null))
		->add('photo3', FileType::class, array('required' => false, 'data_class' => null))
		->add('photo4', FileType::class, array('required' => false, 'data_class' => null))
		->add('photo5', FileType::class, array('required' => false, 'data_class' => null))
		->add('modifier',SubmitType::class);
		$form = $formBuilder->getForm();

		//tableau des jours de la semaine pour simplifier le code
		$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
		$jour_ouverture=array();

		//pour chaque jour on voit si l'horraire est egale a nulle
		//si non alors la case du tableau prend comme valeur true si oui c'est false
		for ($i=0; $i<7; $i++) {
		$ouverture='getHeureOuverture'.$jours[$i];
			if ($etablissement->$ouverture()!=null) {
				$jour_ouverture[$i]=true;
			} else {
				$jour_ouverture[$i]=false;
			}
		}

		//on recupere les 5 photos de l'etablissement pour ne pas les perdre.
		$photo = array();
		$photo[1] = $etablissement->getPhoto1();
		$photo[2] = $etablissement->getPhoto2();
		$photo[3] = $etablissement->getPhoto3();
		$photo[4] = $etablissement->getPhoto4();
		$photo[5] = $etablissement->getPhoto5();


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$i=0;$get='';$set='';
			//pour chaque photo si le champ n'est pas vide alors on remplace par la nouvelle photo
			for ($i=1; $i<6; $i++)  {
				$get='getPhoto'.$i;
				$set='setPhoto'.$i;
				$file = $etablissement->$get();
				if ($file != '') {
					$fileName = md5(uniqid()).'.'.$file->guessExtension();
					$file->move(
						$this->getParameter('avatar_directory'),
						$fileName
						);
						$etablissement->$set($fileName);
					} else if ($photo[$i] != null) {
						$etablissement->$set($photo[$i]);
					}
				}

				//on recupere la lat et la long grace aux variables de session (utilsiation d'ajax)
				$lat=$this->get('session')->get('lat_etablissement');
				$long=$this->get('session')->get('long_etablissement');

				//si on ne modifie pas la position de l'etablissement alors on garde l'ancienne position de l'etablissement
				if ($lat==null || $long==null) {
					$lat=$lat_ancienne;
					$long=$long_ancienne;
				} 

				$etablissement->setLattitude($lat);
				$etablissement->setLongitude($long);
			//on remplace l'etablissement par le nouveau
			$em->persist($etablissement);
			$em->flush();
			return $this->redirectToRoute('lds_platform_detail',array('siret' => $siret, 'id' => 2));
		}

		return $this->render('LDSPlatformBundle:Pro:modifierEtablissement.html.twig', array(
			'form' => $form->createView(),
			'jour_ouverture' => $jour_ouverture,
			'etablissement' => $etablissement,
		));
	}	

  /**
  * Modifier informations Evenement
  *
  * Fonction qui permet au pro de modifier les informations d'un de ses evenements
  *
  **/
	public function modifierInfosEvenementAction($id, Request $request) {
		$evenement = new Evenement();
		$em = $this->getDoctrine()->getManager();
		$evenement =  $em->getRepository(Evenement::class)->findOneById($id);

		//creation du formulaire qui recupere les attributs de l'evnement choisi
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $evenement);
		$formBuilder
		->add('nom',      TextType::class)
		->add('description',   TextareaType::class)
		->add('dateDebut',    DateType::class)
		->add('dateFin',    DateType::class)
		->add('heureDebut',    TimeType::class)
		->add('heureFin',    TimeType::class)
		->add('photo1', FileType::class, array('required' => false, 'data_class' => null))
		->add('photo2', FileType::class, array('required' => false, 'data_class' => null))
		->add('photo3', FileType::class, array('required' => false, 'data_class' => null))
		->add('modifier',      SubmitType::class);
		$form = $formBuilder->getForm();

		//tableau des 3 photos de l'event pour eviter de les perdres
		$photo = array();
		$photo[1] = $evenement->getPhoto1();
		$photo[2] = $evenement->getPhoto2();
		$photo[3] = $evenement->getPhoto3();

		//lorsque le formulaire est soumis:
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			//recuperation des nouvelles photos si l'user en a mis.
			$i=0;
			$get='';
			$set='';
			for ($i=1; $i<4; $i++)  {
				$get='getPhoto'.$i;
				$set='setPhoto'.$i;
				$file = $evenement->$get();
				if ($file != '') {
					$fileName = md5(uniqid()).'.'.$file->guessExtension();
					$file->move(
						$this->getParameter('avatar_directory'),
						$fileName
						);
						$evenement->$set($fileName);
					} else if ($photo[$i] != null) {
						$evenement->$set($photo[$i]);
					}
				}
				$em->persist($evenement);
				$em->flush();
				return $this->redirectToRoute('lds_platform_pro_detail_evenement',array('id' => $id));
			}
			return $this->render('LDSPlatformBundle:Pro:modifierEvenement.html.twig', array(
				'form' => $form->createView(),
				'evenement' => $evenement,
			));
		}

  /**
  * Modifier Demander tag
  *
  * Fonction qui permet au pro de demander un nouveau tag a l'admin si son etablissement propose une
  * activité non proposée dans la liste de la BD.
  *
  **/
		public function demanderTagAction(Request $request) {
			//init des vraibles tag
			$tag = new Tag();
			$tag_verif = new Tag();

			//messages d'erreurs pour TWIG
			$tag_erreur='';
			$spam="";
			$listeErreurs = [];

			$session = $this->get('session');
			$login=$session->get('compte');

			$em = $this->getDoctrine()->getManager();

			//formulaire pour le nouveau tag
			$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $tag);
			$formBuilder
			->add('nom',      TextType::class)
			->add('description',   TextareaType::class)
			->add('demander',      SubmitType::class);
			$form = $formBuilder->getForm();


			if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

				//on recupere le tag avec le nom envoyé
				$tag_verif = $em->getRepository(Tag::class)->findOneByNom($tag->getNom());
				//on recupere les tags en cours de demande envoyés par l'user
				$listeDemandeTag=$em->getRepository(Tag::class)->findBy(array('proprietaire' => $login, 'valide' => 0));
				//si le tag n'est pas deja présent et que l'user n'a pas deja 2 demandes en cours 
				if (count($tag_verif)==0) {
					if (count($listeDemandeTag)<2) {

						//on ajoute le nouveau tag en tant que non valide
						$tag->setProprietaire($login);
						$tag->setValide(0);
						$em->persist($tag);
						$em->flush();
						return $this->redirectToRoute('lds_platform_pro_demande_tag');
					} else {
						$listeErreurs['spam']="Vous avez deja fais 2 demandes de tag sans que l'administrateur n'y ai répondu";
					}
				} else {
					$listeErreurs['tag_erreur']='nom du tag déja existant ou en cours de demande !';
				}
			}

			return $this->render('LDSPlatformBundle:Pro:demanderTag.html.twig', array(
				'form' => $form->createView(),
				'listeErreurs' => $listeErreurs,
			));
		}

  /**
  * Message Admin
  *
  * Fonction qui permet au pro d'envoyer un message à l'admin en cas de problèmes ou en 
  * cas d'une quelconque requete.
  *
  *
  **/
		public function messageAdminAction(Request $request) {
		$message = new MessageAdmin();

		//message d'erreur pour TWIG
		$login='';
		$spam='';
		$listeErreurs = [];

		$session = $this->get('session');
		$login=$session->get('compte');

		$em = $this->getDoctrine()->getManager();
		//formulaire du message
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $message);
		$formBuilder
		->add('titre',      TextType::class)
		->add('message',   TextareaType::class)
		->add('envoyer',      SubmitType::class);
		$form = $formBuilder->getForm();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			//on cherche tous les messages envoyés à l'admin
			$listeMessage = $em->getRepository(MessageAdmin::class)->findByUser($login);

			//si il y'a déja 2 messages envoyés alors on bloque l'envoi pour eviter le spam.
			if (count($listeMessage)<2) {
			$message->setUser($login);
			//on ajoute la date du jour pour previligié les requetes les plus anciennes.
			$message->setDate(new \DateTime('now'));
			$em->persist($message);
			$em->flush();
			} else {
				$listeErreurs['spam']="Vous avez déjà envoyé 2 messages à l'admin sans que celui-ci n'ai eu le temps de répondre";
			}

		}
		return $this->render('LDSPlatformBundle:Pro:messageAdmin.html.twig', array(
				'form' => $form->createView(),
				'listeErreurs' => $listeErreurs,
			));
		}

  /**
  * Supprimer etablissement
  *
  * Fonction qui permet au pro de supprimer l'un de ses etablissements
  *
  *
  **/
		public function supprimerEtablissementAction($siret, Request $request) {
			$em = $this->getDoctrine()->getManager();
  			$etablissement = $em->getRepository(Etablissement::class)->findOneBySiret($siret);

  			$em->remove($etablissement);
 			$em->flush();
  			return $this->redirectToRoute('lds_platform_pro_liste_etablissement');
		}

   /**
  * Supprimer evenement
  *
  * Fonction qui permet au pro de supprimer l'un de ses evenements
  *
  *
  **/
		public function supprimerEvenementAction($id, Request $request) {
			$em = $this->getDoctrine()->getManager();
  			$evenement = $em->getRepository(Evenement::class)->findOneById($id);
  			$siret = $evenement->getEtablissement()->getSiret();
  			$em->remove($evenement);
 			$em->flush();
  			return $this->redirectToRoute('lds_platform_detail', array('siret' => $siret, 'id' => 2));
		}
}
