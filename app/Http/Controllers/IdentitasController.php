<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\Janji;
// use App\Data;
use App\User;
use App\Identitas;

// namespace App\Events;

// use App\Events\SendNotif;

use Carbon\Carbon;

class IdentitasController extends Controller
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

        $janji = Janji::where('id_pasien',$request->user()->id)->orderBy('created_at','DESC')->get();


        
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

    public function store(Request $request)
    {
        
        $validate = Validator::make($request->all(), [
            'id_pasien' => 'required',
            'id_dokter' => 'required',
            'tanggal_janji' => 'required',
            'jam_janji' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }
        
        $cekAntrian = Janji::whereDate('created_at',Carbon::today())->orderBy('nomor_antrian','DESC')->first();

        $store = Janji::create([
            'id_pasien' => $request->id_pasien,
            'id_dokter' => $request->id_dokter,
            'tanggal_janji' => $request->tanggal_janji,
            'jam_janji' => $request->jam_janji,
            'nomor_antrian' => $cekAntrian != null ?$cekAntrian->nomor_antrian + 1 : 1,
            'status' => 0
        ]);


        
    

        return response()->json([
            // 'data' => $upload,
            'status' => 'success',
            'data' => $store,
            // 'antrian' => $cekAntrian->nomor_antrian + 1,
            
        ],200);

    }

    public function delete($id)
    {

        $data = Janji::find($id);
        $data->delete();
        // logActivity('Menghapus Role');
        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {

       

        // $validate = Validator::make($request->all(), [
        //     'nama_pajak' => 'required|unique:jenis_pajak,nama_pajak,' . $request->id,
        //     'nama' => 'required',
        //     'alamat' => 'required',
        //     'umur' => 'required',
        //     'tanggal_lahir' => 'required',
            
        // ]);

        // if ($validate->fails()) {
        //     return response()->json($validate->errors(), 500);
        // }
        

        // $jenisPajak = JenisPajak::find($request->id);

        // $jenisPajak->update([
        //     'nama_pajak' => $request->nama_pajak
        // ]);


        $identitas = Identitas::where('id_pasien',$request->user()->id);
        $identitas->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'umur' => $request->umur,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jk' => $request->jk,
            'suku' => $request->suku,
            'telp' => $request->telp,
            'pekerjaan' => $request->pekerjaan,
            'keluhan_umum' => $request->keluhan_umum,
            'tinggi_berat' => $request->tinggi_berat,
            'goldar' => $request->goldar,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            'alergi_obat' => $request->alergi_obat,
            'alergi_makanan' => $request->alergi_makanan
        ]);
        return response()->json(['status' => 'success']);

    }

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

   
}
