<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\OnlineTransaction;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndQualityAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $centreUser1;
    protected Centre $centre1;
    protected User $centreUser2;
    protected Centre $centre2;
    protected User $studentUser1;
    protected Student $student1;
    protected User $studentUser2;
    protected Student $student2;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $centreRole = Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Centre Director']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);

        $state = DB::table('states')->first();
        $stateId = $state ? $state->id : DB::table('states')->insertGetId(['name' => 'Audit State', 'status' => 1]);
        $district = DB::table('districts')->where('state_id', $stateId)->first();
        $districtId = $district ? $district->id : DB::table('districts')->insertGetId(['state_id' => $stateId, 'name' => 'Audit City', 'status' => 1]);

        $this->course = Course::first() ?? Course::create([
            'course_code' => 'ADCA-AUDIT',
            'course_name' => 'Advanced Diploma in Computer Applications',
            'duration_months' => 12,
            'slug' => 'adca-audit',
            'eligibility' => '10+2',
            'is_active' => true,
        ]);

        // Centre 1
        $this->centre1 = Centre::create([
            'centre_code' => 'DCE-AUDIT-01',
            'centre_name' => 'Drishti Audit Centre Alpha',
            'director_name' => 'Alpha Director',
            'mobile' => '9888800001',
            'email' => 'centre1@audit.com',
            'state_id' => $stateId,
            'district_id' => $districtId,
            'city' => 'Audit City',
            'pincode' => '800001',
            'affiliation_date' => now()->toDateString(),
            'valid_upto' => now()->addYear()->toDateString(),
            'address' => 'Alpha Campus Road',
            'status' => 'active',
        ]);
        $this->centreUser1 = User::create([
            'name' => 'Alpha Director',
            'email' => 'centre1@audit.com',
            'mobile' => '9888800001',
            'password' => Hash::make('Password@123'),
            'centre_id' => $this->centre1->id,
            'is_active' => true,
        ]);
        $this->centreUser1->roles()->sync([$centreRole->id]);

        // Centre 2
        $this->centre2 = Centre::create([
            'centre_code' => 'DCE-AUDIT-02',
            'centre_name' => 'Drishti Audit Centre Beta',
            'director_name' => 'Beta Director',
            'mobile' => '9888800002',
            'email' => 'centre2@audit.com',
            'state_id' => $stateId,
            'district_id' => $districtId,
            'city' => 'Audit City',
            'pincode' => '800002',
            'affiliation_date' => now()->toDateString(),
            'valid_upto' => now()->addYear()->toDateString(),
            'address' => 'Beta Campus Road',
            'status' => 'active',
        ]);
        $this->centreUser2 = User::create([
            'name' => 'Beta Director',
            'email' => 'centre2@audit.com',
            'mobile' => '9888800002',
            'password' => Hash::make('Password@123'),
            'centre_id' => $this->centre2->id,
            'is_active' => true,
        ]);
        $this->centreUser2->roles()->sync([$centreRole->id]);

        // Super Admin
        $this->superAdmin = User::create([
            'name' => 'Audit Super Admin',
            'email' => 'superadmin@audit.com',
            'mobile' => '9888800099',
            'password' => Hash::make('Password@123'),
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->sync([$superAdminRole->id]);

        // Student 1 (Centre 1)
        $this->studentUser1 = User::create([
            'name' => 'Student Alpha',
            'email' => 'student1@audit.com',
            'mobile' => '9777700001',
            'password' => Hash::make('Password@123'),
            'centre_id' => $this->centre1->id,
            'is_active' => true,
        ]);
        $this->studentUser1->roles()->sync([$studentRole->id]);

        $this->student1 = Student::create([
            'user_id' => $this->studentUser1->id,
            'centre_id' => $this->centre1->id,
            'roll_no' => 'DCE-AUD-S1',
            'registration_no' => 'DCE/2026/AUD/001',
            'full_name' => 'Student Alpha',
            'father_name' => 'Father Alpha',
            'mother_name' => 'Mother Alpha',
            'dob' => '2004-01-15',
            'gender' => 'male',
            'mobile' => '9777700001',
            'email' => 'student1@audit.com',
            'state_id' => $stateId,
            'district_id' => $districtId,
            'city' => 'Audit City',
            'address' => 'Alpha Colony',
            'pincode' => '800001',
            'status' => 'enrolled',
            'admission_date' => now(),
        ]);

        // Student 2 (Centre 2)
        $this->studentUser2 = User::create([
            'name' => 'Student Beta',
            'email' => 'student2@audit.com',
            'mobile' => '9777700002',
            'password' => Hash::make('Password@123'),
            'centre_id' => $this->centre2->id,
            'is_active' => true,
        ]);
        $this->studentUser2->roles()->sync([$studentRole->id]);

        $this->student2 = Student::create([
            'user_id' => $this->studentUser2->id,
            'centre_id' => $this->centre2->id,
            'roll_no' => 'DCE-AUD-S2',
            'registration_no' => 'DCE/2026/AUD/002',
            'full_name' => 'Student Beta',
            'father_name' => 'Father Beta',
            'mother_name' => 'Mother Beta',
            'dob' => '2004-05-20',
            'gender' => 'female',
            'mobile' => '9777700002',
            'email' => 'student2@audit.com',
            'state_id' => $stateId,
            'district_id' => $districtId,
            'city' => 'Audit City',
            'address' => 'Beta Colony',
            'pincode' => '800002',
            'status' => 'enrolled',
            'admission_date' => now(),
        ]);
    }

    /**
     * Audit 1: HTTP Security Headers are present on web responses.
     */
    public function test_security_headers_are_attached_to_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Audit 2: Role-based access control and unauthorized redirection.
     */
    public function test_unauthenticated_and_unauthorized_role_boundaries(): void
    {
        // Unauthenticated access
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/centre/dashboard')->assertRedirect(route('login'));
        $this->get('/student/dashboard')->assertRedirect(route('student.login'));

        // Student attempting to access Admin & Centre portals
        $this->actingAs($this->studentUser1);
        $this->get('/admin/dashboard')->assertStatus(403);
        $this->get('/centre/dashboard')->assertStatus(403);

        // Centre Director attempting to access Admin & Student portals
        $this->actingAs($this->centreUser1);
        $this->get('/admin/dashboard')->assertStatus(403);
        $this->get('/student/dashboard')->assertStatus(403);

        // Suspended Centre Director blocked and redirected to login with notice
        $this->centre1->update(['status' => 'suspended']);
        $this->get('/centre/dashboard')->assertRedirect(route('login'));
    }

    /**
     * Audit 3: IDOR protection between students (Receipts, Marksheets, Support Tickets).
     */
    public function test_student_cross_tenant_idor_isolation(): void
    {
        $batch = Batch::create([
            'centre_id' => $this->centre2->id,
            'course_id' => $this->course->id,
            'batch_name' => 'Audit Batch 2',
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
            'days' => 'Mon-Sat',
            'status' => 'active',
        ]);

        $admission2 = Admission::create([
            'student_id' => $this->student2->id,
            'course_id' => $this->course->id,
            'centre_id' => $this->centre2->id,
            'batch_id' => $batch->id,
            'admission_no' => 'ADM-AUD-S2',
            'admission_date' => now(),
            'course_fee' => 5000,
            'total_fee' => 5000,
            'net_fee' => 5000,
            'total_paid' => 2000,
            'status' => 'active',
        ]);

        $payment2 = FeePayment::create([
            'admission_id' => $admission2->id,
            'student_id' => $this->student2->id,
            'centre_id' => $this->centre2->id,
            'receipt_no' => 'RCP-AUD-002',
            'amount' => 2000,
            'payment_date' => now(),
            'payment_mode' => 'cash',
            'status' => 'cleared',
        ]);

        $ticket2 = SupportTicket::create([
            'ticket_no' => 'TCK-AUD-S2',
            'user_id' => $this->studentUser2->id,
            'centre_id' => $this->centre2->id,
            'subject' => 'Secret inquiry of Student 2',
            'category' => 'academic',
            'priority' => 'medium',
            'status' => 'open',
            'description' => 'Confidential inquiry content',
        ]);

        // Student 1 tries to access Student 2's receipt (Policy returns 403 Forbidden)
        $this->actingAs($this->studentUser1);
        $response = $this->get('/student/fees/receipts/' . $payment2->id);
        $response->assertStatus(403);

        // Student 1 tries to access Student 2's support ticket (Scoping returns 404 Not Found to prevent enumeration)
        $ticketResponse = $this->get('/student/support/' . $ticket2->id);
        $ticketResponse->assertStatus(404);

        // Student 1 tries to reply to Student 2's support ticket (Returns 404 Not Found)
        $replyResponse = $this->post('/student/support/' . $ticket2->id . '/reply', [
            'reply' => 'Malicious injection into Student 2 ticket',
        ]);
        $replyResponse->assertStatus(404);
    }

    /**
     * Audit 4: IDOR protection between Centre Directors.
     */
    public function test_centre_cross_tenant_idor_isolation(): void
    {
        // Centre Director 1 attempts to access Centre 2's student dossier
        $this->actingAs($this->centreUser1);

        $response = $this->get('/centre/students/' . $this->student2->id);
        $response->assertStatus(403);

        $idCardResponse = $this->get('/centre/students/' . $this->student2->id . '/id-card');
        $idCardResponse->assertStatus(403);

        $admissionFormResponse = $this->get('/centre/students/' . $this->student2->id . '/admission-form');
        $admissionFormResponse->assertStatus(403);
    }

    /**
     * Audit 5: Academic access restrictions - Unpublished and withheld results are blocked.
     */
    public function test_academic_results_publication_restrictions(): void
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_code'    => 'EXM-AUD-' . uniqid(),
            'exam_title'   => 'Unpublished Internal Exam',
            'session_name' => '2026',
            'exam_date'    => now()->toDateString(),
            'status'       => 'scheduled', // 'scheduled' is not published
        ]);

        $result = ExamResult::create([
            'course_id'            => $this->course->id,
            'centre_id'            => $this->centre1->id,
            'exam_id'              => $exam->id,
            'student_id'           => $this->student1->id,
            'total_max_marks'      => 100,
            'total_marks_obtained' => 85,
            'percentage'           => 85.00,
            'grade'                => 'A+',
            'result_status'        => 'passed',
        ]);

        $this->actingAs($this->studentUser1);

        // Marksheet for unpublished exam must return 403
        $response = $this->get('/student/results/' . $result->id . '/marksheet');
        $response->assertStatus(403);

        // Now publish exam but withhold student's result
        $exam->update(['status' => 'published']);
        $result->update(['result_status' => 'withheld']);

        $responseWithheld = $this->get('/student/results/' . $result->id . '/marksheet');
        $responseWithheld->assertStatus(403);
    }

    /**
     * Audit 6: Study material course and centre scoping.
     */
    public function test_study_materials_course_scoping(): void
    {
        $otherCourse = Course::create([
            'course_code' => 'PYTHON-AUDIT',
            'course_name' => 'Python Programming Masterclass',
            'duration'    => '6 Months',
            'slug'        => 'python-audit',
            'eligibility' => '10th',
            'status'      => true,
        ]);

        $material = StudyMaterial::create([
            'course_id'     => $otherCourse->id,
            'title'         => 'Python Advanced Notes',
            'slug'          => 'python-advanced-notes-' . uniqid(),
            'material_type' => 'pdf',
            'file_path'     => 'materials/python_notes.pdf',
            'file_size'     => 1024,
            'status'        => true,
        ]);

        // Student 1 is not enrolled in python course
        $this->actingAs($this->studentUser1);
        $response = $this->get('/student/study-materials/' . $material->id . '/download');
        $response->assertStatus(403);
    }

    /**
     * Audit 7: Public Certificate Verification privacy protection.
     * Candidate phone, email, and home address must never be displayed publicly.
     */
    public function test_public_certificate_verification_masks_personal_data(): void
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_code'    => 'EXM-AUD-CERT-' . uniqid(),
            'exam_title'   => 'Final Evaluation',
            'session_name' => '2026',
            'exam_date'    => now()->toDateString(),
            'status'       => 'published',
        ]);

        $result = ExamResult::create([
            'course_id'            => $this->course->id,
            'centre_id'            => $this->centre1->id,
            'exam_id'              => $exam->id,
            'student_id'           => $this->student1->id,
            'total_max_marks'      => 100,
            'total_marks_obtained' => 88.50,
            'percentage'           => 88.50,
            'grade'                => 'A+',
            'result_status'        => 'passed',
        ]);

        $cert = Certificate::create([
            'student_id'         => $this->student1->id,
            'course_id'          => $this->course->id,
            'centre_id'          => $this->centre1->id,
            'exam_result_id'     => $result->id,
            'certificate_no'     => 'DCE/CERT/2026/AUD01',
            'marksheet_no'       => 'DCE/MS/2026/AUD01',
            'issue_date'         => now(),
            'grade'              => 'A+',
            'percentage'         => 88.50,
            'status'             => 'issued',
            'verification_token' => hash('sha256', 'DCE/CERT/2026/AUD01-SALT'),
        ]);

        // Search verification
        $response = $this->post('/check-certificate-marksheet', [
            'registration' => $this->student1->registration_no,
            'father'       => $this->student1->father_name,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Student Alpha');
        $response->assertSee('DCE/CERT/2026/AUD01');

        // Verify privacy protection
        $response->assertDontSee($this->student1->mobile);
        $response->assertDontSee($this->student1->email);

        // Direct QR destination
        $directResponse = $this->get('/verify/cert/' . $cert->certificate_no);
        $directResponse->assertStatus(200);
        $directResponse->assertSee('Student Alpha');
        $directResponse->assertDontSee($this->student1->mobile);
        $directResponse->assertDontSee($this->student1->email);
    }

    /**
     * Audit 8: File upload safety - Executable scripts and disallowed extensions are rejected.
     */
    public function test_file_upload_service_rejects_malicious_files(): void
    {
        $dangerousExtensions = ['php', 'exe', 'sh', 'phtml', 'bat', 'js'];

        foreach ($dangerousExtensions as $ext) {
            $fakeFile = UploadedFile::fake()->create("exploit.{$ext}", 50, 'application/octet-stream');
            try {
                FileUploadService::uploadDocument($fakeFile);
                $this->fail("FileUploadService must reject executable extension .{$ext}");
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('Dangerous file extension', $e->getMessage());
            }
        }

        // Oversized file rejection
        $oversizedFile = UploadedFile::fake()->create('large.pdf', 30000, 'application/pdf'); // 30MB
        try {
            FileUploadService::uploadDocument($oversizedFile, 'uploads/docs', 5120); // 5MB cap
            $this->fail('FileUploadService must reject files exceeding maximum size limit');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('exceeds maximum allowed limit', $e->getMessage());
        }
    }

    /**
     * Audit 9: Payment Gateway Signature Verification & Webhook Idempotency.
     */
    public function test_payment_gateway_signature_verification_and_idempotency(): void
    {
        // 1. Signature mismatch must fail
        $orderId = 'order_test_123456';
        $paymentId = 'pay_test_987654';
        $invalidSignature = 'completely_fake_signature_hash';

        $isValid = PaymentGatewayService::verifySignature($orderId, $paymentId, $invalidSignature);
        $this->assertFalse($isValid, 'Tampered payment signature must be rejected');

        // 2. Legitimate signature must pass
        $keySecret = PaymentGatewayService::getKeySecret();
        $payload = $orderId . '|' . $paymentId;
        $validSignature = hash_hmac('sha256', $payload, $keySecret);

        $isValidLegit = PaymentGatewayService::verifySignature($orderId, $paymentId, $validSignature);
        $this->assertTrue($isValidLegit, 'Authoritative HMAC signature must be verified successfully');

        // 3. Webhook idempotency
        $txn = OnlineTransaction::create([
            'transaction_id'     => 'TXN-AUDIT-IDEMP-01',
            'order_id'           => $orderId,
            'gateway_payment_id' => $paymentId,
            'amount'             => 1500,
            'currency'           => 'INR',
            'payer_name'         => 'Audit Payer',
            'payer_mobile'       => '9888800055',
            'purpose'            => 'Course Tuition Fee',
            'status'             => 'pending',
            'payment_mode'       => 'UPI',
        ]);

        $webhookPayload = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => $paymentId,
                        'order_id' => $orderId,
                        'amount' => 150000, // paise
                        'status' => 'captured',
                    ],
                ],
            ],
        ];

        $rawBody = json_encode($webhookPayload);
        $webhookSecret = PaymentGatewayService::getWebhookSecret();
        $signatureHeader = hash_hmac('sha256', $rawBody, $webhookSecret);

        // Process first webhook callback
        $firstResult = PaymentGatewayService::handleWebhook($rawBody, $signatureHeader);
        $this->assertTrue($firstResult['success']);
        $this->assertEquals('success', $firstResult['status']);

        $txn->refresh();
        $this->assertEquals('success', $txn->status);

        // Process identical duplicate webhook callback (replay attack or network duplicate)
        $secondResult = PaymentGatewayService::handleWebhook($rawBody, $signatureHeader);
        $this->assertTrue($secondResult['success']);
        $this->assertEquals('success', $secondResult['status']);
    }

    /**
     * Audit 10: Rate limiting middleware assigned to sensitive routes.
     */
    public function test_rate_limiting_assigned_to_sensitive_endpoints(): void
    {
        $routesToCheck = [
            'login.submit' => 'throttle:6,1',
            'student.login.submit' => 'throttle:6,1',
            'password.email' => 'throttle:6,1',
            'password.update' => 'throttle:6,1',
            'demo.request' => 'throttle:15,1',
            'contact.submit' => 'throttle:15,1',
            'enquiry.admission.submit' => 'throttle:15,1',
            'admission.apply.submit' => 'throttle:15,1',
            'franchise.apply.submit' => 'throttle:15,1',
            'verify.certificate.submit' => 'throttle:30,1',
            'verify.admission.submit' => 'throttle:30,1',
        ];

        foreach ($routesToCheck as $routeName => $expectedThrottle) {
            $route = Route::getRoutes()->getByName($routeName);
            $this->assertNotNull($route, "Route [{$routeName}] should exist");
            $middleware = $route->middleware();
            $this->assertTrue(
                in_array($expectedThrottle, $middleware),
                "Route [{$routeName}] must have middleware [{$expectedThrottle}]. Actual: " . implode(', ', $middleware)
            );
        }
    }

    /**
     * Audit 11: Public Navigation and Core Pages return 200 OK without errors.
     */
    public function test_public_routes_and_navigation_health(): void
    {
        $publicUrls = [
            '/',
            '/about-us',
            '/company',
            '/chairman',
            '/mission',
            '/corporate-members',
            '/facilities',
            '/gallery',
            '/contact-us',
            '/admission-enquiry',
            '/admission-apply',
            '/admission-status',
            '/check-admission',
            '/check-certificate-marksheet',
            '/online-payment',
            '/frenchise',
            '/search_centre',
            '/frenchise-status',
            '/courses',
            '/privacy-policy',
            '/terms-conditions',
            '/refund-policy',
            '/sitemap.xml',
        ];

        foreach ($publicUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }

    /**
     * Audit 12: Database migrations have down methods for rollback safety.
     */
    public function test_all_migrations_have_rollback_safety(): void
    {
        $migrationFiles = glob(database_path('migrations/*.php'));
        $this->assertNotEmpty($migrationFiles);

        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            $filename = basename($file);
            $hasDown = preg_match('/function\s+down\s*\(/', $content);
            $this->assertTrue((bool) $hasDown, "Migration [{$filename}] must define a down() method for rollback safety.");
        }
    }
}
