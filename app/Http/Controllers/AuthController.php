<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;

use Illuminate\Http\Request;
use App\Http\Requests;
use Redirect;
use Sentinel;
use Activation;
use Reminder;
use Validator;
use Mail;
use Storage;
use CurlHttp;
use NoCaptcha;

//use Barryvdh\Debugbar\Facade as Debugbar;

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
        return view('auth.register');//->with('errors',$errors);
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
        try
        {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $remember = (bool) $request->remember;
            if (Sentinel::authenticate($request->all(), $remember))
            {
                return back();//intended('/');
            }
            $errors = \Lang::get('error.incorrect_login_pass');
            return back()
                ->withInput()
                ->withErrors($errors);
        }
        catch (NotActivatedException $e)
        {
            $sentuser= $e->getUser();
            $activation = Activation::create($sentuser);
            $code = $activation->code;
            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function($m) use ($sentuser)
            {
                $m->from('nataly@krc.karelia.ru', \Lang::get('main.site_abbr'));
                $m->to($sentuser->email)->subject(\Lang::get('mail.account_activation_subj'));
            });

            if ($sent === 0)
            {
                return Redirect::to('login')
                    ->withErrors(\Lang::get('error.email_activation_error'));
            }
            $errors = \Lang::get('error.account_not_activated');
            return view('auth.login')->withErrors($errors);
        }
        catch (ThrottlingException $e)
        {
            $delay = $e->getDelay();
            $errors = \Lang::get('error.account_throttle', array('delay'=>$delay));
//            "Ваш аккаунт блокирован на {$delay} секунд.";
        }
        return back()//->getTargetUrl()
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
            'g-recaptcha-response'=>'required|captcha',
        ]);
        $input = $request->all();
        $credentials = [ 'email' => $request->email ];
        
        if($user = Sentinel::findByCredentials($credentials)) {
            return Redirect::to('register')
                //view('auth.register')
                ->withErrors(\Lang::get('error.email_is_registered'));
        }
        
        if ($sentuser = User::registration($input))
        {
            $activation = Activation::create($sentuser);
            $code = $activation->code;
            
            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function($m) use ($sentuser)
            {
                $m->from('nataly@krc.karelia.ru', \Lang::get('main.site_abbr'));
                $m->to($sentuser->email)->subject(\Lang::get('mail.account_activation_subj'));
            });
            if ($sent === 0)
            {
                return Redirect::to('register')
                    ->withErrors(\Lang::get('error.email_activation_error'));
            }

            $role = Sentinel::findRoleBySlug('user');
            $role->users()->attach($sentuser);

            return Redirect::to('login')
                ->withSuccess(\Lang::get('auth.account_is_created'))
                ->with('userId', $sentuser->getUserId());
        }
        return Redirect::to('register')
            ->withInput()
            ->withErrors(\Lang::get('error.register_failed'));
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

        if ( ! Activation::complete($sentuser, $code))
        {
            return Redirect::to("login")
                ->withErrors(\Lang::get('error.activation_code_expired'));
        }

        return Redirect::to('login')
            ->withSuccess(\Lang::get('auth.account_is_activated'));
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
        if ( ! $sentuser)
        {
            return back()
                ->withInput()
                ->withErrors(\Lang::get('error.no_user_with_email'));
        }
        $reminder = Reminder::exists($sentuser) ?: Reminder::create($sentuser);
        $code = $reminder->code;
//dd($code);
        $sent = Mail::send('mail.account_reminder', compact('sentuser', 'code'), function($m) use ($sentuser)
        {
            $m->from('nataly@krc.karelia.ru', \Lang::get('main.site_abbr'));
            $m->to($sentuser->email)->subject(\Lang::get('auth.password_reset'));
        });
        if ($sent === 0)
        {
            return Redirect::to('reset')
                ->withErrors(\Lang::get('error.email_not_sent'));
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
        if ( ! $user)
        {
            return back()
                ->withInput()
                ->withErrors(\Lang::get('error.no_user'));
        }
        if ( ! Reminder::complete($user, $code, $request->password))
        {
            return Redirect::to('login')
                ->withErrors(\Lang::get('error.old_reset_code'));
        }
        return Redirect::to('login')
            ->withSuccess(\Lang::get('auth.password_updated'));
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
