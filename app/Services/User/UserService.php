<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Comment\CommentService;
use App\Services\Project\ProjectService;
use App\Services\Task\TaskService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Str;

class UserService
{
    protected string $imageFolder = 'images/users/';
    protected string $imageFolderTrash = 'images/users/trash';
    /**
     * Lấy danh sách user trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['role' => 'admin', 'position' => 'administration', 'deparment' => 'management'] 
     * $field === 'not_in' value phải là mảng kiểu ['not_in' => ['key' => ['value1', 'value2']]]
     * @return \Illuminate\Support\Collection
     */
    public function getUsers(array $filters = []): Collection
    {
        $query = User::query();

        foreach ($filters as $field => $value) {
            if ($field === 'not_in' && is_array($value)) {
                foreach ($value as $notInField => $notInValues) {
                    $query->whereNotIn($notInField, $notInValues);
                }
            } elseif (is_array($value) && !empty($value)) {
                $query->whereIn($field, $value);
            } elseif (!is_null($value)) {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    /**
     * Lấy tổng số user trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['role' => 'admin, 'position' => 'CEO', 'deparment' => 'management']
     * @param bool $isWhereIn * true => whereIn, where( , '=', ) *false => whereNotIn, where( , '!=', )
     * @return int Tổng số user
     */
    public function countUsers(array $filters = []): int
    {
        return $this->getUsers($filters)->count();
    }

    /**
     * Lấy tổng số user trong hệ thống theo bo loc.
     *
     * @param ?string currentDepartment loc theo Department
     * @param bool $statusActive loc theo status
     * @param bool $exceptClient bỏ user là client
     * @param array $filters filters
     * @return LengthAwarePaginator chứa users theo các điều kiện lọc tuỳ chọn.
     */
    public function getDataUserTable(?string $currentDepartment = null, bool $statusActive = false, bool $exceptClient = false, array $filters = []): LengthAwarePaginator
    {
        $itemsPerPage = env('ITEM_PER_PAGE', 20);

        $query = User::query();

        if ($currentDepartment) {
            $query->orderByRaw("CASE WHEN department = ? THEN 0 ELSE 1 END", [$currentDepartment]);
        }
        if ($statusActive) {
            $query->where('status', 'active');
        }
        if ($exceptClient) {
            $query->whereNot('role', 'client');
        }

        if (!empty($filters['search'])) {
            $value = strtolower($filters['search']);
            $query->where(function ($q) use ($value) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$value}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$value}%"]);
            });
        }

        foreach ($filters as $field => $value) {
            if (!empty($value) && $field !== 'search') {
                $query->where($field, $value);
            }
        }

        return $query->orderBy('role', 'asc')
            ->paginate($itemsPerPage);
    }

    /**
     * Lấy tổng số user đã soft-delete
     * @return LengthAwarePaginator chứa users theo các điều kiện lọc tuỳ chọn.
     */
    public function getDataUserRecycleTable(): LengthAwarePaginator
    {
        $itemsPerPage = env('ITEM_PER_PAGE', 5);

        return User::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate($itemsPerPage);
    }

    /**
     * Lưu ảnh vào nơi chứa ảnh
     * @param UploadedFile $image file ảnh lấy từ request
     * @param string $username tên user đặt cho file ảnh
     * @return string tên ảnh đã lưu
     */
    public function uploadImage(UploadedFile $image, string $username): string
    {
        $nameWithoutSpace = str_replace('-', '', Str::slug($username));
        $imageName = 'avatar_' . $nameWithoutSpace . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path($this->imageFolder), $imageName);
        return $imageName;
    }

    /**
     * Đưa ảnh vào recycle
     * @param string $imageName tên ảnh cần xóa
     * @return void
     */
    public function moveImageToTrash(string $imageName): void
    {
        $originalPath = public_path($this->imageFolder . $imageName);
        $trashPath = public_path($this->imageFolderTrash . $imageName);

        if (file_exists($originalPath)) {
            rename($originalPath, $trashPath);
        }
    }

    /**
     * Restore ảnh
     * @param string $imageName tên ảnh cần xóa
     * @return void
     */
    public function restoreImage(string $imageName): void
    {
        $trashPath = public_path($this->imageFolderTrash . $imageName);
        $originalPath = public_path($this->imageFolder . $imageName);

        if (file_exists($trashPath)) {
            rename($trashPath, $originalPath);
        }
    }

    /**
     * xóa ảnh vĩnh viễn
     * @param string $imageName tên ảnh cần xóa
     * @return void
     */
    public function deleteImagePermanently(string $imageName): void
    {
        $trashPath = public_path($this->imageFolderTrash . $imageName);

        if (file_exists($trashPath)) {
            unlink($trashPath);
        }
    }

    /**
     * Lấy dữ liệu để điền form tạo hoặc edit user
     * @param ?User $user tên user nếu có
     * @return void
     */
    public function getUserToShowOrEdit(?User $user = null, bool $isCreate = false): array
    {
        $canAssignSuperAdmin = Auth::user()->hasRole('super-admin') && $user?->hasRole('super-admin');

        $roles = Role::when(!$canAssignSuperAdmin, fn($q) => $q->where('name', '!=', 'super-admin'))
            ->orderBy('level')
            ->pluck('name');

        return [
            'user' => $isCreate ? null : $user,
            'positions' => config('positions'),
            'departments' => config('departments'),
            'roles' => $roles,
            'isCreate' => $isCreate,

        ];
    }
    /**
     * Kiểm tra xem user có dữ liệu liên quan không.
     * Trả về mảng dữ liệu liên quan (key => count) nếu có, hoặc mảng rỗng nếu không.
     */
    public function checkRelatedData(User $user): array
    {
        $relatedData = [
            'projects_created'  => app(ProjectService::class)->countProjects(['created_by' => $user->id]),
            'projects_assigned' => app(ProjectService::class)->countProjects(['assigned_to' => $user->id]),
            'tasks_created'     => app(TaskService::class)->countTasks(['created_by' => $user->id]),
            'tasks_assigned'    => app(TaskService::class)->countTasks(['assigned_to' => $user->id]),
            'comments'          => app(CommentService::class)->countComments(['user_id' => $user->id]),
            'project_user_links' => DB::table('project_user')->where('user_id', $user->id)->count(),
            'notifications'     => DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->count(),
        ];

        // Lọc ra những key có count > 0
        return collect($relatedData)->filter(fn($count) => $count > 0)->toArray();
    }

    /**
     * Xóa user vĩnh viễn (force delete) nếu không có dữ liệu liên quan.
     * Trả về mảng ['success' => bool, 'message' => string].
     */
    public function forceDeleteUser(User $user): array
    {
        $relatedData = $this->checkRelatedData($user);

        if (!empty($relatedData)) {
            $details = collect($relatedData)->map(fn($count, $key) => "$key ($count)")->implode(', ');
            return [
                'success' => false,
                'message' => "Cannot delete this user. Related data found: $details.",
            ];
        }
        try {
            DB::beginTransaction();
            // Xóa liên kết quyền
            DB::table('model_has_roles')->where('model_type', User::class)->where('model_id', $user->id)->delete();
            DB::table('model_has_permissions')->where('model_type', User::class)->where('model_id', $user->id)->delete();

            // Xóa user
            $user->forceDelete();
            // xóa ảnh
            $this->deleteImagePermanently($user->image);
            DB::commit();

            return [
                'success' => true,
                'message' => 'User has been permanently deleted.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->restoreImage($user->image);
            return [
                'success' => false,
                'message' => 'An error occurred while deleting project.',
            ];
        }
    }
}
