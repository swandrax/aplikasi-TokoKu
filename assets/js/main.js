document.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();

    // Add to cart functionality
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            const existingItemIndex = cart.findIndex(item => item.id === id);
            if (existingItemIndex > -1) {
                cart[existingItemIndex].qty += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1
                });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCounter();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: `${name} ditambahkan ke keranjang!`,
                timer: 1500,
                showConfirmButton: false
            });
        });
    });

    // Image preview
    const imageInput = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.addEventListener('load', function() {
                    imagePreview.setAttribute('src', this.result);
                    imagePreview.style.display = 'block';
                });
                reader.readAsDataURL(file);
            }
        });
    }

    // Confirm delete
    const deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E74C3C',
                cancelButtonColor: '#2C3E50',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    }

    // Initialize Flatpickr
    if (typeof flatpickr !== 'undefined') {
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
        });
    }

    // Character counter for textarea
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const counterId = textarea.getAttribute('id') + '-counter';
        let counterDisplay = document.getElementById(counterId);
        
        if(!counterDisplay) {
            counterDisplay = document.createElement('small');
            counterDisplay.id = counterId;
            counterDisplay.className = 'text-muted d-block mt-1';
            textarea.parentNode.appendChild(counterDisplay);
        }

        const maxLength = textarea.getAttribute('maxlength');
        
        const updateCounter = () => {
            const currentLength = textarea.value.length;
            counterDisplay.textContent = `${currentLength} / ${maxLength} karakter`;
        };
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
});

function updateCartCounter() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    const counterElem = document.getElementById('cart-counter');
    if (counterElem) {
        counterElem.textContent = totalQty;
    }
}
