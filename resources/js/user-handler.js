/**
 * User Handler
 * Handles avatar upload with validation and SweetAlert error messages
 * Uses global SwalAlert utility from app.js
 */
class UserHandler {
    constructor(options = {}) {
        this.options = {
            maxSize: options.maxSize || 2 * 1024 * 1024, // 2MB default
            acceptedTypes: options.acceptedTypes || ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            defaultAvatar: options.defaultAvatar || '/images/avatars/default-avatar.webp',
            ...options
        };

        this.elements = {
            fileInput: null,
            previewImg: null,
            removeBtn: null,
            chooseBtn: null,
            removeFlag: null
        };

        this.init();
        this.validatePhoneNumber();
    }

    init() {
        // Find elements
        this.elements.fileInput = document.getElementById('avatarInput');
        this.elements.previewImg = document.getElementById('avatarPreviewImg');
        this.elements.removeBtn = document.getElementById('removeAvatar');
        this.elements.chooseBtn = document.getElementById('chooseAvatarBtn');
        this.elements.removeFlag = document.getElementById('removeAvatarFlag');

        // Only initialize if elements exist
        if (!this.elements.fileInput || !this.elements.previewImg) {
            return;
        }

        this.bindEvents();
    }

    bindEvents() {
        // Choose button click
        if (this.elements.chooseBtn) {
            this.elements.chooseBtn.addEventListener('click', () => {
                this.elements.fileInput.click();
            });
        }

        // File input change
        this.elements.fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFile(e.target.files[0]);
            }
        });

        // Remove button click
        if (this.elements.removeBtn) {
            this.elements.removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.removeAvatar();
            });
        }
    }

    handleFile(file) {
        // Reset remove flag
        if (this.elements.removeFlag) {
            this.elements.removeFlag.value = '0';
        }

        // Validate file size first (more likely to fail for large files)
        if (file.size > this.options.maxSize) {
            const maxSizeMB = (this.options.maxSize / (1024 * 1024)).toFixed(0);
            this.showError(
                'File Too Large',
                `File size must be less than ${maxSizeMB}MB. Your file is ${this.formatFileSize(file.size)}.`
            );
            this.elements.fileInput.value = '';
            return;
        }

        // Validate file type (with fallback for large files where MIME type might not be detected)
        const isValidType = this.isValidImageType(file);
        if (!isValidType) {
            this.showError(
                'Invalid File Type',
                'Please select a valid image file (JPG, PNG, GIF, WebP or SVG)'
            );
            this.elements.fileInput.value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.elements.previewImg.src = e.target.result;
            if (this.elements.removeBtn) {
                this.elements.removeBtn.style.display = 'flex';
            }
        };
        reader.onerror = () => {
            this.showError('Read Error', 'Failed to read the file. Please try again.');
            this.elements.fileInput.value = '';
        };
        reader.readAsDataURL(file);
    }

    removeAvatar() {
        this.elements.fileInput.value = '';
        this.elements.previewImg.src = this.options.defaultAvatar;

        if (this.elements.removeBtn) {
            this.elements.removeBtn.style.display = 'none';
        }

        if (this.elements.removeFlag) {
            this.elements.removeFlag.value = '1';
        }
    }

    showError(title, message) {
        // Use global SwalAlert if available
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.error(title, message);
        } else if (typeof Swal !== 'undefined') {
            // Fallback to direct Swal call
            Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonText: 'Got it',
                confirmButtonColor: '#0f172a',
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    confirmButton: 'swal-custom-button'
                }
            });
        } else {
            alert(`${title}: ${message}`);
        }
    }

    showWarning(title, message) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.warning(title, message);
        } else {
            alert(`${title}: ${message}`);
        }
    }

    showSuccess(title, message) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.success(title, message);
        } else {
            alert(`${title}: ${message}`);
        }
    }

    isValidImageType(file) {
        // First check MIME type if available
        if (file.type && this.options.acceptedTypes.includes(file.type)) {
            return true;
        }

        // Fallback to file extension for large files where MIME type might not be detected
        const fileName = file.name.toLowerCase();
        const validExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];

        return validExtensions.some(ext => fileName.endsWith(ext));
    }

    formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    validatePhoneNumber() {
        const phoneInputField = document.querySelector("#phone");
        const countryCodeInput = document.querySelector("#country_code");

        // Only initialize if phone field exists
        if (!phoneInputField) {
            return;
        }

        const phoneInput = window.intlTelInput(phoneInputField, {
            separateDialCode: true,
            preferredCountries: ["us", "gb", "in", "de"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // Function to update hidden country code field
        function updateCountryCode() {
            if (countryCodeInput) {
                const selectedCountryData = phoneInput.getSelectedCountryData();
                if (selectedCountryData && selectedCountryData.dialCode) {
                    countryCodeInput.value = '+' + selectedCountryData.dialCode;
                }
            }
        }

        function setCountryFromExistingData() {
            const currentPhoneValue = phoneInputField.value;
            if (currentPhoneValue && currentPhoneValue.trim() !== '') {
                setTimeout(updateCountryCode, 200);
                return;
            }

            // If no phone number but we have a country code, set the country
            if (countryCodeInput && countryCodeInput.value) {
                // Extract dial code from country_code (remove + sign)
                const dialCode = countryCodeInput.value.replace('+', '');

                // Find country by dial code
                const allCountries = phoneInput.getCountryData();
                const countryData = allCountries.find(country => country.dialCode === dialCode);

                if (countryData) {
                    // Set the selected country
                    phoneInput.setCountry(countryData.iso2);
                }
            }
        }

        // Function to show/hide validation feedback
        function updateValidationFeedback() {
            const phoneValue = phoneInputField.value.trim();
            const isValid = phoneInput.isValidNumber();
            const selectedCountryData = phoneInput.getSelectedCountryData();
            const countryName = selectedCountryData ? selectedCountryData.name : 'selected country';

            // Find the phone input wrapper div (parent of the input)
            const phoneWrapper = phoneInputField.closest('.phone-input-wrapper') || phoneInputField.parentNode;

            // Remove existing validation message from the wrapper
            let validationMsg = phoneWrapper.parentNode.querySelector('.phone-validation-message');
            if (validationMsg) {
                validationMsg.remove();
            }

            // Add visual feedback to input
            phoneInputField.classList.remove('phone-valid', 'phone-invalid');

            if (phoneValue) {
                if (isValid) {
                    phoneInputField.classList.add('phone-valid');
                } else {
                    phoneInputField.classList.add('phone-invalid');

                    // Add validation message outside the wrapper div
                    validationMsg = document.createElement('div');
                    validationMsg.className = 'phone-validation-message form-error';
                    validationMsg.textContent = `Invalid format for ${countryName}`;
                    phoneWrapper.parentNode.insertBefore(validationMsg, phoneWrapper.nextSibling);
                }
            }
        }

        // Update country code when country changes
        phoneInputField.addEventListener('countrychange', function() {
            updateCountryCode();
            updateValidationFeedback();
        });

        // Update country code and validation on input change
        phoneInputField.addEventListener('input', function() {
            updateCountryCode();
            updateValidationFeedback();
        });

        // Initialize with existing data after intl-tel-input is ready
        setTimeout(() => {
            setCountryFromExistingData();
            updateCountryCode();
            updateValidationFeedback();
        }, 100);

        // Add validation feedback for form submission
        const form = phoneInputField.closest('form');
        if (form) {
            form.addEventListener('submit', function(event) {
                const phoneValue = phoneInputField.value.trim();
                const isValid = phoneInput.isValidNumber();

                // Only validate if there's a phone number entered
                if (phoneValue && !isValid) {
                    event.preventDefault();

                    // Get country name for better error message
                    const selectedCountryData = phoneInput.getSelectedCountryData();
                    const countryName = selectedCountryData ? selectedCountryData.name : 'selected country';

                    // Show error using SwalAlert
                    if (typeof window.SwalAlert !== 'undefined') {
                        window.SwalAlert.error(
                            'Invalid Phone Number',
                            `The phone number format is not valid for ${countryName}. Please check the number and try again.`
                        );
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Phone Number',
                            text: `The phone number format is not valid for ${countryName}. Please check the number and try again.`,
                            confirmButtonText: 'Got it',
                            confirmButtonColor: '#0f172a',
                            customClass: {
                                popup: 'swal-custom-popup',
                                title: 'swal-custom-title',
                                confirmButton: 'swal-custom-button'
                            }
                        });
                    } else {
                        alert(`Invalid Phone Number: The phone number format is not valid for ${countryName}. Please check the number and try again.`);
                    }

                    // Focus on the phone input for better UX
                    phoneInputField.focus();
                }
            });
        }
    }
}

export default UserHandler;
