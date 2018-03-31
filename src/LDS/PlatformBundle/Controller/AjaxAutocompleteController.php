<?php
namespace LDS\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use LDS\PlatformBundle\Entity\VillesFranceFree;
use LDS\PlatformBundle\Entity\Professionnel;
use LDS\PlatformBundle\Entity\MessageAdmin;

/**
 * AjaxAutoCompleteController
 *
 * @author Boutaljante, Ruiz Ramirez, Herve, Verbrugge
 *
 *Ce controller gère tout ce qui se rapporte à ajax -> pour passer des variables JS a notre controller.
 *Nous l'utilisons pour faire de l'autocompletion sur les villes lorsque l'user en cherche une dans nos
 *formulaires. Cela lui permet de ne pas faire d'erreurs sur l'orthographe de sa ville car avoir le nom exact
 *estprimordial pour nous. Ensuite sur la page d'accueil lorsque l'user cherche un etablissement a partir de son 
 *il y'a des propositions d'etablissments en fonction de ce qu'il ecrit.
 *Ensuite pour recuperer les variables (lat et long) de la geolocalisation google map il nous fallait une fonction
 *Lorsque l'user entre un nouvel etablissement il peut changer manuellement sa position sur la carte et donc pour 
 *recuperer les nouvelles coordonnees il y'avait besoin d'un lien entre le js et le controller 
 *
 *
 */

/**
* VARIABLES DE SESSION:
* 
* ma_lat --> latitude recuperee via la localisation de l'user (Geolocalisation google map)
* ma_long --> longitude recuperee via la localisation de l'user (Geolocalisation google map)
* lat_etablissement --> latitude du nouvel etablissment entrain d'etre inscrit ou d'etre modifié
* long_etablissement --> longitude du nouvel etablissment entrain d'etre inscrit ou d'etre modifié
* email_reponse --> email du pro auquel l'admin repond
*
**/


class AjaxAutocompleteController extends Controller {

  /**
  * Autocompletion ville
  *
  * Fonction permettant de completer automatiquement ce qu'ecrit l'user dans un champ texte pour une ville.
  *
  *
  **/
    public function autocompletionVilleAction(Request $request) {
        $listeVille="";
        //on recupere le texte laissé par la fonction js du champ de texte ville
        $data = $request->get('input');    
        $repository = $this->getDoctrine()->getManager()->getRepository('LDSPlatformBundle:VillesFranceFree');
        //on appelle la fonction qui recupere 6 elements correspondants au debut du texte tapé 
        $results=$repository->autocompletion($data);

        //on transforme la balise match en une liste contenant les 6 villes recuperes via le repository
        $listeVille = '<ul id="matchList">';
        foreach ($results as $result) {
            $matchStringBold = preg_replace('/('.$result->getVilleNomReel().')/i', '<strong>$1</strong>', $result->getVilleNomReel()); 
            //on ajoute aussi un code postal pour facilité la tache a l'user (il peut y'avoir plusieurs fois le meme nom de ville) 
            $listeVille .= '<li id="'.$result->getVilleNomReel().'">'.$matchStringBold.'</li>'.'<p>('. $result->getVilleCodePostal().')</p>'; 
        }
        $listeVille .= '</ul>';

        //on retrourne donc a match une liste a puce des villes via une reponse JSON
        $response = new JsonResponse();
        $response->setData(array('listeVille' => $listeVille));
        return $response;
    }

  /**
  * Autocompletion etablissement
  *
  * Fonction permettant de completer automatiquement ce qu'ecrit l'user dans un champ texte pour un etablissement.
  *
  *
  **/
    public function autocompletionEtablissementAction(Request $request) {

    //meme principe que pour les villes
         $session = $this->get('session');
    $maLatitude=$session->get('ma_lat');
    $maLongitude=$session->get('ma_long');
         $listeEtablissement="";

        $data = $request->get('input');
          $repository = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('LDSPlatformBundle:Etablissement');
    $results=$repository->autocompletionNom($data);

      $calculLongitude = $this->container->get('lds_platform.calculLongitude');
      $results=$calculLongitude->calculDistance(0, $maLatitude, $maLongitude, $results, true);

     $listeEtablissement = '<ul id="matchListEtablissement">';
        foreach ($results as $result) {
            $matchStringBold = preg_replace('/('.$data.')/i', '<strong>$1</strong>', $result->getNomEtablissement()); // Replace text field input by bold one
            $listeEtablissement .= '<li id="'.$result->getNomEtablissement().'">'.$matchStringBold.'</li>'.'<p>('. $result->getDistance().' km)</p>';; // Create the matching list - we put maching name in the ID too
        }
        $listeEtablissement .= '</ul>';

        $response = new JsonResponse();
        $response->setData(array('listeEtablissement' => $listeEtablissement));
        return $response;
    }

  /**
  * get Loacalisation
  *
  * Fonction permettant de recuperer la localisation de la personne connectee 
  *
  *
  **/
    public function getLocalisationAction (Request $request) {
        $this->get('session')->set('ma_lat', $request->get('lat'));
        $this->get('session')->set('ma_long', $request->get('long'));
         return $this->redirectToRoute('lds_platform_homepage');
    }

  /**
  * changer loacalisation etablissement
  *
  * Fonction permettant de recuperer la nouvelle position d'un etablissment lorsque l'utilisateur deplace le curseur sur la carte 
  *
  **/
    public function changerLocalisationEtablissementAction(Request $request) {
         $this->get('session')->set('lat_etablissement', $request->get('lat'));
        $this->get('session')->set('long_etablissement', $request->get('long'));
         return new JsonResponse();
    }



    public function repondreAction(Request $request) {
      $contenu=$request->get('contenu');
      $this->get('session')->set('contenu_mail', $contenu);
       return new JsonResponse();
    }
}