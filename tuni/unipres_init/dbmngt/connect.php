<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    function doConnection($dbName="fil_presences")
    {

        $conn = mysql_connect('localhost', 'root', 'ebhhojbc');
        mysql_select_db($dbName,$conn);
        //error_log("connecting to ".$dbName);
        if (!$conn) {
            die('Problème avec le serveur de la base de données. Veuillez revenir plus tard . ' . mysqli_connect_error());
        }


        mysql_query('SET NAMES \'utf8\'', $conn);

        return $conn;

    }
?>
