document.addEventListener("DOMContentLoaded", function(){
    "use strict";

    let buttons = document.querySelectorAll('.btn-confirmation');
    
    buttons.forEach((button) => {
        button.addEventListener('click', (e) => {
            let label = button.getAttribute('data-confirmation-label');
    
            if(!confirm(label)) {
                e.preventDefault();
            }
        })
    })
});