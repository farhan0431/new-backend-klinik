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
use App\Chat;
use App\Berita;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\File;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facedes\WithHeadings;



class AdminController extends Controller
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
        

        $janji = Janji::count();
        $antrian = Janji::where('status',0)->orWhere('status',1)->orWhere('status',2)->count();
        $user = User::count();
        $dokter = Dokter::count();
        $pasien = Identitas::count();

        $data = [
            'janji' => $janji,
            'antrian' => $antrian,
            'user' => $user,
            'dokter' => $dokter,
            'pasien' => $pasien
        ];

        return response()->json(['status' => 'success','data' => $data],200);

    }

    public function getJanji()
    {
        $janji =  $laporan = Janji::with('data_dokter','data_identitas','data_pasien','resep','kartu_berobat')->orderBy('created_at','DESC')->whereHas('data_identitas', function($query) {
            $query->where('nama','LIKE','%'.request()->q.'%');
        })->orWhereHas('data_dokter', function($query) {
            $query->where('nama','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success','data' => $janji],200);

    }

    public function getAntrian(Request $request)
    {
        $tanggal = $request->date == '' ? Carbon::today() : $request->date;


        $janji =  $laporan = Janji::with('data_dokter','data_identitas','data_pasien','resep','kartu_berobat')->where('tanggal_janji',$tanggal)->where('status','<',2)->orderBy('created_at','DESC')
        ->paginate(10);
        return response()->json(['status' => 'success','data' => $janji,'tanggal' => $request->date],200);

    }

    public function getKonfirmasi()
    {
        $date = Carbon::today()->addDay(1);

        $janji =  Janji::with('data_dokter','data_identitas','data_pasien','resep','kartu_berobat')->orderBy('created_at','DESC')->where('tanggal_janji',$date)->where('status','<',1)
        ->paginate(10)->where('konfirmasi',0);
        return response()->json(['status' => 'success','data' => $janji],200);

    }

    public function getRiwayat(Request $request)
    {

        $janji = Janji::with('data_dokter','data_identitas','data_pasien','resep','kartu_berobat')->where('id_pasien',$request->id)->where('status',3)->orderBy('tanggal_janji','ASC')->get();

        return response()->json(['status' => 'success','data' => $janji],200);


    }

    public function konfirmasi(Request $request)
    {

        if($request->type == 2) {

            Janji::find($request->id)->update([
                'tanggal_janji' => $request->date
            ]);

        }else if($request->type == 3){

            Janji::find($request->id)->update([
                'konfirmasi' => $request->type,
                'status' => 4
            ]);

        }else{
            Janji::find($request->id)->update([
                'konfirmasi' => $request->type,
            ]);

        }
        
        return response()->json(['status' => 'success'],200);

    }

    public function updateDokter(Request $request)
    {
          $validate = Validator::make($request->all(), [
            'nama' => 'required',
            'fee' => 'required',
            'jabatan' => 'required',
            'pengalaman' => 'required',
            'id' => 'required'
            
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        Dokter::find($request->id)->update([
            'nama' => $request->nama,
            'fee' => $request->fee,
            'jabatan' => $request->jabatan,
            'pengalaman' => $request->pengalaman,
        ]);

        return response()->json(['status' => 'success'],200);

        

    }

    public function updateBerita(Request $request) {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'deskripsi' => 'required',
            'id' => 'required'
            
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        Berita::find($request->id)->update([
            'title' => $request->title,
            'fee' => $request->fee
        ]);

        return response()->json(['status' => 'success'],200);
    }

    public function getDokter()
    {
        $user = Dokter::with('data_user')->orderBy('created_at','DESC')->whereHas('data_user', function($query) {
            $query->where('username','LIKE','%'.request()->q.'%');
        })->orWhere('nama','%'.request()->q.'%')
        ->paginate(10);
        return response()->json(['status' => 'success','data' => $user],200);
    }

    public function getPasien(Request $request)
    {

        


        $dataByName = Identitas::orderBy('created_at','DESC')->when(request()->q, function($query) {
            $query->where('nama', 'LIKE', '%' . request()->q . '%');
        });

        

        if($dataByName->count() > 0) {
            $data = $dataByName->paginate(10);
            return response()->json(['status' => 'success','data' => $data],200);

        }else{
            $query = $request->q;
            $id = ltrim(explode("_",$query)[0],"0");
            
            $data = Identitas::where('id', 'LIKE', '%' . $id . '%')->paginate(10);
            

            return response()->json(['status' => 'success','data' => $data],200);


        }


        // $user = Identitas::orderBy('created_at','DESC')->when(request()->q, function($query) {
        //     $query->where('nama', 'LIKE', '%' . request()->q . '%');
        // })->paginate(10);
    }


    public function uploadGambar(Request $request)
    {
        $user = User::find($request->id);
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = rand(0,100).'-'.$user->username.'.'.$file->extension();
            
            move_uploaded_file($file, base_path('public/profile/' . $filename));
            $user->update(['thumb_avatar' => $filename]);
        }
        return response()->json(['status' => $user]);
    }

    public function uploadGambarBerita(Request $request)
    {
        $berita = Berita::find($request->id);
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = rand(0,10000).'-'.$file->extension();
            
            move_uploaded_file($file, base_path('public/berita/' . $filename));
            $berita->update(['file' => $filename]);
        }
        return response()->json(['status' => 'success'],200);
    }

    public function storeBerita(Request $request) {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'deskripsi' => 'required'            
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        $berita = Berita::create([
            'title' => $request->title,
            'deskripsi' => $request->deskripsi
        ]);
        if($berita) {
            return response()->json(['status' => 'success','data' => $berita],200);
        }else{
            return response()->json(['status' => 'gagal'],500);
        }
        
        

        
                

        
    }

    public function getHarian(Request $request)
    {
        $date = $request->date == '' ? Carbon::today() : $request->date;

        if($request->type == 0) {

            $janji =  $laporan = Janji::with('data_dokter','data_pasien','kartu_berobat')->whereDate('tanggal_janji', $date)->where('status',3)->orderBy('tanggal_janji','DESC');


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }



            return response()->json(['status' => 'success','data' => $janji->get(),'jumlah_pemasukan' => $jumlahPemasukan, 'jumlah_janji' => $jumlahJanji, 'date' => $date],200);

        }else if($request->type == 1){

            $month = Carbon::parse($date)->format('m');
            $year = Carbon::parse($date)->format('Y');

            $janji =  $laporan = Janji::with('data_dokter','data_pasien','kartu_berobat')->whereMonth('created_at', $month)->whereYear('tanggal_janji',$year)->where('status',3)->orderBy('tanggal_janji','DESC');


            $jumlahPemasukan = 0;
    
            $jumlahJanji = $janji->count();
    
    
            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }
    
    
    
            return response()->json(['status' => 'success','data' => $janji->get(),'jumlah_pemasukan' => $jumlahPemasukan, 'jumlah_janji' => $jumlahJanji, 'date' => $year],200);

        }else if($request->type == 2){

            $month = Carbon::parse($date)->format('m');
            $year = Carbon::parse($date)->format('Y');

            $janji =  Janji::with('data_pasien','kartu_berobat')->whereMonth('created_at', $month)->whereYear('tanggal_janji',$year)->where('status',3)->where('id_dokter',$request->id_dokter)->orderBy('tanggal_janji','DESC');

            $jumlahPemasukan = 0;
    
            $jumlahJanji = $janji->count();
    
    
            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }
    
    
    
            return response()->json(['status' => 'success','data' => $janji->get(),'jumlah_pemasukan' => $jumlahPemasukan, 'jumlah_janji' => $jumlahJanji, 'date' => $year, 'dokter' => $request->id_dokter],200);

        }

        

    }
    public function exportPdf(Request $request) {

        $path = base_path('public/laporan/');
        $setting = Settings::first();



        $date = $request->date == '' ? Carbon::today()->format('Y-m-d') : Carbon::parse($request->date)->format('Y-m-d');

        // return response()->json(['status' => 'success'],200);

    
        if($request->type == 0) {
            $jenis = 0;

             $janji =  $laporan = Janji::with('data_dokter','data_pasien','kartu_berobat')->whereDate('tanggal_janji', $date)->where('status',3)->orderBy('tanggal_janji','DESC');

             $tanggal = $date;


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();

            $dataJanji = $janji->get();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }

            $pdf = PDF::loadView('reports.laporan', compact('dataJanji','tanggal', 'setting','jumlahPemasukan','jenis'))->setPaper('legal', 'landscape');

            $filename = 'Laporan-Harian-'.$tanggal.'-nomor-'.rand(0,100).$request->id.'.pdf';

            $pdf->save($path.$filename);





            return response()->json(['status' => 'success','data' => url('laporan/'.$filename),'janji' => $dataJanji],200);


            // return response()->json(['status' => 'success'],200);

        }
        else if($request->type == 1){
            $jenis = 1;

            $month = Carbon::parse($date)->format('m');
            $year = Carbon::parse($date)->format('Y');

            $janji =  $laporan = Janji::with('data_dokter','kartu_berobat')->whereMonth('created_at', $month)->whereYear('tanggal_janji',$year)->where('status',3)->orderBy('tanggal_janji','DESC');


            $tanggal = 'Bulan '.$month.' Tahun '.$year;


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();

            $dataJanji = $janji->get();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }

            $pdf = PDF::loadView('reports.laporan', compact('dataJanji','tanggal', 'setting','jumlahPemasukan','jenis'))->setPaper('legal', 'landscape');

            $filename = 'Laporan-'.'Bulan-'.$month.'-Tahun-'.$year.'-nomor-'.rand(0,100).$request->id.'.pdf';

            $pdf->save($path.$filename);





            return response()->json(['status' => 'success','data' => url('laporan/'.$filename),'tanggal' => 'Bulan '.$month.' Tahun '.$year],200);
        }
        else if($request->type == 2){
            $jenis = 2;

            $month = Carbon::parse($date)->format('m');
            $year = Carbon::parse($date)->format('Y');

            $janji =  Janji::with('data_pasien','kartu_berobat')->whereMonth('created_at', $month)->whereYear('tanggal_janji',$year)->where('status',3)->where('id_dokter',$request->id_dokter)->orderBy('tanggal_janji','DESC');

            $dokter = Dokter::find($request->id_dokter);

            $tanggal = 'Bulan '.$month.' Tahun '.$year;


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();

            $dataJanji = $janji->get();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }

            $pdf = PDF::loadView('reports.laporan', compact('dataJanji','tanggal', 'setting','jumlahPemasukan','jenis','dokter'))->setPaper('legal', 'landscape');

            $filename = 'Laporan-'.'Bulan-'.$month.'-Tahun-'.$year.'-nomor-'.rand(0,100).$request->id.'.pdf';

            $pdf->save($path.$filename);





            return response()->json(['status' => 'success','data' => url('laporan/'.$filename),'tanggal' => 'Bulan '.$month.' Tahun '.$year,'dokter' => $dokter],200);
        }

        
        
    }

    public function exportExcel(Request $request) {

        $setting = Settings::first();



        $date = $request->date == '' ? Carbon::today()->format('Y-m-d') : Carbon::parse($request->date)->format('Y-m-d');

        // return response()->json(['status' => 'success'],200);

    
        if($request->type == 0) {
            $jenis = 0;

             $janji = Janji::with('data_dokter','data_pasien','kartu_berobat')->whereDate('tanggal_janji', $date)->where('status',3)->orderBy('tanggal_janji','DESC');

             $tanggal = $date;


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();

            $dataJanji = $janji->get();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }

            $filename = 'Laporan-Harian-'.$tanggal.'-nomor-'.rand(0,100).$request->id.'.xlsx';


            return Excel::download(new UsersExport($dataJanji,$jumlahJanji,$tanggal,$setting,$jumlahPemasukan,$jenis,'2'),$filename);






            // return response()->json(['status' => 'success','data' => url('laporan/'.$filename),'janji' => $dataJanji],200);


            // return response()->json(['status' => 'success'],200);

        }
        else if($request->type == 1){
            $jenis = 1;

            $month = Carbon::parse($date)->format('m');
            $year = Carbon::parse($date)->format('Y');

            $janji =  $laporan = Janji::with('data_dokter','data_pasien','kartu_berobat')->whereMonth('created_at', $month)->whereYear('tanggal_janji',$year)->where('status',3)->orderBy('tanggal_janji','DESC');


            $tanggal = 'Bulan '.$month.' Tahun '.$year;


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();

            $dataJanji = $janji->get();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }

            $filename = 'Laporan-'.'Bulan-'.$month.'-Tahun-'.$year.'-nomor-'.rand(0,100).$request->id.'.xlsx';

            return Excel::download(new UsersExport($dataJanji,$jumlahJanji,$tanggal,$setting,$jumlahPemasukan,$jenis,'2'),$filename);
        }
        else if($request->type == 2){
            $jenis = 2;

            $month = Carbon::parse($date)->format('m');
            $year = Carbon::parse($date)->format('Y');

            $janji =  Janji::with('data_pasien','data_dokter','kartu_berobat')->whereMonth('created_at', $month)->whereYear('tanggal_janji',$year)->where('status',3)->where('id_dokter',$request->id_dokter)->orderBy('tanggal_janji','DESC');

            $dokter = Dokter::find($request->id_dokter);

            $tanggal = 'Bulan '.$month.' Tahun '.$year;


            $jumlahPemasukan = 0;

            $jumlahJanji = $janji->count();

            $dataJanji = $janji->get();


            foreach ($janji->get() as $row) {
                $jumlahPemasukan += $row->kartu_berobat->biaya;
            }

            $filename = 'Laporan-'.'Bulan-'.$month.'-Tahun-'.$year.'-nomor-'.rand(0,100).$request->id.'.xlsx';

            return Excel::download(new UsersExport($dataJanji,$jumlahJanji,$tanggal,$setting,$jumlahPemasukan,$jenis,$dokter),$filename);
        }

        
        
    }
    
    

    //
}
