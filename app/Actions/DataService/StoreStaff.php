<?php

namespace App\Actions\DataService;

use App\Actions\Core\CreateWallet;
use App\Exceptions\DataService\DepartmentNotFound;
use App\Facades\DepartmentLookup;
use App\Models\User;
use Illuminate\Support\Collection;
use Throwable;

class StoreStaff
{
    /**
     * @throws Throwable
     */
    public function execute(
        Collection $payload,
        bool $setDepartment = true,
    ): User {
        return $this->store($payload, $setDepartment);
    }

    /**
     * @throws Throwable
     */
    private function store(Collection $payload, bool $setDepartment): User
    {
        $search = ['username' => $payload['username']];

//        dd($payload);

        $attributes = [];
        $attributes['user_number'] = $payload['employeeNo'];
        $attributes['other_names'] = $payload['middleName'];
        $attributes['phone_number'] = $payload['mobileNo'];
        $attributes['first_name'] = $payload['firstName'];
        $attributes['last_name'] = $payload['lastName'];
        $attributes['username'] = $payload['username'];
        $attributes['email'] = $payload['email'];
        $attributes['domain'] = 'staff';
        $attributes['is_staff'] = true;

        $user = User::firstOrCreate($search, $attributes);

        (new CreateWallet)->execute($user, true);

        return match ($setDepartment) {
            true => $this->updateDepartment($user, $payload),
            false => $user,
        };
    }

    /**
     * @throws Throwable
     */
    private function updateDepartment(User $user, Collection $payload): User
    {
        try {
            $shortname = $payload['departmentShortName'];

            $id = $payload['departmentId'];

            $department = DepartmentLookup::shortname($shortname);

            $department = $department->id($id)->fetch();

            $user->update(['department_id' => $department->id]);
        } catch (Throwable) {
            report(new DepartmentNotFound());
        }

        return $user;
    }
}
