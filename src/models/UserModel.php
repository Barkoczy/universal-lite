<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
  protected $table = 'users';
  protected $fillable = [
    'status',
    'titleBefore',
    'titleAfter',
    'firstname',
    'lastname',
    'fullname',
    'email',
    'phone',
    'avatar',
    'picture',
    'background',
    'password',
    'resetExpire',
    'updatedAt',
    'createdAt'
  ];
}