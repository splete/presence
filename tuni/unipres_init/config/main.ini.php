<?php

    //define('INDEX_PAGE','http://localhost/~marius/unipres');
    //define('ABS_START_PATH','/Volumes/DATA/UsersData/marius/Sites/unipres');
    //define('ABS_START_URL','/~marius/unipres');

    //define('INDEX_PAGE','http://stages.fil.univ-lille1.fr/unipres');
    define('INDEX_PAGE','http://localhost/tuni/unipres_init');
    define('ABS_START_PATH','/var/www/tuni/unipres_init');
    define('ABS_START_URL','/tuni/unipres_init');

    define('REF_YEAR','refYear');
    define('DEFAULT_APPLI','FA');
    define('REF_FORMATIONTYPE','refFT');
    define('MODE','mode');
    define('RESP_MODE','resp');
    define('PROF_MODE','tut');

    function getCurrYear() {
      //error_log ("currentYear : ". (date("Y")-(date("n")<9)));
      //return date("Y")-(date("n")<9);
      return 2013;
    }

    function getAvailableYears() {
      $years=array();
      for ($i=getCurrYear()+1;$i>2008;$i--) {
        $years[]=$i;
      }
      return $years;
    }

    function getMigrationYears() {
      $years=array();
      for ($i=getCurrYear()+1;$i>getCurrYear()-1;$i--) {
        $years[]=$i;
      }
      return $years;
    }
?>