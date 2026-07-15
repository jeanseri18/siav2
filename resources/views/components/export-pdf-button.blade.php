@props(['route', 'class' => 'app-btn app-btn-outline-danger app-btn-sm'])

<a href="{{ $route }}" {{ $attributes->merge(['class' => $class]) }} target="_blank" rel="noopener noreferrer">
    <i class="fas fa-file-pdf me-2"></i>Voir PDF
</a>
