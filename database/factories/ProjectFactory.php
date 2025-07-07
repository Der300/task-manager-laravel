<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use App\Models\Project\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Project::class;

    protected static $projectTemplates;

    protected static $statusIds;
    protected static $issueTypeIds;

    protected static $userAdminIds;
    protected static $userManagerIds;
    protected static $clientIds;

    public function definition(): array
    {
        // tao mau du an
        if (!self::$projectTemplates) {
            self::$projectTemplates = [
                ['name' => 'Website Redesign', 'description' => 'Improving the overall design and layout of the website.', 'issue_type' => 'design'],
                ['name' => 'User Authentication Module', 'description' => 'Implementing secure user login and registration functionality.', 'issue_type' => 'feature'],
                ['name' => 'Payment Gateway Integration', 'description' => 'Integrating third-party payment gateways for seamless transactions.', 'issue_type' => 'support'],
                ['name' => 'Dashboard Feature', 'description' => 'Developing new dashboard components for better data visualization.', 'issue_type' => 'feature'],
                ['name' => 'API Development', 'description' => 'Building RESTful APIs to support mobile and web clients.', 'issue_type' => 'feature'],
                ['name' => 'UI/UX Overhaul', 'description' => 'Revamping user interface and user experience for better usability.', 'issue_type' => 'design'],
                ['name' => 'Bug Fixing Sprint', 'description' => 'Fixing critical bugs identified in recent testing cycles.', 'issue_type' => 'bug'],
                ['name' => 'Mobile Responsive Update', 'description' => 'Making the website fully responsive across all devices.', 'issue_type' => 'improvement'],
                ['name' => 'Performance Optimization', 'description' => 'Enhancing website performance and load times.', 'issue_type' => 'improvement'],
                ['name' => 'Security Enhancement', 'description' => 'Strengthening security measures to protect user data.', 'issue_type' => 'improvement'],
                ['name' => 'Notification System', 'description' => 'Setting up real-time notification systems for users.', 'issue_type' => 'feature'],
                ['name' => 'Reporting Module', 'description' => 'Creating comprehensive reporting modules for analytics.', 'issue_type' => 'feature'],
                ['name' => 'Data Migration', 'description' => 'Migrating data from legacy systems to the new platform.', 'issue_type' => 'task'],
                ['name' => 'Testing Automation', 'description' => 'Automating test cases to improve release cycles.', 'issue_type' => 'testing'],
                ['name' => 'SEO Improvements', 'description' => 'Optimizing website content for better search engine ranking.', 'issue_type' => 'improvement'],
                ['name' => 'Database Refactoring', 'description' => 'Refactoring database schema for improved efficiency.', 'issue_type' => 'improvement'],
                ['name' => 'Admin Panel Upgrade', 'description' => 'Upgrading admin panel with new features and better UI.', 'issue_type' => 'feature'],
                ['name' => 'Client Portal', 'description' => 'Building a dedicated portal for client interactions.', 'issue_type' => 'feature'],
                ['name' => 'Feature Toggle Implementation', 'description' => 'Implementing feature toggles for easier deployment.', 'issue_type' => 'feature'],
                ['name' => 'Error Logging Setup', 'description' => 'Establishing centralized error logging and monitoring.', 'issue_type' => 'support'],
                ['name' => 'Content Management System', 'description' => 'Developing a CMS for easier content updates.', 'issue_type' => 'feature'],
                ['name' => 'Real-time Chat Integration', 'description' => 'Adding real-time chat support for users.', 'issue_type' => 'support'],
                ['name' => 'Email Marketing Integration', 'description' => 'Integrating email marketing tools with the system.', 'issue_type' => 'support'],
                ['name' => 'Analytics Dashboard', 'description' => 'Building dashboards for tracking user behavior and metrics.', 'issue_type' => 'feature'],
                ['name' => 'Third-party API Integration', 'description' => 'Connecting with external APIs for extended functionality.', 'issue_type' => 'support'],
                ['name' => 'Accessibility Improvements', 'description' => 'Enhancing website accessibility for all users.', 'issue_type' => 'improvement'],
                ['name' => 'Cloud Deployment Setup', 'description' => 'Configuring cloud infrastructure and deployment pipelines.', 'issue_type' => 'support'],
                ['name' => 'Backup and Recovery System', 'description' => 'Implementing automatic backups and recovery plans.', 'issue_type' => 'support'],
                ['name' => 'Load Testing', 'description' => 'Performing load tests to ensure scalability.', 'issue_type' => 'testing'],
                ['name' => 'User Role Management', 'description' => 'Managing permissions and user roles effectively.', 'issue_type' => 'feature'],
                ['name' => 'Session Management', 'description' => 'Improving session handling and security.', 'issue_type' => 'improvement'],
                ['name' => 'Payment Refund System', 'description' => 'Creating workflows for handling payment refunds.', 'issue_type' => 'feature'],
                ['name' => 'Multi-language Support', 'description' => 'Adding localization and translation features.', 'issue_type' => 'feature'],
                ['name' => 'Cache Optimization', 'description' => 'Improving caching strategies for better performance.', 'issue_type' => 'improvement'],
                ['name' => 'Push Notification Service', 'description' => 'Implementing push notifications for mobile and web.', 'issue_type' => 'feature'],
                ['name' => 'API Rate Limiting', 'description' => 'Setting up rate limits to prevent abuse.', 'issue_type' => 'support'],
                ['name' => 'Data Encryption', 'description' => 'Encrypting sensitive data in storage and transit.', 'issue_type' => 'improvement'],
                ['name' => 'User Activity Logging', 'description' => 'Tracking user activities for auditing purposes.', 'issue_type' => 'support'],
                ['name' => 'Responsive Email Templates', 'description' => 'Designing email templates optimized for all devices.', 'issue_type' => 'design'],
                ['name' => 'OAuth Integration', 'description' => 'Adding OAuth login support for third-party services.', 'issue_type' => 'support'],
                ['name' => 'Continuous Integration Setup', 'description' => 'Setting up CI pipelines for automated testing and deployment.', 'issue_type' => 'testing'],
                ['name' => 'API Documentation', 'description' => 'Creating detailed documentation for public APIs.', 'issue_type' => 'task'],
                ['name' => 'Search Functionality', 'description' => 'Implementing search with filters and sorting options.', 'issue_type' => 'feature'],
                ['name' => 'User Feedback System', 'description' => 'Collecting and managing user feedback effectively.', 'issue_type' => 'task'],
                ['name' => 'Data Analytics Pipeline', 'description' => 'Building data pipelines for real-time analytics.', 'issue_type' => 'feature'],
                ['name' => 'Content Delivery Network Setup', 'description' => 'Configuring CDN to speed up content delivery.', 'issue_type' => 'support'],
                ['name' => 'Session Timeout Improvements', 'description' => 'Enhancing security by managing session expirations.', 'issue_type' => 'improvement'],
                ['name' => 'Bug Tracking Integration', 'description' => 'Integrating with bug tracking and issue management tools.', 'issue_type' => 'support'],
            ];
        }

        // lay id 
        if (!self::$statusIds) {
            self::$statusIds = DB::table('statuses')->pluck('id')->toArray();
        }

        if (!self::$issueTypeIds) {
            self::$issueTypeIds = DB::table('issue_types')->pluck('id', 'code')->toArray();
        }

        if (!self::$userAdminIds || !self::$userManagerIds || !self::$clientIds) {
            $rolesById = DB::table('users')->pluck('role', 'id'); //=> ['id'=> 'role']

            self::$userAdminIds = $rolesById->filter(fn($role) => in_array($role, ['super_admin', 'admin']))->keys()->toArray();
            self::$userManagerIds = $rolesById->filter(fn($role) => $role === 'manager')->keys()->toArray();
            self::$clientIds     = $rolesById->filter(fn($role) => $role === 'client')->keys()->toArray();
        }

        // lay 1 template tu array projectTemplates
        $template = $this->faker->randomElement(self::$projectTemplates);

        return [
            'name' => $template['name'] . ' #' . uniqid(),
            'description' => $template['description'],

            'status_id' => empty(self::$statusIds) ? null : $this->faker->randomElement(self::$statusIds),
            'issue_type_id' => self::$issueTypeIds[$template['issue_type']] ?? null,

            'created_by' => empty(self::$userAdminIds) ? null : $this->faker->randomElement(self::$userAdminIds),
            'assigned_to' => empty(self::$userManagerIds) ? null : $this->faker->randomElement(self::$userManagerIds),
            'client_id' => empty(self::$clientIds) ? null : $this->faker->randomElement(self::$clientIds),

            'start_date' => now()->subDays(rand(10, 100)),
            'due_date' => now()->addDays(rand(10, 100)),
        ];
    }
}
