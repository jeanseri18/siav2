<?php

namespace App\Support;

use App\Models\BonCommande;
use App\Models\BU;
use App\Models\ConfigGlobal;

/**
 * Logo et libellé société pour les PDF : ConfigGlobal par BU, sinon logo / nom de la table bus.
 */
final class PdfBranding
{
    /**
     * @return array{
     *     config: ?ConfigGlobal,
     *     bu: ?BU,
     *     logo_absolute_path: ?string,
     *     logo_src: ?string,
     *     logo_width: int,
     *     logo_height: int,
     *     nom_entreprise: string,
     *     company: array<string, ?string>
     * }
     */
    public static function forBu(?int $buId): array
    {
        $bu = $buId ? BU::query()->find($buId) : null;
        $config = $buId
            ? ConfigGlobal::query()->where('id_bu', $buId)->first()
            : null;

        if (! $buId) {
            $config = ConfigGlobal::query()->first();
            if ($config && $config->id_bu) {
                $bu = BU::query()->find($config->id_bu);
            }
        }

        $logoAbsolute = self::resolveLogoPath($config, $bu);
        $logoDims = self::logoDisplaySize($logoAbsolute);

        $nom = 'Entreprise';
        if ($config && filled($config->nom_entreprise)) {
            $nom = $config->nom_entreprise;
        } elseif ($bu) {
            $nom = $bu->nom;
        }

        return [
            'config' => $config,
            'bu' => $bu,
            'logo_absolute_path' => $logoAbsolute,
            'logo_src' => self::logoDataUri($logoAbsolute),
            'logo_width' => $logoDims['width'],
            'logo_height' => $logoDims['height'],
            'nom_entreprise' => $nom,
            'company' => self::buildCompanyBlock($config, $bu, $nom),
        ];
    }

    /**
     * BU active en session, puis projet / demandes liées au bon de commande.
     */
    public static function resolveBuIdForBonCommande(BonCommande $bonCommande): ?int
    {
        if (session('selected_bu')) {
            return (int) session('selected_bu');
        }

        if ($bonCommande->projet?->bu_id) {
            return (int) $bonCommande->projet->bu_id;
        }

        if ($bonCommande->relationLoaded('demandeAchat') || $bonCommande->demandeAchat) {
            $buId = $bonCommande->demandeAchat?->projet?->bu_id;
            if ($buId) {
                return (int) $buId;
            }
        }

        if ($bonCommande->relationLoaded('demandeApprovisionnement') || $bonCommande->demandeApprovisionnement) {
            $buId = $bonCommande->demandeApprovisionnement?->projet?->bu_id;
            if ($buId) {
                return (int) $buId;
            }
        }

        return null;
    }

    /**
     * @return array<string, ?string>
     */
    private static function buildCompanyBlock(?ConfigGlobal $config, ?BU $bu, string $nom): array
    {
        return [
            'nom' => $nom,
            'localisation' => filled($config?->localisation) ? $config->localisation : ($bu?->adresse),
            'adresse_postale' => $config?->adresse_postale,
            'rccm' => filled($config?->rccm) ? $config->rccm : $bu?->numero_rccm,
            'cc' => filled($config?->cc) ? $config->cc : $bu?->numero_cc,
            'tel1' => $config?->tel1,
            'tel2' => $config?->tel2,
            'email' => filled($config?->email) ? $config->email : null,
            'horaires_ouverture' => $config?->horaires_ouverture,
        ];
    }

    private static function resolveLogoPath(?ConfigGlobal $config, ?BU $bu): ?string
    {
        foreach (self::logoPathCandidates($config?->logo, $bu?->logo) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function logoPathCandidates(?string $configLogo, ?string $buLogo): array
    {
        $paths = [];

        foreach ([$configLogo, $buLogo] as $relative) {
            if (! filled($relative)) {
                continue;
            }

            $relative = ltrim(str_replace('\\', '/', $relative), '/');

            if (str_starts_with($relative, 'storage/')) {
                $paths[] = public_path($relative);
            }

            $paths[] = public_path('storage/'.$relative);
            $paths[] = storage_path('app/public/'.$relative);
        }

        return array_values(array_unique($paths));
    }

    public static function logoDataUri(?string $absolutePath): ?string
    {
        if (! $absolutePath || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $mime = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($absolutePath));
    }

    /**
     * Dimensions d'affichage PDF (DomPDF) en conservant le ratio d'origine.
     *
     * @return array{width: int, height: int}
     */
    public static function logoDisplaySize(?string $absolutePath, int $maxWidth = 170, int $maxHeight = 90): array
    {
        if ($absolutePath && is_file($absolutePath)) {
            $info = @getimagesize($absolutePath);
            if (is_array($info) && ($info[0] ?? 0) > 0 && ($info[1] ?? 0) > 0) {
                $ratio = min($maxWidth / $info[0], $maxHeight / $info[1], 1.0);

                return [
                    'width' => max(1, (int) round($info[0] * $ratio)),
                    'height' => max(1, (int) round($info[1] * $ratio)),
                ];
            }
        }

        return [
            'width' => $maxWidth,
            'height' => (int) round($maxWidth * 0.45),
        ];
    }
}
