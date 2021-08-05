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
use App\Notifikasi;
use App\Janji;
use Carbon\Carbon;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;
use App\Resep;



class DokterController extends Controller
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

    public function index()
    {
        


        $dokter = Dokter::orderBy('nama','DESC')->when(request()->q, function($query) {
            $query->where('nama','LIKE','%'.request()->q.'%');
        })
        ->get();
        return response()->json(['status' => 'success', 'data' => $dokter]);

    }

    public function getJanjiDokter(Request $request)
    {
        $id_dokter = $request->user()['data_dokter']['id'];


        $data = Janji::with('data_pasien')->where('id_dokter',$id_dokter)->orderBy('created_at','DESC')->get();

        return response()->json(['status' => 'success', 'data' => $data]);

    }

    public function jadwal($id)
    {
        // $date = Carbon::now()->addDays(1)->format('Y M D');

        $tanggal = [];

        for($i = 1; $i <= 3;$i++)
        {
            $date = Carbon::now()->addDays($i);

            $full = [
                'full_date' => $date->format('Y-m-d'),
                'hari' => $date->format('D'),
                'tanggal' => $date->format('d'),
                'bulan' => $date->format('M')
            ];


            array_push($tanggal,$full);
        }
        

        $jadwal =  JadwalDokter::where('id_dokter',$id)->get();
        return response()->json(['status' => 'success', 'data' => $jadwal, 'tanggal' => $tanggal]);
    }

    public function getKartu(Request $request)
    {
        $data = KartuBerobat::with('resep')->find($request->id);
        return response()->json([
            'data' => $data,
            'status' => 'success'
        ],200);
    }

    public function storeKartuBerobat(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id_pasien' => 'required',
            // 'id_dokter' => 'required',
            // 'tanggal_janji' => 'required',
            'nomor_antrian' => 'required',
            'id_janji' => 'required',
            'diagnosa' => 'required',
            'keadaan_umum' => 'required',
            'occlusi' => 'required',
            'kebersihan_mulut' => 'required',
            'perawatan' => 'required',
            'biaya' => 'required',
            'catatan' => 'required',
            'informasi' => 'required',
            'resep' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        KartuBerobat::unguard();

        $no_kartu = KartuBerobat::orderBy('no_kartu','DESC')->first();

        $no_kartu = $no_kartu != null ? $no_kartu->no_kartu + 1 : 1;

    


        $store = KartuBerobat::create([
            'id_pasien' => $request->id_pasien,
            'no_kartu' => $no_kartu,
            'id_janji' => $request->id_janji,
            // 'id_dokter' => $request->,
            // 'tanggal_janji' => $request->,
            'nomor_antrian' => $request->nomor_antrian,
            'diagnosa' => $request->diagnosa,
            'keadaan_umum' => $request->keadaan_umum,
            'occlusi' => $request->occlusi,
            'kebersihan_mulut' => $request->kebersihan_mulut,
            'perawatan' => $request->perawatan,
            'biaya' => $request->biaya,
            'catatan' => $request->catatan,
            'id_dokter' => $request->user()->id
        ]);

        // 1 : Data Janji Dokter | 2: Page Kartu Berobat | 3: Chat

        $notif = Notifikasi::create([
            'id_user' => $request->id_pasien,
            'title' => 'Kartu Pengobatan',
            'desc' => 'Kartu obat anda telah dibuat',
            'tambahan' => $store->id,
            'page' => 2
        ]);

        $pasien = User::find($request->id_pasien);

        $dataSend = [
            'id' => $store->id
        ];

        // OneSignal::sendNotificationToUser(
        //     "Kartu berobat anda sudah tersedia!", 
        //     $pasien->token,
        //     $url = null, 
        //     $data = $dataSend, 
        //     $buttons = null, 
        //     $schedule = null
        // );

        $this->sendMessage('Klinik Ku App',"Kartu berobat anda sudah tersedia!",$pasien->token);


        Resep::create([
            'id_dokter' => $request->user()->id,
            'id_pasien' => $request->id_pasien,
            'id_janji' => $request->id_janji,
            'informasi' => $request->informasi,
            'resep' => $request->resep
        ]);

        Janji::where('id',$request->id_janji)->first()->update([
            'status' => 2
        ]);

        return response()->json([
            // 'data' => $pasien,
            'status' => 'success',
            // 'data' => $store,
            // 'antrian' => $cekAntrian->nomor_antrian + 1,
            
        ],200);

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
                "en" => $title
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
