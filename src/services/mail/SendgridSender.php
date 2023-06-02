<?php
declare(strict_types=1);

namespace App\Services\Mail;

use App\Kernel\Environment;

/**
 * @see https://github.com/sendgrid/sendgrid-php/blob/master/examples/helpers/mail/example.php#L22
 */
final class SendgridSender
{
  /** @var SendGrid **/
  private $sg; 

  /** @var Mail **/
  private $email;

  /**
   * Constructor
   */
  public function __construct() 
  {
    $this->sg = new \SendGrid(Environment::var('SENDGRID_API_KEY'));
    $this->email = new \SendGrid\Mail\Mail(null, null, null, null);
  }

  /**
   * From
   *
   * @param string $email
   * @param string $name
   * @return void
   */
  public function setFrom(string $email = '', string $name = ''): void
  {
    $this->email->setFrom($email, $name);
  }

  /**
   * To
   *
   * @param string $email
   * @param string $name
   * @return void
   */
  public function addTo(string $email = '', string $name = ''): void
  {
    $this->email->addTo($email, $name);
  }

  /**
   * Bcc
   *
   * @param string $email
   * @param string $name
   * @return void
   */
  public function addBcc(string $email = '', string $name = ''): void
  {
    $this->email->addBcc($email, $name);
  }

  /**
   * Subject
   *
   * @param string $subject
   * @return void
   */
  public function setSubject(string $subject = ''): void
  {
    $this->email->setSubject($subject);
  }
  
  /**
   * Custom arguments
   *
   * @param string $key
   * @param string $value
   * @return void
   */
  public function addCustomArg(string $key = '', string $value = ''): void
  {
    $this->email->addCustomArg($key, $value);
  }

  /**
   * TextBody
   *
   * @param string $body
   * @return void
   */
  public function textBody(string $body = ''): void
  {
    $this->email->addContent(new \SendGrid\Mail\Content("text/plain", $body)); 
  }

  /**
   * HtmlBody
   *
   * @param string $body
   * @return void
   */
  public function htmlBody(string $body = ''): void
  {
    $this->email->addContent(new \SendGrid\Mail\Content("text/html", $body));
  }

  /**
   * Attachment
   *
   * @param string $content
   * @param string $type
   * @param string $filename
   * @param string $contentId
   * @return void
   */
  public function addAttachment(
    string $content = '',
    string $type = '',
    string $filename = '',
    string $contentId = ''
  ): void {
    $attachment = new \SendGrid\Mail\Attachment();
    $attachment->setContent($content);
    $attachment->setType($type);
    $attachment->setFilename($filename);
    $attachment->setDisposition("attachment");
    $attachment->setContentId($contentId);
    $this->email->addAttachment($attachment);
  }

  /**
   * @return object
   */
  public function send(): object
  { 
    return $this->sg->send($this->email);
  }
}