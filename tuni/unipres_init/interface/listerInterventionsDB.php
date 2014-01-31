<?php

date_default_timezone_set('Europe/Paris');




echo "<form action='#' method='post'><font size='4'>Imprimer feuilles présences mois ( ";
echo "<input type='hidden' name='formation' value='".$formation."'/>";
echo "<input type='hidden' name='moisPrecedent' value='".$moisPrecedent."'/>";
$moisSuivant=createMoisSelect($conn,"moisSuivant",+1,NULL,$moisSuivant,"javascript:submit();");
echo ")</font></form>";

$debutMoisProchain=mktime(0, 0, 0, date("m")+$moisSuivant  , date("d"), date("Y"));
$results=doQueryInterventionsOffsetDateCouranteParFormation($conn,$moisSuivant,$formation);
$result=mysql_fetch_row($results);

echo "<table>";

while($result) {
  echo "<tr><form method='post' action='genererListePresence.php'>
        <input type='hidden' name='seanceCle' value='".$result[0]."'/>
        <input type='hidden' name='interventionRef' value='".$result[3]."'/>
        <input type='hidden' name='offset' value='".$moisSuivant."'/>
        <input type='hidden' name='groupeRef' value='".$result[5]."'/>
        <input type='hidden' name='matiereNom' value='".$result[2]."'/>
        <input type='hidden' name='interventionTypeRef' value='".$result[4]."'/>
        <input type='hidden' name='profRef' value='".$result[6]."'/>
        <input type='hidden' name='horaire' value='".$result[8]."-".$result[9]."'/>
        <input type='hidden' name='salleRef' value='".$result[7]."'/>
        <input type='hidden' name='mois' value='".$debutMoisProchain."'/>
        <input type='hidden' name='matiereCle' value='".$result[1]."'/>
        <td> * </td><td>$result[5]</td><td><b>".$jours[$result[12]]."</b></td><td><b>$result[2]</b></td><td>$result[4]</td><td>
        <input type='submit' name='lister' value='Lister'/></td>
        </form>
      </tr>";
  $result=mysql_fetch_row($results);
}
echo "</table>";


echo "<br/><br/><hr/><br/><br/>";

echo "<form action='#' method='post'><font size='4'>Saisir présences mois ( ";
echo "<input type='hidden' name='formation' value='".$formation."'/>";
echo "<input type='hidden' name='moisSuivant' value='".$moisSuivant."'/>";
$moisPrecedent=createMoisSelect($conn,"moisPrecedent",-1,NULL,$moisPrecedent,"javascript:submit();");
echo ")</font></form>";

error_log('moisPrec '.$moisPrecedent);

$resultsInterventions=doQueryInterventionsOffsetDateCouranteParFormation($conn,$moisPrecedent,$formation);
$result=mysql_fetch_row($results);

//$resultsSeances=doQuerySeanceOffsetDateCouranteParInterventionEtGroupe($conn,$interventionRef,$groupeRef,$offset);

$debutMoisPrecedent=mktime(0, 0, 0, date("m")+$moisPrecedent  , date("d"), date("Y"));

echo "<table>";
while($result) {

  echo "<tr><form method='post' action='genererListePresence.php'>
        <input type='hidden' name='seanceCle' value='".$result[0]."'/>
        <input type='hidden' name='interventionRef' value='".$result[3]."'/>
        <input type='hidden' name='offset' value='".$moisPrecedent."'/>
        <input type='hidden' name='groupeRef' value='".$result[5]."'/>
        <input type='hidden' name='matiereNom' value='".$result[2]."'/>
        <input type='hidden' name='interventionTypeRef' value='".$result[4]."'/>
        <input type='hidden' name='profRef' value='".$result[6]."'/>
        <input type='hidden' name='horaire' value='".$result[8]."-".$result[9]."'/>
        <input type='hidden' name='salleRef' value='".$result[7]."'/>
        <input type='hidden' name='mois' value='".$debutMoisPrecedent."'/>
        <input type='hidden' name='matiereCle' value='".$result[1]."'/>
        <td> * </td><td>$result[5]</td><td><b>".$jours[$result[12]]."</b></td><td><b>$result[2]</b></td><td>$result[4]</td><td>
        <input type='submit' name='saisir' value='Saisir'/></td>
        </form>
      </tr>";
  $result=mysql_fetch_row($results);
}
echo "</table>";

?>
  </body>
</html>