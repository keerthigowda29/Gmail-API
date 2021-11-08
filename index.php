<?php

require_once("./gmail_service.php");
$google_service = new GmailService();
$google_client = $google_service->get_client();
$login_button = '';


//This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
if(!isset($_SESSION['access_token']))
{
 $login_button = '<a class="btn btn-lg btn-google btn-block text-uppercase btn-outline" href="'.$google_client->createAuthUrl().'"><img src="https://img.icons8.com/color/16/000000/google-logo.png" /> Login with Google</a>';
}

?>
<!DOCTYPE html>
<html>
 <head>
    <meta charset='utf-8' />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>GMail App</title>
  <meta charset='utf-8' />
    <meta http-equiv='x-ua-compatible' content='ie=edge'/>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity= "sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script>
        $(function () {
                $(".selectModal").click(function () {
                    var sender = $(this).find(".sender").text();
                    var subject = $(this).find(".subject").text();
                    var date = $(this).find(".date").text();
                    var body = $(this).find(".body").text();
                    var p = "";
                    p += "<p id='a' name='sender' ><b>From :</b> "+ sender + " </p>";
                    p += "<p id='d' name='date' ><b>Received :</b> " + date + " </p>";
                    p += "<p id='c' name='subject'><b>Subject :</b> " + subject + "</p>";
                    p += "<p id='c' name='body'>" + body + "</p>";
                    $("#showModal").empty();
                    $("#showModal").append(p);
                });
            });
    </script>
</head>
 <body>
   <?php
   if($login_button == '')
   { 
    $inboxMessages = $google_service->getMails();
    $service = new Google_Service_Gmail($google_client);
    echo "
    <img src='https://img.icons8.com/color/144/000000/gmail--v1.png' />GMail Web Application<br>
    <h5 align='right'>Welcome ".$service->users->getProfile('me')->getEmailAddress()."</h5>
    <table style='cursor: pointer;' class='table table-dark table-striped table-hover' ><tbody>
    <thead><th colspan='4'>Inbox</th></thead>";
    foreach ($inboxMessages as $message){
    echo "<tr class='selectModal' data-toggle='modal' class='clickable text-left' data-target='#orderModal'>";
    echo "<td class='sender'>".$message['emailSender']."</td>";
    echo "<td class='subject'>".$message['emailSubject']."</td>";
    echo "<td class='date'>".$message['emailDate']."</td>";
    echo "<td class='body' style='display:none;'>".$message['emailBody']."</td>";
    echo "</tr>";
    }
    echo "</tbody></table>

    <div class='modal fade' id='orderModal' role='dialog'>
    <div class='modal-dialog modal-dialog-centered modal-xl'>
    <div class='modal-content'>
        <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
        <span aria-hidden='true'>×</span>
        </button>
        </div>

        <div class='modal-body'>
        <div class='showModal' id='showModal'></div>
        </div>

        <div class='modal-footer'>
        <button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button>
        </div>
    </div>
    </div>
    </div>
    ";
   }
   else
   {
    echo '<div class="card col d-flex justify-content-center mx-auto" style="width: 25rem;">
    <img class="card-img-top" src="https://img.icons8.com/color/256/000000/gmail--v1.png" alt="Card image cap">
    <div class="card-body">
        <h4 class="card-title">GMail</h4>
        <p class="card-text"><b>Secure, smart, and easy to use email</b></p>
        <div class ="card" align="center">'.$login_button.'</div>
    </div>
    </div';
   }
   ?>
 </body>
</html>