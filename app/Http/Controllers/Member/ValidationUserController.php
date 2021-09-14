<?php

namespace App\Http\Controllers\Member;

use App\User;
use Auth;
use App\AppModel\MenuSubmenu;
use App\AppModel\Users_validation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class ValidationUserController extends Controller
{
    public function index()
    {
        $URL_uri = request()->segment(1).'/'.request()->segment(2);
        $datasubmenu2 = MenuSubmenu::getSubMenuOneMemberURL($URL_uri)->first();

        if( $datasubmenu2->status_sub != 0 )
        {
            $user = User::where('id', Auth::user()->id)->first();
            $validation = Users_validation::where('user_id', $user->id)->first();
            return view('member.validasi_user.index', compact('user', 'validation'));
        }
        else
        {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'ktp'      => 'required',
            'ktp'      => 'required|image|mimes:jpeg,png,jpg',
            'ktp'      => 'required|image|mimes:jpeg,png,jpg|max:1536',
            'image'    => 'required',
            'image'    => 'required|image|mimes:jpeg,png,jpg',
            'image'    => 'required|image|mimes:jpeg,png,jpg|max:1536',
            'tabungan' => 'image|mimes:jpeg,png,jpg',
            'tabungan' => 'image|mimes:jpeg,png,jpg|max:1536',
        ],[
            'ktp.required'      => 'Foto KTP tidak boleh kosong', 
            'ktp.image'         => 'Foto KTP harus berformat gambar',
            'ktp.max'           => 'Foto KTP Max Size 1,5MB',
            'image.required'    => 'Foto Selfie tidak boleh kosong', 
            'image.image'       => 'Foto Selfie harus berformat gambar',
            'image.max'         => 'Foto Selfie Max Size 1,5MB',
            'tabungan.image'    => 'Foto Tabungan harus berformat gambar',
            'tabungan.max'      => 'Foto Tabungan Max Size 1,5MB',
        ]);

        $query = DB::table('users_validations')
            ->select('*')
            ->where('user_id', Auth::user()->id)
            ->count();

        if($query == '1')
        {
            return redirect()->route('validation.user.index')->with('alert-error', 'Anda Sudah melakukan Validasi, silahkan tunggu untuk pengecekan selanjutnya, atau bisa hubungi admin.');
        }
        else
        {
            $fileKtp          = $request->file('ktp');
            $fileSelfie       = $request->file('image');
            $fileTabungan     = $request->file('tabungan');
            
            $fileNameKtp      = 'ktp_'.Auth::user()->id.time().uniqid().'.'.$fileKtp->getClientOriginalExtension();
            
            $fileNameSelfie   = 'selfie_'.Auth::user()->id.time().uniqid().'.'.$fileSelfie->getClientOriginalExtension();
            
            $fileNameTabungan = !empty($fileTabungan) ? 'tabungan_'.Auth::user()->id.time().uniqid().'.'.$fileTabungan->getClientOriginalExtension() : null;

            $fileKtp->move("img/validation/ktp/", $fileNameKtp);
            $fileSelfie->move("img/validation/selfie/", $fileNameSelfie);
            
            if( !empty($fileTabungan) ) {
                $fileTabungan->move("img/validation/tabungan/", $fileNameTabungan);
            }

            DB::table('users_validations')
                ->insert([
                  'user_id'      => Auth::user()->id,
                  'img_ktp'      => $fileNameKtp,
                  'img_selfie'   => $fileNameSelfie,
                  'img_tabungan' => !empty($fileTabungan) ? $fileNameTabungan : null,
                  'status'       => 0,
                  'created_at'   => date('Y-m-d H:i:s'),
                  'updated_at'   => date('Y-m-d H:i:s'),
                ]);

            return redirect()->route('validation.user.index')->with('alert-success', 'Berhasil Menambah Data Validasi, selanjutkan akan dilakukan pengecekan oleh Admin');
        }
    }
    
}