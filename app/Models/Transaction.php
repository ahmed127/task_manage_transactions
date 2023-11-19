<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'amount',
        'paid',
        'unpaid',
        'due_at',
        'vat',
        'is_vat_inclusive',
        'status', //default(1) => outstanding, 2 => overdue, 3 => paid
    ];

    protected $casts = [
        'id'                    => 'integer',
        'admin_id'              => 'integer',
        'user_id'               => 'integer',
        'amount'                => 'float',
        'paid'                  => 'float',
        'unpaid'                => 'float',
        'due_at'                => 'date',
        'vat'                   => 'integer',
        'is_vat_inclusive'      => 'boolean',
        'status'                => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
    ];

    const STATUS_OUTSTANDING = 1;
    const STATUS_OVERDUE = 2;
    const STATUS_PAID = 3;

    const VAT_NOT_INCLUSIVE = 0;
    const VAT_INCLUSIVE = 1;

    public static function statuses(string $select = null)
    {
        $arr = [
            self::STATUS_OUTSTANDING => 'outstanding',
            self::STATUS_OVERDUE => 'overdue',
            self::STATUS_PAID => 'paid',
        ];
        // Check If has select
        if ($select) {
            return array_search($select, $arr);
        }
        return $arr;
    }

    protected $appends = ['status_name'];

    public function getStatusNameAttribute()
    {
        return $this->statuses()[$this->status];
    }

    /**
     * Get the admin that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the payments for the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }
}
