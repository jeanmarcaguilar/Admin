// Purpose/Breakdown Field Functionality for Check-In/Out Tracking

// Check-Out Purpose Change Handler
const checkOutPurpose = document.getElementById('checkOutPurpose');
const checkOutCommentsDiv = document.getElementById('checkOutCommentsDiv');

if (checkOutPurpose && checkOutCommentsDiv) {
    checkOutPurpose.addEventListener('change', function() {
        if (this.value === 'Other') {
            checkOutCommentsDiv.style.display = 'block';
            document.getElementById('checkOutComments').setAttribute('required', 'required');
        } else {
            checkOutCommentsDiv.style.display = 'none';
            document.getElementById('checkOutComments').removeAttribute('required');
            document.getElementById('checkOutComments').value = '';
        }
    });
}

// Check-In Purpose Change Handler
const checkInPurpose = document.getElementById('checkInPurpose');
const checkInCommentsDiv = document.getElementById('checkInCommentsDiv');

if (checkInPurpose && checkInCommentsDiv) {
    checkInPurpose.addEventListener('change', function() {
        if (this.value === 'Other') {
            checkInCommentsDiv.style.display = 'block';
            document.getElementById('checkInComments').setAttribute('required', 'required');
        } else {
            checkInCommentsDiv.style.display = 'none';
            document.getElementById('checkInComments').removeAttribute('required');
            document.getElementById('checkInComments').value = '';
        }
    });
}

// Enhanced Check-Out Confirmation with Purpose
const confirmCheckOutBtn = document.getElementById('confirmCheckOut');
if (confirmCheckOutBtn) {
    confirmCheckOutBtn.addEventListener('click', () => {
        const visitorName = document.getElementById('check_out_visitor_name').textContent;
        const checkOutPurpose = document.getElementById('checkOutPurpose').value;
        const checkOutComments = document.getElementById('checkOutComments').value;
        
        // Validate purpose selection
        if (!checkOutPurpose) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select a check-out purpose.',
                confirmButtonColor: '#059669'
            });
            return;
        }
        
        // If "Other" is selected, validate comments
        if (checkOutPurpose === 'Other' && !checkOutComments.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please provide additional comments for "Other" purpose.',
                confirmButtonColor: '#059669'
            });
            return;
        }
        
        const checkOutModal = document.getElementById('checkOutModal');
        closeModal(checkOutModal);
        
        // Show success message with purpose details
        const successMessage = checkOutPurpose === 'Other' 
            ? `${visitorName} has been checked out successfully. Purpose: ${checkOutPurpose} - ${checkOutComments}`
            : `${visitorName} has been checked out successfully. Purpose: ${checkOutPurpose}`;
        
        Swal.fire({
            icon: 'success',
            title: 'Visitor Checked Out',
            text: successMessage,
            timer: 3000,
            showConfirmButton: false
        });
    });
}

// Enhanced Check-In Form Submission with Purpose
const newCheckInForm = document.getElementById('newCheckInForm');
if (newCheckInForm) {
    newCheckInForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const checkInPurpose = document.getElementById('checkInPurpose').value;
        const checkInComments = document.getElementById('checkInComments').value;
        
        // Validate purpose selection
        if (!checkInPurpose) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select a visit purpose.',
                confirmButtonColor: '#059669'
            });
            return;
        }
        
        // If "Other" is selected, validate comments
        if (checkInPurpose === 'Other' && !checkInComments.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please provide additional comments for "Other" purpose.',
                confirmButtonColor: '#059669'
            });
            return;
        }
        
        const newCheckInModalElem = document.getElementById('newCheckInModal');
        closeModal(newCheckInModalElem);
        
        // Show success message with purpose details
        const successMessage = checkInPurpose === 'Other'
            ? `The visitor check-in has been registered successfully. Purpose: ${checkInPurpose} - ${checkInComments}`
            : `The visitor check-in has been registered successfully. Purpose: ${checkInPurpose}`;
        
        Swal.fire({
            icon: 'success',
            title: 'Check-In Registered',
            text: successMessage,
            timer: 3000,
            showConfirmButton: false
        });
        
        // Reset form
        newCheckInForm.reset();
        checkInCommentsDiv.style.display = 'none';
        document.getElementById('checkInComments').removeAttribute('required');
    });
}

// Reset form fields when modals are closed
const closeCheckOutModalBtn = document.getElementById('closeCheckOutModal');
if (closeCheckOutModalBtn) {
    closeCheckOutModalBtn.addEventListener('click', () => {
        document.getElementById('checkOutPurpose').value = '';
        document.getElementById('checkOutComments').value = '';
        document.getElementById('checkOutCommentsDiv').style.display = 'none';
        document.getElementById('checkOutComments').removeAttribute('required');
        const checkOutModal = document.getElementById('checkOutModal');
        closeModal(checkOutModal);
    });
}

const cancelCheckOutBtn = document.getElementById('cancelCheckOut');
if (cancelCheckOutBtn) {
    cancelCheckOutBtn.addEventListener('click', () => {
        document.getElementById('checkOutPurpose').value = '';
        document.getElementById('checkOutComments').value = '';
        document.getElementById('checkOutCommentsDiv').style.display = 'none';
        document.getElementById('checkOutComments').removeAttribute('required');
        const checkOutModal = document.getElementById('checkOutModal');
        closeModal(checkOutModal);
    });
}
