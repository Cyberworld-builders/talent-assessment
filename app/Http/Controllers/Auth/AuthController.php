<?php

namespace App\Http\Controllers\Auth;

use App\Reseller;
use App\User;
use Auth;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Session;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $redirectTo = '/dashboard';
    protected $loginPath = '/login';
    protected $redirectAfterLogout = '/login';
    protected $username = 'username';

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogin()
	{
		if (session('reseller') && \Auth::user() == null)
		{
			$resellerId = session('reseller')->id;
			Session::flush();
			return redirect('/resellers/'.$resellerId.'/login');
		}

		if (view()->exists('auth.authenticate')) {
			return view('auth.authenticate');
		}

		return view('auth.login');
	}

	/**
	 * Get the path to the login route.
	 *
	 * @return string
	 */
    public function loginPath()
	{
		if (session('reseller') && \Auth::user() == null)
		{
			$resellerId = session('reseller')->id;
			Session::flush();
			return '/resellers/'.$resellerId.'/login';
		}

		return '/login';
	}

	/**
	 * Redirect to a specific path on logout.
	 *
	 * @return string
	 */
	protected function redirectAfterLogout()
	{
		return '/login';
	}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, \Auth::user());
        }

        if (\Auth::user()->is('admin'))
            return redirect()->intended($this->redirectPath());

        if (\Auth::user()->is('reseller'))
        	return redirect('/dashboard');

        if (\Auth::user()->is('client'))
        	return redirect('/dashboard');

        if (\Auth::user()->completed_profile)
            return redirect()->intended('/assignments');

        return redirect()->intended('/profile');
    }

	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getResellerLogin($id)
	{
		$reseller = Reseller::findOrFail($id);

		if (view()->exists('auth.authenticate')) {
			return view('auth.authenticate');
		}

		return view('auth.login', compact('reseller'));
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postResellerLogin($id, Request $request)
	{
		$reseller = Reseller::findOrFail($id);

		// Change the connection to the Reseller's database
		\Config::set('database.connections.mysql.host', $reseller->getDbHost());
		\Config::set('database.connections.mysql.database', $reseller->getDbName());
		\Config::set('database.connections.mysql.username', $reseller->getDbUser());
		\Config::set('database.connections.mysql.password', $reseller->getDbPass());
		DB::reconnect('mysql');

		// Store the reseller in the session
		session(['reseller' => $reseller]);

		return $this->postLogin($request);
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogout()
	{
		$resellerId = null;
		if (session('reseller'))
			$resellerId = session('reseller')->id;

		Session::flush();
		Auth::logout();

		if ($resellerId)
			return redirect('resellers/'.$resellerId.'/login');

		return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
	}
}
