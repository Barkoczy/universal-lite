<?php
declare(strict_types=1);

namespace App\Kernel\Language;

use Symfony\Component\Yaml\Yaml;
use App\Kernel\Environment;
use App\Kernel\Filesystem\Folder;
use App\Kernel\Exceptions\EmptyConfigFile;
use App\Kernel\Exceptions\MissingParameter;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;

final class Language
{
  const WEB_KEY = 'language.web';
  const CONSOLE_KEY = 'language.console';

  /** @var array */
  public $conf = [];

  /** @var string */
  public $webLangISO = '';

  /** @var string */
  public $consoleLangISO = '';

  /**
   * Constructor
   */
  public function __construct()
  {
    // @confPath
    $confPath = Folder::getLanguageConfPath().'/languages.yml';

    // @valid
    if (!is_file($confPath))
      throw new CannotReadFileFromFilesource('Cannot read kernel languages yaml configuration file due to insufficient permissions');

    // @conf
		$this->conf = Yaml::parseFile($confPath)??[];

    // @valid
    if (count($this->conf) === 0)
      throw new EmptyConfigFile('Empty language config file');

    // @setup
    $this->setup();
  }

  /**
   * Web Langauge
   *
   * @return array
   */
  public function getWeb(): array
  {
    // @index
    $index = $this->find($this->webLangISO, 'iso');

    // @data
    return $this->conf[$index];
  }

  /**
   * Console Language
   *
   * @return array
   */
  public function getConsole(): array
  {
    // @index
    $index = $this->find($this->consoleLangISO, 'iso');

    // @data
    return $this->conf[$index];
  }

  /**
   * Setup
   *
   * @return void
   */
  private function setup(): void
  {
    // @locale
    $locale = Environment::var('APP_LOCALE');

    // @valid
    if (strlen($locale) === 0)
      throw new MissingParameter('Missing language parameter in enviroment file (.env)');

    // @valid
    if (!$this->hasISO($locale))
      throw new MissingParameter('Missing language parameter in enviroment file (.env)');

    // @session
    if (!isset($_SESSION[self::WEB_KEY])) {
      $_SESSION[self::WEB_KEY] = $locale;
    }
    if (!isset($_SESSION[self::CONSOLE_KEY])) {
      $_SESSION[self::CONSOLE_KEY] = $locale;
    }

    // @webLangISO
    $this->webLangISO = $_SESSION[self::WEB_KEY];

    // @consoleLangISO
    $this->consoleLangISO = $_SESSION[self::CONSOLE_KEY];
  }

  /**
   * Exists ISO in configuration
   *
   * @param string $iso
   * @return boolean
   */
  private function hasISO(string $iso = ''): bool
  {
    return false !== $this->find($iso, 'iso');
  }

  /**
   * Find index
   *
   * @param string $val
   * @param string $key
   * @return mixed 
   */
  private function find(string $val = '', string $key = ''): mixed
  {
    return array_search($val, array_column($this->conf, $key));
  }
}