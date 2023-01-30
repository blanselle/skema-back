(function() {
    "use strict";
    
    document.addEventListener("DOMContentLoaded", function(){
        document.querySelectorAll('.session-export').forEach((button) => {

            let spinner = button.querySelector('.session-export-spinner')
            let icon = button.querySelector('.session-export-icon')
            let label = button.querySelector('.session-export-label')
            let labelLoading = button.querySelector('.session-export-label--loading')
            let url = button.getAttribute('data-l')
            let filename = button.getAttribute('data-f')

            button.addEventListener('click', () => {
                spinner.classList.remove('d-none')
                icon.classList.add('d-none')
                label.classList.add('d-none')
                labelLoading.classList.remove('d-none')

                download(url, filename).then(() => {
                    spinner.classList.add('d-none')
                    icon.classList.remove('d-none')
                    label.classList.remove('d-none')
                    labelLoading.classList.add('d-none')
                })
            })
        })
        
    });

    function download(url, filename) {
        return fetch(url, {
            method: 'GET',
        })
        .then(response => response.blob())
        .then(blob => {
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
    }
})();