<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\User\UserService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Request;

class UserController extends Controller
{
    use AuthorizesRequests;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(HttpRequest $request)
    {
        $user = Auth::user();

        $departments = User::distinct()->pluck('department')->filter()->sort()->values();
        $positions = User::distinct()->pluck('position')->filter()->sort()->values();
        $filters = $request->only(['search', 'department', 'position', 'status']);

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $data = $this->userService->getDataUserTable(filters: $filters);
        } elseif ($user->hasRole('manager')) {
            $data = $this->userService->getDataUserTable($user->department, statusActive: true, exceptClient: false, filters: $filters);
        } else {
            $data = $this->userService->getDataUserTable($user->department, true, true, filters: $filters);
        }

        return view('users.index', compact('data', 'departments', 'positions'));
    }

    /**
     * Display a listing item soft-deleted.
     */
    public function recycle()
    {
        $data = $this->userService->getDataUserRecycleTable();

        return view('users.recycle', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->userService->getUserToShowOrEdit(isCreate: true);

        return view('users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // xử lý ảnh
            $imageName = null;
            if ($request->hasFile('image')) {
                $imageName = $this->userService->uploadImage($request->file('image'), $request->name);
                $data['image'] = $imageName;
            }
            $data['password'] = Hash::make($data['password']);
            // Mass Assignment
            $user = User::create($data);
            // gán role cho user
            $user->assignRole($data['role']);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User created successfully!');
        } catch (Exception $e) {
            DB::rollback();
            // Xóa ảnh nếu đã upload nhưng transaction bị fail
            if (!empty($imageName)) {
                $this->userService->deleteImage($imageName);
            }
            return back()->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        $data = $this->userService->getUserToShowOrEdit($user);

        return view('users.show', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->validated();

        // Nếu có upload ảnh thì xử lý
        if ($request->hasFile('image')) {
            $data['image'] = $this->userService->uploadImage($request->file('image'), $request->name);
        } else {
            unset($data['image']); // tránh ghi null nếu không có ảnh mới
        }

        // Cập nhật user, trả về true/false
        $updated = $user->update($data);

        // Cập nhật role nếu thay đổi
        $currentRole = $user->getRoleNames()->first();
        if ($currentRole !== $data['role']) {
            $user->syncRoles([$data['role']]);
        }

        if ($updated) {
            return back()->with('success', 'User updated successfully!');
        }
        return back()->with('error', 'User updated fail!');
    }

    /**
     * Soft-delete
     */
    public function softDelete(User $user)
    {
        try {
            $user->delete(); // Soft delete
            return redirect()->route('users.index')->with('success', 'User moved to recycle successfully.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Failed to move user to recycle.');
        }
    }

    /**
     * Restore
     */
    public function restore(User $user)
    {
        try {
            if ($user->trashed()) {
                $user->restore();
                return back()->with('success', 'User restored successfully.');
            }
            return back()->with('error', 'User is not deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore user.');
        }
    }

    /**
     * force-delete
     */
    public function forceDelete(User $user)
    {
        $result = $this->userService->forceDeleteUser($user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }
}
