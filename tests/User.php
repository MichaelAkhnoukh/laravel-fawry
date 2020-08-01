<?php


namespace Caishni\Fawry\Tests;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['phone', 'email'];
    protected $table = 'users';
    public $timestamps = false;

}