<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 function createSelect($name, $keys, $values, $keyIndex)
 {
    echo "<select name=\"",$name,"\">\n";
   
    $nbValues=count($values);
    for ($i=0;$i<$keyIndex;$i++) {
       echo "<option value=\"",$keys[$i],"\">",$values[$i],"</option>\n";
    }
    if ($keyIndex>-1)
        echo "<option value=\"",$keys[$i],"\" selected='true'>",$values[$i],"</option>\n";
    else
        $i==-1;
    for ($i++;$i<$nbValues;$i++) {
       echo "<option value=\"",$keys[$i],"\">",$values[$i],"</option>\n";
    }
    echo "</select>\n";
 }

function createSelectWithOnChange($name, $keys, $values, $keyIndex, $onChange)
 {
    echo "<select name=\"",$name,"\" onChange=\"".$onChange."\">\n";

    $nbValues=count($values);

    echo $nbValues;
    
    for ($i=0;$i<$keyIndex;$i++) {
       echo "<option value=\"",$keys[$i],"\">",$values[$i],"</option>\n";
    }
    if ($keyIndex>-1)
        echo "<option value=\"",$keys[$i],"\" selected='true'>",$values[$i],"</option>\n";
    else
        $i=-1;
    for ($i++;$i<$nbValues;$i++) {
       echo "<option value=\"",$keys[$i],"\">",$values[$i],"</option>\n";
    }
    echo "</select>\n";
 }

 function createRadioGroup($name, $keys, $values, $keyIndex,$sep="<br/>")
 {
    //echo "<select name=\"",$name,"\">\n";

    
      
    $nbValues=count($values);
    for ($i=0;$i<$keyIndex;$i++) {
       echo "<input type=\"radio\" name=\"",$name,"\" value=\"",$keys[$i],"\" >",$values[$i],"</input>\n$sep";
    }
    if ($keyIndex>-1)
        echo "<input type=\"radio\" name=\"",$name,"\" value=\"",$keys[$i],"\" checked=\"checked\">",$values[$i],"</input>\n$sep";
    else {
        $i=-1;
        //echo "<input type=\"radio\" name=\"",$name,"\" value=\"\" style=\"display:none;\"/>";
    }
    for ($i++;$i<$nbValues;$i++) {
       echo "<input type=\"radio\" name=\"",$name,"\" value=\"",$keys[$i],"\" >",$values[$i],"</input>\n$sep";
    }
    
    //echo "</select>\n";
 }

 function createRadioGroupWithOnChange($name, $keys, $values, $keyIndex, $onChange,$sep="<br/>")
 {
    
    $nbValues=count($values);
    for ($i=0;$i<$keyIndex;$i++) {
       echo "<input type=\"radio\" name=\"",$name,"\" value=\"",$keys[$i],"\" checked=\"checked\">",$values[$i],"</input>\n$sep";
    }
    if ($keyIndex>-1)
        echo "<input type=\"radio\" name=\"",$name,"\" value=\"",$keys[$i],"\" checked=\"checked\">",$values[$i],"</input>\n$sep";
    else
        $i=-1;
    for ($i++;$i<$nbValues;$i++) {
       echo "<input type=\"radio\" name=\"",$name,"\" value=\"",$keys[$i],"\" checked=\"checked\">",$values[$i],"</input>\n$sep";
    }
 }

 function getParam($name,$alt=NULL)
 {
     
     if (array_key_exists($name,$_REQUEST))
     {
         //echo "param ", $_REQUEST[$name];
         return $_REQUEST[$name];
     }
     else
        if ( array_key_exists("Cookie_".$name,$_SESSION))
        {
            //echo "cookie ", $_SESSION["Cookie_".$name];
            return $_COOKIE["Cookie_".$name];
        }
        else 
        if ( isset($_SESSION[$name]))
        {
            //echo "cookie ", $_SESSION["Cookie_".$name];
            return $_SESSION[$name];
        }
        else
        {
            //echo "alt ",$alt;
            return $alt;
        }
 }

 function to_html($str)
 {
     $str1=str_replace("\r\n","<br/>",$str);
     $str1=str_replace("\n\r","<br/>",$str1);
     $str1=str_replace("\r","<br/>",$str1);
     $str1=str_replace("\n","<br/>",$str1);
     $final=$str1;
     return $final;
 }

 function to_text($str)
 {
     $str1=str_replace("<br/>","\r\n",$str);

     $final=$str1;
     return $final;
 }
 function to_minimum_lines($str,$nbLines)
 {
     if ($str==null || count($str)==0)
        $lines="";
     else
        $lines=explode("<br/>",$str);
     $add="";
     //echo $str," ",count($lines);
     for ($i=count($lines);$i<$nbLines;$i++)
        $add.="<br/>";
     $final=$str."".$add;
     return $final;
 }
?>
