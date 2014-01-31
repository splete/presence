<?php

    // convert to PDF
    require_once(dirname(__FILE__).'/../html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(7,5,7,12));
        //$html2pdf->setModeDebug();
        $content=str_replace('"',"'",$_POST['tableContent']);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        //echo "content: ".$_POST["tableContent"];
        $html2pdf->writeHTML($content);
        //echo "file: ".$_POST["filename"];
        $html2pdf->Output($_POST["filename"]);
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>