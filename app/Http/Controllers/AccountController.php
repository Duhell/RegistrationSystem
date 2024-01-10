<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Mail\NewRegisteredAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            $user->save();

            $emailData = [
                'subject'=> "Appreciation Message",
                'body'=>"Thank you for registering in our website!"
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
        }catch(Exception $error){
            return response()->json([
                "error"=>$error->getMessage()
            ]);
        }
    }
}
