<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }
    protected function redirectTo()
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (Auth::guard('admin')->check()) {
            // Người dùng đã đăng nhập, lấy thông tin người dùng
            $user = Auth::guard('admin')->user();
            // Kiểm tra nếu role_id = 1 (Admin)
            if ($user->role_id == 1) {
                return '/admin/dashboard'; // Chuyển hướng tới trang dashboard admin
            }
            // Nếu không phải admin, đăng xuất và chuyển hướng về trang chủ
            Auth::guard('admin')->logout();
            return redirect('/')->with('error', 'Bạn không có quyền truy cập vào trang quản trị.');
        }

        // Nếu người dùng chưa đăng nhập, chuyển hướng về trang chủ
        return '/';
    }



}
