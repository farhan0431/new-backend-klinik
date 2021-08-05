<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\Dokter;
use App\JadwalDokter;
use App\KartuBerobat;
use App\Identitas;
use App\User;
use App\Chat;
use App\Notifikasi;
use Carbon\Carbon;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;



class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {

        $index = 0;

        $data = Chat::where('id_pengirim',$request->user()->id)->orWhere('id_penerima',$request->user()->id)->where('aktif',1)->orderBy('created_at','DESC')->get()->groupBy('token_chat')->toArray();

        return response()->json(['status' => 'success', 'data' => array_values($data)]);

    }

    public function getChat(Request $request)
    {
        $check = Chat::where('id_pengirim', $request->user()->id)->where('id_penerima',$request->id_penerima)->where('aktif',1);
        $data;
        if($check->count() > 0)
        {
            $chat = Chat::where('token_chat',$check->first()->token_chat);
            $data = $chat->orderBy('created_at')->get();

            $chat->update([
                'status' => 2
            ]);


        }else{
            $data = [];
        }

        return response()->json(['status' => 'success','data'=>$data]);
        
    }

    public function chatDokter(Request $request)
    {
        $check = Chat::where('token_chat',$request->token)->orderBy('created_at')->get();
        // $data;
        // if($check->count() > 0)
        // {
        //     $data = Chat::where('token_chat',$check->first()->token_chat)->orderBy('created_at')->get();
        // }else{
        //     $data = [];
        // }

        return response()->json(['status' => 'success','data'=>$check]);
        
    }


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            // 'id_pengirim' => 'required',
            'id_penerima' => 'required',
            'isi' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        Chat::unguard();

        $check = Chat::where('id_pengirim', $request->user()->id)->where('id_penerima',$request->id_penerima)->where('aktif',1);

        $token;
        if($check->count() > 0) {
            $token = $check->first()->token_chat;
        }else{
            $token = $this->quickRandom(15);
        }

        $store = Chat::create([
            'id_pengirim' => $request->user()->id,
            'id_penerima' => $request->id_penerima,
            'isi' => $request->isi,
            'token_chat' => $token,
        ]);

        // $notif = Notifikasi::create([
        //     'id_user' => $request->id_pasien,
        //     'title' => 'Kartu Pengobatan',
        //     'desc' => 'Kartu obat anda telah dibuat',
        //     'id_kartu_obat' => $store->id
        // ]);

        // $pasien = User::find($request->id_pasien);

        // $dataSend = [
        //     'id' => $store->id
        // ];

        $token_penerima = User::where('id',$request->id_penerima)->first()->token;
        

        // OneSignal::sendNotificationToUser(
        //     "Ada chat masuk!", 
        //     $token_penerima,
        //     $url = null, 
        //     // $data = $dataSend, 
        //     $buttons = null, 
        //     $schedule = null
        // );

        $this->sendMessage('Pesan Masuk','Ada Pesan Masuk!', $token_penerima);


        return response()->json([
            // 'data' => $pasien,
            'status' => 'success',
            'data' => $token_penerima
            // 'data' => $store,
            // 'antrian' => $cekAntrian->nomor_antrian + 1,
            
        ],200);

    }

    public function storeDokter(Request $request)
    {
        $validate = Validator::make($request->all(), [
            // 'id_pengirim' => 'required',
            'id_penerima' => 'required',
            'isi' => 'required',
            'token' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        Chat::unguard();

        // $check = Chat::where('id_pengirim', $request->user()->id)->where('id_penerima',$request->id_penerima)->where('aktif',1);

        // $token;
        // if($check->count() > 0) {
        //     $token = $check->first()->token_chat;
        // }else{
        //     $token = $this->quickRandom(15);
        // }

        $store = Chat::create([
            'id_pengirim' => $request->user()->id,
            'id_penerima' => $request->id_penerima,
            'isi' => $request->isi,
            'token_chat' => $request->token,
        ]);
        // 1 : Data Janji Dokter | 2: Page Kartu Berobat | 3: Chat

        $notif = Notifikasi::create([
            'id_user' => $request->id_pasien,
            'title' => 'Kartu Pengobatan',
            'desc' => 'Kartu obat anda telah dibuat',
            'tambahan' => $request->token,
            'page' => 3
        ]);

        // $pasien = User::find($request->id_pasien);

        // $dataSend = [
        //     'id' => $store->id
        // ];

        // OneSignal::sendNotificationToUser(
        //     "Kartu berobat anda sudah tersedia!", 
        //     $pasien->token,
        //     $url = null, 
        //     $data = $dataSend, 
        //     $buttons = null, 
        //     $schedule = null
        // );

        $token_penerima = User::where('id',$request->id_penerima)->first()->token;
        

        // OneSignal::sendNotificationToUser(
        //     "Ada chat masuk!", 
        //     $token_penerima,
        //     $url = null, 
        //     // $data = $dataSend, 
        //     $buttons = null, 
        //     $schedule = null
        // );

        $this->sendMessage('Pesan Masuk','Ada Pesan Masuk!', $token_penerima);

        return response()->json([
            // 'data' => $pasien,
            'status' => 'success',
            'data' => $store
            // 'data' => $store,
            // 'antrian' => $cekAntrian->nomor_antrian + 1,
            
        ],200);

    }

    public static function quickRandom($length)
{
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

public function sendMessage($title,$desc,$token) {
    $content      = array(
        "en" => $desc
    );
    $heading = array(
        "en" => $title
    );
    $hashes_array = array();
    array_push($hashes_array, array(
        "id" => "like-button",
        "text" => "Like",
        "icon" => "http://i.imgur.com/N8SN8ZS.png",
        "url" => "https://yoursite.com"
    ));
    array_push($hashes_array, array(
        "id" => "like-button-2",
        "text" => "Like2",
        "icon" => "http://i.imgur.com/N8SN8ZS.png",
        "url" => "https://yoursite.com"
    ));
    $fields = array(
        'app_id' => "eb95097e-825b-44be-bc8a-6a85e2dfb290",
        'include_player_ids' => array(
            $token
        ),
        // 'data' => array(
        //     "foo" => "bar"
        // ),
        'contents' => $content,
        'headings' => array(
            "en" => 'asdasd'
        ),
        // 'web_buttons' => $hashes_array
    );
    
    $fields = json_encode($fields);
    // print("\nJSON sent:\n");
    // print($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic YWJjNWNmYTItOGE2Ni00ZjUyLWJjYzAtMjEwODkxZTExMThk'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}
    //
}
