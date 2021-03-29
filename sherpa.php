<?php

include('common.php');

$sh = $_GET['sh'];
if (!$sh){
  $sh = $_POST['sh'];
}
if (!$sh){
  $sh = 0;
}

$action = $_GET['action'];

$jid = $_GET['jid'];
if ($jid == ""){
  $jid = $_POST['jid'];
}

if ($action == "really_delete"){
  // kill it with fire
   $query = "DELETE FROM MyProjects WHERE id = :JID";
   $query_params = array(
              ':JID' => $jid
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
            die("Failed to run query 4: " . $ex->getMessage());
        }
    $action = "";
    $jid="";
}



if (($jid != "") AND ($sh < 5)){
  echo "Welcome Back!<br>";
  echo "It looks like you want to $action Job ID $jid <br>";
  if ($action == "delete"){
    echo '<button type="button" onClick="window.location.href=\'sherpa.php?action=really_delete&jid='.$jid.'\';">
          Click here to permanently delete this job
          </button>';
  }
  if ($action == "edit"){
    // Load vars and allow edits
     $query = "SELECT * FROM MyProjects WHERE id = :JID";
   $query_params = array(
              ':JID' => $jid
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
            die("Failed to run query 4: " . $ex->getMessage());
        }

    $row = $stmt->fetch();


  echo "<div container>";
  echo "<form name=fm1 method=POST action=\"./sherpa.php\">";
  echo "<table cellpadding=5 cellspacing=3 border=1 >";
  echo "<tr><td>Project Name</td><td>".$row['Name']."</td><td>";
  echo "<input type=text size=40 name=ProjName value=\"".$row['Name']."\"></td></tr>";
  echo "<tr><td>Template ID</td><td>".$row['Template']."</td><td>";


$url = "https://".$apidomain."/api/v1/templates";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, FALSE);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$templateArray = json_decode($response,true);


// List Templates stored in SparkPost
echo '
<select name="SPTemplates" id="SPTemplates">
';

foreach($templateArray as $a=>$b){
  foreach ($b as $c => $d){
      echo "<option value=\"$d[id]\"";
      if ($d[id] == $row['Template']){
        echo "  selected";
      }
      echo ">$d[name]</option>";
  }
}

echo '</select>';


  echo "</td></tr>";
  echo "<tr><td>Start Time</td><td>".$row['StartTime']."</td><td>";
  echo "<input type=text size=40 name=dateStamp value=\"".$row['StartTime']."\"></td></tr>";
  
  echo "</td></tr>";
  echo "<tr><td>Query</td><td>".$row['Query']."</td><td>";



$url = "https://".$apidomain."/api/v1/recipient-lists";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, FALSE);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$reciplistArray = json_decode($response,true);




echo '
Recipient Lists stored in SparkPost<br>
<select name="SPRecipients" id="SPRecipients">
      <option value="" selected>Select A List</option>
';

$parts = explode("-",$row['Query']);
$listname = substr($row['Query'],(-1*(strlen($row['Query'])-14)));

foreach($reciplistArray as $a=>$b){
  foreach ($b as $c=>$d){
      echo "<option value=$d[id] ";
    if ($d[id] == $listname ){
      echo " selected ";
    }
    echo ">$d[name]</option>";
  }
}
echo '</select>';



  echo "</td></tr>";
  echo "<tr><td>Description</td><td>".$row['Description']."</td><td width=300>";
  echo "<input type=text size=40 name=Description value=\"".$row['Description']."\"></td></tr>";

  echo "</td></tr>";
  echo "</table>";
  echo "<input type=hidden name=jid value=$jid>";
  echo "<input type=hidden name=id value=$jid>";
  echo "<input type=hidden name=sh value=5>";
  echo "<input type=submit name=submit value=SAVE>";
  echo "</form>";
  echo "</div>";
 
  }
}

$Description = $_POST['Description'];
$ProjName = $_POST['ProjName'];
$SPTemplate = $_POST['SPTemplates'];
$SPQueries = $_POST['SPQueries'];
$SPRecipients = $_POST['SPRecipients'];
$dateStamp = $_POST['dateStamp'];


