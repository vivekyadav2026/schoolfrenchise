<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => [
                'display_name' => 'Super Administrator',
                'description' => 'Complete nationwide control over all centres, students, academics, finance, and system settings.',
            ],
            'admin' => [
                'display_name' => 'Central Board Administrator',
                'description' => 'Head office staff managing curriculum, franchise approvals, results verification, and certificate dispatch.',
            ],
            'centre' => [
                'display_name' => 'Franchise Centre Director',
                'description' => 'Authorized centre director managing local branch operations, admissions, fees, and attendance.',
            ],
            'staff' => [
                'display_name' => 'Centre Instructor / Staff',
                'description' => 'Branch instructor or faculty taking classes, marking attendance, and entering test marks.',
            ],
            'student' => [
                'display_name' => 'Enrolled Student',
                'description' => 'Student accessing self-service dashboard, ID card, fee receipts, admit cards, and certificates.',
            ],
        ];

        $roleModels = [];
        foreach ($roles as $name => $meta) {
            $roleModels[$name] = Role::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $meta['display_name'],
                    'description' => $meta['description'],
                ]
            );
        }

        $permissionsByModule = [
            'centres' => [
                'view_centres' => 'View Study Centres',
                'create_centres' => 'Create New Study Centre',
                'edit_centres' => 'Edit Study Centre Details',
                'approve_franchise' => 'Approve or Reject Franchise Applications',
                'manage_centre_wallet' => 'Manage Centre Wallet & Credits',
            ],
            'students' => [
                'view_students' => 'View Student Records',
                'create_students' => 'Admit New Student',
                'edit_students' => 'Edit Student Profile',
                'delete_students' => 'Delete/Deactivate Student',
                'view_own_student' => 'View Own Student Profile',
            ],
            'admissions' => [
                'view_admissions' => 'View Admissions',
                'create_admissions' => 'Process Course Admission',
                'review_applications' => 'Review Online Admission Applications',
            ],
            'fees' => [
                'view_fees' => 'View Fee Records',
                'collect_fees' => 'Collect Fee Installments & Issue Receipts',
                'manage_fee_structures' => 'Configure Course Fee Structures',
                'view_own_fees' => 'View Own Fee Ledger & Download Receipts',
            ],
            'academics' => [
                'manage_courses' => 'Create & Edit Courses and Syllabus',
                'manage_batches' => 'Manage Student Batches & Timings',
                'manage_study_materials' => 'Upload & Manage Study Materials',
                'download_study_materials' => 'Download Study Materials',
            ],
            'attendance' => [
                'mark_attendance' => 'Take Student Daily Attendance',
                'view_attendance' => 'View Batch Attendance Reports',
                'view_own_attendance' => 'View Personal Attendance Record',
            ],
            'exams' => [
                'schedule_exams' => 'Schedule Examinations & Generate Admit Cards',
                'enter_marks' => 'Enter Module Marks (Theory/Practical/Viva)',
                'approve_results' => 'Approve Exam Results & Final Grades',
                'issue_certificates' => 'Generate Official Marksheet & Certificate',
                'view_own_results' => 'View Own Results & Certificates',
            ],
            'finance' => [
                'manage_expenses' => 'Record Branch Expenses',
                'manage_marketing' => 'Record Marketing Campaigns & Costs',
                'view_financial_reports' => 'View Institute Financial Reports',
            ],
            'support' => [
                'manage_tickets' => 'Manage Support Desk Tickets',
                'create_ticket' => 'Create Support Ticket',
                'reply_ticket' => 'Reply to Support Ticket',
            ],
            'system' => [
                'manage_settings' => 'Configure Institute Profile & Seals',
                'manage_cms' => 'Manage Notices, Sliders & Gallery',
                'view_audit_logs' => 'View Security Audit Logs',
            ],
        ];

        $permissionModels = [];
        foreach ($permissionsByModule as $module => $perms) {
            foreach ($perms as $permName => $displayName) {
                $permissionModels[$permName] = Permission::firstOrCreate(
                    ['name' => $permName],
                    [
                        'display_name' => $displayName,
                        'module' => $module,
                    ]
                );
            }
        }

        // Assign permissions to Super Admin (All)
        $roleModels['super_admin']->permissions()->sync(array_column($permissionModels, 'id'));

        // Assign permissions to Central Admin
        $adminPerms = [
            'view_centres', 'edit_centres', 'approve_franchise', 'manage_centre_wallet',
            'view_students', 'view_admissions', 'review_applications',
            'view_fees', 'manage_fee_structures', 'manage_courses', 'manage_batches',
            'manage_study_materials', 'download_study_materials', 'view_attendance',
            'schedule_exams', 'approve_results', 'issue_certificates',
            'manage_expenses', 'manage_marketing', 'view_financial_reports',
            'manage_tickets', 'create_ticket', 'reply_ticket',
            'manage_settings', 'manage_cms', 'view_audit_logs',
        ];
        $roleModels['admin']->permissions()->sync(
            array_intersect_key($permissionModels, array_flip($adminPerms))
        );

        // Assign permissions to Centre Manager
        $centrePerms = [
            'view_students', 'create_students', 'edit_students',
            'view_admissions', 'create_admissions', 'review_applications',
            'view_fees', 'collect_fees', 'manage_batches',
            'manage_study_materials', 'download_study_materials',
            'mark_attendance', 'view_attendance',
            'enter_marks', 'manage_expenses', 'manage_marketing',
            'create_ticket', 'reply_ticket',
        ];
        $roleModels['centre']->permissions()->sync(
            array_intersect_key($permissionModels, array_flip($centrePerms))
        );

        // Assign permissions to Staff
        $staffPerms = [
            'view_students', 'view_admissions', 'mark_attendance', 'view_attendance',
            'enter_marks', 'download_study_materials', 'create_ticket', 'reply_ticket',
        ];
        $roleModels['staff']->permissions()->sync(
            array_intersect_key($permissionModels, array_flip($staffPerms))
        );

        // Assign permissions to Student
        $studentPerms = [
            'view_own_student', 'view_own_fees', 'download_study_materials',
            'view_own_attendance', 'view_own_results', 'create_ticket', 'reply_ticket',
        ];
        $roleModels['student']->permissions()->sync(
            array_intersect_key($permissionModels, array_flip($studentPerms))
        );

        // Synchronize all existing users with their role records
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            $userRole = $user->role ?? 'student';
            if (isset($roleModels[$userRole])) {
                $user->roles()->syncWithoutDetaching([$roleModels[$userRole]->id]);
            }
        }
    }
}
