<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendPassword;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkerInfo;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Http\Controllers\LogsController;


class UserController extends Controller
{
    public function users()
    {
        $users = User::get();

        LogsController::addLog(['event' => 'show', 'model' => 'User']);

        return $users;
    }   
    public function delete(Request $request)
    {   
        User::whereIn('id', $request)->delete();

        LogsController::addLog(['event' => 'delete', 'model' => 'User', 'element_id' => $request->all()]);

        return $request;
    }
    public function signature(Request $request)
    {
        $signature = $request->signature;
        $base64_str = substr($request->signature, strpos($request->signature, ",")+1);
        $user = $request->user();
        $user->signature = $base64_str;
        $user->save();

        LogsController::addLog(['event' => 'edit', 'model' => 'User', 'element_id' => [$user->id]]);

        return $user;
    }
    public function create(Request $request)
    {
        $user = new User;
        $password = Str::random(16);
        
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->password = Hash::make($password);
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->workplace = $request->workplace;
        $user->role = $request->role;
        $user->disabled = $request->disabled;
        $user->save();

        Mail::to($user)->send(new SendPassword($password));
        LogsController::addLog(['event' => 'add', 'model' => 'User', 'element_id' => [$user->id]]);

        
        return $user;
    }

    public function update(Request $request) {
        $user = User::find($request->id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->disabled = $request->disabled;
        $user->role = $request->role;
        $user->workplace = $request->workplace;
        $user->save();

        LogsController::addLog(['event' => 'edit', 'model' => 'User', 'element_id' => [$user->id]]);


        return $user;
    }

    public function profileinfo(Request $request)
    {
        $user = User::find($request->id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->save();

        LogsController::addLog(['event' => 'edit', 'model' => 'User', 'element_id' => [$user->id]]);


        return $user;
    }
}
