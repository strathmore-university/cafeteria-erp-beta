<?php

namespace App\Services\DataService;

use App\Actions\DataService\StoreDepartment;
use App\Exceptions\DataService\DepartmentNotFound;
use App\Exceptions\DataService\MissingDepartmentId;
use App\Facades\ApiClient;
use App\Models\Core\Department;
//use App\Services\AttributeSanitizers\DepartmentSanitizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class DepartmentLookup
{
    private ?Department $department = null;

    private ?Collection $payload = null;

    private string $shortName = '';

    private bool $search = true;

    private ?int $id = null;

    private StoreDepartment $storeDepartment;

    public function __construct()
    {
        $this->storeDepartment = new StoreDepartment();
    }

    public function id(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function search(bool $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function shortName(string $shortName): self
    {
        $this->shortName = $shortName;
//        $this->shortName = DepartmentSanitizer::shortName($shortName);

        return $this;
    }

    public function payload(Collection $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function fetch(): ?Department
    {
        $this->department = get_department_by_code($this->shortName);

        $code = tannery(filled($this->department), 1, null);

        $canSearch = and_check($this->search, filled($this->id));

        $canSearch = and_check($canSearch, ! $this->department);

        $code = tannery($canSearch, 2, $code);

        $dontSearch = and_check( ! $this->search, ! $this->department);

        $code = tannery($dontSearch, 3, $code);

        return match ($code) {
            1 => tap($this->department, fn () => $this->reset()),
            2 => tap($this->attemptSearch(), fn () => $this->reset()),
            default => $this->resetAndThrow(),
            3 => null,
        };
    }

    /**
     * @throws Throwable
     */
    public function getParentId(): ?int
    {
        return match ((bool) $this->payload['subDepartment']) {
            true => $this->searchParentId(),
            default => null,
        };
    }

    /**
     * @throws Throwable
     */
    public function store(): ?Department
    {
        return $this->attemptPersist();
    }

    private function reset(): void
    {
        $this->department = null;

        $this->shortName = '';

        $this->payload = null;

        $this->search = true;

        $this->id = null;
    }

    /**
     * @throws Throwable
     */
    private function attemptSearch(): Department
    {
        $id = $this->id;

        $this->payload = $this->payload ?? ApiClient::fetchDepartment($id);

        throw_if($this->payload->isEmpty(), new DepartmentNotFound());

        return $this->attemptPersist();
    }

    /**
     * @throws Throwable
     */
    private function attemptPersist(): Department
    {
        return DB::transaction(function () {
            $id = $this->getParentId();

            $payload = $this->payload;

            return $this->storeDepartment->execute($payload, $id);
        });
    }

    /**
     * @throws Throwable
     */
    private function searchParentId(): int
    {
        $shortName = $this->payload['parentShortName'];

        $parentId = get_department_by_code($shortName)?->id;

        return match ($parentId === null) {
            true => $this->attemptToPersistParent(),
            default => $parentId,
        };
    }

    /**
     * @throws Throwable
     */
    private function attemptToPersistParent(): int
    {
        $payload = ApiClient::fetchDepartment($this->payload['parent']);

        $message = 'Parent department not found.';

        throw_if($payload->isEmpty(), new DepartmentNotFound($message));

        return $this->storeDepartment->execute($payload)->id;
    }

    /**
     * @throws Throwable
     */
    private function resetAndThrow(): null
    {
        $id = $this->id;

        $this->reset();

        throw_if( ! $id, new MissingDepartmentId());

        return null;
    }
}
