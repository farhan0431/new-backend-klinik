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
    //
}
