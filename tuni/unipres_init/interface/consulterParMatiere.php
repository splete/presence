<?php
    require_once '../secure/auth.php';

    //error_log("accueilTuteur for ".getParam(CK_USER,"UNKOWNN"));

    if (!hasRole(RESP_ROLE) && !hasRole(SECR_ROLE) && !hasRole(GEST_ROLE))
        redirectAuth(null);
?>
<html>
    <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
        <link href="../html/calendar/calendar.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="../html/calendar/calendar.js"></script>

        <title>Liste presences</title>
    </head>
  <body>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "../dbmngt/connect.php";
require_once "../dbmngt/queriesStat.php";
require_once "../html/utils.php";
require_once "../html/dbutils.php";
//require_once "../html/calendar/classes/tc_calendar.php";
//require_once "../html/calendar/calendar_form.php";

$conn=doConnection();
//echo "Default value ".(date("n")>9)?(date("Y")):((date("Y")-1));
$annee=getParam("annee",(date("n")>9)?(date("Y")):((date("Y")-1)));
$formation=getParam("formation","M1MIAGEFA");
$matiere=getParam("matiere");
$debut=getParam("debut",mktime(0,0,0,9,1,$annee));
$fin=getParam("fin",mktime(0,0,0,7,15,$annee+1));
echo "Periode :  ".date("Y",$debut)." - ".date("Y",$fin);
/*
echo "Période ";
  $date3_default = $debut;
  $date4_default = $fin;

  $myCalendar = new tc_calendar("date3", true, false);
	$myCalendar->setIcon("../html/calendar/images/iconCalendar.gif");
	$myCalendar->setDate(date('d', strtotime($date3_default))
            , date('m', strtotime($date3_default))
            , date('Y', strtotime($date3_default)));
	$myCalendar->setPath("../html/calendar/");
	$myCalendar->setYearInterval(1970, 2020);
	$myCalendar->setAlignment('left', 'bottom');
	$myCalendar->setDatePair('date3', 'date4', $date4_default);
	$myCalendar->writeScript();

  $myCalendar = new tc_calendar("date4", true, false);
  $myCalendar->setIcon("../html/calendar/images/iconCalendar.gif");
  $myCalendar->setDate(date('d', strtotime($date4_default))
         , date('m', strtotime($date4_default))
         , date('Y', strtotime($date4_default)));
  $myCalendar->setPath("../html/calendar/");
  $myCalendar->setYearInterval(1970, 2020);
  $myCalendar->setAlignment('left', 'bottom');
  $myCalendar->setDatePair('date3', 'date4', $date3_default);
  $myCalendar->writeScript();
*/

echo "<form method='post' action='#'>";

echo "<h2>Choisir année &amp; formation &amp; matiere</h2>";
echo "Année : ";
$annee=createAnneesSelect($conn,"annee",$debut,$fin,$annee,"javascript:submit();");

echo "Formations :";
$formation=createFormationsSelect($conn,"formation",&$formation,"javascript:submit();");
echo "&nbsp;Matière : ";
$matiere=createMatieresSelect($conn,"matiere",$formation,$debut,$fin,$matiere,"javascript:submit();");


echo "</form>";
?>

    <table border="1">
        <thead><tr>
                <!--td></td-->
                <td>Date</td>
                <td>Nombre étudiants</td>
                
               </tr>
        </thead>
        <tbody>
            <?php
            $i=0;
            $divs="'head'";
            $entrs=doQueryPresencesParDateEtMatiere($conn,$debut,$fin,$matiere);
            $nbForm=0;
            $total=0;
            $row=mysql_fetch_row($entrs);
            while ($row)
            {
                echo "<tr>";
                echo "<td>",$row[0],"</td>";
                echo "<td align='center'> ",$row[1],"</td>";
                echo "</tr>";
                $total+=$row[1];
                $nbForm++;

                $row=mysql_fetch_row($entrs);
                $i=$i+1;
            }
            echo "<tr height='4pt'/><tr>";
                echo "<td> Total (",$nbForm," form.)</td>";
                echo "<td align='center'> ",$total,"</td>";
                echo "</tr>";

            ?>
        </tbody>
    </table>
  </body>
</html>