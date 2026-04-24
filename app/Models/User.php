<?php

namespace App\Models;

use App\Models\Reference\Role;
use App\Models\Reference\UserStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'id', 'nom_complet', 'email', 'login_code', 'telephone', 'password',
        'user_status_id', 'photo_profil', 'derniere_connexion',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'derniere_connexion' => 'datetime',
        'password' => 'hashed',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withPivot('assigned_at')->withTimestamps();
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(Client::class, 'id');
    }

    public function supervisedClients(): HasMany
    {
        return $this->hasMany(Client::class, 'superviseur_id');
    }

    public function treatedAdhesionRequests(): HasMany
    {
        return $this->hasMany(AdhesionRequest::class, 'traite_par');
    }

    public function treatedCredits(): HasMany
    {
        return $this->hasMany(Credit::class, 'traite_par');
    }

    public function evaluatedBusinessPlans(): HasMany
    {
        return $this->hasMany(BusinessPlan::class, 'evalue_par');
    }

    public function treatedInsurances(): HasMany
    {
        return $this->hasMany(Insurance::class, 'traite_par');
    }

    public function treatedInsuranceClaims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class, 'traite_par');
    }

    public function treatedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'traite_par');
    }

    public function validatedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'valide_par');
    }

    public function supervisedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'superviseur_id');
    }

    public function authoredPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function validatedPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'valide_par');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes', 'user_id', 'post_id')->withTimestamps();
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_saves', 'user_id', 'post_id')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'from_user_id');
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants', 'user_id', 'conversation_id')
            ->withPivot('unread_count', 'last_read_at')->withTimestamps();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function treatedSupportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'traite_par');
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function hasRole(string $code): bool
    {
        return $this->roles->contains('code', $code);
    }

    public function hasAnyRole(array $codes): bool
    {
        return $this->roles->whereIn('code', $codes)->isNotEmpty();
    }

    public function isSuperAdmin(): bool { return $this->hasRole('super_admin'); }
    public function isAdmin(): bool { return $this->hasRole('admin'); }
    public function isSupervisor(): bool { return $this->hasRole('superviseur'); }
    public function isClient(): bool { return $this->hasRole('client'); }
    public function isAdminLike(): bool { return $this->hasAnyRole(['super_admin', 'admin']); }
    public function isStaff(): bool { return $this->hasAnyRole(['super_admin', 'admin', 'superviseur']); }
}
