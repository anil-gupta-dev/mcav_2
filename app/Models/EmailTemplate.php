<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EmailTemplate extends Model
{
     use SoftDeletes;

    public $table = 'email_templates';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'template_name',
        'subject',
        'message'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'template_name' => 'string',
        'subject' => 'string',
        'message' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

}
