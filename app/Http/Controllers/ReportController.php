<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function itemReport()
    {
        return view('reports.items');
    }

    public function categoryReport()
    {
        return view('reports.category');
    }

    public function salesReport()
    {
        return view('reports.sales');
    }

    public function expenseReport()
    {
        return view('reports.expense-reports');
    }

    public function outstandingPaymentReport()
    {
        return view('reports.outstanding-payment');
    }

    public function expenseSummaryReport()
    {
        return view('reports.expense-summary');
    }

    public function printLog()
    {
        return view('reports.print-log');
    }

    public function deliveryReport()
    {
        return view('reports.delivery-app-report');
    }
}
