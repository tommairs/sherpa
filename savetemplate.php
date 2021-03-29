<?php

include('env.php');
include('common.php');

$now = time();


$tmpID          = $_POST['id'];
$Name           = $_POST['name'];
$Owner          = $_POST['owner']; 
$LastEditBy     = $_SESSION['User'];
$Description    = $_POST['description'];
$BodyHTML       = $_POST['htmlval'];
$BodyTEXT       = $_POST['textval'];
$BodyAMP        = $_POST['ampval'];
$SubVars        = $_POST['subvars'];
$Published      = $_POST['published'];
$ClickTracking  = $_POST['click_tracking'];
$Transactional  = $_POST['transactional'];
$OpenTracking   = $_POST['open_tracking'];
$Shared         = $_POST['shared_with_subaccounts'];
//$LastUpdateSP   = $_POST['last_update_time'];
$LastUpdateSP   = date("Y-m-d\TH:i:s+0:00",$now);
$LastUpdateDB   = date("Y-m-d\TH:i:s+0:00",$now);
$LastUse        = $_POST['last_use'];
$HasPublished   = $_POST['has_published'];
$HasDraft       = $_POST['has_draft'];
$Email          = $_POST['from_email'];
$From_Name      = $_POST['from_name'];
$Subject        = $_POST['subject'];
$ReplyTo        = $_POST['reply_to'];
$quillval       = $_POST['quillval'];


//Override HTML Body if WYSIWYG data is source
  if ($_POST['btnwysiwyg'] == "SAVE"){
    $BodyHTML = $quillval;
  }

    $publish_it = false;
//Publish result if the PUBLISH button was selected
  if ($_POST['publish'] == "PUBLISH"){
    $publish_it = true;
    $BodyHTML = $quillval;
  }



$LastEditBy = str_replace("@","\@",$LastEditBy);

$LastEditBy = "nobody";

if (!$tmpID){$tmpID = "sampleID";}
if (!$Name){$Name = "sampleName";}
if (!$Owner){$Owner = "sampleOwner";}
if (!$BodyHTML){$BodyHTML = "sampleHTML";}
if (!$BodyTEXT){$BodyTEXT = "sampleTEXT";}
if (!$BodyAMP){$BodyAMP = "sampleAMP";}
if (!$SubVars){$SubVars = "sampleVars";}
if (!$Description){$Description = "sampleDesc";}
if (!$LastEditBy){$LastEditBy = "sampleEditor";}


if ($OpenTracking < 1){
  $spOpenTracking = false;
  $OpenTracking = 0;
}
else{
  $spOpenTracking = true;
  $OpenTracking = 1;
}
if ($ClickTracking < 1){
  $spClickTracking = false;
  $ClickTracking = 0;
}
else{
  $spClickTracking = true;
  $ClickTracking = 1;
}
if ($Shared < 1){
  $spShared = 0;
  $Shared = 0;
}
else{
  $spShared = 1 ;
  $Shared = 1 ;
}
if ($Published < 1){
  $spPublished = 0;
  $Published = 0;
}
else{
  $spPublished = 1;
  $Published = 1;
}
if ($Transactional < 1){
  $spTransactional = 0;
  $Transactional = 0;
}
else{
  $spTransactional = 1;
  $Transactional = 1;
}
if ($HasPublished < 1){
  $spHasPublished= 0;
  $HasPublished= 0;
} 
else{
  $spHasPublished = 1;
  $HasPublished= 1;
}
if ($HasDraft < 1){
  $spHasDraft = 0;
  $HasDraft = 0;
}
else{
  $HasDraft = 1;
  $spHasDraft = 1;
}


if (!$LastUpdateSP){$LastUpdateSP = 0;}
if (!$LastUse){$LastUse = 0;}
if (!$Email){$Email = "A";}
if (!$From_Name){$From_Name = "A";}
if (!$Subject){$Subject = "A";}
if (!$ReplyTo){$ReplyTo = $Email;}

echo "saving SESSION Data<br>";

 $_SESSION['templateHTML'] = $BodyHTML;
 $_SESSION['templateTEXT'] = $BodyTEXT;
 $_SESSION['templateAMP'] = $BodyAMP;
 $_SESSION['subvars'] = $SubVars;
 $_SESSION['templateSET'] = array(
   "id" => $tmpID,
   "name" => $Name,
   "owner" => $Owner,
   "LastEditBy" => $LastEditBy,
   "description" => $Description,
   "published" => $spPublished,
   "click_tracking" => $spClickTracking,
   "transactional" => $spTransactional,
   "open_tracking" => $spOpenTracking,
   "shared_with_subaccounts" => $spShared,
   "LastUpdateSP" => $LastUpdateSP,
   "LastUpdateDB" => $LastUpdateDB,
   "LastUse" => $LastUse,
   "has_published" => $spHasPublished,
   "has_draft" => $spHasDraft,
   "from_email" => $Email,
   "from_name" => $From_Name,
   "subject" => $Subject,
   "reply_to" => $ReplyTo
   );



echo "<pre><br> ==========<br>";
var_dump($_SESSION['templateSET']);
echo "</pre>";
//exit;


    $bom = pack('H*','EFBBBF');
    $BodyHTML = preg_replace("/^$bom/", '', $BodyHTML);
    $BodyTEXT = preg_replace("/^$bom/", '', $BodyTEXT);
    $BodyAMP = preg_replace("/^$bom/", '', $BodyAMP);



// lookup MyTemplates $_POST['id'] == tmpID

