<?php
namespace App\Kernel\Controllers;

use Symfony\Component\Yaml\Yaml;
use App\Kernel\Filesystem\Folder;
use App\Kernel\HttpDL\HttpDLClient;
use App\Kernel\Controllers\DataFunctions;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;

final class DataComponent
{
  const QUERY = 'query';

  /**
   * @var DataFunctions
   */
  private $f;

  /** @var array **/
  private $global = [];

  /** @var array **/
  private $schema = [];

  /** @var array **/
  private $data = [];

  /**
   * Constructor
   *
   * @param array $schema
   */
  public function __construct(array $schema = [])
  {
    // @filepath
    $filepath = Folder::getConfigPath().'/template.yml';

    // @validate
    if (!is_file($filepath))
      throw new CannotReadFileFromFilesource('Cannot read theme global vars configuration file due to insufficient permissions');

    // @global
		$this->global = Yaml::parseFile($filepath)??[];

    // @schema
    $this->schema = $schema;

    // @functions
    $this->f = new DataFunctions();
  }

  /**
   * Builder
   *
   * @return array
   */
  public function build(): array
  {
    // @global
    if (!$this->hasEmpty($this->global))
      $this->global();

    // @schema
    if (!$this->hasEmpty($this->schema))
      $this->schema();

    // @return
    return $this->data;
  }

  /**
   * Global
   *
   * @return void
   */
  private function global(): void
  {
    foreach ($this->global as $index => $v) { 
      if (is_string($v)) {
        $this->data[$index] = $this->val($v);
      }
      if (is_array($v)){
        if ($this->hasQuery($v)) {
          $this->data[$index] = (new HttpDLClient($v))->fetch();
        } else {
          $this->data[$index] = $this->recursive($v);
        }
      }
    }
  }

  /**
   * Schema
   *
   * @return void
   */
  public function schema(): void
  {
    if (count($this->schema) == count($this->schema, COUNT_RECURSIVE)) {
      foreach ($this->schema as $index => $v) {
        $this->schemaParamParser($index, $v);
      }
    } else {
      foreach ($this->schema as $line) {
        foreach ($line as $index => $v) {
          $this->schemaParamParser($index, $v);
        }
      }
    }
  }

  /**
   * Schema param parser
   * 
   * @param string $index
   * @param mixed $v
   * @return void
   */
  private function schemaParamParser(string $index = '', mixed $v = null): void 
  {
    if (is_string($v)) {
      $this->data[$index] = $this->val($v);
    }
    if (is_array($v)) {
      if ($this->hasQuery($v)) {
        $this->data[$index] = (new HttpDLClient($v))->fetch();
      } else {
        $this->data[$index] = $v;
      }
    }
  }

  /**
   * Value or Function
   *
   * @param string $v
   * @return mixed
   */
  private function val(string $v = ''): mixed
  {
    return $this->f->has($v) ? $this->f->get($v) : trim($v);
  }

  /**
   * Recursive Array
   *
   * @param array $data
   * @return array
   */
  private function recursive(array $data = []): array
  {
    array_walk_recursive(
      $data,
      function (&$v) {
        $v = is_string($v) ? $this->val($v) : $v;
      }
    );
    return $data;
  }

  /**
   * Has query HttpDL schema
   *
   * @param array $v
   * @return boolean
   */
  private function hasQuery(array $v = []): bool
  {
    return self::QUERY === array_key_first($v);
  }

  /**
   * Empty Array
   *
   * @param array $arr
   * @return bool
   */
  private function hasEmpty(array $arr = []): bool
  {
    return !is_array($arr) || 0 === count($arr);
  }
}