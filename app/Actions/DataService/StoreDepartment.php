<?php

namespace App\Actions\DataService;

use App\Actions\Core\CreateWallet;
use App\Facades\UserLookup;
use App\Models\Core\Department;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class StoreDepartment
{
    /**
     * @throws Throwable
     */
    public function execute(Collection $payload, ?int $id = null): Department
    {
        $username = $payload['hodUsername'];

        throw_if(! $username, new Exception('HOD username is required'));

        $head = UserLookup::username($payload['hodUsername']);

        $head = $head->shouldSetDepartment(false)->fetch();

        throw_if(! $head, new Exception('HOD not found'));

        $attributes = [];
        $attributes['code'] = $payload['shortName'];
        $attributes['parent_department_id'] = $id;
        $attributes['head_user_id'] = $head->id;
        $attributes['name'] = $payload['name'];
        $attributes['sync_id'] = $payload['id'] ?? null;

        $department = Department::create($attributes);
        (new CreateWallet)->execute($department);

        $head->update(['department_id' => $department->id]);

        return $department;
    }
}
