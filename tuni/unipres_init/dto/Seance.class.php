<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Seance
{
  var $debut;
  var $fin;
  var $duree;
  var $date;
  var $cleMatiere;
  var $nomSeance;
  var $typeSeance;
  var $enseignants;
  
  function Seance($cleMatiere,$nomSeance,$typeSeance,$enseignants,$date,$hd,$hf,$dur=null) {

    $this->cleMatiere=$cleMatiere;
    $this->typeSeance=$typeSeance;
    $this->enseignants=$enseignants;
    //error_log($nomSeance." - ".substr($nomSeance,0,strlen($typeSeance))." (".strlen($typeSeance)." - ".substr($nomSeance,strlen($typeSeance)));
    $this->nomSeance=($typeSeance==null || strlen($typeSeance)==0)?
                        $nomSeance:
                          (
                              (strcmp(substr($nomSeance,0,strlen($typeSeance)+1),$typeSeance." ")==0)?
                            substr($nomSeance,strlen($typeSeance)+1)
                            :$nomSeance
                           );
    
    $this->duree=$dur==null?((((int)substr($hf,0,2))-((int)substr($hd,0,2)))*60+
                  (((int)substr($hf,3,2))-((int)substr($hd,3,2))))/60:$dur;
    
    $this->debut=$hd;
    if ($dur==null)
      $this->fin=$hf;
      else {
        $minFin=(int)substr($hd,0,2)*60+(int)substr($hd,3)+$dur*60;
        $this->fin=((int)($minFin/60)).':'.($minFin%60<10?'0':'').$minFin%60;
      }
    $this->date=$date;
    
    if ($this->duree>=6) $this->duree-=2;
  }

  function getDate() {
    return $this->date;
  }

  function getDuree() {
    return $this->duree;
  }

  function getHDebut() {
    return $this->debut;
  }

  function getHFin() {
    return $this->fin;
  }

  function getTypeSeance() {
    return $this->typeSeance;
  }

  function getNomSeance() {
    return $this->nomSeance;
  }

  function getCleMatiere() {
    return $this->cleMatiere;
  }
  
  function getEnseignants() {
    return $this->enseignants;
  }
}
?>
