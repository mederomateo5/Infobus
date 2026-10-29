// Tabs
        function switchTab(tab) {
            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Función para mostrar / ocultar contraseña con el botón del ojito
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Bloquear pegar en confirmar contraseña
        const confirmar = document.getElementById("reg-confirm");
        if (confirmar) {
            ["paste", "cut", "drop"].forEach(evento => {
                confirmar.addEventListener(evento, function(e) {
                    e.preventDefault();
                });
            });
        }

        // Manejar clic en los iconos de ayuda (.info-icon)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('info-icon')) {
        e.preventDefault();
        const tooltipText = e.target.getAttribute('title') || e.target.getAttribute('data-tooltip');
        
        // Guardamos el texto en data-tooltip si no lo tiene
        if (!e.target.getAttribute('data-tooltip')) {
            e.target.setAttribute('data-tooltip', tooltipText);
            e.target.removeAttribute('title'); // Quitamos el title nativo para que no duplique el cartel del navegador
        }

        // Si ya existe un alert flotante previo, lo removemos
        const existingTooltip = document.querySelector('.custom-tooltip-popup');
        if (existingTooltip) {
            existingTooltip.remove();
        }

        // Creamos un popup flotante con el mensaje
        const popup = document.createElement('div');
        popup.className = 'custom-tooltip-popup alert alert-dark py-1 px-2 position-absolute shadow-sm small';
        popup.style.zIndex = '1000';
        popup.textContent = e.target.getAttribute('data-tooltip');
        
        document.body.appendChild(popup);

        // Posicionamos el popup cerca del icono clickeado
        const rect = e.target.getBoundingClientRect();
        popup.style.top = (window.scrollY + rect.bottom + 5) + 'px';
        popup.style.left = (window.scrollX + rect.left) + 'px';

        // Cerramos el popup si hacen clic en cualquier otro lado
        const closePopup = function(event) {
            if (!e.target.contains(event.target)) {
                popup.remove();
                document.removeEventListener('click', closePopup);
            }
        };
        setTimeout(() => {
            document.addEventListener('click', closePopup);
        }, 100);
    }
});