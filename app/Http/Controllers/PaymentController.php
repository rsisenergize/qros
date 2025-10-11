<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index');
    }

    public function due()
    {
        return view('payments.due');
    }

    public function expenses()
    {
        return view('payments.expenses');
    }

    public function expenseCategory()
    {
        return view('payments.expenseCategory');
    }
}
