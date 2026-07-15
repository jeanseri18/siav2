@if(empty($rows) || count($rows) === 0)
<p class="text-muted small mb-0">Aucune donnée pour les filtres sélectionnés.</p>
@else
<div class="table-responsive">
    <table class="table table-sm kpi-mini-table mb-0">
        <thead>
            <tr>
                @foreach($columns as $key => $label)
                <th @if($loop->last && count($columns) > 2) class="text-end" @endif>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                @foreach($columns as $key => $label)
                <td @if($loop->last && count($columns) > 2) class="text-end" @endif>
                    @if($key === 'montant')
                        {{ number_format((float) (data_get($row, 'montant', 0)), 0, ',', ' ') }}
                    @else
                        {{ data_get($row, $key, '—') }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
