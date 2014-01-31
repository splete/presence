<?php
    require_once 'secure/auth.php';

    $service = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

    authenticate($service);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Menu Prensences</title>
        <link href="styles/Css_autentification.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Gestion des présences au département FIL de l'IEEA</h2>

        <p>Bonjour <b><?php echo $_SESSION[CK_USER]?></b>. Si vous n'êtez pas <b><?php echo $_SESSION[CK_USER]?></b> veuillez vous <a href="./disconnect.php">deconnecter</a>!</p>
        
        <ul>
        <?php
            
            if (hasRole(RESP_ROLE)===TRUE ) {
                echo '<li>'
                     .'   <a href="interface/accueilResp.php">Accueil</a>'
                     .'</li>';
            } else
               if (hasRole(SECR_ROLE)===TRUE)
                echo '<li>'
                     .'   <a href="interface/accueilSecr.php">Accueil Sécretaire</a>'
                     .'</li>';
               else if (hasRole(GEST_ROLE)===TRUE)
                echo '<li>'
                     .'   <a href="interface/accueilGest.php">Accueil Gestionnaire</a>'
                     .'</li>';
               else if (hasRole(STUD_ROLE)===TRUE)
                echo '<li>'
                     .'   <a href="interface/accueilEtud.php">Accueil Etudiant</a>'
                     .'</li>';
               else echo "
                <h2>Vous n'êtes pas reconnus par l'application Presences.<br/> Veuillez-vous enregister!</h2>
		En cas de problème persistant veuillez contacter Marius.Bilasco@lifl.fr !
                Cette application ne s'adressent qu'aux sécretaires et responsables de formation.
                <form action='interface/enregNouveauUtilisateur.php' method='post'>
                Nom:<input type='text' name='nom'/><br/>
                Prenom:<input type='text' name='prenom'/><br/>
                <!--Email:<input type='text' name='mail'/><br/>-->
                <input type='hidden' name='uid' value='".$_SESSION[CK_USER]."'/>
                Rôle :<select name='role'><option value='resp'>Responsable</option><option value='secr'>Secretaire</option></select>
                <br/><input type='submit' value='Je m enregistre!'/>
              </form>";
           
        ?>
        </ul>
    </body>
</html>
