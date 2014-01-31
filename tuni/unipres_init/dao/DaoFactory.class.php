<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "../dto/Catalogue.class.php";
require_once "../dto/Matiere.class.php";

require_once "../dbmngt/queriesGCal.php";
require_once "../dao/CalendarDaoFactory.class.php";
require_once "../dao/CatalogueDaoFactory.class.php";
require_once "../dao/LocalCalendarDaoFactory.class.php";


class DaoFactory {
  var $catFactory;
  var $calFactory;
  var $localCalFactory;
  static $_factory=NULL;

  static function createFactory()
  {
    if (DaoFactory::$_factory==NULL)
      DaoFactory::$_factory=new DaoFactory();
    return DaoFactory::$_factory;
   }

  function DaoFactory() {
    $this->catFactory=CatalogueDaoFactory::createFactory();
    $this->calFactory=CalendarDaoFactory::createFactory();
    $this->localCalFactory=LocalCalendarDaoFactory::createFactory();
  }

  public function createCatalogue($conn,$formationRef,$annee=null)
  {
    return $this->catFactory->createCatalogue($conn,$formationRef,
                                       $annee);
  }

  public function createCatalogueEtud($conn,$formationRef,$annee=null,$etudRef=null)
  {
    return $this->catFactory->createCatalogueEtud($conn,$formationRef,
                                       $annee,$etudRef);
  }

  public function createGCalendar($conn,$formationRef,$mois,$annee=null) {
    return $this->calFactory->createGCalendar($conn, $formationRef, $mois, $annee);
  }

  public function createGCalendarEtud($conn,$formationRef,$mois,$annee=null,$etudRef=null,$dayStep=null,$secBeetwenGGLQueries=0,$uniqueSeance=true) {
    return $this->calFactory->createGCalendarEtud($conn, $formationRef, $mois, $annee,$etudRef,$dayStep,$secBeetwenGGLQueries,$uniqueSeance);
  }

  public function createCalendar($conn,$formationRef,$offsetMonth) {
    return $this->calFactory->createCalendar($conn, $formationRef, $offsetMonth);
  }

  public function createLocalCalendarEtud($conn,$formationRef,$mois,$annee=null,$etudRef=null) {
    return $this->localCalFactory->createLocalCalendarEtud($conn, $formationRef, $mois, $annee,$etudRef);
  }
}
?>
