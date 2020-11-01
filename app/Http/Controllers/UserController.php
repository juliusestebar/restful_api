<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Session\Middleware\AuthenticateSession;

class UserController extends ApiController
{
    public function __construct()
    {

        $this->middleware('client.credentials')->only(['store','resend']);
        $this->middleware('auth:api')->except(['store','resend','verify']);
        //$this->middleware('scope:manage-application')->only(['store']);

        //parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $users = User::all();

       return $this->showAll($users);
       //return response()->json(['data' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $rules = [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:6|confirmed',
        // ];

        // $this->validate($request,$rules);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
          return $this->errorResponse($validator->errors(),422);
        }

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        if($request->hasFile('image')){
            $data['image'] = $request->image->store('');
        }

        $user = User::create($data);

        return $this->showOne($user,201);
        //return response()->json(['data' => $user], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
       //$user = User::findOrFail($id);

       return $this->showOne($user);
       //return response()->json(['data' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
       //$user = User::findOrFail($id);
        //dd($request->name);

        // $rules = [
        //     'email' => 'email|unique:users,email,' . $user->id,
        //     'password' => 'min:6|confirmed',
        //     'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        // ];

        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ]);

        if ($validator->fails()) {
          return $this->errorResponse($validator->errors(),422);
        }
        // if ($validator->success()) {
        //   return $this->errorResponse($validator->errors(),422);
        // }

        if($request->has('name')){
            $user->name = $request->name;
        }

        if($request->has('email') && $user->email != $request->email){
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if($request->has('password')){
            $user->password = brypt($request->password);
        }

        if($request->hasFile('image')){
            Storage::delete($user->image);
            $user->image = $request->image->store('');
        }

        if($request->has('admin')){
            if(!$user->isVerified()){
                //return response()->json(['error' => 'Only verified users can modify the admin field', 'code' => 409], 409);
                return $this->errorResponse('Only verified users can modify the admin field',409);
            }

            $user->admin = $request->admin;
        }

        // if(!$user->isDirty()){
        //     //return response()->json(['error' => 'You need to specify a different value to update', 'code' => 422], 422);
        //     return $this->errorResponse('You need to specify a different value to update',422);    
        // }

        $user->save();
        return $this->showOne($user);
        //return response()->json(['data' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
       //$user = User::findOrFail($id);

       $user->delete();

       Storage::delete($user->image);
       return $this->showOne($user);
       //return response()->json(['data' => $user], 200);
    }

    public function verify($token){
        $user = User::where('verification_token',$token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('The account has been verified successfully');
    }

    public function resend(User $user)
    {
        if($user->isVerified()){
            return $this->errorResponse('This user is already verified',409);
        }

        // retry(5, function() use ($user){
        //     Mail::to($user)->send(new UserCreated($user));
        // }, 100);     
            
        Mail::to($user)->send(new UserCreated($user));

        return $this->showMessage('The verification email has been resend');
    }
}
