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

use Barryvdh\Debugbar\Facade as Debugbar;

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
                return Redirect::intended('/');
            }
            $errors = \Lang::get('error.incorrect_login_pass');
            return Redirect::back()
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
                $m->from('noreply@vepkar.krc.karelia.ru', \Lang::get('main.site_abbr'));
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
        return Redirect::back()
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
        ]);
        $input = $request->all();
        $credentials = [ 'email' => $request->email ];

//        Debugbar::error('not mine Error!');
//        Debugbar::info($credentials);
//        exit(0);
        
        
        if($user = Sentinel::findByCredentials($credentials))
        {
//Debugbar::info($user);
//print 'This email is registered already.';
//exit(0);
            return Redirect::to('register')
                //view('auth.register')
                ->withErrors(\Lang::get('error.email_is_registered'));
        }
        
//print '22';
//exit(0);
        
        if ($sentuser = Sentinel::register($input))
        {
            $activation = Activation::create($sentuser);
            $code = $activation->code;
            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function($m) use ($sentuser)
            {
                $m->from('noreply@vepkar.krc.karelia.ru', \Lang::get('main.site_abbr'));
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
                ->withErrors('Неверный или просроченный код активации.');
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
        return view('auth.reset_order');
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
        $email = $request->email;
        $sentuser = Sentinel::findByCredentials(compact('email'));
        if ( ! $sentuser)
        {
            return Redirect::back()
                ->withInput()
                ->withErrors('Пользователь с таким E-Mail в системе не найден.');
        }
        $reminder = Reminder::exists($sentuser) ?: Reminder::create($sentuser);
        $code = $reminder->code;

        $sent = Mail::send('mail.account_reminder', compact('sentuser', 'code'), function($m) use ($sentuser)
        {
            $m->from('noreplay@mysite.com', 'SiteLaravel');
            $m->to($sentuser->email)->subject('Сброс пароля');
        });
        if ($sent === 0)
        {
            return Redirect::to('reset')
                ->withErrors('Ошибка отправки email.');
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
            return Redirect::back()
                ->withInput()
                ->withErrors('Такого пользователя не существует.');
        }
        if ( ! Reminder::complete($user, $code, $request->password))
        {
            return Redirect::to('login')
                ->withErrors('Неверный или просроченный код сброса пароля.');
        }
        return Redirect::to('login')
            ->withSuccess("Пароль сброшен.");
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