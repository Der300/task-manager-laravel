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
        'user'      => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
        'project'   => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
        'task'      => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
        'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
        'file'      => ['view', 'upload', 'download', 'soft-delete', 'restore', 'force-delete'],
    ],
    'role_permissions' => [
        'super_admin' => [
            'level' => 1,
            'permissions' => ['all'], // all = toàn quyền
        ],
        'admin' => [
            'level' => 2,
            'permissions' => [
                'dashboard' => ['view'],
                'user'      => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
                'project'   => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
                'task'      => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],
                'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore', 'force-delete'],    //edit owm
                'file'      => ['view', 'upload', 'download', 'soft-delete', 'restore', 'force-delete'],
            ],
        ],
        'manager' => [
            'level' => 3,
            'permissions' => [
                'dashboard' => ['view'],
                'user'      => ['view', 'edit'], //own edit 
                'project'   => ['view', 'create', 'edit', 'soft-delete', 'restore'], //'soft-delete', 'restore' own
                'task'      => ['view', 'create', 'edit', 'soft-delete', 'restore'], //'soft-delete', 'restore' team
                'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore'], //'soft-delete', 'restore' team, edit own
                'file'      => ['view', 'upload', 'download', 'soft-delete', 'restore'], //'soft-delete', 'restore' team
            ],
        ],
        'leader' => [
            'level' => 4,
            'permissions' => [
                'dashboard' => ['view'],
                'user'      => ['view', 'edit'], // own edit
                'project'   => ['view'],
                'task'      => ['view', 'create', 'edit', 'soft-delete', 'restore'], //'soft-delete', 'restore' own
                'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore'], // 'edit', 'soft-delete', 'restore' own
                'file'      => ['view', 'upload', 'download', 'soft-delete', 'restore'], //'soft-delete', 'restore' own
            ],
        ],
        'member' => [
            'level' => 5,
            'permissions' => [
                'dashboard' => ['view'],
                'user'      => ['view', 'edit'], // own edit 
                'project'   => ['view'],
                'task'      => ['view', 'edit'], // own edit
                'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore'], // 'edit', 'soft-delete', 'restore' own
                'file'      => ['view', 'upload', 'download', 'soft-delete', 'restore'], //'soft-delete', 'restore' own
            ],
        ],
        'client' => [
            'level' => 6,
            'permissions' => [
                'dashboard' => ['view'],
                'user'      => ['edit'],
                'project'   => ['view'],
                'task'      => ['view'],
                'comment'   => ['view', 'create', 'edit', 'soft-delete', 'restore'], // 'edit', 'soft-delete', 'restore' own
                'file'      => ['view', 'download'], //'soft-delete', 'restore' own
            ],
        ],
    ],
];
