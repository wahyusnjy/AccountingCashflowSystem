<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaction) {
            $transaction->journalEntries()->delete();
        });

        static::restoring(function ($transaction) {
            $transaction->journalEntries()->onlyTrashed()->restore();
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'description',
        'reference_type',
        'student_name',
        'period_month',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the journal entries associated with this transaction.
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'transaction_id');
    }
}
