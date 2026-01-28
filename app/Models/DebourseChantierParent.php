<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebourseChantierParent extends Model
{
    const STATUT_BROUILLON = 'brouillon';
    const STATUT_VALIDE = 'valide';
    const STATUT_REFUSE = 'refuse';

    protected $table = 'debourse_chantier_parents';

    public $timestamps = true;

    protected $fillable = [
        'ref',
        'montant_total',
        'statut',
        'dqe_id',
        'contrat_id',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
    ];

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function dqe(): BelongsTo
    {
        return $this->belongsTo(DQE::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(DebourseChantier::class, 'parent_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_BROUILLON => 'Brouillon',
            self::STATUT_VALIDE => 'Validé',
            self::STATUT_REFUSE => 'Refusé',
            default => 'Inconnu',
        };
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_BROUILLON => 'badge-warning',
            self::STATUT_VALIDE => 'badge-success',
            self::STATUT_REFUSE => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}