<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'active', 'email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    static public function sendInfoWhatsapp()
    {
        $users = User::where('active', 1)->where('phone', '!=', null)->get();

        if (!empty($users)) {

            $news = News::where('send', null)->get();

            if (!empty($news)) {

                foreach ($users as $user) {
                    self::sendCurl($user->phone, $news);
                }
            }
        }
    }

    static public function sendCurl($phone, $news)
    {
        $body = [];

        foreach ($news as $key => $value) {

            $description = self::extract_string($value->description, '<img', '>');
            $description = self::extract_string($description, '<br /');

            $body[$key]['receiver'] = $phone;
            $body[$key]['message'] = mb_strimwidth($value->title, 0, 40, '...') . ' ' . mb_strimwidth($description, 0, 250, '...') . ' ' . $value->link;
        }

        //dd(json_encode($body));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => '127.0.0.1:8000/chats/send-bulk?id=markus',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo json_encode($response);
    }

    public function extract_string($string, $param_init, $param_finish = null)
    {
        if (str_contains($string, $param_init)) {

            $string_desc = explode($param_init, $string);
            $position_initial_cut = $string_desc[0];

            if(!empty($param_finish)){

                $string_desc = explode($param_finish, $string);
                $position_finish_cut = $string_desc[1];
                return $position_initial_cut . '' . $position_finish_cut;

            }else{

                return $position_initial_cut;

            }

        } else {
            return $string;
        }
    }
}
