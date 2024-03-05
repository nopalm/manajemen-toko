<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::paginate(10);

        $filterKeyword = $request->get('keyword');
        $status = $request->status;
        if($filterKeyword){

            if($status){
                
                $users = User::where('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate(10);
            
            } else {
            
                $users = User::where('email', 'LIKE', "%$filterKeyword%")->paginate(10);
            }
        }

        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $new_user = new User;
        $new_user->name = $request->get('name');
        $new_user->username = $request->get('username');
        $new_user->roles = json_encode($request->get('roles'));
        $new_user->name = $request->get('name');
        $new_user->address = $request->get('address');
        $new_user->phone = $request->get('phone');
        $new_user->email = $request->get('email');
        $new_user->password = \Hash::make($request->get('password'));

        if($request->file('avatar')){
            $file = $request->file('avatar')->store('avatars', 'public');
            $new_user->avatar = $file;
        }
        $new_user->save();
        return redirect()->route('users.create')->with('status', 'User successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->get('name');
        $user->roles = json_encode($request->get('roles'));
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->status = $request->get('status');

        // check saat update memasukan gambar atau tidak
        if($request->file('avatar')) {
            // jika ada gambar maka gambar lama dihapus diganti menggunakan gambar baru
            if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))){
                \Storage::delete('public/'.$user->avatar);
                $file = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $file;
                // jika tidak ada gambar maka membuat gambar baru
                } elseif($request->file('avatar')){
                    $file = $request->file('avatar')->store('avatars', 'public');
                    $user->avatar = $file;
                } 
        } 
        
        $user->save();
        return redirect()->route('users.edit', $user->id)->with('status','User succesfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('status', 'User successfully delete');
    }
}
