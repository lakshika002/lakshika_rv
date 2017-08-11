<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthorModel extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table      = 'author';
    public    $timestamps = false;
    protected $fillable  = [ "id", 
                             "name"       
                           ];  
}
