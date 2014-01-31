<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CatalogueDaoFactory {

  var $availableCatalogues=array();
  static $_factory=NULL;

  static function createFactory() {
    if (CatalogueDaoFactory::$_factory==NULL)
      CatalogueDaoFactory::$_factory=new CatalogueDaoFactory();
    return CatalogueDaoFactory::$_factory;
  }
  
  function createCatalogue($conn,$formationRef,$annee)
  {
        if (array_key_exists($formationRef,$this->availableCatalogues)) {
          if (array_key_exists($annee,$this->availableCatalogues[$formationRef]))
            if (array_key_exists($annee,$this->availableCatalogues[$formationRef][$annee])) {
              error_log("catalogue pour l'année déjà $annee pour la formation $formationRef");
              return $this->availableCatalogues[$formationRef][$annee];
            }
        } else {
            $this->availableCatalogues[$formationRef]=array();
        }
      
      $catalogue=new Catalogue();
      error_log("creation du nouveau catalogue pour l'année $annee pour la formation $formationRef");
      $results=getCatalogueRows($conn,$formationRef,$annee);
      $row=mysql_fetch_row($results);
      while ($row) {
        $matiere=Matiere::createMatiereFromRow($row);
        $catalogue->add($matiere);
        error_log("adding matiere : ".$matiere->getNom()." with cle ".$matiere->getCle());
        $row=mysql_fetch_row($results);
      }
      
      $this->availableCatalogues[$formationRef][$annee]=$catalogue;

      return $catalogue;
  }


  function createCatalogueEtud($conn,$formationRef,$annee,$etudRef) {
    $catalogue=$this->createCatalogue($conn,$formationRef,$annee);

    if ($etudRef==null)
      return $catalogue;

    error_log("creation du nouveau catalogue pour $etudRef");
    $results=getSpecificCatalogueRowsForEtud($conn,$formationRef,$annee,$etudRef);
    $row=mysql_fetch_row($results);
    while ($row) {
      $matiere=Matiere::createMatiereFromRow($row);
      $catalogue->add($matiere);
      error_log("adding matiere : ".$matiere->getNom()." having cle ".$matiere->getCle());
      $row=mysql_fetch_row($results);
    }
    
    return $catalogue;
  }
}
  
?>
