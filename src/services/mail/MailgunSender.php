<?php
declare(strict_types=1);

namespace App\Services\Mail;

use Mailgun\Mailgun;
use App\Kernel\Environment;

/**
 * @see https://github.com/mailgun/mailgun-php
 */
final class MailgunSender
{
  private $client;
  private $from;
  private $to;
  private $subject;
  private $text;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->client = Mailgun::create(Environment::var('MAILGUN_PRIVATE_API_KEY'));
    $this->domain = Environment::var('MAILGUN_DOMAIN');
  }

  /**
   * From
   *
   * @param string $v
   * @return void
   */
  public function setFrom(string $v = ''): void
  {
    $this->from = $v;
  }

  /**
   * To
   *
   * @param string $v
   * @return void
   */
  public function setTo(string $v = ''): void
  {
    $this->to = $v;
  }

  /**
   * Subject
   *
   * @param string $v
   * @return void
   */
  public function setSubject(string $v = ''): void
  {
    $this->subject = $v;
  }

  /**
   * Text
   *
   * @param string $v
   * @return void
   */
  public function setText(string $v = ''): void
  {
    $this->text = $v;
  }

  /**
   * Send message
   *
   * @return mixed
   */
  public function sendMessage(): mixed
  {
    return $this->client->messages->send(
      $this->domain, $this->data()
    );
  }

  /**
   * @return array
   */
  private function data(): array
  {
    return [
      'from'    => $this->from,
		  'to'      => $this->to,
		  'subject' => $this->subject,
		  'text'    => $this->text
    ];
  }
}