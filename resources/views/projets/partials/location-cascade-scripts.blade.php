{{-- Script cascade : Ville → Communes → Secteurs (localisation) ; remplit aussi quartier_id (hidden). --}}
@push('scripts')
<script>
(function ($) {
    const base = @json(rtrim(url('/'), '/'));
    const init = @json($locationInit ?? []);

    function api(path) {
        return base + path;
    }

    function resetSelect($el, placeholder) {
        $el.empty().append($('<option></option>').val('').text(placeholder));
    }

    function syncQuartierFromSecteur() {
        var $opt = $('#secteur_id').find('option:selected');
        var qid = $opt.attr('data-quartier-id');
        $('#quartier_id').val(qid != null && qid !== '' ? qid : '');
    }

    function loadCommunes(villeId, selectCommuneId, then) {
        resetSelect($('#commune_id'), 'Sélectionner une commune');
        resetSelect($('#secteur_id'), 'Sélectionner un secteur');
        $('#quartier_id').val('');
        if (!villeId) {
            if (then) then();
            return;
        }
        $.getJSON(api('/api/communes/' + encodeURIComponent(villeId)))
            .done(function (rows) {
                (rows || []).forEach(function (c) {
                    $('#commune_id').append(
                        $('<option></option>').val(c.id).text(c.nom)
                    );
                });
                if (selectCommuneId) {
                    $('#commune_id').val(String(selectCommuneId));
                }
                if (then) then();
            })
            .fail(function () {
                if (then) then();
            });
    }

    function loadSecteurs(communeId, selectSecteurId, then) {
        resetSelect($('#secteur_id'), 'Sélectionner un secteur');
        $('#quartier_id').val('');
        if (!communeId) {
            if (then) then();
            return;
        }
        $.getJSON(api('/api/secteurs-par-commune/' + encodeURIComponent(communeId)))
            .done(function (rows) {
                (rows || []).forEach(function (s) {
                    var opt = $('<option></option>')
                        .val(s.id)
                        .text(s.nom)
                        .attr('data-quartier-id', s.quartier_id != null ? s.quartier_id : '');
                    $('#secteur_id').append(opt);
                });
                if (selectSecteurId) {
                    $('#secteur_id').val(String(selectSecteurId));
                    syncQuartierFromSecteur();
                }
                if (then) then();
            })
            .fail(function () {
                if (then) then();
            });
    }

    $(function () {
        $('#ville_id').on('change', function () {
            loadCommunes($(this).val(), null, null);
        });

        $('#commune_id').on('change', function () {
            loadSecteurs($(this).val(), null, null);
        });

        $('#secteur_id').on('change', syncQuartierFromSecteur);

        var v0 = init.ville_id != null ? String(init.ville_id) : '';
        var c0 = init.commune_id != null ? String(init.commune_id) : '';
        var s0 = init.secteur_id != null ? String(init.secteur_id) : '';

        if (v0) {
            loadCommunes(v0, c0, function () {
                if (c0) {
                    loadSecteurs(c0, s0, null);
                }
            });
        }
    });
})(jQuery);
</script>
@endpush
