<?php

namespace App\Observers;

use App\Models\Department;
use App\Services\AttributeSanitizers\DepartmentSanitizer;

class DepartmentObserver
{
    public function creating(Department $department): void
    {
        $department->name = DepartmentSanitizer::name($department->name);

        $department->code = DepartmentSanitizer::shortName($department->code);
    }

    public function created(Department $department): void
    {
    }

    public function updating(Department $department): void
    {
        $department->name = DepartmentSanitizer::name($department->name);

        $department->code = DepartmentSanitizer::shortName($department->code);
    }

    public function updated(Department $department): void
    {
    }

    public function saving(Department $department): void
    {
    }

    public function saved(Department $department): void
    {
    }

    public function deleting(Department $department): void
    {
    }

    public function deleted(Department $department): void
    {
    }

    public function restoring(Department $department): void
    {
    }

    public function restored(Department $department): void
    {
    }
}
