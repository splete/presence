<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Matiere {
  var $matiereNom;
  var $matiereCle;

  static function createMatiereFromRow($row) {
    $_matiere=new Matiere;
    $_matiere->matiereNom=$row[0];
    $_matiere->matiereCle=$row[1];
    return $_matiere;
  }

  public function getNom()
  {
    return $this->matiereNom;
  }
  
  public function getCle()
  {
    return $this->matiereCle;
  }
}
?>
