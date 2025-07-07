<?php

namespace Database\Factories;

use App\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Comment::class;

    protected static $commentTemplates;
    protected static $userIds;
    protected static $taskIds;

    public function definition(): array
    {
        if (!self::$commentTemplates) {
            self::$commentTemplates = [
                ['body' => 'Looks good to me! ✅', 'task_name' => 'Fix broken footer links'],
                ['body' => 'Please review the latest update. 🔄', 'task_name' => 'Design new homepage layout'],
                ['body' => 'Blocked until frontend is ready. 🚧', 'task_name' => 'Add chart to user dashboard'],
                ['body' => 'Need clarification on this one. ❓', 'task_name' => 'Fix broken sitemap.xml'],
                ['body' => 'Reassigning to the design team. 🎨', 'task_name' => 'Redesign settings page'],
                ['body' => 'Tested and working as expected. ✅', 'task_name' => 'Fix client avatar upload bug'],
                ['body' => 'Still waiting for backend API. ⏳', 'task_name' => 'Build user API endpoints'],
                ['body' => 'Updated with the requested changes. ✏️', 'task_name' => 'Setup forgot password flow'],
                ['body' => 'Requires more unit tests. 🧪', 'task_name' => 'Write test cases for login'],
                ['body' => 'Let’s discuss this during standup. 🗣️', 'task_name' => 'Add push notifications'],
                ['body' => 'I’ve pushed a fix to dev branch. 🚀', 'task_name' => 'Fix currency conversion bug'],
                ['body' => 'This task depends on another ticket. 🔗', 'task_name' => 'Create CMS dashboard'],
                ['body' => 'Can you rebase this with main? 🔃', 'task_name' => 'Refactor API authentication'],
                ['body' => 'Assigned to QA for verification. 👨‍🔬', 'task_name' => 'Test responsive navbar'],
                ['body' => 'Code looks clean! ✨', 'task_name' => 'Design client dashboard'],
                ['body' => 'Add more validation checks please. 🛡️', 'task_name' => 'Implement login form validation'],
                ['body' => 'Can we use helper here instead? 🧩', 'task_name' => 'Setup PHPUnit configuration'],
                ['body' => 'Marked as duplicate. 🗂️', 'task_name' => 'Fix chart display in report'],
                ['body' => 'Closed due to inactivity. 💤', 'task_name' => 'Fix XSS vulnerability'],
                ['body' => 'Waiting on client feedback. 📩', 'task_name' => 'Fix panel scroll issue'],
                ['body' => 'Please review the latest update. 🔄', 'task_name' => 'Design email notification template'],
                ['body' => 'Still waiting for backend API. ⏳', 'task_name' => 'Create feature flag system'],
                ['body' => 'Let’s discuss this during standup. 🗣️', 'task_name' => 'Fix broken unsubscribe links'],
                ['body' => 'Blocked until frontend is ready. 🚧', 'task_name' => 'Log JavaScript errors'],
                ['body' => 'Need clarification on this one. ❓', 'task_name' => 'Fix rich text editor bug'],
                ['body' => 'Requires more unit tests. 🧪', 'task_name' => 'Normalize DB columns'],
                ['body' => 'Add more validation checks please. 🛡️', 'task_name' => 'Fix broken sitemap.xml'],
                ['body' => 'Can you rebase this with main? 🔃', 'task_name' => 'Setup Sentry integration'],
                ['body' => 'Code looks clean! ✨', 'task_name' => 'Test data import tool'],
                ['body' => 'This task depends on another ticket. 🔗', 'task_name' => 'Fix UI layout in mobile view'],
                ['body' => 'Tested and working as expected. ✅', 'task_name' => 'Fix token expiration bug'],
                ['body' => 'Reassigning to the design team. 🎨', 'task_name' => 'Style dashboard widgets'],
                ['body' => 'Marked as duplicate. 🗂️', 'task_name' => 'Improve button consistency'],
                ['body' => 'Please review the latest update. 🔄', 'task_name' => 'Add role management UI'],
                ['body' => 'Add more validation checks please. 🛡️', 'task_name' => 'Fix JavaScript errors'],
                ['body' => 'Can we use helper here instead? 🧩', 'task_name' => 'Test feature toggle fallback'],
                ['body' => 'Let’s discuss this during standup. 🗣️', 'task_name' => 'Fix broken footer links'],
                ['body' => 'Waiting on client feedback. 📩', 'task_name' => 'Fix missing analytics data'],
                ['body' => 'Still waiting for backend API. ⏳', 'task_name' => 'Integrate PayPal gateway'],
                ['body' => 'Marked as duplicate. 🗂️', 'task_name' => 'Update meta tags for SEO'],
                ['body' => 'This task depends on another ticket. 🔗', 'task_name' => 'Test chat reconnection logic'],
                ['body' => 'Can you rebase this with main? 🔃', 'task_name' => 'Clean migrated records'],
                ['body' => 'Closed due to inactivity. 💤', 'task_name' => 'Add push notifications'],
                ['body' => 'Please review the latest update. 🔄', 'task_name' => 'Design analytics chart UI'],
                ['body' => 'Add more validation checks please. 🛡️', 'task_name' => 'Fix mobile layout'],
                ['body' => 'Still waiting for backend API. ⏳', 'task_name' => 'Generate PDF report'],
                ['body' => 'Tested and working as expected. ✅', 'task_name' => 'Rename legacy tables'],
                ['body' => 'Looks good to me! ✅', 'task_name' => 'Improve button consistency'],
                ['body' => 'Blocked until frontend is ready. 🚧', 'task_name' => 'Fix XSS vulnerability'],
                ['body' => 'Waiting on client feedback. 📩', 'task_name' => 'Create CMS dashboard'],
            ];
        }

        if (!self::$userIds) {
            self::$userIds = DB::table('users')->pluck('id')->toArray();
        }

        if (!self::$taskIds) {
            self::$taskIds = [];
            $temp = DB::table('tasks')->pluck('id', 'name')->toArray();
            foreach ($temp as $name => $id) {
                $taskName = explode(' #', $name)[0]; //"UI/UX Overhaul #686a80b5499b5" => "UI/UX Overhaul"
                self::$taskIds[$taskName] = $id;
            }
        }

        $template = $this->faker->randomElement(self::$commentTemplates);
        $taskId = self::$taskIds[$template['task_name']] ?? $this->faker->randomElement(array_values(self::$taskIds));

        return [
            'body' => $template['body'],

            'user_id' => $this->faker->randomElement(self::$userIds),
            'task_id' => $taskId,

        ];
    }
}
