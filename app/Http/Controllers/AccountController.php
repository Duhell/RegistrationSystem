<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Mail\NewRegisteredAccount;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AccountController extends Controller
{
    public function getAccount(?string $id = null)
    {
        $response = array();

        if($id == null){
            return view('Registration');
        }else{
            $data = \App\Models\User::where('user_UUID',$id)->first();

            if(!$data){
                $response = [
                    "user"=> null,
                    'msg'=> "User not found!",
                    'status_code'=> 404
                ];
            }else{
                $response = [
                    'user'=>$data,
                    'msg'=>"Confirmed!",
                    'status_code'=> 200
                ];
            }
        }

        return view('Confirmation')->with('response',$response);
    }

    public function createAccount(AccountRequest $request)
    {
        $validatedDataRequest = $request->validated();
        try{
            $user = new \App\Models\User;
            $user->user_UUID = Uuid::uuid4()->toString();
            $user->fill($validatedDataRequest);
            $user->fullname = $validatedDataRequest['firstname']." ".$validatedDataRequest['lastname'];
            $user->password = Hash::make($validatedDataRequest['password']);

            if ($user->save()) {
                $qrcodeDirectory = 'storage/qrcodes/';
                if (!file_exists(public_path($qrcodeDirectory))) {
                    mkdir(public_path($qrcodeDirectory), 0777, true);
                }
                $qr_filename = $validatedDataRequest['firstname']."_".uniqid() . '.png';
                $qrcodePath = $qrcodeDirectory . $qr_filename;
                QrCode::format('png')->size(150)->generate('http://192.168.249.129:8000/'.$user->user_UUID, public_path($qrcodePath));

                $user->qrcodepath = $qrcodePath;
                $user->save();

                $emailData = [
                    'subject' => "Download Qr Code",
                    'qrpath' => $qrcodePath,
                    'qrname' => $qr_filename
                ];

                Mail::to($user->email)->send(new NewRegisteredAccount($emailData));
                return response()->json([
                    'response'=>[
                        'code'=>201,
                        'method'=>"POST",
                        'is_success'=> true,
                        'is_sent_email'=>true,
                        'message'=>'Successfully Registered.'
                    ]
                ]);
            }else{
                return response()->json([
                    'error'=> "Data not saved!",
                    'file'=> 'AccountController.createAccount'
                ]);
            }


        }catch(Exception $error){
            return response()->json([
                "error"=>$error->getMessage(),
                'line'=>$error->getLine(),
                'file'=>$error->getFile()
            ]);
        }
    }
}
