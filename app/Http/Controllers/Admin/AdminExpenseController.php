<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Expense;
use App\Models\MarketingExpense;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['centre', 'recorder']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('reference_no', 'like', "%{$s}%")
                    ->orWhere('notes', 'like', "%{$s}%")
                    ->orWhereHas('centre', function ($cq) use ($s) {
                        $cq->where('centre_name', 'like', "%{$s}%")
                            ->orWhere('centre_code', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest('expense_date')->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_expenses'  => (float) Expense::sum('amount'),
            'month_expenses'  => (float) Expense::whereBetween('expense_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->sum('amount'),
            'marketing_total' => (float) MarketingExpense::sum('amount_spent'),
            'top_category'    => Expense::select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->first(),
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.expenses.index', compact('expenses', 'stats', 'centres'));
    }

    public function create()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.expenses.create', compact('centres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'centre_id'    => 'nullable|exists:centres,id',
            'category'     => 'required|string|max:50',
            'title'        => 'required|string|max:191',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi,card',
            'reference_no' => 'nullable|string|max:100',
            'receipt_doc'  => 'nullable|file|max:5120',
            'notes'        => 'nullable|string',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_doc')) {
            $receiptPath = FileUploadService::uploadDocument($request->file('receipt_doc'), 'uploads/expenses');
        }

        $expense = Expense::create([
            'centre_id'    => $validated['centre_id'] ?? null,
            'category'     => $validated['category'],
            'title'        => $validated['title'],
            'amount'       => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'payment_mode' => $validated['payment_mode'],
            'reference_no' => $validated['reference_no'] ?? null,
            'receipt_doc'  => $receiptPath,
            'notes'        => $validated['notes'] ?? null,
            'recorded_by'  => Auth::id(),
        ]);

        AuditLog::record('expense_recorded', $expense, null, $expense->toArray());

        return redirect()->route('admin.expenses.index')
            ->with('success', "Expense of ₹" . number_format($expense->amount, 2) . " for '{$expense->title}' logged successfully.");
    }

    public function edit(Expense $expense)
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.expenses.edit', compact('expense', 'centres'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'centre_id'    => 'nullable|exists:centres,id',
            'category'     => 'required|string|max:50',
            'title'        => 'required|string|max:191',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi,card',
            'reference_no' => 'nullable|string|max:100',
            'receipt_doc'  => 'nullable|file|max:5120',
            'notes'        => 'nullable|string',
        ]);

        $oldData = $expense->toArray();

        if ($request->hasFile('receipt_doc')) {
            $validated['receipt_doc'] = FileUploadService::uploadDocument($request->file('receipt_doc'), 'uploads/expenses');
        }

        $expense->update($validated);

        AuditLog::record('expense_updated', $expense, $oldData, $expense->toArray());

        return redirect()->route('admin.expenses.index')
            ->with('success', "Expense entry '{$expense->title}' updated successfully.");
    }

    public function destroy(Expense $expense)
    {
        $oldData = $expense->toArray();
        $expense->delete();

        AuditLog::record('expense_deleted', $expense, $oldData, null);

        return redirect()->route('admin.expenses.index')
            ->with('success', "Expense entry has been archived.");
    }

    // --- Marketing Campaigns Submodule ---

    public function marketing(Request $request)
    {
        $query = MarketingExpense::with(['centre', 'recorder']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('campaign_name', 'like', "%{$s}%")
                    ->orWhere('platform', 'like', "%{$s}%")
                    ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        $campaigns = $query->latest('start_date')->latest('id')->paginate(15)->withQueryString();

        $totalSpent = (float) MarketingExpense::sum('amount_spent');
        $totalLeads = (int) MarketingExpense::sum('leads_generated');
        $totalConversions = (int) MarketingExpense::sum('conversions_count');

        $stats = [
            'total_campaigns'   => MarketingExpense::count(),
            'total_budget'      => (float) MarketingExpense::sum('budget'),
            'total_spent'       => $totalSpent,
            'total_leads'       => $totalLeads,
            'total_conversions' => $totalConversions,
            'cpl'               => $totalLeads > 0 ? ($totalSpent / $totalLeads) : 0.00,
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.expenses.marketing', compact('campaigns', 'stats', 'centres'));
    }

    public function createMarketing()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.expenses.create_marketing', compact('centres'));
    }

    public function storeMarketing(Request $request)
    {
        $validated = $request->validate([
            'centre_id'         => 'nullable|exists:centres,id',
            'campaign_name'     => 'required|string|max:191',
            'platform'          => 'required|string|max:50',
            'budget'            => 'required|numeric|min:0',
            'amount_spent'      => 'required|numeric|min:0',
            'start_date'        => 'required|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'leads_generated'   => 'nullable|integer|min:0',
            'conversions_count' => 'nullable|integer|min:0',
            'notes'             => 'nullable|string',
        ]);

        $campaign = MarketingExpense::create([
            'centre_id'         => $validated['centre_id'] ?? null,
            'campaign_name'     => $validated['campaign_name'],
            'platform'          => $validated['platform'],
            'budget'            => $validated['budget'],
            'amount_spent'      => $validated['amount_spent'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'] ?? null,
            'leads_generated'   => $validated['leads_generated'] ?? 0,
            'conversions_count' => $validated['conversions_count'] ?? 0,
            'notes'             => $validated['notes'] ?? null,
            'recorded_by'       => Auth::id(),
        ]);

        AuditLog::record('marketing_campaign_created', $campaign, null, $campaign->toArray());

        return redirect()->route('admin.expenses.marketing')
            ->with('success', "Marketing campaign '{$campaign->campaign_name}' logged successfully.");
    }

    public function editMarketing(MarketingExpense $marketing)
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.expenses.edit_marketing', compact('marketing', 'centres'));
    }

    public function updateMarketing(Request $request, MarketingExpense $marketing)
    {
        $validated = $request->validate([
            'centre_id'         => 'nullable|exists:centres,id',
            'campaign_name'     => 'required|string|max:191',
            'platform'          => 'required|string|max:50',
            'budget'            => 'required|numeric|min:0',
            'amount_spent'      => 'required|numeric|min:0',
            'start_date'        => 'required|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'leads_generated'   => 'nullable|integer|min:0',
            'conversions_count' => 'nullable|integer|min:0',
            'notes'             => 'nullable|string',
        ]);

        $oldData = $marketing->toArray();
        $marketing->update($validated);

        AuditLog::record('marketing_campaign_updated', $marketing, $oldData, $marketing->toArray());

        return redirect()->route('admin.expenses.marketing')
            ->with('success', "Marketing campaign '{$marketing->campaign_name}' updated successfully.");
    }

    public function destroyMarketing(MarketingExpense $marketing)
    {
        $oldData = $marketing->toArray();
        $marketing->delete();

        AuditLog::record('marketing_campaign_deleted', $marketing, $oldData, null);

        return redirect()->route('admin.expenses.marketing')
            ->with('success', "Marketing campaign record removed.");
    }
}
