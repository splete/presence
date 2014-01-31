<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


    if (!isset($_SESSION)) {
      session_start();
    }

    function rel($r, &$f) {
      $res=file_exists( ( $f = ( $r.'/'.$f ) ) );
      //echo $f."-".($res==TRUE?'true':'false')."<br/>";
      return $res;

    }
    function relro($r, $f) { 
        if (rel($r,$f)) {
           require_once($f);
           return TRUE;
        } else
        {
            return FALSE;
        }
     }

    relro('../config','auth.ini.php');
    relro('../config','main.ini.php');
    relro('../dbmngt','connect.php') ;
    relro('../html','utils.php') ;

    relro('./config','auth.ini.php');
    relro('./config','main.ini.php');
    relro('./dbmngt','connect.php');
    relro('./html','utils.php');
    
   
    function authenticate($service) {

      $_SESSION[CK_USER]="stefan.dochez"; 
      checkAllRoles();
      return;

       //$_SESSION[CK_USER]="donovan.watteau";
       //$_SESSION[CK_USER]="bilasco";
       //$_SESSION['CK_USER']="marquet";
       //$_SESSION['CK_USER']="marvie";
       //checkAllRoles();
       //return;
        
        // propre URL

        /*
         * error_log ("------------------------------------");
         
        error_log ("------------------------------------");
        error_log ("------------------------------------");
        error_log ("------------------------------------");
        error_log ("------------------------------------");
        error_log ("------------------------------------");
        error_log ("------------------------------------");

        error_log ($service);
        
        error_log ("récupération du ticket (retour du serveur CAS) pour ".$service);
        */
       if (array_key_exists('ticket',$_REQUEST)===FALSE) {

            // pas de ticket : on redirige le navigateur web vers le serveur CAS
            //error_log ("redirection " . 'Location: ' . CAS_BASE . '/login?service=' . $service);
            header('Location: ' . CAS_BASE . '/login?service=' . $service);
            exit() ;
        }

        $ticket=$_REQUEST['ticket'];
        //error_log("un ticket ".$ticket." a été transmis, on essaie de le valider auprès du serveur CAS");
        /*error_log(CAS_BASE . '/serviceValidate?service='
           . preg_replace('/\?ticket=/','&ticket=',$service) . '<br>'
           //.$service."&ticket=".$ticket
           );
           */
        $fpage = fopen (CAS_BASE . '/serviceValidate?service='
            . preg_replace('/\?ticket=/','&ticket=',$service)
            //.$service."&ticket=".$ticket
            , 'r');

        
        if ($fpage) {
            $page="";
            while (!feof ($fpage)) { $page .= fgets ($fpage, 1024); }
            //error_log("FPAGE : ".$page);
            if (preg_match('|<cas:authenticationSuccess>.*</cas:authenticationSuccess>|mis',$page)) {
                    if(preg_match('|<cas:user>(.*)</cas:user>|',$page,$match)){
                        //error_log("autheticated : ".$match[1]);
                        
                        session_unset();
			$_SESSION[CK_USER]=$match[1];
			if (strcmp($match[1],"bilasco")==0) {
				//$_SESSION[CK_USER]="mahiddine";
			}
                       

                        //$_SESSION[CK_USER]="antoine.craske";
                        //$_SESSION[CK_USER]="guillaume.despois";
                        //$_SESSION[CK_USER]="sebastien.velay";
                        //$_SESSION[CK_USER]="gael.ceriez";
                        //$today=array("");

                        //$_SESSION[CK_USER]="lepallec";
                        //$_SESSION[CK_USER]="kuttler";
                        //$today=array("");

                        //$today=array("");
                        $fauthname="auth.pxml";
                        rel("../xml",$fauthname);
                        rel("./xml",$fauthname);
                        $fauth=fopen($fauthname,"a");
                        fprintf($fauth,"<a><t>%s</t><u>%s</u></a>\n",date("c"),$match[1]);
                        fclose($fauth);
                        checkAllRoles();
                        return ;
                    }
                }
            }
         /*
         
         //$_SESSION['CK_USER']="remi.pourchelle";
         $_SESSION['CK_USER']="bilasco";
         //$_SESSION['CK_USER']="marquet";
         //$_SESSION['CK_USER']="marvie";
         $fauthname="auth.pxml";
        rel("../xml",$fauthname);
        rel("./xml",$fauthname);
        //$fauth=fopen($fauthname,"a");
        //fprintf($fauth,"<a><t>%ld</t><u>%s</u></a>\n",$today[0],$match[1]);
        //fclose($fauth);
        checkAllRoles();
        return ;
        */
        // problème de validation
        redirectAuth(INDEX_PAGE);
    }


    function checkRoles($user)
    {

        if (!isset($_SESSION[REF_YEAR])) {
          $_SESSION[REF_YEAR]=getCurrYear();
        }
        
        $conn=doConnection();
        
        $queryString = "Select roleRef from roles_membres"
                        ." where userRef='".$user."' and roleRef in (select roleCle from roles) "
                        ." and anneeReference<=".$_SESSION[REF_YEAR]." and obsolete=0;";

        
        $roles=mysql_query($queryString,$conn);
        if (mysql_errno()!=0) error_log($queryString." - ".mysql_error());

        $role=mysql_fetch_row($roles);
        for ($i=0;$role;$role=mysql_fetch_row($roles),$i++)
        {
          //  echo $role;
            //error_log($role[0]);
            $_SESSION[CK_ROLES] .= $role[0]." ";
        }
        //error_log ($_SESSION[CK_ROLES]);
        
        $queryString = "Select roleRef from roles_membres"
                        ." where userRef='".$user."' "
                        ." and anneeReference<=".$_SESSION[REF_YEAR]." and obsolete=0;";
                        
        
        //error_log($queryString." <br/>");

        $roles=mysql_query($queryString,$conn);
        if (mysql_errno()!=0) error_log($queryString." - ".mysql_error());

        $role=mysql_fetch_row($roles);
        for ($i=0;$role;$role=mysql_fetch_row($roles),$i++)
            checkRoles($role[0]);
        
    }

    function checkProfRole($user)
    {
        $conn=doConnection();
        $queryString = "select profCle from membre where profCle like '".$user."';";
        
        $profs=mysql_query($queryString,$conn);
        if (mysql_errno()!=0) error_log($queryString." - ".mysql_error());
        //echo 'query done';
        $prof=mysql_fetch_row($profs);
        
        if ($prof) {
            //echo "yess proff<br/>";
            $_SESSION[CK_ROLES] .= PROF_ROLE." ";
        }
    }

    function checkStudentRole($user)
    {
        $conn=doConnection();
        $queryString = "select etudCle from etudiant where etudCle like '".$user."' ;";
        echo $queryString;

        $roles=mysql_query($queryString,$conn);
        if (mysql_errno()!=0) error_log($queryString." - ".mysql_error());
        
        if ($roles) {
          $role=mysql_fetch_row($roles);
          //echo $role[0];
          if ($role) {$_SESSION[CK_ROLES] .= STUD_ROLE." ";}
          //echo $_SESSION[CK_ROLES] .= STUD_ROLE." ";
        }
    }

    function checkAllRoles()
    {
        $_SESSION[CK_ROLES]="";

        $user=getParam(CK_USER,$_SESSION[CK_USER]);;

        error_log("checking roles for ".$user);
        
        checkStudentRole($user);
        //else { // users others than STUDENT can have several roles
        //if (strcmp($role,PROF_ROLE)===0)
        checkProfRole($user);
        checkRoles($user);
        //}

        error_log("found roles ".$_SESSION[CK_ROLES]);

//        setCookie(CK_USER,$_SESSION[CK_USER],time()+600);
//        setCookie(CK_ROLES,$_SESSION[CK_ROLES],time()+600);
    }

    function hasRole($role)
    {
        $user=getParam(CK_USER,$_SESSION[CK_USER]);
        $roles=getParam(CK_ROLES,$_SESSION[CK_ROLES]);

        //error_log("requires role ".$role." for ".$user. " | ". $roles);
        if ($user===null || $roles === null)
        {
            //error_log("cookie does not exists");
            return FALSE;
        }
        
        if (strlen($role)==0)
          return TRUE;
        
        //error_log("cookie exist : ".$roles);
        return (strpos($roles,$role,0)!== FALSE);     
    }

    function hasRespRole()
    {
        $user=getParam(CK_USER,$_SESSION[CK_USER]);
        $roles=getParam(CK_ROLES,$_SESSION[CK_ROLES]);

        //error_log("requires role ".$role." for ".$user. " | ". $roles);
        if ($user===null || $roles === null)
        {
            //error_log("cookie does not exists");
            return FALSE;
        }

        //error_log("cookie exist : ".$roles);
        return (strpos($roles,"RESP",0)!== FALSE);
    }

    function redirectAuth($url)
    {
        //header('Location: '.INDEX_PAGE);
        if ($url) 
            header('Location: '.$url);
        else
            header('Location: '.INDEX_PAGE);
        exit();
    }

    function constructAllGroupesKeys()
    {
        $keys=array("M1MIAGEFA,M2MIAGEFA,M1INFOFA,M2IAGLFA,"
                               ."M2ESERVFA,M2TIIRFA,M2IVIFA,M2MOCADFA",
                               "M1MIAGEFA,M2MIAGEFA",
                               "M1MIAGEFA",
                               "M2MIAGEFA",
                               "M1INFOFA,M2IAGLFA,M2ESERVFA,M2TIIRFA,M2IVIFA,M2MOCADFA",
                               "M1INFOFA",
                               "M2IAGLFA,M2ESERVFA,M2TIIRFA,M2IVIFA,M2MOCADFA",
                               "M2IAGLFA",
                               "M2ESERVFA",
                               "M2TIIRFA",
                               "M2IVIFA",
                               "M2MOCADFA");
        $values=array("Toutes","MIAGE FA","M1 MIAGE FA","M2 MIAGE FA","INFO FA","M1 INFO FA","M2 INFO FA","M2 IAGL FA","M2 E-SERV FA","M2 TIIR FA", "M2 IVI FA","M2 MOCAD FA" );
        $result=array("keys"=>$keys,"values"=>$values);
        return $result;
    }

    function initPredefinedFIKeysValues($onlyM1INFO=false) {

            $predefinedKeys["ALL_RESP_FI"]="M1MIAGEFI,M2MIAGEFI,M1INFOFI,M2IAGLFI,"
                               ."M2ESERVFI,M2TIIRFI,M2IVIFI,M2MOCADFI";
            $predefinedValues["ALL_RESP_FI"]="MIAGE &amp; INFO FI";

            $predefinedKeys["L3MIAGEFI_RESP"]="L3MIAGEFI";
            $predefinedValues["L3MIAGEFI_RESP"]=" - MIAGE - L3 FI";

            $predefinedKeys["MMIAGEFI_RESP"]="M1MIAGEFI,M2MIAGEFI";
            $predefinedValues["MMIAGEFI_RESP"]="MIAGE - M1 &amp; M2 FI";

            $predefinedKeys["M1MIAGEFI_RESP"]="M1MIAGEFI";
            $predefinedValues["M1MIAGEFI_RESP"]=" - MIAGE - M1 FI";

            $predefinedKeys["M2MIAGEFI_RESP"]="M2MIAGEFI";
            $predefinedValues["M2MIAGEFI_RESP"]=" - MIAGE - M2 FI";

            if ($onlyM1INFO==false)  {
              $predefinedKeys["MINFOFI_RESP"]="M1INFOFI,M2IAGLFI,M2ESERVFI,M2TIIRFI,M2IVIFI,M2MOCADFI";
              $predefinedValues["MINFOFI_RESP"]="INFO - M1 &amp; M2 FI";
            }
            
            $predefinedKeys["M1INFOFI_RESP"]="M1INFOFI,M1IAGLFI,M1ESERVFI,M1TIIRFI,M1IVIFI,M1MOCADFI";
            $predefinedValues["M1INFOFI_RESP"]="INFO - M1 FI";

            if ($onlyM1INFO==false) {
              $predefinedKeys["M1ESERVFI_RESP"]="M1ESERVFI";
              $predefinedValues["M1ESERVFI_RESP"]=" - ESERV - M1 FI";

              $predefinedKeys["M1IAGLFI_RESP"]="M1IAGLFI";
              $predefinedValues["M1IAGLFI_RESP"]=" - IAGL - M1 FI";

              $predefinedKeys["M1IVIFI_RESP"]="M1IVIFI";
              $predefinedValues["M1IVIFI_RESP"]=" - IVI - M1 FI";

              $predefinedKeys["M1MOCADFI_RESP"]="M1MOCADFI";
              $predefinedValues["M1MOCADFI_RESP"]=" - MOCAD - M1 FI";

              $predefinedKeys["M1TIIRFI_RESP"]="M1TIIRFI";
              $predefinedValues["M1TIIRFI_RESP"]=" - TIIR - M1 FI";

              $predefinedKeys["M1INFOFIG_RESP"]="M1INFOFI";
              $predefinedValues["M1INFOFIG_RESP"]=" - Sans Spec - M1 FI";
            }
            $predefinedKeys["M2INFOFI_RESP"]="M2IAGLFI,M2ESERVFI,M2TIIRFI,M2IVIFI,M2MOCADFI";
            $predefinedValues["M2INFOFI_RESP"]="INFO - M2 FI";

            $predefinedKeys["M2ESERVFI_RESP"]="M2ESERVFI";
            $predefinedValues["M2ESERVFI_RESP"]=" - ESERV - M2 FI";

            $predefinedKeys["M2IAGLFI_RESP"]="M2IAGLFI";
            $predefinedValues["M2IAGLFI_RESP"]=" - IAGL - M2 FI";

            $predefinedKeys["M2IVIFI_RESP"]="M2IVIFI";
            $predefinedValues["M2IVIFI_RESP"]=" - IVI - M2 FI";

            $predefinedKeys["M2MOCADFI_RESP"]="M2MOCADFI";
            $predefinedValues["M2MOCADFI_RESP"]=" - MOCAD - M2 FI";

            $predefinedKeys["M2TIIRFI_RESP"]="M2TIIRFI";
            $predefinedValues["M2TIIRFI_RESP"]=" - TIIR - M2 FI";


            return array("keys"=>$predefinedKeys,"values"=>$predefinedValues);
    }


