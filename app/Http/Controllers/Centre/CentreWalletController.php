<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentreWalletController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();

        $wallet = CentreWallet::firstOrCreate(
            ['centre_id' => $centre->id],
            [
                'balance' => 0.00,
                'total_credited' => 0.00,
                'total_debited' => 0.00,
                'status' => 'active',
            ]
        );

        $query = CentreWalletTransaction::where('centre_id', $centre->id)->with('approver');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'balance' => (float) $wallet->balance,
            'status' => $wallet->status,
            'total_credited' => (float) $wallet->total_credited,
            'total_debited' => (float) $wallet->total_debited,
            'pending_requests' => CentreWalletTransaction::where('centre_id', $centre->id)->where('status', 'pending')->count(),
            'pending_amount' => (float) CentreWalletTransaction::where('centre_id', $centre->id)->where('status', 'pending')->sum('amount'),
        ];

        return view('centre.wallet.index', compact('centre', 'wallet', 'transactions', 'stats'));
    }

    public function topupForm()
    {
        $centre = $this->getAuthCentre();
        $wallet = $centre->wallet;

        return view('centre.wallet.topup', compact('centre', 'wallet'));
    }

    public function storeTopup(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_mode' => 'required|in:upi,bank_transfer,neft,rtgs,imps,cheque',
            'reference_no' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255',
            'payment_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $slipPath = $request->hasFile('payment_slip')
            ? FileUploadService::uploadDocument($request->file('payment_slip'), 'uploads/wallet/slips')
            : null;

        $desc = "Recharge request via " . strtoupper($validated['payment_mode']) . " (Ref: {$validated['reference_no']})";
        if (!empty($validated['notes'])) {
            $desc .= " - " . $validated['notes'];
        }

        $txn = CentreWalletTransaction::create([
            'centre_id' => $centre->id,
            'type' => 'credit',
            'amount' => (float) $validated['amount'],
            'balance_after' => (float) ($centre->wallet?->balance ?? 0.00),
            'reference_no' => strtoupper(trim($validated['reference_no'])),
            'description' => $desc,
            'status' => 'pending',
        ]);

        AuditLog::record('centre_wallet_topup_requested', $txn, null, [
            'centre_id' => $centre->id,
            'amount' => $txn->amount,
            'reference_no' => $txn->reference_no,
        ]);

        return redirect()->route('centre.wallet.index')
            ->with('success', "Top-up request for ₹" . number_format($txn->amount, 2) . " submitted successfully! Central Accounts team will verify your UTR and credit your balance.");
    }
}
