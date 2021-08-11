<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Roles;
use Validator;
use Hash;
use App\Notifikasi;
use App\Identitas;
use App\Dokter;

class UserController extends Controller
{
    public function index()
    {
        $user = User::with('role_name')->orderBy('created_at', 'DESC')->when(request()->q, function($query) {
            $query->where('username', 'LIKE', '%' . request()->q . '%');
        });
        return response()->json([
            'status' => 'success', 
            'data' => request()->type == 'all' ? $user->get():$user->paginate(10)
        ]);
    }

    public function roles()
    {
        $roles = Roles::get();
        
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }

    public function notif(Request $request)
    {
        $notif = Notifikasi::where('id_user',$request->user()->id);

        $data = $notif->orderBy('created_at','DESC')->get();
        $count = $notif->where('status','1')->count();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'count' => $count
        ]);
    }

    public function store(Request $request)
    {
        Identitas::unguard();

        if($request->adminCreated != 'yes'){

            $validate = Validator::make($request->all(), [
                // 'name' => 'required|string',
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                // 'nik' => 'required',
                'telpon' => 'required'
            ]);
    
            if ($validate->fails()) {
                return response()->json(['status' => 'error', 'data' => $validate->errors()], 500);
            }
    
            $user = User::create([
                'name' => $request->username,
                'username' => $request->username,
                'email' => $request->email,
                'password' => app('hash')->make($request->password),
                'role_id' => 1,
                // 'nik' => '1',
                'telpon' => $request->telpon
            ]);

            

            Identitas::create([
                'id_pasien' => $user->id,
                'nama' => $request->username,
                'alamat' => '-',
                'umur' => 0,
                'tanggal_lahir' => '2021-06-01',
                // 'jk' => $request->jk,
                'suku' => '-',
                'telp' => $request->telpon,
                'pekerjaan' => '-',
                'keluhan_umum' => '-',
                'tinggi_berat' => '-',
                'goldar' => '-',
                'riwayat_penyakit' => '-',
                'alergi_obat' => '-',
                'alergi_makanan' => '-',
                'jk' => 1
            ]);

        }else{

            $validate = Validator::make($request->all(), [
                'name' => 'required|string',
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);
    
            if ($validate->fails()) {
                return response()->json($validate->errors(), 500);
            }

            if($request->role_id == 3) {
                Dokter::create([
                    'id_user' => $user->id,
                    'nama' => $request->nama,
                    'fee' => '0',
                    'jabatan' => '-',
                    'foto' => null,
                    'pengalaman' => 1
                ]);
               
            }else{
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => app('hash')->make($request->password),
                    'role_id' => $request->role_id,
                    // 'nik' => "0",
                    'telpon' => "0"
                ]);
            }
    
           

        }

        


        


        // $this->validate($request, [
        //     'name' => 'required|string',
        //     'email' => 'required|email|unique:users,email',
        //     'password' => 'required|min:6',
        //     'nik' => 'required',
        //     'telpon' => 'required'
        // ]);

        // User::create([
        //     'name' => $request->name,
        //     'username' => $request->username,
        //     'email' => $request->email,
        //     'password' => app('hash')->make($request->password),
        //     'role_id' => $request->role_id
        // ]);
        return response()->json(['status' => 'success','data' => $request->all(),200]);
    }

    public function edit($id)
    {
        $user = User::find($id);
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function setToken(Request $request)
    {
        $user = User::find($request->user()->id);
        $user->update([
            'token' => $request->token
        ]);
        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            // 'name' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $request->user()->id,
            // 'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'nullable|string|min:6',
            // 'role_id' => 'requ÷ired'
        ]);

        $user = User::find($request->user()->id);
        $user->update([
            // 'name' => $request->name,
            'username' => $request->username,
            // 'email' => $request->email,
            'password' => $request->password != '' ? app('hash')->make($request->password):$user->password,
            // 'role_id' => $request->type == 'edit' ? $request->user()->role_id : $request->role_id
        ]);
        return response()->json(['status' => 'success']);
    }

    public function editData(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id
            // 'role_id' => 'requ÷ired'
        ]);

        $user = User::find($request->id);
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password == 'none' ? $user->password : app('hash')->make($request->password),
            'nik' => $request->nik,
            'telpon' => $request->telp
        ]);
        return response()->json(['status' => 'success']);
    }

    public function uploadPicture(Request $request)
    {
        $user = $request->user();
        if ($request->hasFile('file')) {
            
            $file = $request->file('file');
            $filename = rand(0,100).'-'.$user->username.'.'.$file->extension();
            
            move_uploaded_file($file, base_path('public/profile/' . $filename));

            $user->update(['thumb_avatar' => $filename]);


        }
        return response()->json(['status' => $user]);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        // logActivity('Menghapus Pengguna');
        return response()->json(['status' => 'success']);
    }

    public function updateProfile(Request $request)
    {
        $user = request()->user();
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data = $request->only('nik', 'name', 'username', 'email', 'telp');
        if ($request->password != '') {
            $data['password'] = app('hash')->make($request->password);
        }
        $user->update($data);
        return response()->json(['status' => 'success']);
    }

    public function updateProfileAvatar(Request $request)
    {
        $user = $request->user();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $user->username . '.png';
            move_uploaded_file($file, base_path('public/user/avatar/' . $filename));

            $user->update(['thumb_avatar' => $filename]);
        }
        return response()->json(['status' => 'success']);
    }

    public function updatePasswordUser(Request $request)
    {
        $this->validate($request, [
            'old' => 'required|string',
            'new' => 'required|string|confirmed'
        ]);

        $user = $request->user();
        if (Hash::check($request->old, $user->password)) {
            $user->update([
                'password' => bcrypt($request->new)
            ]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }

    // public function getUserLogin()
    // {
    //     // $user = request()->user()->load(['role', 'role.role_permission.permission']);
    //     $user = request()->user();
    //     $setting = Setting::first();
    //     $user['setting'] = $setting;
    //     return response()->json(['status' => 'success', 'data' => $user]);
    // }

    // public function getCaptchaImage()
    // {
    //     $captcha = \Captcha::create('flat', true);
    //     return response()->json(['status' => 'success', 'data' => $captcha]);
    // }

    public function readNotif(Request $request) {
        $notif = Notifikasi::where('id',$request->id)->first();

        $notif->update([
            'status' => 2
        ]);
        
        return response()->json(['status' => 'success']);

    }
}
