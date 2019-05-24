<?php 
//Seller.php

namespace App;

//Class which implements Illuminate\Contracts\Auth\Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
	protected $table = 'admin';
  //Mass assignable attributes
  protected $fillable = [
      'full_name', 'email', 'password',
  ];

  //hidden attributes
   protected $hidden = [
       'password', 'remember_token',
   ];

}