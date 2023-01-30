$(document).ready(function(){
    $(document).on ('click', '.media.preview', function(e){
        e.preventDefault()
        let url = $(this).attr('data-url')
        let src = $(this).attr('href')
        $.ajax({
            url: url,
            method: 'get',
            data: {
                src: src
            },
            success: function(result){
                $('#popupModal #model_container').html(result)
                $('#popupModal').modal('show')
            }
        })
    })

    $(document).on("click", '#preview_fullscreen', function () {
        if ($('#popupModal .modal-dialog').hasClass('modal-xl')) {
            $('#popupModal .modal-dialog').removeClass('modal-xl')
        } else {
            $('#popupModal .modal-dialog').addClass('modal-xl')
        }

    })

    $('#popupModal').on('hidden.bs.modal', function (e) {
        $('#popupModal .modal-dialog').removeClass('modal-xl')
    })

    $('#modal-close').click(function (){
        $('#popupModal').modal('hide')
    })
})