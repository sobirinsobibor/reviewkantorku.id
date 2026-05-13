import Swal from 'sweetalert2'

export function useSweetAlert() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    })

    const toast = ({ icon = 'success', title = '' }) => {
        Toast.fire({ icon, title })
    }

    const confirm = async ({
        title = 'Apakah kamu yakin?',
        text = '',
        icon = 'warning',
        confirmText = 'Ya, lanjutkan!',
        cancelText = 'Batal',
    } = {}) => {
        const result = await Swal.fire({
            title,
            text,
            icon,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        })
        return result.isConfirmed
    }

    const alert = ({ title, text, icon = 'info' }) => {
        return Swal.fire({ title, text, icon })
    }

    return { toast, confirm, alert }
}