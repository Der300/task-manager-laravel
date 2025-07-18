<?php

namespace App\Services\Status;

use App\Models\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatusService
{
    /**
     * Lấy danh sách status trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['code' => 'open', 'order' => 1]
     * $field === 'not_in' value phải là mảng kiểu ['not_in' => ['key' => ['value1', 'value2']]]
     * @return \Illuminate\Support\Collection
     */
    public function getStatuses(array $filters = []): Collection
    {
        $query = Status::query();
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
     * Lấy Id status theo code.
     *
     * @param string|null $code VD: 'open', 'cancel'...
     * @return ?int
     */
    public function getIdByCode(?string $code): ?int
    {
        return Status::where('code', $code)->value('id');
    }

    /**
     * Lấy toàn bộ id và code status.
     *
     * @return array ['code'=> id,'code'=> id]
     */
    public function getAllIdsWithCodes(): array
    {
        return Status::pluck('id', 'code')->toArray();
    }
}
