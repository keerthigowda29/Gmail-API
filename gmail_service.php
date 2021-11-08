<?php

class GmailService{
    private $google_client;
    public function __construct(){
        require_once 'vendor/autoload.php';
        $this->init();
    }
    public function get_client() {
        return $this->google_client;
    }
    private function init(){
    $this->google_client = new Google_Client();
    $this->google_client->setApplicationName('Gmail Application');
    $this->google_client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    $this->google_client->setAuthConfig('credentials.json');
    $this->google_client->setAccessType('offline');
    $this->google_client->setPrompt('select_account consent');
    $this->google_client->addScope('email');
    
    $this->google_client->addScope('profile');
    
    session_start();

    if(isset($_SESSION['access_token'])){
        $accessToken = $_SESSION['access_token'];
        $this->google_client->setAccessToken($accessToken);
    }
    
    if ($this->google_client->getRefreshToken()) {
        $accessToken = $this->google_client->fetchAccessTokenWithRefreshToken($this->google_client->getRefreshToken());
        $this->google_client->setAccessToken($accessToken);
        $_SESSION['access_token'] = $accessToken;  
        }
    elseif (isset($_GET['code'])) {
        $authCode = $_GET['code'];
        $accessToken = $this->google_client->fetchAccessTokenWithAuthCode($authCode);
        $this->google_client->setAccessToken($accessToken);
        $_SESSION['access_token'] = $accessToken;
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }
    }
    }
    public function decodeBody($body) {
        $rawData = $body;
        $sanitizedData = strtr($rawData,'-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);
        if(!$decodedMessage){
            $decodedMessage = FALSE;
        }
        return $decodedMessage;
    }
    public function getMails() : array
    {
        $service = new Google_Service_Gmail($this->google_client);

        $results = $service->users_messages->listUsersMessages('me',['maxResults' => 10, 'q' => 'category:primary']);

        $messageList = $results->getMessages();
        $inboxMessage = [];

        foreach($messageList as $message){

            $single_message = $service->users_messages->get('me',$message->id, ['format' => 'full']);

            $headers = $single_message->getPayload()->getHeaders();
            $email_body = $this->decodeBody($single_message->getPayload()->getBody()['data']);
            $payload = $single_message->getPayload();
            if(!$email_body) {
                $parts = $payload->getParts();
                foreach ($parts  as $part) {
                    if($part['body'] && $part['mimeType'] == 'text/plain') {
                        $FOUND_BODY = $this->decodeBody($part['body']->data);
                        break;
                    }
                }
            }   
            if(!$email_body) {
                foreach ($parts  as $part) {
                    if($part['parts'] && !$email_body) {
                        foreach ($part['parts'] as $p) {
                            if($p['mimeType'] === 'text/plain' && $p['body']) {
                                $email_body = $this->decodeBody($p['body']->data);
                                break;
                            }
                        }
                    }
                    if($email_body) {
                        break;
                    }
                }
            }
            foreach($headers as $header) {

                if ($header->getName() == 'Subject') {
                    $email_subject = $header->getValue();
                }
                else if ($header->getName() == 'Date') {
                    $email_date = $header->getValue();
                    $email_date = date('M jS Y h:i A', strtotime($email_date));
                }
                else if ($header->getName() == 'From') {
                    $email_sender = $header->getValue();
                    $email_sender = str_replace('"', '', $email_sender);
                }
            }
            $inboxMessages[] = [
                'emailBody' => $email_body,
                'emailSubject' => $email_subject,
                'emailDate' => $email_date,
                'emailSender' => $email_sender,
            ];
        }
        return $inboxMessages;
    }
    public function logout(){
        $this->google_client->revokeToken();
        session_destroy();
        header('location:index.php');
    }
}
?>