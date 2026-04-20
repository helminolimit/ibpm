<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Database\Factories\LoanRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['applicant_id', 'on_behalf_of', 'status'])]
class LoanRequest extends Model
{
    /** @use HasFactory<LoanRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'on_behalf_of' => 'array',
            'status' => LoanStatus::class,
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }
}
