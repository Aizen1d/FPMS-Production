<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

    function checkIfGuest() {
        if (!Auth::guard('faculty')->check() && !Auth::guard('admin')->check()) {
            return true;
        }
        else{
            return false;
        }
    }

    function getValidationMessages() {
        return [
            'password.regex' => 'Your password must contain at least one uppercase letter and one number.',
            'username.required' => 'The username is required.',
            'username.min' => 'The username must be at least 3 characters.',
            'username.max' => 'The username must not be greater than 20 characters.',
            'contactnumber.numeric' => 'The contact number must be a number.',
            'contactnumber.digits' => 'The contact number must be exactly 11 digits.',
        ];
    }

    function getFacultyLoginValidation() {
        return [
            'email'=>'required|email',
            'password'=>'required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/'
        ];
    }

    function getAdminLoginValidation() {
        return [
            'email'=>'required|email',
            'password'=>'required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/'
        ];
    }

    function getFacultyRegisterValidation() {
        return [
            'username'=>'required|unique:faculties|regex:/^[a-zA-Z0-9]*$/|min:3|max:20',
            'email'=>'required|email|unique:faculties',
            'password'=>'required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/|confirmed'
        ];
    }

    function getAdminRegisterValidation() {
        return [
            'username'=>'required|unique:admins|regex:/^[a-zA-Z0-9]*$/|min:3|max:20',
            'email'=>'required|email|unique:admins',
            'password'=>'required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/|confirmed'
        ];
    }

    function getFacultyProfileValidation() {
        return [
            'username' => [
                'required',
                Rule::unique('faculties')->ignore(Auth::guard('faculty')->user()->id),
                'regex:/^[a-zA-Z0-9]*$/',
                'min:3',
                'max:20',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('faculties')->ignore(Auth::guard('faculty')->user()->id),
            ],
            'password'=>'bail|required|min:5|max:20|regex:/^(?=.*[A-Z])(?=.*\d).+$/|confirmed',
            'firstname' => 'required|regex:/^[\pL\s]+$/u|max:255',
            'middlename' => 'nullable|regex:/^[\pL\s]+$/u|max:255',
            'lastname' => 'required|regex:/^[\pL\s]+$/u|max:255',         
            'contactnumber' => 'bail|numeric|digits:11',
        ];
    }
?>