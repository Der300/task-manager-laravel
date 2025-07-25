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
        $data = $this->userService->getUserDataFromForm(isCreate: true);

        return view('users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

            // xử lý ảnh
            if ($request->hasFile('image')) {
                $imageName = $this->userService->uploadImage($request->file('image'), $request->name);
            }
            // Mass Assignment
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['image'] = $imageName ?? null;
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
            return redirect()->back()->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        $data = $this->userService->getUserDataFromForm($user);

        return view('users.show', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        // Nếu có upload ảnh thì xử lý
        if ($request->hasFile('image')) {
            $imageName = $this->userService->uploadImage($request->file('image'), $request->name);
        }

        $data = $request->validated();

        // Chỉ gán image nếu có ảnh mới
        if (isset($imageName)) {
            $data['image'] = $imageName;
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
        } else {
            return back()->with('error', 'User updated fail!');
        }
    }

    public function softDelete(User $user)
    {
        try {
            $user->delete(); // Soft delete
            return redirect()->route('users.index')->with('success', 'User moved to recycle successfully.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Failed to move user to recycle.');
        }
    }

    public function restore(User $user)
    {
        try {
            if ($user->trashed()) {
                $user->restore();
                return redirect()->back()->with('success', 'User restored successfully.');
            }
            return redirect()->back()->with('info', 'User is not deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore user.');
        }
    }

    public function forceDelete(User $user)
    {
        $result = $this->userService->forceDeleteUser($user);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }
}
