<?php

namespace App\Services\Project;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Lấy danh sách project trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['client_id' => 1, 'status_id' => 23, 'issue_type_id' => 2] $field === 'not_in' value phải là mảng kiểu ['not_in' => ['key' => ['value1', 'value2']]]
     * @return \Illuminate\Support\Collection
     */
    public function getProjects(array $filters = []): Collection
    {
        $query = Project::query();

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
     * Lấy tổng số project trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['client_id' => 1, 'status_id' => 23, 'issue_type_id' => 2]
     * @return int Tổng số project
     */
    public function countProjects(array $filters = []): int
    {
        return $this->getProjects($filters)->count();
    }

    /**
     * Trả về query builder cho project active (status != done, cancel).
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function baseActiveProjectQuery(): Builder
    {
        return Project::with([
            'status:id,name,color',
            'assignedUser:id,name',
        ])
            ->whereHas(
                'status',
                fn($s) => $s->whereNotIn('code', ['done', 'cancel'])
            );
    }

    /**
     * Lấy projects trong hệ thống đang active(status khác done, cancel)
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveProjects(): Collection
    {
        return $this->baseActiveProjectQuery()->get();
    }

    /**
     * Lấy projects trong hệ thống đang active(status khác done, cancel) theo tháng hiện tại
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveProjectsInMonth(): Collection
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return $this->baseActiveProjectQuery()
            ->whereDate('start_date', '<=', $startOfMonth)
            ->whereDate('due_date', '>=', $endOfMonth)
            ->get();
    }
}
