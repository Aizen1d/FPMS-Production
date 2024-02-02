<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\admins;
use App\Models\FacultyPendingAccounts;
use App\Models\User;
use App\Models\Logs;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

require_once(resource_path('views/validation_standards.php'));

class AuthController extends Controller
{
    function chooseLogin() {
        if (checkIfGuest()) {
            return view('auth.choose_login');
        }
        else {
            return back();
        }
    }
    
    function loginFaculty() {
        if (checkIfGuest()) {
            return view('auth.login_faculty');
        }
        else {
            return back();
        }
    }

    function loginAdmin(){
        if (checkIfGuest()) {
            return view('auth.login_admin');
        }
        else {
            return back();
        }
    }

    function registerFaculty() {
        if (checkIfGuest()) {
            return view('auth.register_faculty');
        }
        else {
            return back();
        }
    }

    function registerAdmin() {
        if (checkIfGuest()) {
            return view('auth.register_admin');
        }
        else {
            return back();
        }
    }
    
    function submitRegisterFaculty(Request $request) {
        //return $request->input();

        //require_once(public_path('standards/validation_standards.blade.php'));
        $request->validate(getFacultyRegisterValidation(), getValidationMessages());

        /*$faculty = new Faculty;
        $faculty->username = $request->username;
        $faculty->email = $request->email;
        $faculty->password = Hash::make($request->password);
        $save = $faculty->save();

        if ($save) {
            return back()->with('success', "Faculty account successfully registered.");
        }
        else{
            return back()->with('fail', "Something went wrong, try again later.");
        }*/

        $pendingFaculty = new FacultyPendingAccounts;
        $pendingFaculty->username = $request->username;
        $pendingFaculty->email = $request->email;
        $pendingFaculty->password = Hash::make($request->password);
        $pendingFaculty->request_date = Carbon::now();
        $save = $pendingFaculty->save();

        if ($save) {
            Logs::create([
                'user_id' => null,
                'user_role' => 'Faculty',
                'action_made' => $pendingFaculty->username . ' has requested a Faculty registration.',
                'type_of_action' => 'Register',
            ]);

            return back()->with('success', "Your account is now on the waitlist.");
        }
        else{
            return back()->with('fail', "Something went wrong, try again later.");
        }
        
    }

    function submitRegisterAdmin(Request $request) {
        //return $request->input();

        //require_once(public_path('standards/validation_standards.blade.php'));
        $request->validate(getAdminRegisterValidation(), getValidationMessages());

        $admin = new admins;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $save = $admin->save();

        if ($save) {
            Logs::create([
                'user_id' => null,
                'user_role' => 'Admin',
                'action_made' => $admin->username . ' has been registered as an Admin.',
                'type_of_action' => 'Register',
            ]);

            return back()->with('success', "Admin account successfully registered.");
        }
        else{
            return back()->with('fail', "Something went wrong, try again later.");
        }
    }

    function validateLoginFaculty(Request $request) {
        //return $request->input();

        //require_once(public_path('standards/validation_standards.blade.php'));
        $request->validate(getFacultyLoginValidation(), getValidationMessages());

        $facultyInfo = Faculty::where('email', '=', $request->email)->first();

        if (!$facultyInfo) {
            return back()->with('fail', "Email not recognized in the system.");
        }
        else {
            if (Auth::guard('faculty')->attempt(['email' => $request->email, 'password' => $request->password])) {
                $fullName = ($facultyInfo->first_name ? $facultyInfo->first_name . ' ' : '') . ($facultyInfo->middle_name ? $facultyInfo->middle_name . ' ' : '') . ($facultyInfo->last_name ? $facultyInfo->last_name : '');
                Logs::create([
                    'user_id' => $facultyInfo->id,
                    'user_role' => 'Faculty',
                    'action_made' => '(' . $facultyInfo->username . ') ' . $fullName . ' has logged in to the system.',
                    'type_of_action' => 'Login',
                ]);
                return redirect('/faculty-home');
            }
            else {
                return back()->with('fail', "Password is incorrect.");
            }
        }
    }

