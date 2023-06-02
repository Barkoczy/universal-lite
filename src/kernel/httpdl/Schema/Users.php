<?php
namespace App\Kernel\HttpDL\Schema;

use App\Models\UserModel as Model;
use App\Kernel\HttpDL\Enum\Attributes;
use App\Kernel\HttpDL\Enum\Direction;

class Users
{
  private $attributes;
  private $notWhereAttribute;
  private $notWhereValue;
  private $orderByAttribute;
  private $orderByDirection;
  private $skipCount = 0;
  private $takeCount = 1;

  /**
   * Find record by id
   *
   * @param string $id
   * @return array
   */
  public function find(string $id = ''): array
  {
    $data = Model::find($id);

    return $data ? $data->toArray() : [];
  }

  /**
   * Which attributes whant
   *
   * @param array $attributes
   * @return void
   */
  public function select(array $attributes= []): void
  {
    $this->attributes = $attributes;
  }

  /**
   * NotWhere
   *
   * @param string $attribute
   * @param string $value
   * @return void
   */
  public function notWhere(string $attribute = Attributes::_id, string $value = ''): void
  {
    $this->notWhereAttribute = $attribute;
    $this->notWhereValue = $value;
  }

  /**
   * Order
   *
   * @param string $attribute
   * @param string $direction
   * @return void
   */
  public function order(string $attribute = Attributes::createdAt, string $direction = Direction::desc): void
  {
    $this->orderByAttribute = $attribute;
    $this->orderByDirection = strtolower($direction);
  }

  /**
   * Skip
   *
   * @param integer $skipCount
   * @return void
   */
  public function skip(int $skipCount = 0): void
  {
    $this->skipCount = $skipCount;
  }

  /**
   * Take
   *
   * @param integer $takeCount
   * @return void
   */
  public function take(int $takeCount = 1): void
  {
    $this->takeCount = $takeCount;
  }

  /**
   * Data
   *
   * @return array
   */
  public function get(): array
  {
    // @model
    $model = Model::orderBy(
      $this->orderByAttribute, 
      $this->orderByDirection
    );

    // @hasNotWhere
    if ($this->hasNotWhere()) {
      $model->where(
        $this->notWhereAttribute,
        '!=',
        $this->notWhereValue
      );
    }

    // @skip
    $model->skip($this->skipCount);

    // @take
    $model->take($this->takeCount);

    // @array
    return $model->get($this->attributes)->toArray();
  }

  /**
   * Has NotWhere
   *
   * @return boolean
   */
  private function hasNotWhere(): bool
  {
    if (0 === strlen($this->notWhereAttribute))
      return false;
    if (0 === strlen($this->notWhereValue))
      return false;

    return true;
  }
}