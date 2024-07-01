<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'encrypted_value' => 'encrypted',
            'is_encrypted' => 'boolean',
        ];
    }

    public function value(): string
    {
        $encrypted = $this->encrypted_value;

        return tannery($this->is_encrypted, $encrypted, $this->value);
    }

    protected static function booted(): void
    {
        parent::creating(function (Setting $setting) {
            $one = filled($setting->encrypted_value);
            $encrypted = or_check($one, $setting->is_encrypted ?? false);
            $value = $setting->encrypted_value;

            $setting->encrypted_value = tannery($encrypted, $value, null);
            $setting->value = tannery(!$encrypted, $setting->value, null);
            $setting->is_encrypted = $encrypted;
        });
    }
}