    function validateLoginAdmin(Request $request) {
        //return $request->input();

        //require_once(public_path('standards/validation_standards.blade.php'));
        $request->validate(getAdminLoginValidation(), ['password.regex' => 'Password is incorrect.']);

        $adminInfo = admins::where('email', '=', $request->email)->first();

        if (!$adminInfo) {
            return back()->with('fail', "Email not recognized in the system.");
        }
        else {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
                Logs::create([
                    'user_id' => $adminInfo->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminInfo->username . ') has logged in to the system.',
                    'type_of_action' => 'Login',
                ]);

                return redirect('/admin-home');
            }
            else {
                return back()->with('fail', "Password is incorrect.");
            }
        }
    }

    function logoutFaculty() {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $username = $user->username;
            $userFullName = ($user->first_name ? $user->first_name . ' ' : '') . ($user->middle_name ? $user->middle_name . ' ' : '') . ($user->last_name ? $user->last_name : '');

            Logs::create([
                'user_id' => Auth::guard('faculty')->user()->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $username . ') ' . $userFullName . ' has logged out from the system.',
                'type_of_action' => 'Logout',
            ]);

            Auth::guard('faculty')->logout();

            return redirect('/login-faculty');
        }
    }

    function logoutAdmin() {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $username = $user->username;

            Logs::create([
                'user_id' => Auth::guard('admin')->user()->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $username . ') has logged out from the system.',
                'type_of_action' => 'Logout',
            ]);

            Auth::guard('admin')->logout();
            return redirect('/login-admin');
        }
    }

    public function showFacultyForgotForm(){
        if (checkIfGuest()) {
            return view('auth.faculty_forgot_password');
        }
        else {
            return back();
        }
    }

    public function sendFacultyResetLink(Request $request){
        $request->validate([
            'email'=>'required|email|exists:faculties,email'],

            ['email.exists' => 'The email entered is not found in the system.']);

        $existingToken = \DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();
    
        if ($existingToken) {
            $createdAt = Carbon::parse($existingToken->created_at);
            if ($createdAt->diffInMinutes(Carbon::now()) >= 10) { // if the token is expired
                \DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();
            }
            else{ // if token not expired
                return back()->with('fail', 'We already emailed the reset link!');
            }
        }

        $token = \Str::random(64);
        \DB::table('password_reset_tokens')->insert([
            'email'=>$request->email,
            'token'=>$token,
            'created_at'=>Carbon::now(),
        ]);

        $action_link = route('faculty-password-reset-form', ['token'=>$token, 'email'=>$request->email]);
        $body = "We have received a request to reset the password for <b>Faculty Account</b> associated with ".$request->email.
        ". You can reset your password by clicking the button below. <br><br>This reset form is only available for <b>10 minutes.</b>";

        \Mail::send('auth/faculty_forgot_email_template',['action_link'=>$action_link, 'body'=>$body], function($message) use ($request){
            //$message->from('noreply@example.com'. 'Faculty Monitoring System');
            $message->to($request->email, 'Faculty Member')
                    ->subject('Reset Password');
        });

        //$facultyInfo = Faculty::where('username', '=', $request->username)->first();
        //$request->session()->put('FacultyPasswordResetProcess', $facultyInfo->id);
        return back()->with('success', 'We have e-mailed your password reset link');
    }

    public function showFacultyResetForm(Request $request, $token = null){
        $existingToken = \DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();
    
        if ($existingToken) {
            $createdAt = Carbon::parse($existingToken->created_at);
            if ($createdAt->diffInMinutes(Carbon::now()) >= 10) { // if the token is expired
                \DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return view('auth.faculty_forgot_token_expires')->with('fail', 'The token for your forgot password was expired. Please try again.');
            }
            else{ // if token is not yet expired
                return view('auth.faculty_forgot_reset')->with(['token'=>$token, 'email'=>$request->email]);
            }
        }
        else { // email, token not found on the resets table (possible scenario: resets completes/expires and answered a pre-loaded form)
            return view('auth.faculty_forgot_token_expires')->with('fail', 'The token for your forgot password was expired. Please try again.');
        }
    }

    public function actionFacultyPasswordReset(Request $request){
        $request->validate([
            'email'=>'required|email|exists:faculties,email',
            'password'=>'required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/|confirmed'],

            ['email.exists' => 'The email entered is not found in the system.', 
            'password.regex' => 'Your password must contain at least one uppercase letter and one number.']);

        $faculty = Faculty::where('email', $request->email)->first();
        if (\Hash::check($request->password, $faculty->password)) {
            return back()->withInput()->with('fail', 'The new password entered is the same as the previous password.');
        }

        $check_token = \DB::table('password_reset_tokens')->where([
            'email'=>$request->email,
            'token'=>$request->token,
        ])->first();

        if (!$check_token){
            return back()->withInput()->with('fail', 'Invalid token, please try again.');
        }
        else{

            Faculty::where('email', $request->email)->update([
                'password'=>\Hash::make($request->password)
            ]);

            \DB::table('password_reset_tokens')->where([
                'email'=>$request->email
            ])->delete();
            
            //if (session()->has('FacultyPasswordResetProcess')){
                //session()->pull('FacultyPasswordResetProcess');
            //}

            $user = $faculty;
            $username = $user->username;
            $userFullName = ($user->first_name ? $user->first_name . ' ' : '') . ($user->middle_name ? $user->middle_name . ' ' : '') . ($user->last_name ? $user->last_name : '');

            Logs::create([
                'user_id' => $user->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $username . ') ' . $userFullName . ' has changed their password.',
                'type_of_action' => 'Change Password',
            ]);

            return redirect()->route('login-faculty')->with('success', 'Your password has been successfully changed.');
        }
    }

    ///////// ADMIN forgot password methods /////////

    public function showAdminForgotForm() {
        if (checkIfGuest()) {
            return view('auth.admin_forgot_password');
        }
        else {
            return back();
        }
    }

    public function sendAdminResetLink(Request $request){
        $request->validate([
            'email'=>'required|email|exists:admins,email'],

            ['email.exists' => 'The email entered is not found in the system.']);

        $existingToken = \DB::table('admins_password_reset_tokens')
        ->where('email', $request->email)
        ->first();
    
        if ($existingToken) {
            $createdAt = Carbon::parse($existingToken->created_at);
            if ($createdAt->diffInMinutes(Carbon::now()) >= 10) { // if the token is expired
                \DB::table('admins_password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();
            }
            else{ // if token not expired
                return back()->with('fail', 'We already emailed the reset link!');
            }
        }

        $token = \Str::random(64);
        \DB::table('admins_password_reset_tokens')->insert([
            'email'=>$request->email,
            'token'=>$token,
            'created_at'=>Carbon::now(),
        ]);

        $action_link = route('admin-password-reset-form', ['token'=>$token, 'email'=>$request->email]);
        $body = "We have received a request to reset the password for <b>Admin Account</b> associated with ".$request->email.
        ". You can reset your password by clicking the button below. <br><br>This reset form is only available for <b>10 minutes.</b>";

        \Mail::send('auth/admin_forgot_email_template',['action_link'=>$action_link, 'body'=>$body], function($message) use ($request){
            //$message->from('noreply@pupfmc.com'. 'Faculty Monitoring System');
            $message->to($request->email, 'Admin Member')
                    ->subject('Reset Password');
        });

        //$facultyInfo = Faculty::where('username', '=', $request->username)->first();
        //$request->session()->put('FacultyPasswordResetProcess', $facultyInfo->id);
        return back()->with('success', 'We have e-mailed your password reset link');
    }

    public function showAdminResetForm(Request $request, $token = null){
        $existingToken = \DB::table('admins_password_reset_tokens')
        ->where('email', $request->email)
        ->first();
    
        if ($existingToken) {
            $createdAt = Carbon::parse($existingToken->created_at);
            if ($createdAt->diffInMinutes(Carbon::now()) >= 10) { // if the token is expired
                \DB::table('admins_password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return view('auth.admin_forgot_token_expires')->with('fail', 'The token for your forgot password was expired. Please try again.');
            }
            else{ // if token is not yet expired
                return view('auth.admin_forgot_reset')->with(['token'=>$token, 'email'=>$request->email]);
            }
        }
        else { // email, token not found on the resets table (possible scenario: resets completes/expires and answered a pre-loaded form)
            return view('auth.admin_forgot_token_expires')->with('fail', 'The token for your forgot password was expired. Please try again.');
        }
    }

    public function actionAdminPasswordReset(Request $request){
        $request->validate([
            'email'=>'required|email|exists:admins,email',
            'password'=>'required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/|confirmed'],

            ['email.exists' => 'The email entered is not found in the system.', 
            'password.regex' => 'Your password must contain at least one uppercase letter and one number.']);

        $admin = admins::where('email', $request->email)->first();
        if (\Hash::check($request->password, $admin->password)) {
            return back()->withInput()->with('fail', 'The new password entered is the same as the previous password.');
        }

        $check_token = \DB::table('admins_password_reset_tokens')->where([
            'email'=>$request->email,
            'token'=>$request->token,
        ])->first();

        if (!$check_token){
            return back()->withInput()->with('fail', 'Invalid token, please try again.');
        }
        else{

            admins::where('email', $request->email)->update([
                'password'=>\Hash::make($request->password)
            ]);

            \DB::table('admins_password_reset_tokens')->where([
                'email'=>$request->email
            ])->delete();
            
            //if (session()->has('FacultyPasswordResetProcess')){
                //session()->pull('FacultyPasswordResetProcess');
            //}

            $user = $admin;
            $username = $user->username;

            Logs::create([
                'user_id' => $user->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $username . ') has changed their password.',
                'type_of_action' => 'Change Password',
            ]);

            return redirect()->route('login-admin')->with('success', 'Your password has been successfully changed.');
        }
    }
}
