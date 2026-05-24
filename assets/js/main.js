document.addEventListener('DOMContentLoaded', function() {
    // Favorite Toggle
    const favButtons = document.querySelectorAll('.fav-btn');
    favButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const kostId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('kost_id', kostId);

            // Determine base URL dynamically
            const baseUrl = window.BASE_URL || '/e-kost-system/';
            const endpoint = baseUrl + 'pages/toggle_favorite.php';

            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'added') {
                        this.classList.remove('btn-outline-dark');
                        this.classList.add('btn-danger');
                        this.innerHTML = '<i class="bi bi-heart-fill"></i> Tersimpan';
                    } else {
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-dark');
                        this.innerHTML = '<i class="bi bi-heart"></i> Simpan';
                    }
                } else {
                    alert(data.message);
                    if (data.message.includes('login')) {
                        window.location.href = baseUrl + 'modules/auth/login.php';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