function initPredefinedFAKeysValues($onlyM1INFO=false) {


            $predefinedKeys["ALL_RESP_FA"]="M1MIAGEFA,M2MIAGEFA,M1INFOFA,M2IAGLFA,"
                               ."M2ESERVFA,M2TIIRFA,M2IVIFA,M2MOCADFA";
            $predefinedValues["ALL_RESP_FA"]="MIAGE &amp; INFO FA";

            $predefinedKeys["L3MIAGEFA_RESP"]="L3MIAGEFA";
            $predefinedValues["L3MIAGEFA_RESP"]=" - MIAGE - L3 FA";

            $predefinedKeys["MMIAGEFA_RESP"]="M1MIAGEFA,M2MIAGEFA";
            $predefinedValues["MMIAGEFA_RESP"]="MIAGE - M1 &amp; M2 FA";

            $predefinedKeys["M1MIAGEFA_RESP"]="M1MIAGEFA";
            $predefinedValues["M1MIAGEFA_RESP"]=" - MIAGE - M1 FA";

            $predefinedKeys["M2MIAGEFA_RESP"]="M2MIAGEFA";
            $predefinedValues["M2MIAGEFA_RESP"]=" - MIAGE - M2 FA";
            
            $predefinedKeys["M2IPINTFA_RESP"]="M2IPINTFA";
            $predefinedValues["M2IPINTFA_RESP"]=" - IPINT - M2 FA";

            if ($onlyM1INFO==false)  {
                $predefinedKeys["MINFOFA_RESP"]="M1INFOFA,M2IAGLFA,M2ESERVFA,M2TIIRFA,M2IVIFA,M2MOCADFA";
                $predefinedValues["MINFOFA_RESP"]="INFO - M1 &amp; M2 FA";
            }
            
            $predefinedKeys["M1INFOFA_RESP"]="M1INFOFA,M1IAGLFA,M1ESERVFA,M1TIIRFA,M1IVIFA,M1MOCADFA";
            $predefinedValues["M1INFOFA_RESP"]="INFO - M1 FA";

            if ($onlyM1INFO==false) {
              $predefinedKeys["M1ESERVFA_RESP"]="M1ESERVFA";

              $predefinedValues["M1ESERVFA_RESP"]=" - ESERV - M1 FA";

              $predefinedKeys["M1IAGLFA_RESP"]="M1IAGLFA";
              $predefinedValues["M1IAGLFA_RESP"]=" - IAGL - M1 FA";

              $predefinedKeys["M1IVIFA_RESP"]="M1IVIFA";
              $predefinedValues["M1IVIFA_RESP"]=" - IVI - M1 FA";

              $predefinedKeys["M1MOCADFA_RESP"]="M1MOCADFA";
              $predefinedValues["M1MOCADFA_RESP"]=" - MOCAD - M1 FA";

              $predefinedKeys["M1TIIRFA_RESP"]="M1TIIRFA";
              $predefinedValues["M1TIIRFA_RESP"]=" - TIIR - M1 FA";

              $predefinedKeys["M1INFOFAG_RESP"]="M1INFOFA";
              $predefinedValues["M1INFOFAG_RESP"]=" - Sans Spec - M1 FA";
            }

            $predefinedKeys["M2INFOFA_RESP"]="M2IAGLFA,M2ESERVFA,M2TIIRFA,M2IVIFA,M2MOCADFA";
            $predefinedValues["M2INFOFA_RESP"]="INFO - M2 FA";

            $predefinedKeys["M2ESERVFA_RESP"]="M2ESERVFA";
            $predefinedValues["M2ESERVFA_RESP"]=" - ESERV - M2 FA";

            $predefinedKeys["M2IAGLFA_RESP"]="M2IAGLFA";
            $predefinedValues["M2IAGLFA_RESP"]=" - IAGL - M2 FA";

            $predefinedKeys["M2IVIFA_RESP"]="M2IVIFA";
            $predefinedValues["M2IVIFA_RESP"]=" - IVI - M2 FA";

            $predefinedKeys["M2MOCADFA_RESP"]="M2MOCADFA";
            $predefinedValues["M2MOCADFA_RESP"]=" - MOCAD - M2 FA";

            $predefinedKeys["M2TIIRFA_RESP"]="M2TIIRFA";
            $predefinedValues["M2TIIRFA_RESP"]=" - TIIR - M2 FA";

            return array("keys"=>$predefinedKeys,"values"=>$predefinedValues);
            
    }

    function constructGrantedGroupesKeys($unique=false,$all=false,$ft=null,$onlyM1INFO=false)
    {
        //error_log("constructing with unique:$unique all:$all ft:$ft");
        if ($ft==null)
          $ft=isset($_SESSION[REF_FORMATIONTYPE])?$_SESSION[REF_FORMATIONTYPE]:"FA/FI";
        $roles = getParam(CK_ROLES,$_SESSION[CK_ROLES]);
        
        /*if (strpos($roles,"ALL_RESP",0)) {
            return constructAllGroupesKeys();
        } 
        else */
      {
          
            $predefKeys=array();
            $predefValues=array();

          
            
            //error_log("formationType ref is ".$ft);
            if (strstr($ft,"FA")!=FALSE) {
              $res=initPredefinedFAKeysValues($onlyM1INFO);
              $keys=$res["keys"];$values=$res["values"];
              $ikeys=array_keys($keys);
              for ($i=0;$i<count($ikeys);$i++) {
                $predefKeys[$ikeys[$i]]=$keys[$ikeys[$i]];
                $predefValues[$ikeys[$i]]=$values[$ikeys[$i]];
              }
            }
            if (strstr($ft,"FI")!=FALSE) {
              $res=initPredefinedFIKeysValues($onlyM1INFO);
              $keys=$res["keys"];$values=$res["values"];
              $ikeys=array_keys($keys);
              for ($i=0;$i<count($ikeys);$i++) {
                $predefKeys[$ikeys[$i]]=$keys[$ikeys[$i]];
                $predefValues[$ikeys[$i]]=$values[$ikeys[$i]];
              }
            }
 

            $roles = preg_replace("/PROF/","",$roles);
            $roles = preg_replace("/ETUD/","",$roles);
	    
	    $roles = preg_replace("/SECR/","RESP",$roles);

            //error_log("total nb of authorizations : ".count($predefKeys));
            $index=array_keys($predefKeys);

            $keys=array();
            $values=array();
            
            for ($i=1,$k=1;$i<count($index);$i++) {
                $pos = strpos($roles,$index[$i],0);

//                error_log("checking for ".$index[$i]);
//                error_log($unique." ".strpos($predefKeys[$index[$i]],',')." ".$index[$i]);
//                error_log($predefValues[$index[$i]]);
//                error_log("cond 1 :".(($all==true)||($pos!==FALSE)) );
//                error_log("cond 2 : ".(!$unique || (strpos($predefKeys[$index[$i]],',')==FALSE)));
//                error_log("cond 3 : ".((strpos($predefKeys[$index[$i]],'1INFO')==1)?1:0));
//
                if (
                  (($all==true)||($pos!==FALSE)) &&
                  ((!$unique || (strpos($predefKeys[$index[$i]],',')==FALSE)) ||
                    ($onlyM1INFO && ((strpos($predefKeys[$index[$i]],'1INFO'))==1)))
                )
                {
                    $keys[$k]=$predefKeys[$index[$i]];
                    $values[$k]=$predefValues[$index[$i]];
                    $k++;
                }
            }

            if ($k>1&&!$unique) {
                $keys[0]=$keys[1];
                $values[0]="Toutes";
                for ($i=2;$i<$k;$i++)
                    $keys[0] .= ",".$keys[$i];
            } else {
              $keys[0]="";$values[0]="Aucune";
            }
        }
        $result=array("keys"=>$keys,"values"=>$values);
        //error_log ($result["values"][0]);
        //error_log ($result["keys"][0]);
        return $result;
    }
?>
