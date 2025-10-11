@extends('vendor.installer.layouts.master')

@section('title', 'Finished')
@section('container')
    <p @class([
            'alert alert-success',
            'alert-danger'=> session()->has('message') && session('message')['status'] !=='success',
        ])
       style="text-align: center;">
        Application has been successfully installed
    </p>

    <div @class([
            'alert alert-success',
            'alert-danger'=> session()->has('message') && session('message')['status'] !=='success',
        ])
       >
        <h6 style="margin-top: unset;text-align: center">Superadmin login details</h6>
        <table >
            <tr>
                <td style="text-align: right">Email:</td>
                <td style="text-align: left"><b>superadmin@example.com</b></td>
            </tr>
            <tr>
                <td style="text-align: right">Password:</td>
                <td style="text-align: left"><b>123456</b></td>
            </tr>
        </table>
    </div>

    <div class="buttons">
        <a href="{{ url('/') }}" class="button">Click here to exit</a>
    </div>
@stop
