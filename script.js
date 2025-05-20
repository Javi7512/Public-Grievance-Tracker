document.addEventListener('DOMContentLoaded', function() {
    // Image upload preview for grievance form
    const imageInput = document.getElementById('image');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // File size validation (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Maximum size is 5MB.');
                e.target.value = '';
                return;
            }
            
            // File type validation
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file.');
                e.target.value = '';
                return;
            }
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const uploadLabel = imageInput.parentElement;
                uploadLabel.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="max-h-40 mx-auto rounded" />
                    <p class="mt-2 text-sm text-gray-500">Click to change image</p>
                `;
            };
            reader.readAsDataURL(file);
        });
    }
    
    // Flash messages auto-hide
    const flashMessages = document.querySelectorAll('.bg-green-100[role="alert"], .bg-red-100[role="alert"]');
    
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.transition = 'opacity 1s ease-out';
            message.style.opacity = '0';
            setTimeout(() => {
                message.style.display = 'none';
            }, 1000);
        }, 5000);
    });
});