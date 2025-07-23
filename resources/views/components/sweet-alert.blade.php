<script>
    // Hiển thị thông báo success/error từ session
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 1500,
            showConfirmButton: false,
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{{ session('error') }}",
            timer: 1500,
            showConfirmButton: false,
        });
    @endif

    // Hàm confirm chung, truyền message, callback khi confirm
    function swalConfirmWithForm(e, {
        title = 'Are you sure?',
        text = '',
        icon = 'warning',
        confirmButtonText = 'Yes',
        cancelButtonText = 'Cancel'
    }) {
        e.preventDefault();

        const form = e.target.closest('form');
        if (!form) {
            console.error('No form found for confirmation.');
            return false;
        }

        Swal.fire({
            title,
            text,
            icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText,
            cancelButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });

        return false;
    }
</script>
