<?php

namespace App\Http\Controllers\Member;

use Auth, Response, DB,Hash,Validator,Mail,Exception;
use App\AppModel\Testimonial;
use App\AppModel\Mutasi;
use App\AppModel\Informasi;
use App\AppModel\MenuSubmenu;
use App\AppModel\Pin;
use App\AppModel\Users_validation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppModel\SMSGateway;
use App\AppModel\SMSGatewaySetting;
use App\User;
use App\AppModel\PaypalModel;
use Illuminate\Support\Str;
class ProfilController extends Controller
{
    public function index()
    {
    	return view('member.profile.index');
    }
    
	public function biodata()
	{
    	$URL_uri = request()->segment(1).'/'.request()->segment(2);
        $datasubmenu2 = MenuSubmenu::getSubMenuOneMemberURL($URL_uri)->first();
        
        $getSetting = SMSGatewaySetting::all();
        $sms_setting = [];
        
        foreach($getSetting as $s)
        {
            $n = $s->name;
            $v = $s->value;
            
            $sms_setting[$n] = $v;
        }

        if( $datasubmenu2->status_sub != 0 )
        {
			return view('member.profile.biodata', compact('sms_setting'));
        }
        else
        {
            abort(404);
        }
    }	
    
    public function pin()
	{
		return view('member.profile.pin');
    }
    
    public function getPinSend()
    {
        $userCek  = User::where('id',Auth::user()->id)->first();
        
        $text = 'Pin anda adalah '.$userCek->pin.', simpan dan gunakan untuk transaksi';
        SMSGateway::send($userCek->phone, $text);
        
        return redirect()->route('get.profile.pin')->with('alert-success', 'Informasi PIN Anda berhasil dikirim ke no anda. Silahkan tunggu max 5 menit');
    }

    public function getPinGenerate()
    {
        $userCek  = User::where('id', Auth::user()->id)->firstOrFail();
        $generatePin = Pin::GeneratePin($userCek->id);
    		
        $text = 'Pin anda dirubah '.$generatePin.', simpan dan gunakan untuk transaksi';
        
        SMSGateway::send($userCek->phone, $text);
        
        return redirect()->route('get.profile.pin')->with('alert-success', 'PIN baru berhasil dikirim ke no anda. Silahkan tunggu max 5 menit');
    }
    
    public function ubahPin(Request $request)
    {
        foreach($request->formdata as $item){
            $formdata_proc[$item['name']]  = $item['value'];
        }
        
        $pin      = addslashes(trim($formdata_proc['newpin']));
        $password = addslashes(trim($formdata_proc['password']));
        
        if( is_numeric($pin) && strlen($pin) == 4 )
        {
            if(Hash::check($password, Auth::user()->password))
            {
                DB::table('users')
        			->where('id', Auth::user()->id)
        			->update(['pin'=>$pin]);
        		
        		return 1;
            }
            
            return 0;
        }
        
        return 2;
    }
    
	public function storeBiodata(Request $request)
	{
	    $this->validate($request, [
			'name' => 'required',
			'email' => 'required|unique:users,email,'.Auth::user()->id,
			'city' => 'required',
			//'buyer' => 'max:140'
		],[
			'name.required' => 'Nama tidak boleh kosong',
			'email.required' => 'Email tidak boleh kosong',
			'email.unique' => 'Email telah digunakan akun lain',
			'city.required' => 'Kota tidak boleh kosong',
			//'buyer.max' => 'Maksimal karakter adalah 140',
		]);
		$profile = Auth::user();
		$profile->email = $request->email;
		$profile->name = $request->name;
		$profile->city = $request->city;
// 		if (!empty($request->buyer)) {
// 			$profile->sms_buyer = $request->buyer;
// 		}else{
// 			$profile->sms_buyer = null;
// 		}
		$profile->save();
		return redirect()->back()->with('alert-success', 'Berhasil Merubah Data Profile');
	}

	public function password()
	{
		return view('member.profile.ubah-password');
	}

	public function updatePassword(Request $request)
	{
	    $user = Auth::user();

        $this->validate($request, [
            'password' => 'required|passcheck:' . $user->password,
            'new_password' => 'required|confirmed|min:6',
        ], [
            'password.required' => 'Kata Sandi tidak boleh kosong',
            'password.passcheck' => 'Kata Sandi tidak cocok',
            'new_password.required' => 'Kata Sandi Baru tidak boleh kosong',
            'new_password.confirmed' => 'Konfirmasi Kata Sandi tidak cocok',
            'new_password.min' => 'Kata Sandi minimal 6 digit',
        ]);

        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return redirect()->back()->with('alert-success', 'Kata Sandi Berhasil Diubah');
	}

	public function picture()
	{
		return view('member.profile.ubah-foto');
	}

	public function updatePicture(Request $request)
	{
	    $users = Auth::user();
		if ($users->image != null) {
			$target = 'admin-lte/dist/img/avatar/'.$users->image;
	        if (file_exists($target)) {
	            unlink($target);
	        }
		}
		
		$data = $request->image;
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);

		$data = str_replace(['<?php', '<script', '<?='], '', $data);
        $data = base64_decode($data);
        $imageName = time().'.jpg';
        file_put_contents('admin-lte/dist/img/avatar/'.$imageName, $data);

        $users->image = $imageName;
        $users->save();
        return Response::json($users);
	}

	public function testimonial()
	{
		return view('member.profile.kirim-testimonial');
	}
	
	public function rekening()
	{
	    $cek = DB::table('users_bank')
			->select('users_bank.nama_pemilik_bank','users_bank.no_rekening','bank_swifs.name','bank_swifs.code')
			->leftjoin('bank_swifs','users_bank.id_bank','bank_swifs.id')
			->where('users_bank.user_id',Auth::user()->id)
			->first();
		$sesi = $cek ? 'VIEW' : 'CREATE';
		return view('member.profile.rekening',compact('sesi','cek'));
	}

	public function insertRekening(Request $request)
	{
	    $this->validate($request, [
              'nama_pemilik_bank' => 'required',
              'jenis_rek'         => 'required',
              'rek'               => 'required',
		],[
           'nama_pemilik_bank.required' => 'Nama Pemilik Bank tidak boleh kosong.',
           'jenis_rek.required'         => 'Jenis Bank tidak boleh kosong.',
           'rek.required'               => 'No Rekening tidak boleh kosong.'
		]);
		
		DB::table('users_bank')
        ->insert([
          'user_id'          => Auth::user()->id,
          'id_bank'        => $request->jenis_rek,
          'nama_pemilik_bank'=> strtoupper(addslashes(trim($request->nama_pemilik_bank))),
          'no_rekening'      => addslashes(trim($request->rek)),
        ]);
    
		return redirect()->route('index.rekening-bank')->with('alert-success', 'Menambah Alamat Bank success.');
	}

	public function sendTestimonial(Request $request)
	{
	    $this->validate($request, [
		    'rate' => 'required',
			'review' => 'required',
		],[
			'review.required' => 'Review/Isi Testimonial tidak boleh kosong.',
			'rate.required' => 'Rate/Penilaian tidak boleh kosong.'
		]);
		$testimonials = new Testimonial();
		$testimonials->user_id = Auth::user()->id;
		$testimonials->review = $request->review;
		$testimonials->rate = $request->rate;
		$testimonials->save();
		return redirect()->back()->with('alert-success', 'Terimakasih telah mengirimkan testimonial anda.');
	}

	public function pusatInformasi()
	{
	    $info = Informasi::orderBy('created_at', 'DESC')->paginate(20);
		return view('member.profile.pusat-informasi', compact('info'));
	}

}