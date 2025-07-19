<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected static $statusIds;
    protected static $issueTypeIds;
    protected static $projectIds;

    protected static $userIds;
    protected static $userManagerIds;
    protected static $taskTemplates;

    public function definition(): array
    {
        // template task
        self::$taskTemplates ??= [
            ['name' => 'Design new homepage layout', 'description' => 'Create a fresh UI for the homepage section.', 'issue_type' => 'design', 'project_name' => 'Website Redesign'],
            ['name' => 'Fix broken footer links', 'description' => 'Resolve broken links in the footer area.', 'issue_type' => 'bug', 'project_name' => 'Website Redesign'],
            ['name' => 'Implement login form validation', 'description' => 'Add front-end and back-end validation for login.', 'issue_type' => 'feature', 'project_name' => 'User Authentication Module'],
            ['name' => 'Setup forgot password flow', 'description' => 'Allow users to recover password via email.', 'issue_type' => 'task', 'project_name' => 'User Authentication Module'],
            ['name' => 'Integrate PayPal gateway', 'description' => 'Add PayPal as a payment method.', 'issue_type' => 'feature', 'project_name' => 'Payment Gateway Integration'],
            ['name' => 'Fix currency conversion bug', 'description' => 'Resolve incorrect amount conversion.', 'issue_type' => 'bug', 'project_name' => 'Payment Gateway Integration'],
            ['name' => 'Add chart to user dashboard', 'description' => 'Implement visual data using charts.', 'issue_type' => 'feature', 'project_name' => 'Dashboard Feature'],
            ['name' => 'Style dashboard widgets', 'description' => 'Improve visual appearance of dashboard.', 'issue_type' => 'design', 'project_name' => 'Dashboard Feature'],
            ['name' => 'Build user API endpoints', 'description' => 'Create RESTful endpoints for users.', 'issue_type' => 'task', 'project_name' => 'API Development'],
            ['name' => 'Refactor API authentication', 'description' => 'Improve security of API tokens.', 'issue_type' => 'improvement', 'project_name' => 'API Development'],
            ['name' => 'Redesign settings page', 'description' => 'Create a better UX for settings.', 'issue_type' => 'design', 'project_name' => 'UI/UX Overhaul'],
            ['name' => 'Improve button consistency', 'description' => 'Unify button styles across app.', 'issue_type' => 'improvement', 'project_name' => 'UI/UX Overhaul'],
            ['name' => 'Fix UI layout in mobile view', 'description' => 'Resolve overlapping sections on mobile.', 'issue_type' => 'bug', 'project_name' => 'Mobile Responsive Update'],
            ['name' => 'Test responsive navbar', 'description' => 'Verify navbar works on all screens.', 'issue_type' => 'testing', 'project_name' => 'Mobile Responsive Update'],
            ['name' => 'Remove redundant CSS rules', 'description' => 'Clean up unused styles.', 'issue_type' => 'improvement', 'project_name' => 'Performance Optimization'],
            ['name' => 'Optimize image sizes', 'description' => 'Reduce large image file sizes.', 'issue_type' => 'task', 'project_name' => 'Performance Optimization'],
            ['name' => 'Fix XSS vulnerability', 'description' => 'Escape user input to prevent XSS.', 'issue_type' => 'bug', 'project_name' => 'Security Enhancement'],
            ['name' => 'Enforce strong passwords', 'description' => 'Add policy for complex passwords.', 'issue_type' => 'feature', 'project_name' => 'Security Enhancement'],
            ['name' => 'Design email notification template', 'description' => 'Create HTML layout for email alerts.', 'issue_type' => 'design', 'project_name' => 'Notification System'],
            ['name' => 'Add push notifications', 'description' => 'Implement push notifications using Firebase.', 'issue_type' => 'feature', 'project_name' => 'Notification System'],
            ['name' => 'Fix chart display in report', 'description' => 'Resolve chart rendering issues.', 'issue_type' => 'bug', 'project_name' => 'Reporting Module'],
            ['name' => 'Generate PDF report', 'description' => 'Allow reports to be downloaded as PDF.', 'issue_type' => 'feature', 'project_name' => 'Reporting Module'],
            ['name' => 'Test data import tool', 'description' => 'Validate migration process for edge cases.', 'issue_type' => 'testing', 'project_name' => 'Data Migration'],
            ['name' => 'Clean migrated records', 'description' => 'Remove corrupted entries.', 'issue_type' => 'support', 'project_name' => 'Data Migration'],
            ['name' => 'Write test cases for login', 'description' => 'Add automated tests for login scenarios.', 'issue_type' => 'testing', 'project_name' => 'Testing Automation'],
            ['name' => 'Setup PHPUnit configuration', 'description' => 'Configure PHPUnit for project.', 'issue_type' => 'task', 'project_name' => 'Testing Automation'],
            ['name' => 'Update meta tags for SEO', 'description' => 'Improve search engine discoverability.', 'issue_type' => 'improvement', 'project_name' => 'SEO Improvements'],
            ['name' => 'Fix broken sitemap.xml', 'description' => 'Regenerate sitemap for Google.', 'issue_type' => 'bug', 'project_name' => 'SEO Improvements'],
            ['name' => 'Normalize DB columns', 'description' => 'Apply normalization to reduce redundancy.', 'issue_type' => 'improvement', 'project_name' => 'Database Refactoring'],
            ['name' => 'Rename legacy tables', 'description' => 'Update table names to new conventions.', 'issue_type' => 'task', 'project_name' => 'Database Refactoring'],
            ['name' => 'Add role management UI', 'description' => 'Let admin manage roles via panel.', 'issue_type' => 'feature', 'project_name' => 'Admin Panel Upgrade'],
            ['name' => 'Fix panel scroll issue', 'description' => 'Resolve scrolling bug in settings.', 'issue_type' => 'bug', 'project_name' => 'Admin Panel Upgrade'],
            ['name' => 'Design client dashboard', 'description' => 'Create dashboard for clients to view updates.', 'issue_type' => 'design', 'project_name' => 'Client Portal'],
            ['name' => 'Fix client avatar upload bug', 'description' => 'Resolve file upload error.', 'issue_type' => 'bug', 'project_name' => 'Client Portal'],
            ['name' => 'Create feature flag system', 'description' => 'Add backend and frontend toggles.', 'issue_type' => 'feature', 'project_name' => 'Feature Toggle Implementation'],
            ['name' => 'Test feature toggle fallback', 'description' => 'Ensure features hide when disabled.', 'issue_type' => 'testing', 'project_name' => 'Feature Toggle Implementation'],
            ['name' => 'Setup Sentry integration', 'description' => 'Track errors via Sentry platform.', 'issue_type' => 'support', 'project_name' => 'Error Logging Setup'],
            ['name' => 'Log JavaScript errors', 'description' => 'Capture frontend JS errors.', 'issue_type' => 'task', 'project_name' => 'Error Logging Setup'],
            ['name' => 'Create CMS dashboard', 'description' => 'Design admin UI for content managers.', 'issue_type' => 'feature', 'project_name' => 'Content Management System'],
            ['name' => 'Fix rich text editor bug', 'description' => 'Resolve editor crash on paste.', 'issue_type' => 'bug', 'project_name' => 'Content Management System'],
            ['name' => 'Build chat backend API', 'description' => 'Create socket-based real-time API.', 'issue_type' => 'feature', 'project_name' => 'Real-time Chat Integration'],
            ['name' => 'Test chat reconnection logic', 'description' => 'Ensure chat works on reconnect.', 'issue_type' => 'testing', 'project_name' => 'Real-time Chat Integration'],
            ['name' => 'Connect to Mailchimp API', 'description' => 'Send users to Mailchimp lists.', 'issue_type' => 'feature', 'project_name' => 'Email Marketing Integration'],
            ['name' => 'Fix broken unsubscribe links', 'description' => 'Correct link in email footer.', 'issue_type' => 'bug', 'project_name' => 'Email Marketing Integration'],
            ['name' => 'Design analytics chart UI', 'description' => 'Create dashboard for usage data.', 'issue_type' => 'design', 'project_name' => 'Analytics Dashboard'],
            ['name' => 'Fix missing analytics data', 'description' => 'Resolve data aggregation errors.', 'issue_type' => 'bug', 'project_name' => 'Analytics Dashboard'],
            ['name' => 'Integrate GitHub API', 'description' => 'Pull repo data via GitHub API.', 'issue_type' => 'feature', 'project_name' => 'Third-party API Integration'],
            ['name' => 'Fix token expiration bug', 'description' => 'Refresh access tokens automatically.', 'issue_type' => 'bug', 'project_name' => 'Third-party API Integration'],
        ];


        // lay id 
        if (!self::$statusIds) {
            self::$statusIds = DB::table('statuses')->pluck('id')->toArray();
        }

        if (!self::$projectIds) {
            self::$projectIds = [];
            $temp = DB::table('projects')->pluck('id', 'name')->toArray();
            foreach ($temp as $name => $id) {
                $projectName = explode(' #', $name)[0]; //"UI/UX Overhaul #686a80b5499b5" => "UI/UX Overhaul"
                self::$projectIds[$projectName] = $id;
            }
        }

        if (!self::$issueTypeIds) {
            self::$issueTypeIds = DB::table('issue_types')->pluck('id', 'code')->toArray();
        }

        if (!self::$userManagerIds || !self::$userIds) {
            $rolesById = DB::table('users')->pluck('role', 'id'); //=> ['id'=> 'role']

            self::$userManagerIds = $rolesById->filter(fn($role) => $role === 'manager')->keys()->toArray();
            self::$userIds = $rolesById->filter(fn($role) => in_array($role, ['leader', 'member']))->keys()->toArray();
        }

        $template = $this->faker->randomElement(self::$taskTemplates);

        $projectId = self::$projectIds[$template['project_name']] ?? $this->faker->randomElement(array_values(self::$projectIds));
        $range = DB::table('projects')
            ->where('id', $projectId)
            ->select('start_date', 'due_date')
            ->first();
        $projectStart = Carbon::parse($range->start_date);
        $projectEnd = Carbon::parse($range->due_date);
        $startDate = $this->faker->dateTimeBetween($projectStart, $projectEnd->copy()->subDays(5));
        $dueDate = $this->faker->dateTimeBetween($startDate, $projectEnd);

        return [
            'name' => $template['name'] . ' #' . uniqid(),
            'description' => $template['description'],

            'status_id' => empty(self::$statusIds) ? null : $this->faker->randomElement(self::$statusIds),
            'issue_type_id' => self::$issueTypeIds[$template['issue_type']] ?? null,

            'created_by' => empty(self::$userManagerIds) ? null : $this->faker->randomElement(self::$userManagerIds),
            'assigned_to' => empty(self::$userIds) ? null : $this->faker->randomElement(self::$userIds),
            'project_id' => $projectId,

            'start_date' => $startDate,
            'due_date' => $dueDate,
        ];
    }
}
