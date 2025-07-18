<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Collection;

class UserService
{
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
}
