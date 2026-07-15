{{-- Remplit le select secteur d'activité à partir du client (API /api/clients/{id}). --}}
@push('scripts')
<script>
(function ($) {
    var base = @json(rtrim(url('/'), '/'));

    function urlClient(id) {
        return base + '/api/clients/' + encodeURIComponent(id);
    }

    function appliquerSecteurDepuisClient(clientId) {
        if (!clientId) {
            return;
        }
        $.getJSON(urlClient(clientId))
            .done(function (data) {
                if (data.secteur_activite_id) {
                    $('#secteur_activite_id').val(String(data.secteur_activite_id));
                }
            });
    }

    $(function () {
        $('#client').on('change', function () {
            var id = $(this).val();
            if (!id) {
                $('#secteur_activite_id').val('');
                return;
            }
            appliquerSecteurDepuisClient(id);
        });

        if ($('#client').val() && !$('#secteur_activite_id').val()) {
            appliquerSecteurDepuisClient($('#client').val());
        }
    });
})(jQuery);
</script>
@endpush