$query = "SELECT tmpID FROM MyTemplates WHERE tmpID = '".$tmpID."'";
   $query_params = array(
              ':ID' => $_POST['id']
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
            die("Failed to run query: " . $ex->getMessage());
        }

 
  $row = $stmt->fetch();

var_dump($row);



  if ($row['tmpID'] != ""){ 

echo "Updating existing template...<br>";

    $query = "UPDATE MyTemplates SET 
tmpID = :ID,
name = :N,
Owner = :O,
LastEditBy = :LE,
Description = :DE,
BodyHTML = :HTML,
BodyText = :TEXT,
BodyAMP = :AMP,
SubVars = :VARS,
Published = :PUB,
ClickTracking = :CL,
Transactional = :TR,
OpenTracking = :OT,
Shared = :SH,
LastUpdateSP = :LUSP,
LastUpdateDB = :LUDB,
LastUse = :LU,
HasPublished = :HP,
HasDraft = :HD,
Email = :EMAIL,
FromName = :FROM,
Subject = :SUBJ,
ReplyTo = :RT   
WHERE tmpID = :ID";
  }
  else {
    $query = "INSERT INTO MyTemplates (
tmpID,
name,
Owner,
LastEditBy,
Description,
BodyHTML,
BodyText,
BodyAMP,
SubVars,
Published,
ClickTracking,
Transactional,
OpenTracking,
Shared,
LastUpdateSP,
LastUpdateDB, 
LastUse,
HasPublished,
HasDraft,
Email,
FromName,
Subject,
ReplyTo)  
VALUES (:ID,:N,:O,:LE,:DE,:HTML,:TEXT,:AMP,:VARS,:PUB,:CL,:TR,:OT,:SH,:LUSP,:LUDB,:LU,:HP,:HD,:EMAIL,:FROM,:SUBJ,:RT)";

}


   $query_params = array(
              ':ID' => $tmpID,
              ':N' => $Name,
              ':O' => $Owner,
              ':LE' => $LastEditBy,
              ':DE' => $Description,
              ':HTML' => $BodyHTML,
              ':TEXT' => $BodyTEXT,
              ':AMP' => $BodyAMP,
              ':VARS' => $SubVars,
              ':PUB' => $Published,
              ':CL' => $ClickTracking,
              ':TR' => $Transactional,
              ':OT' => $OpenTracking,
              ':SH' => $Shared,
              ':LUSP' => $LastUpdateSP,
              ':LUDB' => $LastUpdateDB,
              ':LU' => $LastUse,
              ':HP' => $HasPublished,
              ':HD' => $HasDraft,
              ':EMAIL' => $Email,
              ':FROM' => $From_Name,
              ':SUBJ' => $Subject,
              ':RT' => $ReplyTo
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
            die("Failed to run query: " . $ex->getMessage());
        }



echo "made it here 1<br>";



    $BodyHTML = json_encode($BodyHTML);
    $BodyTEXT = json_encode($BodyTEXT);
    $BodyAMP = json_encode($BodyAMP);


    if ($OpenTracking < 1){$OpenTracking = "false";}else{$OpenTracking = "true";}
    if ($ClickTracking < 1){$ClickTracking = "false";}else{$ClickTracking = "true";}
    if ($Shared < 1){$Shared = "false";}else{$Shared = "true";}
    if ($Published < 1){$Published = "false";}else{$Published = "true";}
    if ($Transactional < 1){$Transactional = "false";}else{$Transactional = "true";}
    if ($HasPublished < 1){$HasPublished= "false";}else{$HasPublished = "true";}
    if ($HasDraft < 1){$HasDraft = "false";}else{$HasDraft = "true";}

  // If We are finished and publishing, save the final version to SP Saved Templates
  if ( $publish_it == true){
    echo "Publishing this version to SparkPost<br>";

    $json =  "{
      \"id\": \"$tmpID\",
      \"name\": \"$Name\",
      \"published\": true,
      \"description\": \"$Description\",
      \"shared_with_subaccounts\": $Shared,
      \"options\": {
        \"open_tracking\": $OpenTracking,
        \"click_tracking\": $ClickTracking
      },
      \"content\": {
        \"from\": {
          \"email\": \"$Email\",
          \"name\": \"$From_Name\"
       },
        \"subject\": \"$Subject\",
        \"reply_to\": \"$ReplyTo\",
        \"text\": $BodyTEXT,
        \"html\": $BodyHTML,
        \"amp\": $BodyAMP
        }
      }
    }
    ";

    // Check to see if the template exists in SparkPost already
    $url = "https://".$apidomain."/api/v1/templates/".$tmpID."";
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
    $response = json_decode(curl_exec($ch),1);
    curl_close($ch);


    if ($response['errors'][0]['message'] !="" ){

echo "Creating a new template here... <br>";

// FIXME... not exactly sure why this is returning a NULL and not creating a new template.

      // if it is not there, create it
      $url = "https://".$apidomain."/api/v1/templates";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      //curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: $apikey"
      ));
      $response = json_decode(curl_exec($ch),1);
      curl_close($ch);

var_dump($response);
echo "<br><br>";
var_dump($json);


exit;

    }
    else{

      // update exitsing template and publish it
      $url = "https://".$apidomain."/api/v1/templates/".$tmpID."?update_published=true";


      $ch = curl_init();
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $fp);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: $apikey"
      ));
      $response = json_decode(curl_exec($ch),1);
      curl_close($ch);

echo "Update Response:<br>"; 
 var_dump($response);
echo "<br>";
var_dump($json);



    }

echo "made it here 3<br>";

  }


echo "made it here 4<br>";
   
//exit;

echo '
<script>
  location.replace("./quill.php");
</script>
';


?>
