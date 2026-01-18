import Swal from 'sweetalert2'

// Success Alert
export const showSuccessAlert = (title, text = '') => {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        timerProgressBar: true,
    })
}

// Error Alert
export const showErrorAlert = (title, text = '') => {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        confirmButtonColor: '#ef4444',
    })
}

// Confirm Dialog (Delete Confirmation)
export const showConfirmDialog = (title, text = '') => {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    })
}

// Loading Alert
export const showLoadingAlert = (title = 'Loading...') => {
    return Swal.fire({
        title: title,
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading()
        }
    })
}

// SMS Send Confirmation Dialog
export const showSMSConfirmDialog = (count) => {
    return Swal.fire({
        title: 'Send SMS Credentials?',
        text: `Are you sure you want to send credentials to ${count} selected student(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981', 
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Send SMS!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    })
}

// Close any open alert
export const closeAlert = () => {
    Swal.close()
}

export default Swal