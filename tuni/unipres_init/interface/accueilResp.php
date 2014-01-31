<?php
    require '../secure/auth.php';
    //error_log("accueilTuteur for ".getParam(CK_USER,"UNKOWNN"));

    if (!hasRole(RESP_ROLE))
        redirectAuth(null);
?>
<html>
     <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Responsables</title>
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
    </head>
    <frameset rows="60pt,*">
        <frame name="menu" src="menuResp.php"/>
        <frame name="main" src="welcomeResp.php"/>
    </frameset>
</html>