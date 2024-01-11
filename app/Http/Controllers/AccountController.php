<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Mail\NewRegisteredAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AccountController extends Controller
{
    public function getAccount()
    {
        return response()->json([
            'data'=> \App\Models\User::all()
        ]);
    }

    public function createAccount(AccountRequest $request)
    {
        $validatedDataRequest = $request->validated();
        try{
            $user = new \App\Models\User;
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
                QrCode::format('png')->size(150)->generate('https://www.google.com', public_path($qrcodePath));

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
