<HTML>   
 <HEAD>  
  <TITLE>Contact Cycle Atlanta</TITLE> 
  <link rel="stylesheet" href="css/style.css" /> 
 </HEAD>   
 <!-- ------------------------------------------------------------- -->  
 <BODY>  
  <div id="content"> 
   <h3>Cycle Atlanta</h3> 
<?php

$to      = "info@cycleatlanta.org";
$subject = "Cycle Atlanta App Inquiry";
$message = "Please follow-up with me about Cycle Atlanta.";
$email   = $_POST["email"];

function is_valid_email($email) {
  return preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $email);
}

function contains_bad_str($str_to_test) {
  $bad_strings = array(
                "content-type:"
                ,"mime-version:"
                ,"multipart/mixed"
		,"Content-Transfer-Encoding:"
                ,"bcc:"
		,"cc:"
		,"to:"
  );
  
  foreach($bad_strings as $bad_string) {
    if(eregi($bad_string, strtolower($str_to_test))) {
      echo "$bad_string found. Suspected injection attempt - mail not being sent.";
      exit;
    }
  }
}

function contains_newlines($str_to_test) {
   if(preg_match("/(%0A|%0D|\\n+|\\r+)/i", $str_to_test) != 0) {
     echo "newline found in $str_to_test. Suspected injection attempt - mail not being sent.";
     exit;
   }
} 

if($_SERVER['REQUEST_METHOD'] != "POST") {
   echo("Unauthorized attempt to access page.");
   exit;
}

$goodReferer = "cycleatlanta.org";
$theReferer  = $_SERVER['HTTP_REFERER'];
$pos = strpos($theReferer, $goodReferer);

if ($pos === false) {
   echo("Unauthorized attempt to access page from bad referer.");
   exit;
}

contains_bad_str($email);
contains_bad_str($message);
contains_newlines($email);


if (!is_valid_email($email)) {
  echo 'Sorry, invalid email. Please try again.';
} else {

  $headers = "From: $email";
  if (mail($to, $subject, $message, $headers)) {
    echo "<p>Thanks for your interest in Cycle Atlanta. We'll be in touch soon!</p>";
  } else {
    echo("<p>Sorry. Message delivery failed. Please try again.</p>");
  }
  
}
?>
  </div>   
 </BODY> 
</HTML> 




