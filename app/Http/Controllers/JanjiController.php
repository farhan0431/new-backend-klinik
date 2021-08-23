<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\Janji;
// use App\Data;
use App\User;
use App\Dokter;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;
use App\Notifikasi;
use App\Resep;
use App\Berita;
use App\Slider;
use App\BuktiTransfer;
// namespace App\Events;
use App\KartuBerobat;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\File;
use App\Penilaian;
// use App\Events\SendNotif;

use Carbon\Carbon;

class JanjiController extends Controller
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

        $janji = Janji::with('data_dokter','resep','kartu_berobat')->where('id_pasien',$request->user()->id)->where('status',3)->orWhere('status',4)->orderBy('created_at','DESC')->get();


        
        // $laporan;
        // // event(new ExampleEvent(true));
        // if(4 == 5)
        // {
        //     $laporan = Laporan::orderBy('created_at','DESC')->when(request()->q, function($query) {
        //         $query->where('no_identitas','LIKE','%'.request()->q.'%');
        //     })
        //     ->paginate(10);
        // }else{
        //     $laporan = Laporan::orderBy('created_at','DESC')->when(request()->q, function($query) {
        //         $query->where('no_identitas','LIKE','%'.request()->q.'%');
        //     })
        //     ->paginate(10);
        // }
        return response()->json(['status' => 'success', 'data' => $janji]);

    }

    public function activeJanji(Request $request)
    {
        $janji =  Janji::with('data_dokter')->where('id_pasien',$request->user()->id)->where('status',0)->orWhere('status',1)->orWhere('status',2)->orderBy('created_at','DESC')->first();

        return response()->json(['status' => 'success', 'data' => $janji,'berita' => $this->berita(),'slider' => $this->sliderImage()]);

    }

    public function sliderImage()
    {
        return Slider::take(3)->get();
    }

    public function batalJanji(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }
        
        $janji = Janji::find($request->id);
        $janji->update([
            'status' => 4
        ]);
        return response()->json(['status' => 'success'],200);
    }

    public function resepSend(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }
        
        $janji = Janji::find($request->id);

        if($janji->status == 2)
        {
            $user = User::find($janji->id_pasien);

            $this->sendMessage('Klinik Ku App',"Resep Obat Anda Telah Tersedia!",$user->token);

            $resep = Resep::where('id_janji',$request->id)->first();

            // 1 : Data Janji Dokter | 2: Page Kartu Berobat | 3: Chat | 4: Resep
    
            $janji->update([
                'status' => 3
            ]);
            Notifikasi::create([
                'id_user' => $user->id,
                'status' => 1,
                'title' => 'Resep Obat Anda Telah Tersedia!',
                'desc' => 'Klik disini untuk melihat data.',
                'tambahan' => $resep->id,
                'page' => 4
            ]);
            return response()->json(['status' => 'success'],200);
        } 
        return response()->json(['status' => 'gagal'],400);

    }

    public function getResep(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        $resep = Resep::find($request->id);
        return response()->json(['status' => 'success','data' => $resep]);


    }

    public function berita()
    {
        $berita = Berita::take(5)->get();
        return $berita;

    }

    public function getBerita(Request $request)
    {
        $berita = Berita::orderBy('created_at','DESC')->when(request()->q, function($query) {
            $query->where('title','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $berita]);
    }

    public function storeBuktiTransfer(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'file' => 'required',
            'id_kartu' => 'required'
        ]);

        $filename = '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = rand(0,100).'-'.$request->id_kartu.'-bukti-transfer.'.$file->extension();
            
            move_uploaded_file($file, base_path('public/bukti-transfer/' . $filename));
        }

        $data = BuktiTransfer::create([
            'nama' => $request->nama,
            'file' => $filename,
            'id_kartu' => $request->id_kartu
        ]);

        return response()->json(['status' => 'success','data' => $data],200);




    }



    public function store(Request $request)
    {
        
        $validate = Validator::make($request->all(), [
            'id_pasien' => 'required',
            'id_dokter' => 'required',
            'tanggal_janji' => 'required',
            // 'jam_janji' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }
        
        $queryAntrian = Janji::whereDate('created_at',Carbon::today())->where('id_dokter',$request->id_dokter)->where('status','!=','4')->orderBy('nomor_antrian','DESC');

        $cekAntrian = $queryAntrian->first();


        if($queryAntrian->count() < 5) {
            $store = Janji::create([
                'id_pasien' => $request->id_pasien,
                'id_dokter' => $request->id_dokter,
                'tanggal_janji' => $request->tanggal_janji,
                'jam_janji' => null,
                'nomor_antrian' => $cekAntrian != null ?$cekAntrian->nomor_antrian + 1 : 1,
                'status' => 0
            ]);
    
            $dokter = Dokter::where('id',$request->id_dokter)->first();
    
            $user = User::where('id',$dokter->id_user)->first();
    
            // OneSignal::sendNotificationToUser(
            //     "Pasien telah membuat janji dengan anda!", 
            //     $user->token,
            //     $url = null, 
            //     // $data = $dataSend, 
            //     $buttons = null, 
            //     $schedule = null
            // );
    
            $this->sendMessage('Dokter Ku App',"Pasien telah membuat janji dengan anda!",$user->token);
    
            // 1 : Data Janji Dokter | 2: Page Kartu Berobat | 3: Chat
    
            Notifikasi::create([
                'id_user' => $dokter->id_user,
                'status' => 1,
                'title' => 'Pasien telah membuat janji dengan anda!',
                'desc' => 'Klik disini untuk melihat data.',
                'tambahan' => $store->id,
                'page' => 1
            ]);
    
    
            
    
    
            
        
    
            return response()->json([
                // 'data' => $upload,
                'status' => 'success',
                'data' => $store,
                // 'antrian' => $cekAntrian->nomor_antrian + 1,
                
            ],200);
        }
        

    }

    public function delete($id)
    {

        $data = Janji::find($id);
        $data->delete();
        // logActivity('Menghapus Role');
        return response()->json(['status' => 'success']);
    }

    // public function update(Request $request)
    // {

       

    //     $validate = Validator::make($request->all(), [
    //         'nama_pajak' => 'required|unique:jenis_pajak,nama_pajak,' . $request->id
    //     ]);

    //     if ($validate->fails()) {
    //         return response()->json($validate->errors(), 500);
    //     }
        

    //     $jenisPajak = JenisPajak::find($request->id);

    //     $jenisPajak->update([
    //         'nama_pajak' => $request->nama_pajak
    //     ]);
    //     return response()->json(['status' => 'success']);

    // }

    // public function status(Request $request)
    // {
    //     $laporan = Laporan::find($request->id);

    //     $laporan->update([
    //         'status' => $request->status
    //     ]);
    //     return response()->json(['status' => 'success']);
    // }

    // public function asuransi(Request $request)
    // {

    //     // $data = Data::create([
    //     //     'id_laporan' => $request->id,
    //     //     'jenis_identitas' => $request->jenis_identitas,
    //     //     'no_identitas' => $request->no_identitas,
    //     //     'nama' => $request->nama,
    //     //     'kondisi_korban' => $request->kondisi_korban,
    //     //     'jenis_kecelakaan' => $request->jenis_kecelakaan
    //     // ]);

    //     $laporan = Laporan::find($request->id);
    //     $laporan->update([
    //         // 'status_laporan' => 1,
    //         'jenis_identitas' => $request->jenis_identitas,
    //         'no_identitas' => $request->no_identitas,
    //         'nama' => $request->nama,
    //         'kondisi_korban' => $request->kondisi_korban,
    //         'jenis_kecelakaan' => $request->jenis_kecelakaan
    //     ]);
    

        

    //     return response()->json(['status' => 'success','data' => 'ok']);
    // }

    // public function laporanSaya(Request $request)
    // {
    //     $laporan = Laporan::where('id_pembuat',$request->user()->id)->get();


    //     return response()->json(['status' => 'success','data' => $laporan],200);


    // }

    // public function informasiStatus($id)
    // {
    //     $laporan = Laporan::where('id',$id)->first();

    //     return response()->json(['status' => 'success','data' => $laporan],200);

    // }


    // public function bulanIni() {
    //     $tanggal = Carbon::now();
    //     $tahun = $tanggal->format('Y');
    //     $bulan = $tanggal->format('m');


    //     $dataLaporan = Laporan::get();


    //     $total = Laporan::count();

    //     $kecelakaanBulanIni = Laporan::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->count();

    //     $tunggalBulanIni = Laporan::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->where('jenis_kecelakaan',0)->count();

    //     $gandaBulanIni = Laporan::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->where('jenis_kecelakaan',1)->count();

    //     $user = User::count();

    //     return response()->json([
    //         'tahun' => $tahun,
    //         'bulan' => $bulan,
    //         'kecelakaan_bulan_ini' => $kecelakaanBulanIni,
    //         'tunggal_bulan_ini' => $tunggalBulanIni,
    //         'ganda_bulan_ini' => $gandaBulanIni,
    //         'total' => $total,
    //         'data' => $dataLaporan,
    //         'user' => $user

    //     ],200);
    // }

    // public function updateNopol(Request $request) {

    //     $laporan = Laporan::where('id', $request->id);
    //     $laporan->update([
    //         'nopol' => $request->nopol
    //     ]);
    //     return response()->json(['status' => 'eheem','data' => $laporan]);

    // }

    // public function testing() {

    //     event(new \App\Events\NewMessageEvent('my-channel','asd'));

    //     return response()->json(['status' => 'eheem']);
    // }



    //

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
            'data' => array(
                "foo" => "bar"
            ),
            'contents' => $content,
            'headings' => $heading,
            'web_buttons' => $hashes_array
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

    public function invoicePdf(Request $request) {

        $path = base_path('public/invoice/');
        $setting = Settings::first();

        $kartuBerobat = KartuBerobat::with('resep')->find($request->id);

        $pembayaran = $kartuBerobat->bukti_transfer != null ? 'Transfer' : 'Tunai';

        $tanggal = Carbon::parse($kartuBerobat->created_at)->format('Y-m-d');
        $pdf = PDF::loadView('reports.invoice', compact('kartuBerobat','pembayaran' ,'setting','tanggal'))->setPaper('legal', 'landscape');
        

        $filename = 'invoice-'.rand(0,100).'-'.$request->id.'.pdf';



        $pdf->save($path.$filename);

        return response()->json([
            'status' => 'success',
            'data' => url('invoice/'.$filename),
            // 'tanggal' => $tanggal,
            // 'kartu' => $kartuBerobat
        ]);


    }

     public function kwitansiPDf(Request $request) {

        $path = base_path('public/kwitansi/');
        $setting = Settings::first();

        $kartuBerobat = KartuBerobat::with('resep')->find($request->id);
        $pembayaran = $kartuBerobat->bukti_transfer != null ? 'Transfer' : 'Tunai';

        $tanggal = Carbon::parse($kartuBerobat->created_at)->format('Y-m-d');
        $pdf = PDF::loadView('reports.kwitansi', compact('kartuBerobat','pembayaran', 'setting','tanggal'))->setPaper('legal', 'landscape');

        $filename = 'kwitansi-'.rand(0,100).'-'.$request->id.'.pdf';



        $pdf->save($path.$filename);

        return response()->json([
            'status' => 'success',
            'data' => url('kwitansi/'.$filename),
            // 'tanggal' => $tanggal,
            // 'kartu' => $kartuBerobat
        ]);


    }

    public function storePenilaian(Request $request) {
        $validate = Validator::make($request->all(), [
            'id_dokter' => 'required',
            'id_janji' => 'required',
            'penilaian' => 'required',
            'catatan' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        $penilaian = Penilaian::create([
            'id_dokter' => $request->id_dokter,
            'id_janji' => $request->id_janji,
            'penilaian' => $request->penilaian,
            'catatan' => $request->catatan
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $penilaian,
            // 'tanggal' => $tanggal,
            // 'kartu' => $kartuBerobat
        ]);



    }
}
