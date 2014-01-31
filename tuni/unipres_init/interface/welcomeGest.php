<?php
    require '../secure/auth.php';
    //error_log("accueilResp for ".getParam(CK_USER,"UNKOWNN"));

    if (!hasRole(GEST_ROLE))
        redirectAuth(null);
?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Accueil Etudiant</title>
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
    </head>
  <body>
      <h1>Bienvenue!</h1>
      <h2>Informations</h2>
      <p>Prenez le soin d'imprimer à l'avance la nouvelle feuille de présence et de la présenter à chaque cours.</p>
      </h2>
      <h2>Nouveautés</h2>
      <ul>
      </ul>
  </body>
</html>
