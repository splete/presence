<?php
/* 
 * To creata a Calendar instance par formationRef et par periode
 * 
 */
class Calendar {
  var $seances=array();

  function containsSeanceType($jour)
  {
    return array_key_exists($jour,$this->seances);
  }

  function addSeance($jour,$seance,$unique=true) {
    if (!$this->containsSeanceType($jour))
      $this->seances[$jour]=array();

    if ($unique==false) 
      $this->seances[$jour][]=$seance;
    else {
      for ($i=0;$i<count($this->seances[$jour]) &&
          strcmp($this->seances[$jour][$i]->getHDebut(),$seance->getHDebut())!=0;
          $i++) ;
      if ($i==count($this->seances[$jour])) $this->seances[$jour][]=$seance;
    }
  }

  function getSeancesCles() {
//    $seancesCles=array();
//    foreach (array_keys($this->seances) as $seance) {
//      if
//
//    }
    return array_keys($this->seances);
  }

  function countSeances() {
    return count($this->seances);
  }

  function getSeanceParCle($cle) {
    return $this->seances[$cle];
  }

  function getSeanceParIndex($idx) {
    $cles=$this->getSeancesCles();
    return $this->seances[$cles[$idx]];
  }
  
}
?>