echo "<h2>Sherpa is a guide through the process</h2>";
echo "<h3>Follow each step in order below, beginning with 'Select Content'</h3>";
echo "<p><font size=4>";
if ($sh > 0){echo "<font color=green>";}
echo "Select Content ==>";
if ($sh > 0){echo "</font>";}
if ($sh > 1){echo "<font color=green>";}
echo "Select Recipients ==>";
if ($sh > 1){echo "</font>";}
if ($sh > 2){echo "<font color=green>";}
echo "Schedule it ==>";
if ($sh > 2){echo "</font>";}
if ($sh > 3){echo "<font color=green>";}
echo "Save and Execute ==>";
if ($sh > 3){echo "</font>";}
if ($sh > 4){echo "<font color=green>";}
echo "Report on it !";
if ($sh > 4){echo "</font>";}
echo "</font></p>";



if ($sh < 1){
echo '
<button type="button" onClick="window.location.href=\'sherpa.php?sh=1\';">Follow the Sherpa...</button>';

echo '
<div container>
<p>Most recent scheduled Projects:<br>
<table cellpadding=5 cellspacing=3 border=1 >
 <tr>
  <th width=30>ID</th>
  <th width=100>Project Name</th>
  <th width=100>Owner</th>
  <th width=200>Start Time</th>
  <th width=200>Build Time</th>
  <th width=300>Query</th>
  <th width=200>Description</th>
  <th width=150>ACTION</th>
 </tr>
';


   $query = "SELECT * FROM MyProjects ORDER BY StartTime DESC LIMIT 10";
   $query_params = array(
              ':PN' => $ProjName
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
            die("Failed to run query 4: " . $ex->getMessage());
        }

   while ($row = $stmt->fetch()){
     echo "<tr><td>".$row['id']."</td><td>".$row['Name']."</td><td>".$row['Owner']."</td><td>".$row['StartTime']."</td>
               <td>".$row['BuildTime']."</td><td>".$row['Query']."</td><td>".$row['Description']."</td>";
       echo "<td align=center> 
                  <a href=\"launcher.php?test=true&job=".$row['id']."\">TEST</a> | 
                  <a href=\"sherpa.php?action=edit&jid=".$row['id']."\">EDIT</a> | 
                  <a href=\"sherpa.php?action=delete&jid=".$row['id']."\">DELETE</a> | ";
     if($row['TransID'] !=""){
       echo " <a href=\"reports.php?tid=".$row['TransID']."\">VIEW</a> </td></tr>";
     }
     else {
       echo " VIEW</td></tr>";
     }

   }



echo '</table></p> </div>';



}

if ($sh == 1) {

$url = "https://".$apidomain."/api/v1/templates";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, FALSE);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$templateArray = json_decode($response,true);


// List Templates stored in SparkPost
echo '
<p>
<form name="f1" method="POST" action="./sherpa.php">
Select a stored tempalte<br>
<select name="SPTemplates" id="SPTemplates">
';

foreach($templateArray as $a=>$b){
  foreach ($b as $c => $d){
      echo "<option value=\"$d[id]\" selected>$d[name]</option>";
  }
}

echo '
</select>
<input type=hidden name=sh value=2>
<button type="submit">NEXT</button> &nbsp;
</form>
</p>';

}


if ($sh == 2) {

echo "Using template <b>$SPTemplate</b> <br>";
 
$url = "https://".$apidomain."/api/v1/recipient-lists";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, FALSE);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$reciplistArray = json_decode($response,true);

echo '
<p>
<form name="f1" method="POST" action="./sherpa.php">
Recipient Lists stored in SparkPost<br>
<select name="SPRecipients" id="SPRecipients">
      <option value="" selected>Select A List</option>
';

foreach($reciplistArray as $a=>$b){
  foreach ($b as $c=>$d){
      echo "<option value=$d[id]>$d[name]</option>";
  }
}

echo '
</select>
<button type="submit">NEXT</button> &nbsp;
</p>';

echo '
<input type=hidden name=sh value=3>
<input type=hidden name=SPTemplates value="'.$SPTemplate.'">
<button type="submit">NEXT</button> &nbsp;
</form>
';

echo '
<input type=hidden name=SPQueries value="'.$SPQueries.'">
';

}

