<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CentreWalletTransaction;
use App\Models\Exam;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationDispatchService
{
    /**
     * Dispatch student admission approval notification via System Notification + WhatsApp/SMS.
     */
    public static function dispatchAdmissionApproval(Student $student, ?string $plainPassword = null): array
    {
        $mobile = $student->mobile;
        $name = $student->full_name;
        $rollNo = $student->roll_no;
        $regNo = $student->registration_no;
        $courseTitle = $student->admissions->first()?->course?->course_name ?? 'Computer Course';
        $loginUrl = url('/student');
        $passwordText = $plainPassword ? " Temporary Password: {$plainPassword}." : " Default password is your mobile number.";

        $message = "Dear {$name}, congratulations! Your admission at TECH ERA COMPUTER for {$courseTitle} has been officially approved. Reg No: {$regNo}, Roll No: {$rollNo}. Access your Student Portal at {$loginUrl}.{$passwordText} - TECH ERA COMPUTER";

        // 1. Create System Notification for Student
        if ($student->user_id) {
            SystemNotification::create([
                'user_id' => $student->user_id,
                'title' => 'Admission Approved & Portal Activated',
                'message' => "Welcome to TECH ERA COMPUTER! Your Roll No is {$rollNo} and Reg No is {$regNo}.",
                'type' => 'system',
                'action_url' => '/student/profile',
                'data' => [
                    'student_id' => $student->id,
                    'roll_no' => $rollNo,
                    'registration_no' => $regNo,
                ],
            ]);
        }

        // 2. Dispatch simulated/live WhatsApp & SMS
        $dispatchResult = self::sendRaw($mobile, $message, 'both');

        return [
            'type' => 'admission_approval',
            'recipient' => $mobile,
            'message' => $message,
            'dispatch' => $dispatchResult,
        ];
    }

    /**
     * Dispatch fee payment confirmation and receipt alert.
     */
    public static function dispatchFeeReceipt(PaymentReceipt $receipt): array
    {
        $receipt->loadMissing(['student.user', 'admission.course', 'centre']);
        $student = $receipt->student;
        $mobile = $student?->mobile;
        $name = $student?->full_name ?? 'Student';
        $amount = number_format((float) $receipt->amount, 2);
        $receiptNo = $receipt->receipt_no;
        $centreName = $receipt->centre?->centre_name ?? 'TECH ERA COMPUTER';
        $receiptUrl = url("/student/fees/receipts/{$receipt->id}");

        $message = "Dear {$name}, your fee payment of Rs. {$amount} has been successfully received at {$centreName}. Receipt No: {$receiptNo}. Download your computerized receipt: {$receiptUrl}. - TECH ERA COMPUTER";

        // 1. Create System Notification for Student
        if ($student?->user_id) {
            SystemNotification::create([
                'user_id' => $student->user_id,
                'title' => "Fee Receipt Generated [{$receiptNo}]",
                'message' => "Payment of Rs. {$amount} recorded successfully. Mode: " . strtoupper($receipt->payment_mode),
                'type' => 'fee',
                'action_url' => "/student/fees/receipts/{$receipt->id}",
                'data' => [
                    'receipt_id' => $receipt->id,
                    'receipt_no' => $receiptNo,
                    'amount' => $receipt->amount,
                ],
            ]);
        }

        $dispatchResult = self::sendRaw($mobile, $message, 'both');

        return [
            'type' => 'fee_receipt',
            'recipient' => $mobile,
            'message' => $message,
            'dispatch' => $dispatchResult,
        ];
    }

    /**
     * Dispatch examination admit card release alert.
     */
    public static function dispatchAdmitCard(Exam $exam, Student $student): array
    {
        $mobile = $student->mobile;
        $name = $student->full_name;
        $examTitle = $exam->exam_title;
        $examDate = $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'Announced Date';
        $downloadUrl = url("/student/exams/{$exam->id}/admit-card");

        $message = "Dear {$name}, your Admit Card / Hall Ticket for {$examTitle} scheduled on {$examDate} has been released. Please download and print your hall ticket: {$downloadUrl}. - TECH ERA COMPUTER";

        if ($student->user_id) {
            SystemNotification::create([
                'user_id' => $student->user_id,
                'title' => "Admit Card Released - {$examTitle}",
                'message' => "Hall ticket for {$examTitle} ({$examDate}) is now ready for download.",
                'type' => 'exam',
                'action_url' => "/student/exams/{$exam->id}/admit-card",
                'data' => [
                    'exam_id' => $exam->id,
                    'exam_date' => $examDate,
                ],
            ]);
        }

        $dispatchResult = self::sendRaw($mobile, $message, 'both');

        return [
            'type' => 'admit_card',
            'recipient' => $mobile,
            'message' => $message,
            'dispatch' => $dispatchResult,
        ];
    }

    /**
     * Dispatch certificate and marksheet issuance alert.
     */
    public static function dispatchCertificateIssued(Certificate $certificate): array
    {
        $certificate->loadMissing(['student.user', 'course']);
        $student = $certificate->student;
        $mobile = $student?->mobile;
        $name = $student?->full_name ?? 'Student';
        $courseName = $certificate->course?->course_name ?? 'Diploma Course';
        $certNo = $certificate->certificate_no;
        $verifyUrl = url('/check-certificate-marksheet');

        $message = "Congratulations {$name}! Your official Certificate #{$certNo} for {$courseName} has been officially issued by Central Examination Board. Verify online at {$verifyUrl}. - TECH ERA COMPUTER";

        if ($student?->user_id) {
            SystemNotification::create([
                'user_id' => $student->user_id,
                'title' => "Official Certificate Issued [{$certNo}]",
                'message' => "Your certificate for {$courseName} has been verified and issued.",
                'type' => 'certificate',
                'action_url' => '/student/certificates',
                'data' => [
                    'certificate_id' => $certificate->id,
                    'certificate_no' => $certNo,
                ],
            ]);
        }

        $dispatchResult = self::sendRaw($mobile, $message, 'both');

        return [
            'type' => 'certificate_issued',
            'recipient' => $mobile,
            'message' => $message,
            'dispatch' => $dispatchResult,
        ];
    }

    /**
     * Dispatch Centre Wallet recharge alert to Centre Director.
     */
    public static function dispatchWalletAlert(CentreWalletTransaction $txn): array
    {
        $txn->loadMissing(['centre.user']);
        $centre = $txn->centre;
        $mobile = $centre?->mobile;
        $centreName = $centre?->centre_name ?? 'Study Centre';
        $type = strtoupper($txn->type);
        $amount = number_format((float) $txn->amount, 2);
        $balAfter = number_format((float) $txn->balance_after, 2);
        $ref = $txn->reference_no;

        $message = "Dear {$centreName}, your franchise wallet has been {$type}ed with Rs. {$amount}. Current Balance: Rs. {$balAfter}. Ref: {$ref}. - TECH ERA COMPUTER";

        if ($centre?->user_id) {
            SystemNotification::create([
                'user_id' => $centre->user_id,
                'title' => "Wallet {$type}: Rs. {$amount}",
                'message' => "Transaction Ref: {$ref}. Updated balance: Rs. {$balAfter}.",
                'type' => 'alert',
                'action_url' => '/centre/wallet',
                'data' => [
                    'transaction_id' => $txn->id,
                    'amount' => $txn->amount,
                    'type' => $txn->type,
                ],
            ]);
        }

        $dispatchResult = self::sendRaw($mobile, $message, 'both');

        return [
            'type' => 'wallet_alert',
            'recipient' => $mobile,
            'message' => $message,
            'dispatch' => $dispatchResult,
        ];
    }

    /**
     * Dispatch Support Ticket reply notification to relevant party.
     */
    public static function dispatchTicketReply(\App\Models\SupportTicket $ticket, \App\Models\SupportTicketReply $reply): array
    {
        $ticket->loadMissing(['user', 'centre', 'assignee']);

        // If staff replied, notify ticket creator (student/centre)
        if ($reply->is_staff_reply) {
            $recipientUser = $ticket->user;
            $url = ($recipientUser && $recipientUser->hasRole('student'))
                ? "/student/support/{$ticket->id}"
                : "/centre/support/{$ticket->id}";
            $title = "Support Reply: Ticket #{$ticket->ticket_no}";
            $message = "Staff has responded to your ticket regarding '{$ticket->subject}'.";

            if ($recipientUser) {
                SystemNotification::create([
                    'user_id' => $recipientUser->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'system',
                    'action_url' => $url,
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'ticket_no' => $ticket->ticket_no,
                        'reply_id' => $reply->id,
                    ],
                ]);
            }

            $dispatchResult = self::sendRaw($recipientUser?->mobile, "Dear {$recipientUser?->name}, you have a new response on your support ticket #{$ticket->ticket_no} at TECH ERA COMPUTER. View details: " . url($url), 'both');
        } else {
            // Student or centre user replied, notify assigned staff or super admin
            $recipientUser = $ticket->assignee ?? \App\Models\User::where('role', 'super_admin')->first();
            $url = "/admin/support/{$ticket->id}";
            $title = "New Reply on Ticket #{$ticket->ticket_no}";
            $message = "{$reply->user?->name} replied to ticket '{$ticket->subject}'.";

            if ($recipientUser) {
                SystemNotification::create([
                    'user_id' => $recipientUser->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'system',
                    'action_url' => $url,
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'ticket_no' => $ticket->ticket_no,
                        'reply_id' => $reply->id,
                    ],
                ]);
            }

            $dispatchResult = self::sendRaw($recipientUser?->mobile, "Support Ticket #{$ticket->ticket_no} updated with new user reply. Review at: " . url($url), 'both');
        }

        return [
            'type' => 'ticket_reply',
            'recipient' => $recipientUser?->mobile,
            'message' => $message,
            'dispatch' => $dispatchResult,
        ];
    }

    /**
     * Underlying transport: dispatches to WhatsApp and/or SMS gateway.
     * Uses configured webhook/API endpoint or sandbox simulation with structured logging.
     */
    public static function sendRaw(?string $mobile, string $message, string $channel = 'both'): array
    {
        if (empty($mobile)) {
            return [
                'status' => 'skipped',
                'reason' => 'No mobile number provided',
            ];
        }

        $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
        $whatsappApiUrl = config('services.whatsapp.api_url');
        $whatsappToken = config('services.whatsapp.token');
        $smsApiUrl = config('services.sms.api_url');
        $smsApiKey = config('services.sms.api_key');

        $results = [];

        // WhatsApp Transport
        if (in_array($channel, ['whatsapp', 'both'])) {
            if (!empty($whatsappApiUrl) && !empty($whatsappToken)) {
                try {
                    $response = Http::withToken($whatsappToken)
                        ->timeout(5)
                        ->post($whatsappApiUrl, [
                            'to' => $cleanMobile,
                            'message' => $message,
                        ]);
                    $results['whatsapp'] = [
                        'status' => $response->successful() ? 'sent' : 'failed',
                        'response' => $response->json(),
                    ];
                } catch (\Throwable $e) {
                    Log::error("WhatsApp dispatch error to {$cleanMobile}: " . $e->getMessage());
                    $results['whatsapp'] = ['status' => 'error', 'message' => $e->getMessage()];
                }
            } else {
                // Sandbox simulation
                Log::info("[NOTIFICATION][WHATSAPP] To: {$cleanMobile} | Msg: {$message}");
                $results['whatsapp'] = [
                    'status' => 'simulated',
                    'mobile' => $cleanMobile,
                    'driver' => 'log_sandbox',
                    'timestamp' => now()->toIso8601String(),
                ];
            }
        }

        // SMS Transport
        if (in_array($channel, ['sms', 'both'])) {
            if (!empty($smsApiUrl) && !empty($smsApiKey)) {
                try {
                    $response = Http::timeout(5)->post($smsApiUrl, [
                        'apikey' => $smsApiKey,
                        'mobile' => $cleanMobile,
                        'message' => $message,
                        'sender' => config('services.sms.sender_id', 'DRSHTI'),
                    ]);
                    $results['sms'] = [
                        'status' => $response->successful() ? 'sent' : 'failed',
                        'response' => $response->json(),
                    ];
                } catch (\Throwable $e) {
                    Log::error("SMS dispatch error to {$cleanMobile}: " . $e->getMessage());
                    $results['sms'] = ['status' => 'error', 'message' => $e->getMessage()];
                }
            } else {
                // Sandbox simulation
                Log::info("[NOTIFICATION][SMS] To: {$cleanMobile} | Msg: {$message}");
                $results['sms'] = [
                    'status' => 'simulated',
                    'mobile' => $cleanMobile,
                    'driver' => 'log_sandbox',
                    'timestamp' => now()->toIso8601String(),
                ];
            }
        }

        return $results;
    }
}
