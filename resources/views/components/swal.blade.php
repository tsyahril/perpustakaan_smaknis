{{-- Notifikasi flash + konfirmasi SweetAlert2 global. Butuh CDN sweetalert2 di layout. --}}
@if (session('success') || session('error'))
<script>
    window.addEventListener('DOMContentLoaded', function () {
        var message = @json(session('success') ?? session('error'));
        var type = @json(session('success') ? 'success' : 'error');
        if (typeof Swal === 'undefined') return;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
    });
</script>
@endif

<script>
    // Konfirmasi global: <form data-confirm="..."> ditahan, tampilkan Swal dulu.
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        var message = form.getAttribute('data-confirm');
        if (!message) return;
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm(message)) form.submit();
            return;
        }
        Swal.fire({
            title: 'Yakin?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '11d4ed8',
            cancelButtonColor: '16b7280',
            confirmButtonText: 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });
</script>
