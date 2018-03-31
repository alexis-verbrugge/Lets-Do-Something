<?php

namespace LDS\PlatformBundle\CalculsLongitude;

class LDSCalculLongitude {


  /**
  * Calcul distance
  *
  * Fonction qui prend en argument
  * distance --> la distance max que l'etablissement ne doit pas depasser (a partir de notre position ou la position choisie)
  * maLatitude, ma Longitude --> Position de la personne ou de la ville choisie
  * listeEtablissement --> tableau comportant au prealable plusieurs etablissements
  * bool --> pour savoir si on utilise la fonction pour la recherche ou pour les meilleurs etablissements
  *
  * Cette fonction qui permet donc de recuperer tous les etablissements presents a une certaine distance
  * de l'utilisateur (utilisee pour la recherche etblissement, evenements, meilleurs etablissements)
  * Creation d'un service car fonction redondante dans le Controller.
  *
  **/

  public function calculDistance($distance, $maLatitude, $maLongitude, $listeEtablissement, $bool) {

   $tmp=array();
   $compteur=0;
   $tab_distance=array();

   //on converti en radiant notre lat et notre long
   $latFrom = deg2rad($maLatitude);
   $lonFrom = deg2rad($maLongitude);

   //pour chaque etablissement
   foreach ($listeEtablissement as $e) {

    //on converti aussi sa lat et sa long en radiant
    $latTo = deg2rad($e->getLattitude());
    $lonTo = deg2rad($e->getLongitude());

    //on soustrait a la position de l'etablissemnt notre position pour sa lat et sa long
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    //calcul des 2 points pour trouver l'angle
    $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

    //arc tangeante de racineCaree(a) et de b
    $angle = atan2(sqrt($a), $b);

    //POur avoir une distance en km on multiplie l'angle par le diametre de la Terre et on divise par 1000
    $dist=($angle * 6371000)/1000; 

    //Si la distane choisie est 0 on prend tous les etablissement
    if ($distance==0) {
      $tmp[$compteur]=$e;
      //on rajoute la distance pour ensuite pouvoir classé nos etablissemnts du moins loin au plus loin
      $tmp[$compteur]->setDistance(ceil($dist));
      $compteur++;

      //si bool=true alors on recupere tous les etablissements dont la distance est inferieure a celle choisie
    } else if ($bool) {
    if (ceil($dist)<=$distance) {
      $tmp[$compteur]=$e;
      $tmp[$compteur]->setDistance(ceil($dist));
      $compteur++;
    }
  }

  // Pour les meilleurs etablissemnts on ne chosie que les etablissements ayant une moyenne > 2.5
  else {
     if (ceil($dist)<=$distance && $e->getMoyenne()>2.5) {
      $tmp[$compteur]=$e;
      $tmp[$compteur]->setDistance(ceil($dist));
      $compteur++;
    }
  }
  }

  //tri a bulle en fonction de la distance
  $n=$compteur;
  $i = null; $j = null; $temp = null;
  if ($bool) {
    for ($i = 0; $i < ($n - 1); $i++) {
      for ($j = ($i + 1); $j < $n; $j++) {
        if ($tmp[$j]->getDistance() < $tmp[$i]->getDistance()) {
          $temp = $tmp[$i];
          $tmp[$i] = $tmp[$j];
          $tmp[$j] = $temp;  
        } 
      }
    }
  //tri a bulle en fonction du coefficient de l'etablissemnt (pour fc meilleurs etablissements)
  } else {
   for ($i = 0; $i < ($n - 1); $i++) {
    for ($j = ($i + 1); $j < $n; $j++) {
      if ($tmp[$j]->getCoefficient() > $tmp[$i]->getCoefficient()) {
        $temp = $tmp[$i];
        $tmp[$i] = $tmp[$j];
        $tmp[$j] = $temp;  
      } 
    }
  }
}

//on retourne alors le nouveau tableau d'etablissemnts
return $tmp;
}


}