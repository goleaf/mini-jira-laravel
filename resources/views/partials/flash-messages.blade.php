@if ($errors->any())
    <div class="alert alert-danger">
        <h4 class="alert-heading">{{ __('error_heading') }}</h4>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>
@endpush

@php
    if ($errors->any() || session('success') || session('error') || session('warning') || session('info')) {
        try {
            $user = auth()->user();
            $logMessage = $errors->any() ? __('log.validation_errors') : __('log.session_message', ['type' => session()->has('success') ? 'success' : (session()->has('error') ? 'error' : (session()->has('warning') ? 'warning' : 'info'))]);
            
            Log::channel('user_actions')->info($logMessage, [
                'user_id' => $user ? $user->id : null,
                'user_email' => $user ? $user->email : null,
                'errors' => $errors->all(),
                'session_messages' => [
                    'success' => session('success'),
                    'error' => session('error'),
                    'warning' => session('warning'),
                    'info' => session('info')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error(__('log.error_logging_user_action'), ['exception' => $e->getMessage()]);
        }
    }
@endphp
