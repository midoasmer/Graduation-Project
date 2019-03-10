<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\agent;
use App\User;
use Validator;
use Hash;
use DB;

class AgentController extends Controller
{
    public static function index() {
        $agent = User::has('agent')->get();
        return response()->json($agent, 200);
    }
    public function show($id) {
        return response()->json(user::findOrfail($id), 200);
    }  
    public function store(request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        if($validator->fails())
            return response()->json([$validator->errors()], 401);

        $user = new user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $agent = new agent();
        $agent->user_id = user::where('email',$request->input('email'))->first()->id;
        $agent->company_id = company::where('name', $request->input('company'))->first()->id;
        $agent->save(); 
        return response()->json(null, 200);
    }
    public function update(request $request , $id) {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|max:50',
            'password' => 'required|min:8',
        ]);
        if($validator->fails())
            return response()->json([$validator->errors()], 401);

        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return response()->json(null, 201);
    }
    public function destroy($id) {
        user::where('id',$id)->delete();
        agent::where('user_id',$id)->delete();
        return response()->json(null, 204);
    }

}