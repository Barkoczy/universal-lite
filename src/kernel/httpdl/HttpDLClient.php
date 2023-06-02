<?php
namespace App\Kernel\HttpDL;

use Symfony\Component\Yaml\Yaml;
use App\Kernel\HttpDL\Enum\Direction;
use App\Kernel\Filesystem\Folder;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;

class HttpDLClient
{
  const MIN = 1;
  const MAX = 500;
  const QUERY = 'query';
  const COLUMN = 'name';

  private $q;
  private $conf;

  /**
   * Query
   *
   * @param array $q
   */
  public function __construct(array $q = [])
  {
    // @validate
    if (!is_file($this->filepath()))
      throw new CannotReadFileFromFilesource('Cannot read configuration file due to insufficient permissions');

    // @conf
		$this->conf = Yaml::parseFile($this->filepath());

    // @query
    if (isset($q[self::QUERY])) {
      $this->q = $q[self::QUERY];
    }
  }

  /**
   * Database Query Builder
   *
   * @return array
   */
  public function fetch(): array
  {
    // @from(Schema)
    $schema = $this->schema();

    // @validate
    if(null === $schema)
      return [];

    // @find
    if ($this->validFind())
      return $schema->find($this->q['find']);

    // @select
    if ($this->validSelect()) {
      $schema->select($this->q['select']);
    }

    // @notWhere
    if ($this->validNotWhere()) {
      $schema->notWhere(
        $this->q['notWhere']['attribute'],
        $this->q['notWhere']['value']
      );
    }

    // @order
    if ($this->validOrder()) {
      $schema->order(
        $this->q['order']['attribute'],
        $this->q['order']['direction']
      );
    }

    // @skip
    if ($this->validSkip()) {
      $schema->skip($this->q['skip']);
    }

    // @take
    if ($this->validTake()) {
      $schema->take($this->q['take']);
    }

    // @array
    return $schema->get();
  }

  /**
   * Schema
   *
   * @return mixed
   */
  private function schema(): mixed
  {
    // @index
    $index = array_search(
      $this->q['from'],
      array_column($this->conf, self::COLUMN)
    );
      
    // @validate
    if (false === $index)
      return null;

    // @schema
    $schema = $this->conf[$index]['path'];

    // @return
    return new $schema();
  }

  /**
   * Verify find
   *
   * @return void
   */
  private function validFind()
  {
    if (!isset($this->q['find']))
      return false;
    if (0 === strlen($this->q['find']))
      return false;

    return true;
  }

  /**
   * Verify select
   *
   * @return boolean
   */
  private function validSelect(): bool
  {
    return isset($this->q['select']) && is_array($this->q['select']) && 0 < count($this->q['select']);
  }

  /**
   * Verify order
   *
   * @return boolean
   */
  private function validNotWhere(): bool
  {
    if (!isset($this->q['notWhere']['attribute']))
      return false;
    if (!isset($this->q['notWhere']['value']))
      return false;
    if (0 === strlen($this->q['notWhere']['attribute']))
      return false;
    if (0 === strlen($this->q['notWhere']['value']))
      return false;

    return true;
  }

  /**
   * Verify order
   *
   * @return boolean
   */
  private function validOrder(): bool
  {
    if (!isset($this->q['order']['attribute']))
      return false;
    if (!isset($this->q['order']['direction']))
      return false;
    if (0 === strlen($this->q['order']['attribute']))
      return false;
    if (0 === strlen($this->q['order']['direction']))
      return false;

    return $this->validDirection();
  }

  /**
   * Verify direction (asc|desc)
   *
   * @return boolean
   */
  private function validDirection(): bool
  {
    if (Direction::asc === $this->q['order']['direction'])
      return true;
    if (Direction::desc === $this->q['order']['direction'])
      return true;

    return false;
  }

  /**
   * Verify skip
   *
   * @return boolean
   */
  private function validSkip(): bool
  {
    return isset($this->q['skip']) && is_int($this->q['skip']) && self::MIN < $this->q['skip'] && self::MAX > $this->q['skip'];
  }

  /**
   * Verify take
   *
   * @return boolean
   */
  private function validTake(): bool
  {
    return isset($this->q['take']) && is_int($this->q['take']) && self::MIN < $this->q['take'] && self::MAX > $this->q['take'];
  }

  /**
   * Return config file path
   *
   * @return string
   */
  private function filepath(): string
  {
    return Folder::getHttpDLConfPath().'/schema.yml';
  }
}