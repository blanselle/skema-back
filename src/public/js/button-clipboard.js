(function() {
    "use strict";

    function updateClipboard(newClip) {
        navigator.clipboard.writeText(newClip).then(function() {
            // Le clip board est mis à jour
        }, function() {
            // Buge
        });
    }

    function highlightButton(btn) {
        btn.classList.add('btn-success')
        setTimeout(() => {
            btn.classList.remove('btn-success')
        }, 600); 
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.btn-clipboard').forEach((btn) => {
            btn.addEventListener('click', () => {
                updateClipboard(document.querySelector('#' + btn.getAttribute('for')).value)
                highlightButton(btn)
            })
        })
    })
})();