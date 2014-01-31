<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

set_include_path(get_include_path().PATH_SEPARATOR.'/usr/share/php/libzend-framework-php');

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

class CalendarDaoFactory {

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
    else if (strcmp(strtoupper(substr($desc,0,2)),"TD")==0)
      return "TD";
    else if (strcmp(strtoupper(substr($desc,0,2)),"TP")==0)
      return "TP";
    else return null;

  }

  static function createFactory() {
    if (CalendarDaoFactory::$_factory==NULL)
      CalendarDaoFactory::$_factory=new CalendarDaoFactory();
    return CalendarDaoFactory::$_factory;
  }
  
  function extraireEnseignants($event) {
      $enseignants=$event->where[0];
      
      $enseignants=str_replace(
                array(" ;",",","M5-","B5-","SUP-","M3-","M1-","MDL-"),
                array(";",";","M5 ","B5 ","SUP ","M3 ","M1 ","MDL "),
              $enseignants);
      
      $enseignants=str_replace(array(
          
                    "salle 105 puis 14 au B5; ","M3 amphi Turing","M3 Delattre puis M5 B12; ",
                    "M5 B02; ","M5 B4; ","M5 B4; ","M5 B10; ",
                    "M5 amphi A; ","M5 Amphi; ",
                    "M5 A2; ","M5 A6; ","M5 A7; ","M5 A8; ","M5 A9; ","M5 A11; ","M5 A14; ","M5 A15; ",
                    "M5  ","M5 B11; ","M5 B12; ","M5 B13; ",
                    "M3 Delattre; ","M3 salle Delattre; ","M3 226; ",
                    "B5 06; ", "B5 6; ","B5 210; ","B5 005; ","B5 006; ",
                    "B5 Salle11; ","B5 Salle210; ",
                    "P2-212; ","???; ","B10; ","Amphi xxx; ",
                    "M1 Cauchy; ","M1 Levy; ",
                    "SUP Amphi 13; ","SUP110; ","SUP111; ",
                    "CUEEP; ","SEMM; ","MDL 111"),
                array(
                    "","","",
                    "","","","",
                    "","",
                    "","","","","","","","",
                    "","","","",
                    "","",
                    "","","","","",
                    "","","",
                    "","","","",
                    "","",
                    "","","",
                    "","",""),$enseignants);

      $enseignants=str_replace(array(
                    "salle 105 puis 14 au B5","M3 amphi Turing","M3 Delattre puis M5 B12",
                    "M3 226",
                    "M5 B02","M5 B4","M5 B10","M5 B11","M5 B12","M5 B13",
                    "M5 amphi A","M5 Amphi",
                    "M5 A2", "M5 A6","M5 A7","M5 A8","M5 A9","M5 A11","M5 A14","M5 A15",
                    "M3 Delattre","M3 salle Delattre",
                    "CUEEP","B10","EuraTechnologie",
                    "B5 06","B5 6","B5 210", "B5 005","B5 006",
                    "SUP Amphi 13","SUP110","SUP111", 
                    "B5 Salle11","B5 Salle210",
                    "M1 Cauchy","M1 Levy",
                    "Amphi Bacchus","MDL 111"),
                array(
                    "","","",
                    "",
                    "","","","","","",
                    "","",
                    "","","","","","","",
                    "",
                    "","","",
                    "","","","","",
                    "","",
                    "","","",
                    "","",
                    "","",
                    "","",""),$enseignants);

      $enseignants=str_replace(
              array(
                    "M5"),
              array(
                    ""),$enseignants);

      //Abreviation MOCAD
      $enseignants=str_replace(
              array("Michèle ","Isabelle ","Bilel ",
                  "Maude ","Joachim ","Bruno ","Sébastien ",
                  "Angela ","Jean-Stéphane ","Gilles ",
                  "Philippe ","Pascal ","Jean-Marie ",
                  "Cédric ","Arnaud ","François ","Olivier "),
              array("M ","I ","B ",
                  "M ","J ","B ","S ",
                  "A ","JS ","G ",
                  "P ","P ","JM ",
                  "C ","A ","F ","O "),
              $enseignants);

     
      if ($enseignants===FALSE || strlen($enseignants)==0) return; 
      
      if ($enseignants[0]==';') {
          $enseignants=substr($enseignants, 2);
      }
      
      if ($enseignants[strlen($enseignants)-2]==';') {
          $enseignants=substr($enseignants,0,strlen($enseignants)-2);
      }
      
      if (strlen($enseignants)!=0)
        echo("==== RETRIEVING WHERE XXX ".$enseignants." XXX\n");
      
      return $enseignants;
  }

  function createGCalendarEtud($conn,$formationRef,$mois,$anneeRef=null,$etudRef=null,$dayStep=null,$secBeetwenGGLQueries=0,$uniqueSeance=true)
  {
      $anneeRef=($anneeRef==null)?(date("n")<9?date("Y")-1:date("Y")):$anneeRef;

      //$mois=$anneeRef==date("Y")?$mois:$mois+12;

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

      
      $calendar = new Calendar();
      $gdataCal = new Zend_Gdata_Calendar();
      if ($etudRef==null)
        $catalogue=DaoFactory::createFactory()->createCatalogue($conn, $formationRef, $anneeRef);
      else
        $catalogue=DaoFactory::createFactory()->createCatalogueEtud($conn, $formationRef, $anneeRef, $etudRef);

  $nbJours=array(31,29,31,30,31,30,31,31,30,31,30,31);
  /*echo ("anneeRef : $anneeRef - mois for $mois normalise à ".
    (($mois-1+12)%12)." avec nb jours = ".
    $nbJours[($mois-1+12)%12].
    " <br/>");
  */
  $step=$dayStep==null?(strcmp($formationRef,"M1INFOFA")==0?2:5):$dayStep;
  for ($w=0;$w<31/$step;$w++) {
      
      $startDate=date("c",mktime(0, 0, 0, $mois, 1+($w*$step), $anneeRef));
      $endDate=date("c",mktime(0,0,0, $mois, min(1+($w*$step)+$step,1+$nbJours[($mois-1+12)%12]), $anneeRef));
      
      echo ("RETRIEVING btw : ".$startDate." ".$endDate. " for $mois <br/>");

      $agenda=getAgendaUserForFormation($conn,$formationRef);
      $query = $gdataCal->newEventQuery();
      $query->setUser($agenda);
      $query->setVisibility('public');
      $query->setProjection('full');
      $query->setOrderby('starttime');
      $query->setSortOrder('ascending');
      $query->setStartMin($startDate);
      $query->setStartMax($endDate);
      
      sleep($secBeetwenGGLQueries);

      $notOK=true; $count=0;
      while($notOK && $count<10) {
        try
        {
          $eventFeed = $gdataCal->getCalendarEventFeed($query);
          $notOK=false;
        } catch (Exception $e) {
          $count++;
          error_log("retrying ($count) to query calendar : ".$agenda." -> ".$e);
        }
      }
      
     
      //echo "<ul>\n";
        $lastSeance=null;;
          foreach ($eventFeed as $event) {
              
              //error_log("evt btw : ".date('Y-m-d',$startDate)." ".date('Y-m-d', $endDate));

              //echo  $event->title->text." with status ".$event->eventStatus."<br/>\n";
              /*
               * continue;
              */
              if (strcmp(substr($event->eventStatus,
                      strlen($event->eventStatus)-strlen("event.canceled")
                        ),"event.canceled")==0) {
                        //echo ("skipping ".$event->title->text."<br/>");
                        continue;
                        }
                
              $desc=$event->title->text;
              
              if (strpos(strtolower($desc), "en autonomie")) {
                  error_log("$desc ignored as - XXX en autonomie XXX");
                  continue;
              }
              
              if (strpos(strtolower($desc), "uniquement FI")) {
                  error_log("$desc ignored as - XXX en autonomie XXX");
                  continue;
              }
              $matiere=$catalogue->getMatiereCorrespondingTo($desc);
              
              $type=$this->determineTypeSeance($desc);
              if (!$matiere) {
                echo ("Pas de matière correspondante à XXX $desc XXX ds le catalogue!\n") ;
               //error_log("Pas de matière correspondante à XXX $desc XXX ds le catalogue!");
                continue;
              }
              
              
              $enseignants=$this->extraireEnseignants($event);
              error_log("Retaining NOM = $enseignants");
              
              
              $matiereCle=$matiere->getCle();
              echo ("Récuperer $matiereCle depuis le catalogue pour XXX $desc XXX  avec type YYY $type YYY !\n");
              

              //echo "\t\t<ul>\n";
              foreach ($event->when as $when) {
                //echo $desc." - ".$when->startTime." - ".$when->endTime." - ".$desc."<br/>";

                $tpos=strpos($when->startTime,"T");
                if ($tpos!=FALSE) {
                  $date=substr($when->startTime,0,$tpos);
                  $hd=substr($when->startTime,$tpos+1,5);
                  $jour=substr($when->startTime,$tpos-2,2);
                  //error_log($when->startTime." - ".$jour);
                  $tpos=strpos($when->endTime,"T");
                  $hf=substr($when->endTime,$tpos+1,5);
                } else {
                  
                  $date=$when->startTime;
                  $hd="08:00";$hf="18:00";
                  $jour=substr($when->startTime,strlen($when->startTime)-2);
                }
                if ($formationRef=="M2IVIFA") {
                  $hd=(substr($hd,0,2)+2).substr($hd,2); if (strlen($hd)==4) $hd="0".$hd;
                  $hf=(substr($hf,0,2)+2).substr($hf,2); if (strlen($hf)==4) $hf="0".$hf;
                }
                $duree=$this->computeDureeEnH($hd,$hf,$formationRef);
                //echo " --- $jour $matiereCle $type $desc $date $hd $hf $duree \n";
                if ($duree>=6) {
                  if ($lastSeance!=null) $calendar->addSeance(substr($lastSeance->getDate(),strlen($lastSeance->getDate())-2),$lastSeance,$uniqueSeance);
                  $calendar->addSeance($jour, new Seance($matiereCle,$desc,"",$enseignants,$date,$hd,($hd[3]=='3')?"12:30":"12:00"),$uniqueSeance);
                  $calendar->addSeance($jour, new Seance($matiereCle,$desc,"",$enseignants,$date,($hd[3]=='3')?"13:30":"14:00",$hf),$uniqueSeance);
                  $lastSeance=null;
                } else {
                  $currSeance=new Seance($matiereCle,$desc,$type,$enseignants,$date,$hd,$hf,$duree);
                  if ($lastSeance!=null) {
                      if (0 && $lastSeance->getCleMatiere()==$currSeance->getCleMatiere() &&
                      strcmp($lastSeance->getDate(),$currSeance->getDate())==0 &&
                      $this->computeDureeEnH($lastSeance->getHFin(),$currSeance->getHDebut())<=0.5) {
                          $calendar->
                            addSeance($jour,
                                new Seance($matiereCle,"-","",$enseignants,$date,
                                    $lastSeance->getHDebut(),
                                      $currSeance->getHFin()),$uniqueSeance);
                          $lastSeance=null;
                          } else {
                            $calendar->addSeance(
                                    substr($lastSeance->getDate(),strlen($lastSeance->getDate())-2),
                                    $lastSeance,$uniqueSeance);
                            $lastSeance=$currSeance;
                          }
                      } else {
                          $lastSeance=$currSeance;
                      }
                }
                      //echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
                      //echo "\t\t\t<li>Ends: " . $when->endTime . "</li>\n";

                    }
                  //echo "\t\t</ul>\n";
                  //echo "\t</li>\n";
              }
              if ($lastSeance!=null) {
      	        //echo "Last Seance : ".$lastSeance->getTypeSeance()." = ".$lastSeance->getCleMatiere()."\n";
                $calendar->addSeance(substr($lastSeance->getDate(),strlen($lastSeance->getDate())-2),$lastSeance,$uniqueSeance);
              }

              //echo "</ul>\n";
 }
       $this->availableCalendars[$formationRef][$anneeRef][$mois]=$calendar;
       return $calendar;
  }

  function createCalendar($conn,$formationRef,
                                       $offsetMonth
                                       ) {
     
     $startDate=date("Y-m-d",mktime(0, 0, 0, date("m")+$offsetMonth  , 1, date("Y")));
     $endDate=date("Y-m-d",mktime(0,0,0, date("m")+$offsetMonth  , 31, date("Y")));
     $mois=date("m",mktime(0,0,0, date("m")+$offsetMonth  , 1, date("Y")));
     
     if (array_key_exists($formationRef,$this->availableCalendars)) {
        if (array_key_exists($startDate."-".$endDate,$this->availableCalendars[$formationRef]))
          return $this->availableCalendars[$formationRef][$mois];
      } else {
          $this->availableCalendars[$formationRef]=array();
      }
      $calendar = new Calendar();

      $catalogue=DaoFactory::createFactory()->createCatalogue($conn, $formationRef, substr($startDate,0,4));

      $interventionsSet=doQueryInterventionsOffsetDateCouranteParFormation($conn,$offsetMonth,$formationRef);

      $intervention=mysql_fetch_row($interventionsSet);
      echo "<ul>\n";
//      seance.seanceCle as seanceCle,
//      matiere.matiereCle as matiereCle,
//      matiere.nom as matiereNom,
//      intervention.interventionCle as interventionCle,
//      intervention.typeInterventionRef as type,
//      groupeRef,
//      profRef as prof,
//      salleRef as salle,
//      seance.debut as debut,
//      ADDTIME(seance.debut,intervention.duree) as fin,
//      periode.debut as date1ereseance,periode.fin as datederniereseance,
//      seance.jour as jour
      while ( $intervention ) {

              //echo "\t<li>" . $event->title->text;
              $matiereCle=$intervention[1];
              $matiereNom=$intervention[2];
              $interventionRef=$intervention[3];
              $typeSeance=$intervention[4];
              $groupeRef=$intervention[5];
              $hd=$intervention[8];
              $hf=$intervention[9];

              $matiere=$catalogue->getMatiereCorrespondingTo($matiereNom);
              if (!$matiere) {
                echo "<b>RIEN</b>";
                continue;
              }
              $matiereCle=$matiere->getCle();

              $datesSet=doQuerySeanceOffsetDateCouranteParInterventionEtGroupe($conn,$interventionRef,$groupeRef,$offsetMonth);

              $date=mysql_fetch_row($datesSet);
              //echo "\t\t<ul>\n";
              while ($date) {
                
                $calendar->addSeance(date("d",$date[0]), new Seance($matiereCle,$typeSeance,$date[0],$hd,$hf));
                $date=mysql_fetch_row($datesSet);
              }

              //echo "</ul>\n";

              $intervention=mysql_fetch_row($interventionsSet);
      }
       $this->availableCalendars[$formationRef][$startDate."-".$endDate]=$calendar;
       return $calendar;
  }

  function createGCalendar($conn,$formationRef,$mois,$anneeRef=null)
  {
    return $this->createGCalendarEtud($conn,$formationRef,$mois,$anneeRef,null);
  }
  
}
?>
