<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

require_once '../dbmngt/queriesGCal.php';
require_once '../dbmngt/queries.php';
require_once '../dao/DaoFactory.class.php';
require_once '../dto/Calendar.class.php';
require_once '../dto/Seance.class.php';

class LocalCalendarDaoFactory {

  var $availableCalendars=array();
  static $_factory=NULL;


  static function computeDureeEnH($hd,$hf,$formationRef=null) {
    $duree=((((int)substr($hf,0,2))-((int)substr($hd,0,2)))*60+
                  (((int)substr($hf,3,2))-((int)substr($hd,3,2))))/60;

    if (strcmp($formationRef,"M2ESERVFA")==0 && $duree==4.25)
      return 4;

    return $duree;
  }

  static function determineTypeSeance($desc) {
    //error_log($desc." puis ".substr($desc,0,5)." puis ".substr($desc,0,3)." puis ".substr($desc,0,2));
    if (strcmp(strtoupper(substr($desc,0,5)),"COURS")==0)
      return "Cours";
    else if (strcmp(strtoupper(substr($desc,0,3)),"CTD")==0)
      return "CTD";
    else if (strcmp(strtoupper(substr($desc,0,5)),"TD/TP")==0)
      return "TD/TP";
    else if (strcmp(strtoupper(substr($desc,0,2)),"TD")==0)
      return "TD";
    else if (strcmp(strtoupper(substr($desc,0,2)),"TP")==0)
      return "TP";
    else return null;

  }

  static function createFactory() {
    if (LocalCalendarDaoFactory::$_factory==NULL)
      LocalCalendarDaoFactory::$_factory=new LocalCalendarDaoFactory();
    return LocalCalendarDaoFactory::$_factory;
  }

  function toDescriptionCfSUDES($desc,$formationRef) {
      //IAGL
      if (strcmp($formationRef,"M2IAGLFA")==0)
          $desc=str_replace(array("[CAL]","[OPL]","[IDL]","[Innovation]"),
              array("[GLA]","[CAGL]","[IA]","[IIR]"),$desc);
      
      if (strcmp($formationRef,"M2IVIFA")==0) 
          $desc=str_replace(array("IIR"),
              array("RIC"),$desc);
          
      return $desc;

  }
  
  function createLocalCalendarEtud($conn,$formationRef,$mois,$anneeRef=null,$etudRef=null)
  {
      $anneeRef=($anneeRef==null)?getCurrYear():$anneeRef;

      if ($etudRef==null) {
        if (array_key_exists($formationRef,$this->availableCalendars)) {
          if (array_key_exists($mois,$this->availableCalendars[$formationRef]))
            if (array_key_exists($mois,$this->availableCalendars[$formationRef][$anneeRef])) {
              if (array_key_exists($mois,$this->availableCalendars[$formationRef][$anneeRef][$mois]))
                return $this->availableCalendars[$formationRef][$anneeRef][$mois];}
            else
              $this->availableCalendars[$formationRef][$anneeRef]=array();
        } else {
            $this->availableCalendars[$formationRef]=array();
            $this->availableCalendars[$formationRef][$anneeRef]=array();
        }
      }


     $catalogue=DaoFactory::createFactory()->
                              createCatalogueEtud
                              ($conn, $formationRef, $anneeRef,$etudRef);

      print("../seances2/$formationRef/$anneeRef/$mois/");
     if (!file_exists("../seances2/$formationRef/$anneeRef/$mois/"))
      die("<h2 style='color:red'>Demandez à Marius.Bilasco@lifl.fr de mettre à jour les informations concernant le mois (
          $mois) sélectionné!</h2>");

     $seanceFiles=scandir("../seances2/$formationRef/$anneeRef/$mois/");

     $calendar = new Calendar();
     for ($i=0;$i<count($seanceFiles);$i++) {
        //error_log("processing ".$seanceFiles[$i]);
        
        $seanceFileName=$seanceFiles[$i];
        if ($seanceFileName[0]=='.') continue;
        
        $infos=explode("_",$seanceFileName);
        $date=$infos[0];
        $hd=$infos[1];
        $hf=$infos[2];
        $matiereCle=str_replace(array("-","."),array("_","/"),$infos[3]);
        $desc=str_replace(array("-","."),array("_","/"),$infos[4]);
        
        $desc=$this->toDescriptionCfSUDES($desc,$formationRef);
        
        $jour=substr($date,strlen($date)-2);
        $enseignants=str_replace(array("+"),array(";"),$infos[5]);

        $type=$this->determineTypeSeance($desc);
        
        if ($catalogue->getMatiereParCle($matiereCle)) {
          $calendar->addSeance($jour, new Seance($matiereCle,$desc,$type,$enseignants,$date,$hd,$hf));
        } else {
          error_log($seanceFileName." ne correspond à aucune matière du catalogue ! ");
        }
        
    }

    if ($etudRef==null)
      $this->availableCalendars[$formationRef][$anneeRef][$mois]=$calendar;
    return $calendar;
  }

  
}
?>
