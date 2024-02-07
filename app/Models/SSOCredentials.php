<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SSOCredentials extends Model
{
    use HasFactory;
    public $table = 'sso_credentials';

    public function metadata() {
        return $this->belongsTo(\App\Models\SSOProvider::class,'provider','provider');
    }
}
