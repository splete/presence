<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function doDBEscape($str)
{
    return str_replace("'","\\'",$str);
}


?>
