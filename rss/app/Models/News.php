<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'link', 'title', 'description', 'send'
    ];

    static public function setDataBaseInfo($data)
    {
        foreach ($data as $info) {

            $new = News::where('link', $info->link)->where('title', $info->title)->first();

            if (empty($new)) {

                News::create([
                    'link' => $info->link,
                    'title' => $info->title,
                    'description' => $info->description,
                ]);
            }

        }
    }
}
