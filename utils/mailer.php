<?php

require_once "verbose_errors.php";
require_once "console_log.php";
// Include PEAR and the Mail package
require_once "PEAR.php";
require_once "Mail.php";

class Mailer {

    /**
     * This class is an abstraction to simplify sending emails from anywhere
     * As you can see, the mailer instance is created in the file, so the intended usage is:
     * 
     * require_once "utils/mailer.php"
     * $mailer->sendEmail($recipient, $subject, $body)
     */

    private $sender    = "Auction Website <uclauctionsite2024g27@gmail.com>";
    private $username = "uclauctionsite2024g27@gmail.com";
    private $password = "fbat vjrj ouqj ykcr";
    private $server   = "smtp.gmail.com";
    private $port     = "587";
    private $smtp;

    public function __construct() {
        $this->smtp = Mail::factory("smtp",
        array(
            "host"     => $this->server,
            "username" => $this->username,
            "password" => $this->password,
            "auth"     => true,
            "port"     => $this->port
        )
        );
    }

    /**
     * Sends an unformatted Email to the intended recipient
     * 
     * @param string $recipient the email of the person
     * @param string $subject
     * @param string $body
     * 
     * @return void
     */
    function sendEmail(string $recipient, string $subject, string $body) {
        $headers = array(
            "From"    => $this->sender,
            "To"      => $recipient,
            "Subject" => $subject
        );
        $mail = $this->smtp->send($recipient, $headers, $body);
 
        if (PEAR::isError($mail)) {
            console_log($mail->getMessage());
        } else {
            console_log("Message successfully sent!");
        }
    }

}

$mailer = new Mailer();