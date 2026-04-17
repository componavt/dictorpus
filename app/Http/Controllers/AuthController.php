<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use CurlHttp;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;

use App\Models\User;

class AuthController extends Controller
{

    /**
     * Show login page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Show Register page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function register()
    {
        return view('auth.register'); //->with('errors',$errors);
    }


    /**
     * Show wait page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wait()
    {
        return view('auth.wait');
    }


    /**
     * Process login users
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function loginProcess(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $remember = (bool) $request->remember;
            if (Sentinel::authenticate($request->all(), $remember)) {
                return back(); //intended('/');
            }
            $errors = trans('error.incorrect_login_pass');
            return back()
                ->withInput()
                ->withErrors($errors);
        } catch (NotActivatedException $e) {
            $sentuser = $e->getUser();
            $activation = Activation::create($sentuser);
            $code = $activation->code;
            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function ($m) use ($sentuser) {
                $m->from('nataly@krc.karelia.ru', trans('main.site_abbr'));
                $m->to($sentuser->email)->subject(trans('mail.account_activation_subj'));
            });

            if ($sent === 0) {
                return Redirect::to('login')
                    ->withErrors(trans('error.email_activation_error'));
            }
            $errors = trans('error.account_not_activated');
            return view('auth.login')->withErrors($errors);
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            $errors = trans('error.account_throttle', array('delay' => $delay));
            //            "Ваш аккаунт блокирован на {$delay} секунд.";
        }
        return back() //->getTargetUrl()
            ->withInput()
            ->withErrors($errors);
    }


    /**
     * Process register user from site
     *
     * @param Request $request
     * @return $this
     */
    public function registerProcess(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
            'city' => 'required',
            'country' => 'required',
            'affilation' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ]);
        $input = $request->all();
        $credentials = ['email' => $request->email];

        if ($user = Sentinel::findByCredentials($credentials)) {
            return Redirect::to('register')
                //view('auth.register')
                ->withErrors(trans('error.email_is_registered'));
        }

        if ($sentuser = User::registration($input)) {
            $activation = Activation::create($sentuser);
            $code = $activation->code;

            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function ($m) use ($sentuser) {
                $m->from('nataly@krc.karelia.ru', trans('main.site_abbr'));
                $m->to($sentuser->email)->subject(trans('mail.account_activation_subj'));
            });
            if ($sent === 0) {
                return Redirect::to('register')
                    ->withErrors(trans('error.email_activation_error'));
            }

            $role = Sentinel::findRoleBySlug('user');
            $role->users()->attach($sentuser);

            return Redirect::to('login')
                ->withSuccess(trans('auth.account_is_created'))
                ->with('userId', $sentuser->getUserId());
        }
        return Redirect::to('register')
            ->withInput()
            ->withErrors(trans('error.register_failed'));
    }


    /**
     *  Activate user account by user id and activation code
     *
     * @param $id
     * @param $code
     * @return $this
     */
    public function activate($id, $code)
    {
        $sentuser = Sentinel::findById($id);

        if (! Activation::complete($sentuser, $code)) {
            return Redirect::to("login")
                ->withErrors(trans('error.activation_code_expired'));
        }

        return Redirect::to('login')
            ->withSuccess(trans('auth.account_is_activated'));
    }


    /**
     * Show form for begin process reset password
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resetOrder()
    {
        //var_dump(1);
        return view('auth.reset_order');
        //        return view('auth.login');
    }


    /**
     * Begin process reset password by email
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function resetOrderProcess(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        //dd($request);
        $email = $request->email;
        $sentuser = Sentinel::findByCredentials(compact('email'));
        //dd($sentuser);
        if (! $sentuser) {
            return back()
                ->withInput()
                ->withErrors(trans('error.no_user_with_email'));
        }
        $reminder = Reminder::exists($sentuser) ?: Reminder::create($sentuser);
        $code = $reminder->code;
        //dd($code);
        $sent = Mail::send('mail.account_reminder', compact('sentuser', 'code'), function ($m) use ($sentuser) {
            $m->from('nataly@krc.karelia.ru', trans('main.site_abbr'));
            $m->to($sentuser->email)->subject(trans('auth.password_reset'));
        });
        if ($sent === 0) {
            return Redirect::to('reset')
                ->withErrors(trans('error.email_not_sent'));
        }
        return Redirect::to('wait');
    }

    /**
     * Show form for complete reset password
     *
     * @param $id
     * @param $code
     * @return mixed
     */
    public function resetComplete($id, $code)
    {
        $user = Sentinel::findById($id);
        return view('auth.reset_complete');
    }


    /**
     * Complete reset password
     *
     * @param Request $request
     * @param $id
     * @param $code
     * @return $this
     */
    public function resetCompleteProcess(Request $request, $id, $code)
    {
        $this->validate($request, [
            'password' => 'required',
            'password_confirm' => 'required|same:password',
        ]);
        $user = Sentinel::findById($id);
        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(trans('error.no_user'));
        }
        if (! Reminder::complete($user, $code, $request->password)) {
            return Redirect::to('login')
                ->withErrors(trans('error.old_reset_code'));
        }
        return Redirect::to('login')
            ->withSuccess(trans('auth.password_updated'));
    }

    /**
     * @return mixed
     */
    public function logoutuser()
    {
        Sentinel::logout();
        return Redirect::intended('/');
    }
}
