<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catatan extends Model
{
    //HasFactory untuk menambahkan dummy data, gak kepake sekarang tapi biasanya kepake 
    use HasFactory;

    protected $table = 'catatan';

    protected $fillable = [
        'judul',
        'isi',
        'user_id'
    ];

     public function user()
    {
        // Model Catatan ini "milik" (belongsTo) satu User
        return $this->belongsTo(User::class);
    }
}