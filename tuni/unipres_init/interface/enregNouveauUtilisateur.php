
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Nouveau utilisateur
</title>
<link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

    $today=getdate();
    $nom=$_REQUEST["nom"];
    $prenom=$_REQUEST["prenom"];
    //$mail=$_REQUEST["mail"];
    $uid=$_REQUEST["uid"];
    $role=$_REQUEST["role"];
    
    $filename=sprintf("../xml/user%s%ld%s.xml",
        $role,$today[0],$uid);
        
    //printf("%s<br/>",$filename);

    $data=fopen($filename,"w+");
    //printf("%s<br/>",$data);

    fprintf($data,"<item>\n");
    foreach ($_REQUEST as $key=>$value)
    {
        fprintf($data,"<%s>%s</%s>\n",$key,$value,$key);

    }
    fprintf($data,"</item>\n");
    fclose($data);
?>

<?php
    $data2=fopen("../xml/users.pxml","a+");
    fprintf($data2,"<item>\n");
    foreach ($_REQUEST as $key=>$value)
    {
        fprintf($data2,"<%s>%s</%s>\n",$key,$value,$key);

    }
    fprintf($data2,"</item>\n");
    fclose($data2);
?>
<h2>Informations enregistrées avec succès!</h2>

L'équipe assurant le suivi des alternants vous remercie de votre disponibilité! <br/> <br/>
Nous finaliserons votre inscription rapidement. Vous serez prévenus par mail. <br/> <br/>

<a href="http://www.fil.univ-lille1.fr">www.fil.univ-lille1.fr</a>
</body>
</html>

