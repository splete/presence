<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

require_once '../dbmngt/queriesGCal.php';
require_once '../dao/DaoFactory.class.php';
require_once '../dto/Catalogue.class.php';
require_once '../dto/Matiere.class.php';

function createCalendar($conn,$userID,
                                     $startDate='2011-12-01',
	                                   $endDate='2011-12-30'
                                     )
{
	  $gdataCal = new Zend_Gdata_Calendar();
	  $query = $gdataCal->newEventQuery();
	  $query->setUser($userID);
	  $query->setVisibility('public');
	  $query->setProjection('full');
	  $query->setOrderby('starttime');
    //$query->setEventStatus('confirmed');
	  $query->setStartMin($startDate);
	  $query->setStartMax($endDate);
	  $eventFeed = $gdataCal->getCalendarEventFeed($query);

    //$catalogue=DaoFactory::createCatalogue($conn, $formationRef, substr($startDate,0,4));
    
	  echo "<ul>\n";
		    foreach ($eventFeed as $event) {

			      echo "\t<li>" . $event->title->text;
            
            $desc=$event->title->text;
              
//            if (!$catalogue->containsMatiereCorrespondingTo($desc)) {
//              echo "<b>RIEN</b>";
//              continue;
//            }
				    echo "\t\t<ul>\n";
				    foreach ($event->when as $when) {
					          echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
                    echo "\t\t\t<li>Ends: " . $when->endTime . "</li>\n";
                    
						      }
                  echo "\t\t\t<li>status: ". $event->eventStatus."</li>\n";
				        echo "\t\t</ul>\n";
				        echo "\t</li>\n";
					  }
            
            echo "</ul>\n";
}

createCalendar(NULL,"mastermocad@gmail.com");

?>
