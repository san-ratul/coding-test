<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * generate a report of all transactions by head type
     * show the report in a view
     * @return View
     */
    public function index()
    {
        $head_types = AccountHead::HEADTYPE;
        $account_heads =  AccountHead::query()
            ->join('transactions','transactions.account_head_id','account_heads.id')
            ->groupBy('account_heads.head_type','account_heads.name')
            ->selectRaw('account_heads.name,
            account_heads.head_type,
            SUM(transactions.debit) AS total_debit,
            SUM(transactions.credit) AS total_credit')
            ->get();
        $account_heads = $account_heads->map(function ($item, $key) use ($head_types) {
            $item->total_amount = (in_array($item->head_type, ['asset', 'expense'])) ?
                $item->total_debit - $item->total_credit :
                $item->total_credit - $item->total_debit;
            return $item;
        });
        $account_heads = $account_heads->groupBy('head_type');
        return view('components.report-1', compact('head_types', 'account_heads'));
    }

    /**
     * load the report-2 page
     * @return View
     */
    public function report2()
    {
        return view('components.report-2');
    }

    /**
     * generate the report-2 data in a date range
     * @param Request $request
     * @return JsonResponse
     */
    public function getReport(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'head_type' => 'required|in:asset,liability,equity,income,expense',
        ]);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $head_type = $request->head_type;
        $opening_balance = $this->getOpeningOrClosingBalance($start_date, $head_type);
        $closing_balance = $this->getOpeningOrClosingBalance($end_date, $head_type);
        $report =  AccountHead::query()
            ->join('transactions','transactions.account_head_id','account_heads.id')
            ->whereBetween('transactions.date', [$start_date, $end_date])
            ->where('account_heads.head_type', $head_type)
            ->groupBy('account_heads.name')
            ->selectRaw('account_heads.id, account_heads.name, SUM(transactions.debit) AS total_debit, SUM(transactions.credit) AS total_credit')
            ->get();

        $finalReport['total'] = $this->getTotal($report, $opening_balance, $closing_balance);
        $finalReport['report'] = $this->formatReport($report, $opening_balance, $closing_balance);

        return response()->json($finalReport, Response::HTTP_OK);
    }

    /**
     * get the opening or closing balance of a head type
     * @param $date
     * @param $head_type
     * @return array
     */
    private function getOpeningOrClosingBalance($date, $head_type)
    {
        $query = (in_array($head_type, ['asset', 'expense'])) ?
            "(SUM(transactions.debit) - SUM(transactions.credit))" :
            "(SUM(transactions.credit) - SUM(transactions.debit))";
        return Transaction::query()
            ->join('account_heads','transactions.account_head_id','account_heads.id')
            ->where('account_heads.head_type', $head_type)
            ->where('transactions.date', '<', $date)
            ->groupBy('transactions.account_head_id')
            ->selectRaw($query.' AS balance, transactions.account_head_id')
            ->get()
            ->keyBy('account_head_id')
            ->toArray();
    }

    /**
     * get the total of report-2
     * @param Collection|array $report
     * @param array $opening_balance
     * @param array $closing_balance
     * @return array
     */
    private function getTotal(Collection|array $report, array $opening_balance, array $closing_balance)
    {
        $finalReport['debit'] = number_format($report->sum('total_debit'), 2);
        $finalReport['credit'] = number_format($report->sum('total_credit'), 2);
        $finalReport['opening_balance'] = number_format(collect($opening_balance)->sum('balance'), 2);
        $finalReport['closing_balance'] = number_format(collect($closing_balance)->sum('balance'), 2);
        return $finalReport;
    }

    /**
     * format the report-2 data
     * @param Collection|array $report
     * @param array $opening_balance
     * @param array $closing_balance
     * @return Collection|\Illuminate\Support\Collection
     */
    private function formatReport(Collection|array $report, array $opening_balance, array $closing_balance)
    {
        return $report->map(function($item) use ($opening_balance, $closing_balance) {
            $item->total_debit = number_format($item->total_debit, 2);
            $item->total_credit = number_format($item->total_credit, 2);
            $item->opening_balance = number_format($opening_balance[$item->id]['balance'] ?? 0, 2);
            $item->closing_balance = number_format($closing_balance[$item->id]['balance'] ?? 0, 2);
            return $item;
        });
    }
}
