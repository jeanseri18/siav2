/**
 * Calculateur BPU - Calculs automatiques selon les formules BPU
 */

// Fonction pour calculer les valeurs dérivées
function calculateBpuValues() {
    // Récupération des valeurs saisies
    const materiaux = parseFloat(document.querySelector('input[name="materiaux"]')?.value) || 0;
    const tauxMo = parseFloat(document.querySelector('input[name="taux_mo"]')?.value) || 0;
    const tauxMat = parseFloat(document.querySelector('input[name="taux_mat"]')?.value) || 0;
    const tauxFc = parseFloat(document.querySelector('input[name="taux_fc"]')?.value) || 0;
    const tauxFg = parseFloat(document.querySelector('input[name="taux_fg"]')?.value) || 0;
    const tauxBenefice = parseFloat(document.querySelector('input[name="taux_benefice"]')?.value) || 0;

    // Calculs selon les formules BPU
    // MAIN D'ŒUVRE (MO) = % MO x MATERIAUX
    const mainOeuvre = (tauxMo / 100) * materiaux;
    
    // MATERIEL (MAT) = % MAT x MATERIAUX
    const materiel = (tauxMat / 100) * materiaux;
    
    // DEBOURSE SEC (DS) = MATERIAUX + MAIN D'ŒUVRE + MATERIEL
    const debourseSec = materiaux + mainOeuvre + materiel;
    
    // FRAIS CHANTIER (FC) = % FC x DS
    const fraisChantier = (tauxFc / 100) * debourseSec;
    
    // FRAIS GENERAUX (FG) = % FG x DS
    const fraisGeneraux = (tauxFg / 100) * debourseSec;
    
    // BENEFICE (B) = % B x DS
    const benefice = (tauxBenefice / 100) * debourseSec;
    
    // P.U HT = DS + FC + FG + B
    const puHt = debourseSec + fraisChantier + fraisGeneraux + benefice;

    // Affichage des résultats calculés
    updateCalculatedField('main_oeuvre_calc', mainOeuvre);
    updateCalculatedField('materiel_calc', materiel);
    updateCalculatedField('debourse_sec_calc', debourseSec);
    updateCalculatedField('frais_chantier_calc', fraisChantier);
    updateCalculatedField('frais_generaux_calc', fraisGeneraux);
    updateCalculatedField('benefice_calc', benefice);
    updateCalculatedField('pu_ht_calc', puHt);
}

// Fonction pour mettre à jour un champ calculé
function updateCalculatedField(fieldId, value) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.textContent = formatNumber(value);
    }
}

// Fonction pour formater les nombres
function formatNumber(value) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

// Initialisation des événements
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des écouteurs d'événements sur tous les champs de saisie
    const inputFields = [
        'input[name="materiaux"]',
        'input[name="taux_mo"]',
        'input[name="taux_mat"]',
        'input[name="taux_fc"]',
        'input[name="taux_fg"]',
        'input[name="taux_benefice"]'
    ];

    inputFields.forEach(selector => {
        const field = document.querySelector(selector);
        if (field) {
            field.addEventListener('input', calculateBpuValues);
            field.addEventListener('change', calculateBpuValues);
        }
    });

    // Calcul initial
    calculateBpuValues();
});