<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'status',
       'phone', 'whatsapp', 'birthdate', 'cpf', 'cep', 'street', 'number', 'complement', 'city', 'neighborhood', 'state', 'notifications_enabled'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }
       
    public function belongsToBranch(int $branch_id): bool
    {
        return $this->branch()->where('branch_id', $branch_id)->exists();
    }
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }
    public function assignRole($role): void
        {
            $this->roles()->attach(['role_id'=> $role]);
        }

    public function doService(int $service_id): bool
    {
        return $this->services()->where('service_id', $service_id)->exists();
    }
    public function assignService($service_id): void
    {
        $this->services()->attach(['service_id' => $service_id]);
    }
    public function resignService($service_id): void
    {
        $this->services()->detach(['service_id' => $service_id]);
    }
    public function isActive()
    {
    return $this->status === 'Ativo';
    }
    public function appointments()
    {
    return $this->hasMany(\App\Models\Appointment::class, 'employee_id');
    }
    
    public function lastAppointment()
    {
        return $this->hasOne(\App\Models\Appointment::class, 'customer_id')->latest('appointment_date');
    }
    public function clientAppointments()
    {
        return $this->hasMany(\App\Models\Appointment::class, 'customer_id');
    }
    public function employeeAppointments()
    {
        return $this->hasMany(\App\Models\Appointment::class, 'employee_id')
            ->with('customer') // Eager load the customer relationship
            ->with('branch'); // Eager load the branch relationship
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function currentSubscription()
    {
        return $this->subscriptions()
        ->where('status', 'Ativo')
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->latest()->first();    
    }
}
    