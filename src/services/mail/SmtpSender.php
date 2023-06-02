<?php
declare(strict_types=1);

namespace App\Services\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use App\Kernel\Environment;

final class SmtpSender
{
  /** @var PHPMailer **/
	private $mailer;
  
  /** @var bool **/
  protected $isHTML = false;
  
  /** @var string **/
  protected $subject = '';
  
  /** @var string **/
  protected $body = '';

  /** @var string **/
  protected $fromEmail = '';
  
  /** @var string **/
  protected $fromAlias = '';
  
  /** @var string **/
  protected $toEmail = '';
  
  /** @var string **/
  protected $toAlias = '';
  
  /** @var string **/
  protected $replyEmail = '';
  
  /** @var string **/
  protected $replyAlias = '';
  
  /** @var array **/
  protected $bcc = [];
  
  /** @var array **/
  protected $attachments = [];

  /**
   * Constructor
   */
  public function __construct() 
  {
    // @instance
    $this->mailer = new PHPMailer();
    
    // @charset
    $this->mailer->CharSet = Environment::var('SMTP_CHARSET');
    
    // @smtp
    $this->mailer->isSMTP();
    $this->mailer->SMTPAuth   = "false" === Environment::var('SMTP_AUTH') ? false : true;
    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         
    $this->mailer->Port       = Environment::var('SMTP_PORT');
    
    // @settings
    $this->mailer->Host     = Environment::var('SMTP_HOST');                       
    $this->mailer->Username = Environment::var('SMTP_USERNAME');                     
    $this->mailer->Password = Environment::var('SMTP_PASSWORD');
  }
  
  /**
   * @param string $subject
   * @return void
   */
  public function setSubject(string $subject = ''): void
  {
    $this->subject = $subject;
  }
  
  /**
   * @param string $email
   * @param string $alias
   * @return void
   */
  public function setFrom(string $email = '', string $alias = ''): void
  {
    $this->fromEmail = $email;
    $this->fromAlias = $alias;
  }
  
  /**
   * @param string $email
   * @param string $alias
   * @return void
   */
  public function addTo(string $email = '', string $alias = ''): void
  {
    $this->toEmail = $email;
    $this->toAlias = $alias;
  }
  
  /**
   * @param string $email
   * @return void
   */
  public function addBcc(string $email = ''): void
  {
    $this->bcc[] = $email;
  }
  
  /**
   * @param string $email
   * @param string $alias
   * @return void
   */
  public function addReplyTo(string $email = '', string $alias = ''): void
  {
    $this->replyEmail = $email;
    $this->replyAlias = $alias;
  }
  
  /**
   * @param string $path
   * @param string $filename
   * @return void
   */
  public function addAttachment(string $path = '', string $filename = ''): void
  {
    // @attachment
    $attachment = new \stdClass();
    $attachment->path = $path;
    $attachment->filename = $filename;
    
    // @push
    $this->attachments[] = $attachment;
  }
  
  /**
   * @param bool $state
   * @return void
   */
  public function isHTML(bool $state = false): void
  {
    $this->isHTML = $state;
  }
  
  /**
   * @param string $body
   * @return void
   */
  public function body(string $body = ''): void
  {
    $this->body = $body;
  }
  
  /**
   * @return bool
   */
  public function send(): bool
  {
    // @from
    $this->mailer->setFrom($this->fromEmail, $this->fromAlias);
    
    // @to
    $this->mailer->addAddress($this->toEmail, $this->toAlias);

    // @reply
    $this->mailer->addReplyTo($this->replyEmail, $this->replyAlias);

    // @bcc
    if (is_array($this->bcc) && 0 !== count($this->bcc)) {
      foreach ($this->bcc as $email){
        $this->mailer->addBCC($email);
      }
    }

    // @attachments
    if (is_array($this->attachments) && 0 !== count($this->attachments)) {
      foreach ($this->attachments as $attachment){
        $this->mailer->addAttachment($attachment->path, $attachment->filename);
      }
    }
    
    // @html
    if ($this->isHTML) {
      $this->mailer->isHTML(true);
    }

    // @subject         
    $this->mailer->Subject = $this->subject;
    
    // @body
    $this->mailer->Body = $this->body;

    // @return
    return $this->mailer->send();
  }
}