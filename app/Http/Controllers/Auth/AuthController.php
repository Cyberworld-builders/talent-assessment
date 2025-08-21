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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'industry_id' => 'nullable|exists:industries,id',
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
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'industry_id' => $data['industry_id'] ?? null,
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Auth::login($this->create($request->all()));

        return redirect($this->redirectPath());
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required', 'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $this->hasTooManyLoginAttempts($request));
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => trans('auth.failed'),
            ]);
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return app('cache')->has(
            $this->getLoginLockoutKey($request)
        );
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        app('cache')->forget($this->getLoginLockoutKey($request));
    }

    /**
     * Get the login lockout error message key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getLoginLockoutKey(Request $request)
    {
        return 'login:'.sha1($request->input('username').$request->ip());
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
