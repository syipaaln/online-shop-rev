<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\models\user;
use app\models\Checkout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class homecontroller extends controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function dashboard() {
        return view('home');
    }
    
    public function user() {
        return view('home');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $cartCount = 0;
        if (Auth::check()) {
            $userId = Auth::id();
            $cartCount = Checkout::where('user_id', $userId)->sum('quantity');
        }

        return view('home', compact('cartCount'));
    }
    
    public function manageUser() {
        $users = User::all();
        return view('superadmin.manageUser', compact('users'));
    }

    public function superadminSearchUser(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('name', 'LIKE', "%{$query}%")
                            ->orWhere('alamat', 'LIKE', "%{$query}%")
                            ->get();

        return view('superadmin.manageUser', compact('users'));
    }

    public function manageUserCreate() {
        return view('superadmin.manageUserCreate');
    }

    public function registerUser(Request $request)
    {
        // Validasi data pengguna
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin,superadmin',
        ]);

        // Jika validasi gagal, kembali ke halaman pendaftaran dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect('/manage-user/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Buat pengguna baru jika validasi berhasil
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Redirect pengguna setelah pendaftaran
        return redirect('/manage-user');
    }

    public function manageUserDelete(User $user) {
        $user->delete();
        return redirect()->back()->with('success', 'Berhasil Hapus User');
    }

    public function manageUserUpdate(Request $request, User $user) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin,superadmin', // Tambahkan validasi untuk bidang lainnya
        ]);
    
        $user->update($request->all());
    
        return redirect('/manage-user')->with('success', 'Berhasil Ubah User');
    }

    public function manageUserEdit(User $user)
    {
        return view('superadmin.manageUserEdit', compact('user'));
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */ 
    public function adminManageUser()
    {
        $users = User::all();
        return view('admin.manageUser', compact('users'));
    }

    public function adminSearchUser(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('name', 'LIKE', "%{$query}%")
                     ->orWhere('alamat', 'LIKE', "%{$query}%")
                     ->get();
    
        return view('admin.manageUser', compact('users'));
    }

    public function adminManageUserCreate() {
        return view('admin.manageUserCreate');
    }

    public function adminRegistUser(Request $request)
    {
        // Validasi data pengguna
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin,superadmin',
        ]);

        // Jika validasi gagal, kembali ke halaman pendaftaran dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect('/admin/manage-user/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Buat pengguna baru jika validasi berhasil
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Redirect pengguna setelah pendaftaran
        return redirect('/admin/manage-user');
    }

    // public function manageUserDelete(User $user) {
    //     $user->delete();
    //     return redirect()->back()->with('success', 'Berhasil Hapus User');
    // }

    public function adminManageUserUpdate(Request $request, User $user) {
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
             'password' => 'required|string|min:8',
             'role' => 'required|string|in:user,admin,superadmin', // Tambahkan validasi untuk bidang lainnya
         ]);
    
         $user->update($request->all());
    
         return redirect('/admin/manage-user')->with('success', 'Berhasil Ubah User');
    }

    public function adminManageUserEdit(User $user)
    {
         return view('admin.manageUserEdit', compact('user'));
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userHome()
    {
        return view('user.home');
    }
}
