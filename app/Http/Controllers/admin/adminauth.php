<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\controller;
use Illuminate\Support\Facades\Config;
use DB;
use Carbon\Carbon;
use App\news;
use App\users;
use App\Mail\sendmail;
use App\Mail\VeifyEmail;
use App\Admin;
use App\Mail\Dooney;
use Lang;
use App\Jobs\sendmailiob;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Mail;
use Hash;



class adminauth extends Controller
{
   
       public function showRegister()
    {
        return view('admin.register');
    }
    

    public function adminregister(Request $request)
    {
        $validate = $this->validate(request(),[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'age' => 'required|integer|min:18',
            'gender' => 'required|in:male,female',
            'mobile' => 'required|min:9|max:15',
            'email' => 'required|string|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $validate['password'] = Hash::make(request('password'));
        $validate['verifyToken']= str_random(40);

        $user= Admin::create($validate);

        $user->sendverficationEmail();

       
        //Mail::to($user->email)->send(new VeifyEmail($user));


        //$emai_token=$user->generateEmailVerificationToken();
       // return dd($emai_token);
       // $url=URL::signedRoute('verify-email',['token'=> $emai_token]);
       // Mail::to($use->email)->send(new sendmail($url));
        auth()->login($user);      
       return redirect(aurl('account'));

    }


        public function login (){
                return view('admin.login');
        }

        public function dologin(Request $request){
            $validate = $this->validate(request(),[
                'email' => 'required|string',
                'password' => 'required ',
            ]);
            $rememberme=request('rememberme')==1 ?true:false;
            if (admin()->attempt(['email'=>request('email'),'password'=>request('password')],$rememberme)){

                return redirect('admin');
            }
            else{
               // session()->flash('error',trans('admin.inccorrect_information_login'));
                return  back()->withErrors('invalid_Email Or Password');

            }
            
          
        }

        public function logout(Request $request) {
            auth()->guard('admin')->logout();
    

            return redirect(aurl('login'));
        } 
    
        public function forgot_password() {
            return view('admin.forgot_password');
        }

        public function forgot_password_post(Request $request){
            $admin= Admin::where('email',request('email'))->first();
            if(!empty($admin)){
                $token = app('auth.password.broker')->createToken($admin);
                $data = DB::table('password_resets')->insert([
                    'email'=> $admin->email,
                    'token'=>$token,
                    'created_at'=>Carbon::now(),

                ]);
                Mail::to($admin->email)->send(new Dooney(['data'=>$admin,'token'=>$token]));
                session()->flash('success',Lang::get(' Chick your Email Reset link is sent To your Email Chech it out '));
                return back();

                
            

            }
            return back();

            
        }  


            
        public function reset_password($token){
            $check_toke=DB::table('password_resets')->where('token',$token)->where('created_at','>',Carbon::now()->subHours(1))->first();
 
            if(!empty($check_toke)){
                return view('admin.reset_password',['data'=>$check_toke]);
            }
           else{

                return redirect(aurl('forget/password'));
            }

        }
    


       
        public function reset_password_final($token){
          
            $this->validate(request(),[
                'password'=>'required|confirmed',
                'password_confirmation'=>'required',

            ],[],[

                'password'=>'password',
                'password_confirmation'=>'password_confirmation',
            ]);

            $check_toke=DB::table('password_resets')
            ->where('token',$token)->where('created_at','>',
            Carbon::now()->subHours(1))->first();
            if(!empty($check_toke)){

                $admin=Admin::where('email',$check_toke->email)
                ->update(['email'=> $check_toke->email,
                'password'=>bcrypt(request('password'))]);

                DB::table('password_resets')->where('email',request('email'))->delete();
                admin()->attempt(['email'=> $check_toke->email,'password'=>request('password')],true);
                return redirect(aurl());
    
            }else{
                return redirect(aurl('forgot_password'));          
              }
        }


        public function all_news_post(Request $request){

            
            news::Create([
        'content'=>request('content')
     ]);


        return back();



      /*
        $add= new news;

        $add->title=request('title');
        $add->discreption=request('discreption');
        $add->status=request('status');
        $add->add_by=request('add_by');
        $add->content=request('content');
        $add->save();

       

*/


    }
    public function all_news_send(Request $request){
        $post = news::all();
        return view('admin.all_news',['post'=>$post]);
    }



    //multi delete fun  delete
 public function delete($id=null ){
    
        if($id != null){
        $dell =  news::find($id);
        $dell->delete();
        }elseif(request()->has('id') and request()->has('Multibledelete')){
            news::destroy(request('id'));
        }

        return back();
     }
            
   public function books( ){
           return view('admin.books');
   }
    
   public function images( ){
           return view('admin.images');
   }
        
    
   public function novals( ){
           return view('admin.novels');
   }


   public function sendemail( ){

    
    $email='sami@gmail.com';
    $data=[
        'title'=>'laravel8',
        'url'=>'https:://blog.mail.com'
    ];
    Mail::to($email)->send(new sendmail($data));

   }
  public function verify($token){
   
    $verify=Admin::where('verifyToken',$token)->firstOrFail()->update(['verifyToken'=> null]);

        return redirect(aurl('/'))->with('success','Êã ÊÝÚíá ÍÓÇÈß');

    
  }  


  public function chickup(){
    return view('admin.Emailchick');
  }
}