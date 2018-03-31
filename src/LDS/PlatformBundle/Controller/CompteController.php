<?php

namespace LDS\PlatformBundle\Controller;

use LDS\PlatformBundle\Entity\Etablissement;
use LDS\PlatformBundle\Entity\Professionnel;
use LDS\PlatformBundle\Entity\Particulier;
use LDS\PlatformBundle\Entity\VillesFranceFree;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * AdminController
 *
 * @author Boutaljante, Ruiz Ramirez, Herve, Verbrugge
 *
 * Ce controller a pour but de gerer tout ce qui concerne les comptes des utilisateurs
 * Les principales fonctionnalités sont l'inscription du professionnel et de son etablissement.
 * L'inscription du particulier (en une etape), et la connexion avec le choix entre professionnel 
 * et particulier.
 *
 */

class CompteController extends Controller {

  /**
  * inscription particulier
  *
  * Fonction permettant a un nouveau particulier de s'inscrire
  *
  **/
	public function inscriptionAction(Request $request) {
		$particulier = new Particulier();
		$erreur=true;
		$type=0;
		$listeErreurs = [];
		$ville="";

		// creation du formulaire pour l'inscription
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $particulier);
		$formBuilder
		->add('login',      TextType::class)
		->add('password',     PasswordType::class, array('label' => 'Mot de passe'))
		->add('confirmer',     PasswordType::class, array('mapped' => false))
		->add('nom',   TextType::class)
		->add('prenom',    TextType::class)
		->add('email',    EmailType::class)
		->add('telephone',    TextType::class, array('required' => false))
		->add('codePostal',    TextType::class)
		->add('ville',    TextType::class)
		->add('sexe', ChoiceType::class, array(
			'choices' => array(
				'Homme' => 'homme',
				'Femme' => 'femme',
			), 'required' => false))
		->add('avatar', FileType::class, array('required' => false))
		->add('inscription',      SubmitType::class);
		$form = $formBuilder->getForm();

		$repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree'); 

		//Lorsque l'on appui sur le bouton inscription cette action se produit(POST)
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			$file = $particulier->getAvatar();
			if ($file != '') {
				$fileName = md5(uniqid()).'.'.$file->guessExtension();
				$file->move(
					$this->getParameter('avatar_directory'),
					$fileName
				);
				$particulier->setAvatar($fileName);	
			}
			else {
				$mauvais_format_fichier="Veuillez choisir un bon format de fichier (jpeg, png, gif)";
				$erreur=false;
			}
				

			//on verifie que le login soit unique
			$em = $this->getDoctrine()->getManager();
			$valid_login =$em
			->getRepository('LDSPlatformBundle:Particulier')
			->count_login($particulier->getLogin());

			//on verifie que le mail soit unique
			$valid_mail =$em
			->getRepository('LDSPlatformBundle:Particulier')
			->count_mail($particulier->getEmail());

			$countVille=count($repository->findOneBy(array('villeNomReel' => $particulier->getVille(), 'villeCodePostal' => $particulier->getCodePostal())));
			//on compare le password à confirmer pour voir s'ils correspondent
			if ($particulier->getPassword() == $form['confirmer']->getData()) {
				$valid_password = true;
			} else {
				$valid_password = false;
			}

			//Gestion des differents cas d'erreurs avec un message a la cle
			//a la moindre erreur le booleen erreur d'inverse pour empecher l'inscription

			if ($valid_login==true) {
				$listeErreurs['erreur_login']="login déja utilisé";
				$erreur=false;
			}

			if ($valid_mail==true) {
				$listeErreurs['erreur_mail']="email déja utilisé";
				$erreur=false;
			}

			if ($valid_password==false) {
				$listeErreurs['erreur_password']="Les mots de passe ne correspondent pas";
				$erreur=false;
			}

			if($countVille==0) {
				$listeErreurs['erreur_ville']="Pas de ville avec ce nom ou ce code Postal";
				$erreur=false;
			}

