<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "../dto/Matiere.class.php";

class Catalogue {
  var $matieres=array();
  public function add ($matiere) {
    $this->matieres[$matiere->getCle()]=$matiere;
    //error_log("adding matiere ".($matiere->getNom()));
  }

  public function getMatiereParCle($cle) {
    if (array_key_exists($cle,$this->matieres))
      return $this->matieres[$cle];
    else
      return null;
  }
  
  public function getMatiereCorrespondingTo($description) {

    //EXCEPTIONS
    $description=str_replace("Voice XML", "VoiceXML",strtolower($description));
    $description=str_replace(
              array("cours ","ctd ","td/tp ","td ","tp "),
              array("","","","",""),
              $description);
    $description=str_replace(
              array("[","]"),
              array("",""),
              $description);
    
    //NORMAL
    $posEsp=strpos($description," ");$posEsp0=$posEsp;
    $lastPosEsp=$posEsp;
    while (($posEsp=strpos($description," ",$lastPosEsp+1))!=FALSE) {
      $lastPosEsp=$posEsp;
    }
    if ($posEsp0>0) {
      $desc=substr($description,0,$posEsp0);
      $desc2=substr($description,$lastPosEsp+1);
    }
    else {
      $desc=$description;
      $desc2=$description;
    }


    $debug=false;
    $keys=array_keys($this->matieres);
    
    if ($debug) error_log("using full description  : $description");
    
    //vérifier Nom complet matière
    for ($i=0;$i<count($this->matieres);$i++)
    {
      $matiere=$this->matieres[$keys[$i]];
      $nomMatiere=strtolower($matiere->getNom());
      $cleMatiere=strtolower($matiere->getCle());
      
      if ($debug) error_log("comparing $nomMatiere against $description ?");
      if (strcmp($nomMatiere,$description)==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }
    }
    
    //vérifier si description commence par nom matiere
    if ($debug) error_log("vérifier si description commence par nom matiere");
    for ($i=0;$i<count($this->matieres);$i++)
    {
      $matiere=$this->matieres[$keys[$i]];
      $nomMatiere=strtolower($matiere->getNom());
      $cleMatiere=strtolower($matiere->getCle());
      if (strlen($description)<strlen($nomMatiere)) continue;
      if ($debug) error_log("comparing : ".substr($description,0,strlen($nomMatiere))." first chars against : ".$matiere->getNom());
      if (strcmp($nomMatiere,substr($description,0,strlen($nomMatiere)))==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }
    }
    
    
    if ($debug) error_log("using the pair : $desc - $desc2");
    for ($i=0;$i<count($this->matieres);$i++)
    {
      $matiere=$this->matieres[$keys[$i]];
      $nomMatiere=strtolower($matiere->getNom());
      $cleMatiere=strtolower($matiere->getCle());

      if (strcmp($cleMatiere,$desc2)==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }

      if (strcmp($cleMatiere,$desc)==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }
      

      /*if (strcmp($nomMatiere,$desc2)==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }*/

      if ($debug) error_log("comparing : $description against : ".$matiere->getNom());
      if (strcmp($nomMatiere,$description)==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }

      /*if ($debug) error_log("comparing : $desc against end of: ".$matiere->getNom());
      if (strpos($nomMatiere," ".$desc)>0 &&
          strpos($nomMatiere," ".$desc)==
            (strlen($nomMatiere)-strlen($desc)-1)) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }*/

      if (strpos($nomMatiere," ".$desc2)>0 &&
          strpos($nomMatiere," ".$desc2)==
            (strlen($nomMatiere)-strlen($desc2)-1)) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }

      if ($debug) error_log("comparing : -".substr($nomMatiere,0,strlen($desc))."- against -".$desc."-");

      if (strcmp(substr($nomMatiere,0,strlen($desc)+1),$desc." ")==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }

      if ($debug) error_log("comparing : -".substr($nomMatiere,0,strlen($desc2))."- against -".$desc2."-");

      if (strcmp(substr($nomMatiere,0,strlen($desc2)+1),$desc2." ")==0) {
        if ($debug) error_log ("found ".($matiere->getNom())." for ".$description);
        return $matiere;
      }

      
    }
    if ($debug) error_log("did not found ".$description);
    return false;
  }
}
?>
