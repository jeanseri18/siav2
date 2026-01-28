/**
 * Fonction de diagnostic pour vérifier l'état des champs de calcul
 */
function diagnosticChamps(rubriqueId) {
    console.log('=== DIAGNOSTIC CHAMPS RUBRIQUE ' + rubriqueId + ' ===');
    
    // Champs visibles pour le calcul
    const champsVisibles = {
        quantite: document.getElementById('quantite_' + rubriqueId),
        puHt: document.getElementById('pu_ht_' + rubriqueId),
        montantHt: document.getElementById('montant_ht_' + rubriqueId)
    };
    
    console.log('Champs visibles trouvés:', {
        quantite: !!champsVisibles.quantite,
        puHt: !!champsVisibles.puHt,
        montantHt: !!champsVisibles.montantHt
    });
    
    // Vérifier chaque champ en détail
    Object.keys(champsVisibles).forEach(nom => {
        const champ = champsVisibles[nom];
        if (champ) {
            console.log(`Détail champ ${nom}_${rubriqueId}:`, {
                id: champ.id,
                type: champ.type,
                value: champ.value,
                defaultValue: champ.defaultValue,
                placeholder: champ.placeholder,
                disabled: champ.disabled,
                readonly: champ.readOnly,
                required: champ.required,
                className: champ.className,
                style_display: champ.style.display,
                style_visibility: champ.style.visibility,
                offsetParent: !!champ.offsetParent,
                clientHeight: champ.clientHeight,
                clientWidth: champ.clientWidth,
                dataAttributes: {
                    rubriqueId: champ.getAttribute('data-rubrique-id'),
                    // Autres attributs data
                }
            });
        }
    });
    
    // Vérifier si la ligne parent est visible
    const ligneAddForm = document.getElementById('add-form-' + rubriqueId);
    if (ligneAddForm) {
        console.log('Ligne add-form détails:', {
            id: ligneAddForm.id,
            style_display: ligneAddForm.style.display,
            style_visibility: ligneAddForm.style.visibility,
            offsetParent: !!ligneAddForm.offsetParent,
            clientHeight: ligneAddForm.clientHeight,
            innerHTML: ligneAddForm.innerHTML.substring(0, 200) + '...'
        });
    }
    
    // Vérifier les écouteurs d'événements
    console.log('Écouteurs d\'événements:');
    if (champsVisibles.quantite) {
        console.log('quantite_' + rubriqueId + ' a des écouteurs:', {
            input: !!champsVisibles.quantite.oninput,
            change: !!champsVisibles.quantite.onchange,
            keyup: !!champsVisibles.quantite.onkeyup,
            blur: !!champsVisibles.quantite.onblur
        });
    }
    if (champsVisibles.puHt) {
        console.log('pu_ht_' + rubriqueId + ' a des écouteurs:', {
            input: !!champsVisibles.puHt.oninput,
            change: !!champsVisibles.puHt.onchange,
            keyup: !!champsVisibles.puHt.onkeyup,
            blur: !!champsVisibles.puHt.onblur
        });
    }
    
    // Test de modification de valeur
    console.log('Test de modification de valeur...');
    if (champsVisibles.quantite) {
        const valeurTest = '5.5';
        console.log('Avant modification:', champsVisibles.quantite.value);
        champsVisibles.quantite.value = valeurTest;
        console.log('Après modification:', champsVisibles.quantite.value);
        
        // Déclencher l'événement input
        const event = new Event('input', { bubbles: true });
        champsVisibles.quantite.dispatchEvent(event);
    }
    
    console.log('=== FIN DIAGNOSTIC ===');
}

// Fonction pour tester tous les champs visibles
function diagnosticTousChamps() {
    console.log('=== DIAGNOSTIC TOUS LES CHAMPS ===');
    const calculateInputs = document.querySelectorAll('.calculate-input');
    console.log('Nombre total de champs calculate-input:', calculateInputs.length);
    
    const rubriquesTraitees = new Set();
    calculateInputs.forEach(input => {
        const rubriqueId = input.getAttribute('data-rubrique-id');
        if (rubriqueId && !rubriquesTraitees.has(rubriqueId)) {
            rubriquesTraitees.add(rubriqueId);
            diagnosticChamps(rubriqueId);
        }
    });
}

// Ajouter un bouton de diagnostic
window.addEventListener('DOMContentLoaded', function() {
    const diagnosticBtn = document.createElement('button');
    diagnosticBtn.textContent = 'DIAGNOSTIC CHAMPS';
    diagnosticBtn.style.position = 'fixed';
    diagnosticBtn.style.top = '60px';
    diagnosticBtn.style.right = '10px';
    diagnosticBtn.style.zIndex = '9999';
    diagnosticBtn.style.backgroundColor = 'orange';
    diagnosticBtn.style.color = 'white';
    diagnosticBtn.style.padding = '10px';
    diagnosticBtn.onclick = diagnosticTousChamps;
    document.body.appendChild(diagnosticBtn);
    
    console.log('Bouton de diagnostic ajouté - cliquez pour tester tous les champs');
});

// Export pour utilisation dans la console
window.diagnosticChamps = diagnosticChamps;
window.diagnosticTousChamps = diagnosticTousChamps;