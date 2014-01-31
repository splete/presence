<?php
    require '../secure/auth.php';
    if (!hasRole(SECR_ROLE))
        redirectAuth();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Accueil Sécretaire</title>
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
    </head>
  <body>
      <!--H2 style="margin-bottom:1pt">Bonjour <b><?php echo $_SESSION[CK_USER];?> (responsable)</b>
          <a href="accueilTuteur.php" target="_top">(passez tuteur)</a>
      </H2-->
      <table width="90%" height="60%" border="0">
          <tr style="font-size:10pt">
          <td><a href="listerInterventions.php" target="main">Feuille Présences</a></td>
          <td><a href="consulterParMatiere.php" target="main">Consulter par matière</a></td>
          <td><a href="../disconnect.php" target="_top">Deconnexion</a></td>
          </tr>
      </table>
  </body>
</html>