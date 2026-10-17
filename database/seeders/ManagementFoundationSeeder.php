<?php

namespace Database\Seeders;

use App\Models\AdmissionApplication;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\MarketingExpense;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ManagementFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $centre = Centre::first();
        $student = Student::first();
        $batch = Batch::first();
        $user = User::where('role', 'super_admin')->first() ?? User::first();

        // 1. Fee Structures for All Courses
        $courses = Course::all();
        foreach ($courses as $c) {
            FeeStructure::firstOrCreate(
                ['course_id' => $c->id, 'centre_id' => null, 'name' => 'Standard Institute Fee Plan'],
                [
                    'total_fee' => $c->default_course_fee > 0 ? $c->default_course_fee : 9000.00,
                    'admission_fee' => 1500.00,
                    'tuition_fee' => max(0, ($c->default_course_fee > 0 ? $c->default_course_fee : 9000.00) - 2500.00),
                    'exam_fee' => 500.00,
                    'certificate_fee' => 500.00,
                    'installments_count' => 3,
                    'terms' => 'Examination fee and certificate fee included in total amount. Non-transferable.',
                    'is_active' => true,
                ]
            );
        }

        // 2. Payment Receipt for existing FeePayment
        $payments = FeePayment::all();
        foreach ($payments as $p) {
            PaymentReceipt::firstOrCreate(
                ['fee_payment_id' => $p->id],
                [
                    'receipt_no' => $p->receipt_no,
                    'student_id' => $p->student_id,
                    'admission_id' => $p->admission_id,
                    'centre_id' => $p->centre_id,
                    'amount' => $p->amount,
                    'payment_mode' => $p->payment_mode,
                    'receipt_date' => $p->payment_date,
                    'qr_token' => hash('sha256', "RECEIPT:{$p->receipt_no}:{$p->amount}:{$p->payment_date}"),
                    'issued_by' => $p->collected_by ?? $user?->id,
                    'notes' => 'Computerized official receipt generated upon fee settlement.',
                ]
            );
        }

        // 3. Study Materials
        $adcaCourse = Course::where('slug', 'advance-diploma-in-computer-application')->first() ?? $courses->first();
        if ($adcaCourse) {
            StudyMaterial::firstOrCreate(
                ['slug' => 'adca-module-1-computer-fundamentals-notes'],
                [
                    'centre_id' => null, // Global Board Material
                    'course_id' => $adcaCourse->id,
                    'title' => 'ADCA Module 1: Computer Fundamentals & OS Architecture',
                    'material_type' => 'pdf',
                    'file_path' => 'materials/adca_module1_fundamentals.pdf',
                    'file_size' => '3.4 MB',
                    'description' => 'Complete theory syllabus, hardware concepts, memory hierarchy, and MS-DOS / Windows commands.',
                    'uploaded_by' => $user?->id,
                    'status' => true,
                ]
            );

            StudyMaterial::firstOrCreate(
                ['slug' => 'tally-prime-gst-practical-guide'],
                [
                    'centre_id' => null,
                    'course_id' => $adcaCourse->id,
                    'title' => 'Tally Prime with GST: Practical Voucher Entry & Balance Sheet',
                    'material_type' => 'pdf',
                    'file_path' => 'materials/tally_prime_gst_guide.pdf',
                    'file_size' => '4.8 MB',
                    'description' => 'Step-by-step transaction walkthroughs for IGST/CGST/SGST, E-way bill generation, and financial reporting.',
                    'uploaded_by' => $user?->id,
                    'status' => true,
                ]
            );
        }

        // 4. Sample Admission Applications
        if ($centre && $adcaCourse) {
            AdmissionApplication::firstOrCreate(
                ['application_no' => 'APP-2026-N0192'],
                [
                    'centre_id' => $centre->id,
                    'course_id' => $adcaCourse->id,
                    'applicant_name' => 'Vikram Aditya Singh',
                    'father_name' => 'Manoj Singh',
                    'mother_name' => 'Suman Devi',
                    'dob' => '2004-06-15',
                    'gender' => 'male',
                    'mobile' => '9811223344',
                    'email' => 'vikram.aditya@example.com',
                    'state_id' => $centre->state_id,
                    'district_id' => $centre->district_id,
                    'city' => $centre->city,
                    'address' => 'Sector 62, Near Electronic City',
                    'pincode' => '201309',
                    'highest_qualification' => 'Bachelor of Commerce (Pursuing)',
                    'status' => 'pending',
                ]
            );
        }

        // 5. Sample Attendance Records
        if ($centre && $batch && $student) {
            for ($i = 0; $i < 5; $i++) {
                $date = now()->subDays($i)->toDateString();
                Attendance::firstOrCreate(
                    [
                        'batch_id' => $batch->id,
                        'student_id' => $student->id,
                        'attendance_date' => $date,
                    ],
                    [
                        'centre_id' => $centre->id,
                        'status' => $i === 3 ? 'absent' : 'present',
                        'in_time' => '10:00:00',
                        'out_time' => '11:30:00',
                        'remarks' => $i === 3 ? 'Informed medical leave' : 'Regular Lab Session',
                        'marked_by' => $user?->id,
                    ]
                );
            }
        }

        // 6. Support Tickets
        if ($user && $centre) {
            $ticket = SupportTicket::firstOrCreate(
                ['ticket_no' => 'TCK-2026-8812'],
                [
                    'user_id' => $user->id,
                    'centre_id' => $centre->id,
                    'category' => 'certificates',
                    'priority' => 'high',
                    'subject' => 'Request for expedited certificate dispatch for Batch 2025-Q4',
                    'description' => 'The final exam evaluation has completed and marks have been approved. Kindly dispatch physical certificates for 15 successful candidates.',
                    'status' => 'in_progress',
                    'last_reply_at' => now(),
                ]
            );

            SupportTicketReply::firstOrCreate(
                [
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                ],
                [
                    'message' => 'Central Board has verified the marks. The certificates are queued for security hologram stamping.',
                    'is_staff_reply' => true,
                ]
            );
        }

        // 7. Centre Expenses & Marketing Expenses
        if ($centre && $user) {
            Expense::firstOrCreate(
                ['reference_no' => 'EXP-2026-1001-01'],
                [
                    'centre_id' => $centre->id,
                    'category' => 'rent',
                    'title' => 'Lab & Classroom Premises Monthly Lease',
                    'amount' => 25000.00,
                    'expense_date' => now()->startOfMonth()->toDateString(),
                    'payment_mode' => 'bank_transfer',
                    'notes' => 'Commercial premises rent paid via NEFT to landlord.',
                    'recorded_by' => $user->id,
                ]
            );

            Expense::firstOrCreate(
                ['reference_no' => 'EXP-2026-1001-02'],
                [
                    'centre_id' => $centre->id,
                    'category' => 'utilities',
                    'title' => 'High-Speed Fiber Internet & Electricity',
                    'amount' => 6450.00,
                    'expense_date' => now()->subDays(5)->toDateString(),
                    'payment_mode' => 'upi',
                    'notes' => 'Airtel Broadband & Power Bill for Computer Lab.',
                    'recorded_by' => $user->id,
                ]
            );

            MarketingExpense::firstOrCreate(
                ['campaign_name' => 'Noida Cyber City Career Fest 2026'],
                [
                    'centre_id' => $centre->id,
                    'platform' => 'pamphlets',
                    'budget' => 15000.00,
                    'amount_spent' => 12500.00,
                    'start_date' => now()->subDays(20)->toDateString(),
                    'end_date' => now()->subDays(5)->toDateString(),
                    'leads_generated' => 148,
                    'conversions_count' => 32,
                    'notes' => 'Distribution of full course brochures at colleges and metro stations.',
                    'recorded_by' => $user->id,
                ]
            );
        }
    }
}
