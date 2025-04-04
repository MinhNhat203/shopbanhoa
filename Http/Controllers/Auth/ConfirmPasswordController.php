<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Hash;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function showConfirmForm(Request $request)
    {
        return view('auth.passwords.confirm', ['redirectTo' => $this->redirectTo]);
    }

    public function store(Request $request)
    {
        // Thực hiện kiểm tra thêm trước khi xác nhận mật khẩu
        $this->validate($request, [
            'password' => 'required|password',
        ]);

        // Nếu mật khẩu đúng, thực hiện hành động tiếp theo
        if (Hash::check($request->password, auth()->user()->password)) {
            return redirect()->intended($this->redirectTo);
        }

        // Nếu mật khẩu sai, trả về lỗi
        return back()->withErrors(['password' => 'The password is incorrect.']);
    }

}