			//Si tout s'est bien passé on se redirige vers la page d'accueil et on commit le nouveau particulier
			if ($erreur==true) {
				//methode pour hasher le mot de passe -> securite des users
				$hash=password_hash($particulier->getPassword(), PASSWORD_DEFAULT);
				$particulier->setPassword($hash);
				$particulier->setValide(true);
				//on ajoute a notre BD le nouveau particulier
				$em->persist($particulier);
				$em->flush();
				return $this->redirectToRoute('lds_platform_homepage');
			}
		}

		return $this->render('LDSPlatformBundle:Compte:inscriptionParticulier.html.twig', array(
			'form' => $form->createView(),
			'listeErreurs' => $listeErreurs,
			'type' => $type,
		));
	}


  /**
  * Deconnexion
  *
  * Fonction permettant a un pro ou a un particulier de se deconnecté en supprimant toutes variables de session
  *
  **/
	public function deconnexionAction() {
    	$this->get('session')->clear();
    	return $this->redirectToRoute('lds_platform_homepage');
	}


	
  /**
  * Connexion
  *
  * Fonction permettant a un pro ou a un particulier de se conncté au site et avoir acces aux avantges 
  *
  **/
	public function connexionAction(Request $request) {
		$particulier = new Particulier();
		$professionnel = new Professionnel();
		$listeErreurs = [];
		$em = $this->getDoctrine()->getManager();
		$erreur='';
    // On crée le FormBuilder grâce au service form factory
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $particulier);

    // On ajoute les champs de l'entité que l'on veut à notre formulaire
		$formBuilder
		->add('login',      TextType::class)
		->add('password',     PasswordType::class)
		->add('compte', ChoiceType::class, array(
			'choices' => array(
				'Particulier' => 'Particulier',
				'Professionnel' => 'Professionnel',
			), 'mapped' => false, 'label' => 'Choisir un type de compte'))
		->add('connexion',      SubmitType::class);
		$form = $formBuilder->getForm();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			// on recupere le type de compte (pro ou particulier)
			$compte = $form['compte']->getData();
			if ($compte=='Particulier') {
				$repository=$em->getRepository('LDSPlatformBundle:Particulier');

				$particulier_verify=$particulier;

				//on verifie que ce login existe 
				$particulier=$repository->findOneBy(array('login' => $particulier->getLogin()));

				if ($particulier!=null) {
				//s'il existe alors on verifie que le password correspond a celui de la BD avec la fc d=qui verifie le hachage
				if(password_verify ($particulier_verify->getPassword() , $particulier->getPassword() )) {
					//on verifie que le particulier ne soit pas banni (valide=true si non banni)
					if ($particulier->getValide()==false) {
			$listeErreurs["banni"]='Vous avez été banni !';
  					} else {

						//le champ de session particulier passe à ok pour que la vue sache qu'il est connecté
						$this->get('session')->set('compte', $particulier->getLogin());
  						$this->get('session')->set('particulier', 'ok');
  						$this->get('session')->set('pro', 'non');
  						/*return $this->redirectToRoute('lds_platform_homepage', array(
                'message' => "Vous êtes connecté !"
              ));*/

              return $this->render('LDSPlatformBundle:Message:message.html.twig', array(
                'message' => "Vous êtes maintenant connecté à votre compte !"
              ));
            }
          }
          else {
           $listeErreurs["erreur_authentification"]="Erreur d'authentification : le mot de passe est peut-être incorrect";
         }
       } else {
        $listeErreurs["erreur_inexistant"]="Ce compte particulier n'existe pas";
      }
    }
			
				//meme chose pour le pro mais ils ne peuvent pas etre bannis du site
    if ($compte=='Professionnel') {

     $repository=$em
     ->getRepository('LDSPlatformBundle:Professionnel');

     $professionnel_verify=$particulier;
     $professionnel=$repository->findOneBy(array('login' => $professionnel_verify->getLogin(), 'valide' => 1));

     if ($professionnel !=null) {
      if(password_verify ($professionnel_verify->getPassword() ,$professionnel->getPassword() )) {
					//le champ de session professionnel passe à ok pour que la vue sache qu'il est connecté
       $this->get('session')->set('compte', $particulier->getLogin());
       $this->get('session')->set('particulier', 'non');
       $this->get('session')->set('pro', 'ok');
       return $this->render('LDSPlatformBundle:Message:message.html.twig', array(
        'message' => "Vous êtes maintenant connecté à votre compte professionnel !"
      ));
     }
     else {
       $listeErreurs["erreur_authentification"]="Erreur d'authentification : le mot de passe est peut-être incorrect";
     }
   } else {
    $listeErreurs["erreur_inexistant"]="Ce compte professionnel n'existe pas";
  }
}
		}
		
		return $this->render('LDSPlatformBundle:Compte:connexionParticulier.html.twig', array(
			'form' => $form->createView(),
			'listeErreurs'  => $listeErreurs,
		));
	}




  /**
  * inscription professionnel
  *
  * Fonction permettant a un pro de s'inscrire au site LDS
  *
  **/
	public function inscriptionProAction($id, Request $request) {

		// cas ou l'user arrive sur a page d'inscription la 1ere fois
		if ($id==1) {
			$professionnel = new Professionnel();
		}

		//cas ou l'user avait rempli le formulaire et est passé a l'etape suivant mais au final est revenu a la premiere etape
		//avec le bouton retour.
		if ($id==2) {
			$session = $request->getSession();
			//on recupere alors les donnees presentes dans la variable de session
			$professionnel = $session->get('inscription_pro');
		}

		//differents messages d'erreur (init -> vide)
		$erreur=true;
		$listeErreurs = [];
		
		//Formulaire d'inscription
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $professionnel);
		$formBuilder
		->add('login',      TextType::class)
		->add('password',     PasswordType::class, array('label' => 'Mot de passe'))
		->add('confirmer',     PasswordType::class, array('mapped' => false))
		->add('nom',   TextType::class)
		->add('prenom',    TextType::class)
		->add('email',    EmailType::class)
		->add('telephone',    TextType::class, array('required' => false))
		->add('codePostal',    TextType::class)
		->add('ville',    TextType::class)
		->add('sexe', ChoiceType::class, array(
			'choices' => array(
				'Homme' => 'homme',
				'Femme' => 'femme',
			),'required' => false))->add('suivant', SubmitType::class);
		$professionnel->setValide(false);
		$form = $formBuilder->getForm();

		
		//POST du formulaire
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {


			$em = $this->getDoctrine()->getManager();
			$valid_login =$em
			->getRepository('LDSPlatformBundle:Professionnel')
			->count_login($professionnel->getLogin());

			$valid_mail =$em
			->getRepository('LDSPlatformBundle:Professionnel')
			->count_mail($professionnel->getEmail());

			if ($professionnel->getPassword() == $form['confirmer']->getData()) {
				$valid_password = true;
			} else {
				$valid_password = false;
			}
			
			//On affiche les messages en cas d'erreur 
			// a la moindre erreur le booleen erreur passe a false -> empeche l'inscription
			if ($valid_login==true) {
  			$listeErreurs["erreur_login"]="Login déja utilisé";
  			$erreur=false;
  		}

  		if ($valid_mail==true) {
  			$listeErreurs["erreur_mail"]="email déjà utilisée";
  			$erreur=false;
  		}

  		if ($valid_password==false) {
  			$listeErreurs["erreur_password"]="Les mots de passe ne correspondent pas";
  			$erreur=false;
  		}

			//il faut absolument que la ville choisie soit dans la table VilleFranceFree
  		$repository=$em->getRepository('LDSPlatformBundle:VillesFranceFree');
  		$countVille=count($repository->findOneBy(array('villeNomReel' => $professionnel->getVille(), 'villeCodePostal' => $professionnel->getCodePostal())));

  		if ($countVille==0) {
  			$listeErreurs["erreur_ville"]="Code postal ne correspondant pas à la ville";
  			$erreur=false;
  		}

			//si tout s'est bien passé alors le pro passe en variable de session pour la prochaine etape
			if ($erreur==true) {
				$em = $this->getDoctrine()->getManager();
				//hachage du password
				$hash=password_hash($professionnel->getPassword(), PASSWORD_DEFAULT);
				$professionnel->setPassword($hash);
				$session = $this->get('session');
				$session->set('inscription_pro', $professionnel);

				return $this->redirectToRoute('lds_platform_inscription_etablissement', array('id' => 1, 
					'id2' => 1));
			}
		}
		return $this->render('LDSPlatformBundle:Compte:inscriptionProfessionnel.html.twig', array(
			'form' => $form->createView(),
			'pro' => $professionnel,
			'listeErreurs' => $listeErreurs,
		));
	}


	
  /**
  * inscription etablissement
  *
  * Fonction permettant a un pro d'inscrire son etablissmeent pour la 1ere fois ou
  * d'en inscrire un autre, ou encore a l'admin d'en ajouter un 
  * fonction condensée pour eviter la redondance dans le code 
  *
  **/
	public function inscriptionEtablissementAction($id,$id2, Request $request) {
		$etablissement = new Etablissement();
		$pro = new Professionnel();
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();

		//inscription du pro
		if ($id==1) {
			$pro = $session->get('inscription_pro');
		}

		//ajout d'un etablissement par un pro deja inscrit
		if ($id==2) {
			$login = $session->get('compte');
			$pro =$em->getRepository('LDSPlatformBundle:Professionnel')->findOneByLogin($login);
		}

		//ajout d'un etablissement par l'admin
		if ($id==3) {
			$pro =$em->getRepository('LDSPlatformBundle:Professionnel')->findOneByLogin("admin");
		}

		$repository=$em->getRepository('LDSPlatformBundle:VillesFranceFree');
		$ville=$repository->findOneBy(array('villeNomReel' => $pro->getVille(), 'villeCodePostal' => $pro->getCodePostal()));
		
		
			$long_ancienne=$ville->getVilleLongitudeDeg();
			$lat_ancienne=$ville->getVilleLatitudeDeg();
			$long=$long_ancienne;
			$lat=$lat_ancienne;


		//on ajoute directement le mail du pro comme mail a l'etablissement et le login du pro comme proprietaire
		$etablissement->setEmail($pro->getEmail());
		$etablissement->setProprietaire($pro->getLogin());

		//booleen pour gerer les cas d'erreur dans le formulaire
		$erreur=true;
		//message d'erreurs du formulaire
		$listeErreurs = [];
		/*on initialise le variable de session changer_localisatiin a false -> elle changere si l'user chnage la localisation 
		de son etablissement*/
		$this->get('session')->set('changer_localisation', false);

		

		//on trouve tous les tags valides pour les ajouter au formulaire (multichoix)
		$listeTag =$em
		->getRepository('LDSPlatformBundle:Tag')->trouverTagValide();
		$tab_tag = array();
		$i=0;
		foreach ($listeTag as $tag) {
			$tab_tag[$i]=$tag->getNom();
			$i++;
		}

		//creation du formulaire
		$formBuilder2 = $this->get('form.factory')->createBuilder(FormType::class, $etablissement);
		$formBuilder2
		->add('siret',      NumberType::class)
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
				->where('t.valide = true')
				->orderBy('t.nom', 'ASC');
			},
			'choice_label' => 'nom',
			'multiple'     => true,
			'expanded' => true,))
		->add('photo1', FileType::class)
		->add('photo2', FileType::class, array('required' => false))
		->add('photo3', FileType::class, array('required' => false))
		->add('photo4', FileType::class, array('required' => false))
		->add('photo5', FileType::class, array('required' => false))
		->add('inscription',SubmitType::class,  array('label' => 'Ajouter établissement'));

		$form2 = $formBuilder2->getForm();

		if ($request->isMethod('POST') && $form2->handleRequest($request)->isValid()) {

			$valid_siret =$em->getRepository('LDSPlatformBundle:Etablissement')->count_siret($etablissement->getSiret());

			//on prend la date d'ajd comme etant la date de demande d'ajout (facilité l'ordre de la liste de l'admin)
			$etablissement->setDateDemande(new \DateTime('now'));

			//pour chaque photo on creer un id unique pour les recuperer dans le dossier avatar
			$i=0;
			$get='';
			$set='';
			for ($i=1; $i<6; $i++) {
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
				}
			}
			
				// cas ou le siret est deja utilisé
			if ($valid_siret==true) {
        $listeErreurs["erreur_siret"]="Siret déja utilisé par un autre établissement";
        $erreur=false;
      }

      $listeDemandeEtablissement=$em->getRepository('LDSPlatformBundle:Etablissement')
      ->findBy(array('proprietaire' => $pro->getLogin(), 'valide' => 0));

      $listeEtablissement=$em->getRepository('LDSPlatformBundle:Etablissement')
      ->findBy(array('proprietaire' => $pro->getLogin(), 'valide' => 1));

			//on limite a une le nombre de demande simultanement pour un user
      if (count($listeDemandeEtablissement)>0 && $id!=3) {
        $listeErreurs["deja_demande"]="Vous avez déjà une demande d'établissement en cours";
        $erreur=false;
      }

			//on limite a 5 le nombre d'etablissemnts d'un pro
      if (count($listeEtablissement)>=5 && $id!=3) {
        $listeErreurs["spam_etablissement"]="Vous avez déjà 5 établissements";
        $erreur=false;
      }


      $repository=$em->getRepository('LDSPlatformBundle:VillesFranceFree');
      $countVille=count($repository->findOneBy(array('villeNomReel' => $pro->getVille(), 'villeCodePostal' => $pro->getCodePostal())));

			//cas ou il n'y a pas de villes avec la combinaison nom-codePostal entré par l'user
      if ($countVille==0) {
        $listeErreurs["erreur_ville"]="Le code postal ne correspond pas à la ville";
        $erreur=false;
      }

			//cas ou il n'y a aucun erreur dans le formulaire
			if ($erreur==true) {
				$etablissement->setValide(false);
				$em = $this->getDoctrine()->getManager();
				$repository = $em->getRepository('LDSPlatformBundle:VillesFranceFree');
				$ville=$repository->findOneByVilleCodePostal($etablissement->getCodePostal());

				/*si l'user a changé de localisation via google map alors lat et long prennent la valeur de la 
				variable de session sinon c'est la valeur du début*/
				$lat=$this->get('session')->get('lat_etablissement');
				$long=$this->get('session')->get('long_etablissement');

				if ($lat==null || $long==null) {
					$lat=$lat_ancienne;
					$long=$long_ancienne;
				}
				$etablissement->setLattitude($lat);
				$etablissement->setLongitude($long);

				//on inscrit alors en meme temps le pro et l'etablissment comme etant non valide
				$em->persist($pro);
				$em->persist($etablissement);
				$em->flush();
				$this->get('session')->set('changer_localisation', false);
							return $this->render('LDSPlatformBundle:Message:message.html.twig', array(
			'form' => $form2->createView(),
			'message' => 'Votre établissement est en cours de validation par notre équipe',
		));
			}

			$this->get('session')->set('changer_localisation', false);
		}

		return $this->render('LDSPlatformBundle:Compte:inscriptionEtablissement.html.twig', array(
			'form' => $form2->createView(),
			'pro' => $pro,
			'lat' => $lat,
			'long' => $long,
			'listeErreurs' => $listeErreurs,
			'id' => $id,
			
		));
	}

}