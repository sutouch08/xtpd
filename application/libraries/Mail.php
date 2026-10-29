<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APPPATH . 'third_party/PHPMailer/src/Exception.php';
require_once APPPATH . 'third_party/PHPMailer/src/PHPMailer.php';
require_once APPPATH . 'third_party/PHPMailer/src/SMTP.php';

class Mail
{
  public $ci;
  public $host;
  public $port = 587;
  public $from;
  public $to;
  public $token;

  public function __construct()
  {
    $this->ci =& get_instance();
    
    $this->host = getConfig('SMTP_SERVER');
    $this->from = getConfig('SMTP_EMAIL');
    $this->token = getConfig('SMTP_PASSWORD');
    $this->port = getConfig('SMTP_PORT');    
  }

  public function send($to, $subject, $message, $attachments = [], $cc = [])
  {
    $mail = new PHPMailer(true);

    try
    {
      // SMTP config
      $mail->isSMTP();
      $mail->Host       = $this->host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $this->from;
      $mail->Password   = $this->token;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = $this->port ? $this->port : 587;

      // for thai language
      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';
      $mail->setLanguage('en');

      // Sender & Recipient
      $mail->setFrom($this->from, 'TPD System');
      $mail->addAddress($to);
      if (!empty($cc))
      {
        foreach ($cc as $address)
        {
          $mail->addCC($address);
        }
      }

      // Email content
      $mail->Subject = $subject;
      $mail->Body    = $message;
      $mail->isHTML(true);

      // Attach files
      if (!empty($attachments))
      {
        foreach ($attachments as $file)
        {
          $mail->addAttachment($file);
        }
      }

      return $mail->send();
    }
    catch (Exception $e)
    {
      log_message('error', 'Mail Error: ' . $mail->ErrorInfo);
      return false;
    }
  }
}
