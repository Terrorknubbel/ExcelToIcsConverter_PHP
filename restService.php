<?php

$methode = $_SERVER['REQUEST_METHOD'];                                                            //Request Methode wir in Variable $methode gespeichert

$daten = $_POST["q"];
$summary = $_POST["Betreff"];
$Datum = $_POST["Datum"];
$Uhrzeit = $_POST["Uhrzeit"];

switch ($methode) {                                                                              //Welche Methode wird benutzt?
  case 'GET':

    break;
  case 'POST':
  
    
    JsonToIcs($daten, $summary, $Datum, $Uhrzeit);
    break;
  case 'PUT':

    include($sendPUT);
    break;
  case 'DELETE':
    unlink('files/'.$dateiname);

    break;
}

function JsonToIcs($json, $summary, $Datum, $Uhrzeit){

  utf8_encode($json);                                                                       //String in utf8

  $json = json_decode($json);                                                               //String in json

  $my_file = "tmp.ics";                                                               //Name der fertigen Datei
  $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);                    //Öffne File in $handle

  $data = "BEGIN:VCALENDAR\nVERSION:2.0\nCALSCALE:GREGORIAN";                               //Inhalt der File beschreiben
  foreach ($json as $key => $valueArray) {
    
    for ($i=0; $i < sizeof($valueArray); $i++) {
      $data = $data . "\nBEGIN:VEVENT";
      $data = $data . "\nSUMMARY:" . $summary[$i];


      

      list($day, $month, $year) = explode(".", $valueArray[$i]->$Datum);
      list($hour, $minute) = explode(":", $valueArray[$i]->$Uhrzeit);

      if(!checkdate($month, $day, $year)){
        echo $month;
        echo $day;
        echo $year;
        //header("Location: index.html?error=invalidDate");
        exit();
      }

      if(!$hour || !$minute){
        header("Location: index.html?error=invalidTime");
        exit();
      }

      $data = $data . "\nDTSTART:" . $year . $month . $day . "T" . $hour . $minute . "00";
      $data = $data . "\nDTEND:" . $year . $month . $day . "T" . $hour . $minute . "00";
      $data = $data . "\nDESCRIPTION:";
      foreach ($valueArray[$i] as $key => $value) {
        $data = $data . $key . ": " . $value . ", ";
      }
      $data = substr($data, 0, -6) . "\nEND:VEVENT";
    }
    $data = $data . "\nEND:VCALENDAR";

  }
 
  fwrite($handle, $data);
  $file = $my_file;

  if (file_exists($file)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($file).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($file));
      readfile($file);
  }

}
?>
