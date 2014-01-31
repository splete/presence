<?php
    require '../secure/auth.php';
    //error_log("accueilTuteur for ".getParam(CK_USER,"UNKOWNN"));

    if (!hasRole(SECR_ROLE))
        redirectAuth(null);
?>
<html>
     <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Etudiants</title>
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
    </head>
    <frameset rows="45pt,*">
        <frame name="menu" src="menuSecr.php"/>
        <frame name="main" src="welcomeSecr.php"/>
    </frameset>
</html>