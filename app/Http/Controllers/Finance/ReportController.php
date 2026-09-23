<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\RentLedger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.financial');
    }

    public function financial(Request $request): View
    {
        $user = $request->user();
        $from = $request->date('from')?->startOfMonth() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfMonth() ?? now()->endOfMonth();

        $scope = fn ($q) => $user->is_super_admin ? $q : $q->where('landlord_id', $user->landlord_id);

        $collected = $scope(Payment::query())->where('status', 'confirmed')
            ->whereBetween('payment_date', [$from, $to])->sum('amount');

        $billed = $scope(RentInvoice::query())
            ->whereBetween('due_date', [$from, $to])->sum('total_due');

        $arrears = $scope(RentLedger::query())
            ->whereIn('transaction_type', ['rent_charge', 'adjustment'])->sum('debit')
            - $scope(RentLedger::query())->whereIn('transaction_type', ['payment', 'credit', 'reversal'])->sum('credit');

        $byMethod = $scope(Payment::query())->where('status', 'confirmed')
            ->whereBetween('payment_date', [$from, $to])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')->pluck('total', 'payment_method');

        return view('finance.reports.financial', compact('collected', 'billed', 'arrears', 'byMethod', 'from', 'to'));
    }
}