if ($sh == 3) {

$nowFormatted = strftime("%FT%T%z");
$nowFormatted = substr_replace( $nowFormatted, ":", -2, 0 );

  echo "Using template <b>$SPTemplate</b> <br>";

  if($SPRecipients != ""){
    echo "Using list <b>$SPRecipients</b> <br>";
  }

  if ($SPQueries != ""){
    $parts=explode("-", $SPQueries);
    echo "Using query <b>$parts[1]</b> <br>";
  }

  echo "<p>Pick a day and time to send this. 
        <br>The current time is shown below, edit to the time you want and click NEXT.</p>";
  echo '
    <form name="f1" method="POST" action="./sherpa.php">
      Provide a name for this job:
      <input type=text size=30 name=ProjName value=""><br>
      Provide a short description:
      <input type=text size=30 name=Description value=""><br><br>
      <input type=text size=30 name=dateStamp value='.$nowFormatted.'>
      <button type="submit">NEXT</button> &nbsp;
      <br><font size=2px>YYYY-MM-DD hh:mm:ss-TZ Offset</font><br>
      <input type=hidden name=sh value=4>
      <input type=hidden name=SPTemplates value="'.$SPTemplate.'">
      <input type=hidden name=SPRecipients value="'.$SPRecipients.'">
      <input type=hidden name=SPQueries value="'.$SPQueries.'">
    </form>
';

}

if ($sh == 4){

  echo "Project <b>$ProjName</b> has been scheduled with the following parameters:<br>
        Description: <b>$Description</b><br>
        Using template <b>$SPTemplate</b> <br>";

  if($SPRecipients != ""){
    echo "Using list <b>$SPRecipients</b> <br>";
  }

  if ($SPQueries != ""){
    $parts=explode("-", $SPQueries);
    echo "Using query <b>$parts[1]</b> <br>";
  }

  echo "Scheduled for <b>$dateStamp</b> <br>";


  echo "<p>If you are satisfied with the above settings, click [SAVE and EXECUTE] to schedule this project.<br>
         Otherwize, restart the Sherpa and these settings will be discarded.</p>";
  echo '
    <form name="f1" method="POST" action="./sherpa.php">
      <button type="submit">SAVE and EXECUTE</button> &nbsp;
      <input type=hidden name=sh value=5>
      <input type=hidden name=SPTemplates value="'.$SPTemplate.'">
      <input type=hidden name=SPRecipients value="'.$SPRecipients.'">
      <input type=hidden name=SPQueries value="'.$SPQueries.'">
      <input type=hidden name=dateStamp value="'.$dateStamp.'">
      <input type=hidden name=ProjName value="'.$ProjName.'">
      <input type=hidden name=Description value="'.$Description.'">
    </form>
';

}

if ($sh == 5){

/*

     <input type=hidden name=SPTemplates value="'.$SPTemplate.'">
      <input type=hidden name=SPRecipients value="'.$SPRecipients.'">
      <input type=hidden name=SPQueries value="'.$SPQueries.'">
      <input type=hidden name=dateStamp value="'.$dateStamp.'">
      <input type=hidden name=ProjName value="'.$ProjName.'">
      <input type=hidden name=Description value="'.$Description.'">
*/


    // execute the job immediately
    echo "Executing now...";


//  if (strtotime($dateStamp) <= $now){
//       echo "This one qualifies to send now<br>";
      

       $json = '

        {
          "name": "Fall Sale",
          "campaign_id": "fall",
          "options": {
            "start_time": "'.$dateStamp.'"
          },
          "recipients": {
            "list_id": "'.$SPRecipients.'"
          },
          "content": {
            "template_id": "'.$SPTemplate.'"
          }
        }';




  // Issue the Transmission API call
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, "https://$apidomain/api/v1/transmissions");
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
       curl_setopt($ch, CURLOPT_HEADER, FALSE);
       curl_setopt($ch, CURLOPT_POST, TRUE);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         "Content-Type: application/json",
         "Authorization: $apikey"
       ));

       $response = curl_exec($ch);
       curl_close($ch);

       $res = json_decode($response,true);
       $TransID = $res['results']['id'];
  echo "<pre><br>";
       var_dump($res);
  echo "<br> USING <br>";
       var_dump($json);
  echo "</pre><br>";

       if ($res['total_rejected_recipients'] > 0){
           echo 'Message could not be sent to ' .$res['total_rejected_recipients']. ' recipients <br>';
       }
       if ($res['errors'] != "") {
           echo 'Message could not be sent. <br>';
       }
       else {
         echo "<p>   Message sent to customer!  </p> ";
         echo "Res: $response <br>";
         echo "Transmission ID = $TransID <br>";
         echo "<br/> <br/>";
       }

  echo '<a href="./sherpa.php">Cliick here</a> to return to the Sherpa<br>';


}


?>

