<?php


namespace Caishni\Fawry\Tests;

use Caishni\Fawry\Traits\HasCards;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasCards;

    protected $fillable = ['phone', 'email'];
    protected $table = 'users';
    public $timestamps = false;

}