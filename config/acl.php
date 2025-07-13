<?php
return [
    'roles' => [
        'super_admin'   => 'super-admin',
        'admin'         => 'admin',
        'manager'       => 'manager',
        'leader'        => 'leader',
        'member'        => 'member',
        'client'        => 'client',
    ],
    // module.action
    'permissions' => [
        'dashboard' => ['view'],
        'user'      => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete', 'assign'],
        'project'   => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete', 'assign'],
        'task'      => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete', 'assign'],
        'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
    ],
    'role_permissions' => [
        'super_admin' => [
            'level' => 1,
            'permissions' => ['all'], // all = toàn quyền
        ],
        'admin' => [
            'level' => 2,
            'permissions' => ['all'],
        ],
        'manager' => [
            'level' => 3,
            'permissions' => [
                'dashboard' => ['view'],
                'user' => ['view', 'edit'], //own edit 
                'project' => ['view', 'create', 'edit', 'assign'],
                'task' => ['view', 'create', 'edit', 'soft-delete', 'force-delete', 'restore', 'assign'],
                'comment' => ['view', 'create', 'edit']
            ],
        ],
        'leader' => [
            'level' => 4,
            'permissions' => [
                'dashboard' => ['view'],
                'user' => ['view', 'edit'], // own edit
                'project' => ['view'],
                'task' => ['view', 'create', 'edit', 'assign'],
                'comment' => ['view', 'create', 'edit'], // own edit
            ],
        ],
        'member' => [
            'level' => 5,
            'permissions' => [
                'dashboard' => ['view'],
                'user' => ['view', 'edit'], // own edit 
                'project' => ['view'],
                'task' => ['view', 'edit'], // own edit
                'comment' => ['view', 'create', 'edit'], // own edit
            ],
        ],
        'client' => [
            'level' => 6,
            'permissions' => [
                'dashboard' => ['view'],
                'project' => ['view'],
                'task' => ['view'],
                'comment' => ['view', 'create', 'edit'], // own edit
            ],
        ],
    ],
];
