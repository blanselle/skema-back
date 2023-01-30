$(document).ready(function(){
    $(document).on('click', '.media_form .process.validate', function(){
        let media_code= $(this).attr('data-code')
        let student_status = $(this).attr('data-student-status')
        var wording = 'Voulez vous vraiment valider le media ?';
        var reload = false;

        if(media_code === "certificat_eligibilite" && student_status === "check_diploma"){
            var wording = 'La validation de ce média entrainera la validation de l\'éligibilité. Validez-vous cette action ?';
            var reload = true;
        }

        if (confirm(wording)) {
            let obj = $(this)
            let media_id = $(this).attr('data-id')
            let choice = $(this).attr('data-choice')
            let url = $(this).attr('data-url')
            let url_callback = $(this).attr('data-callback')
            $.ajax({
                url: url,
                method: 'post',
                data: {
                    media: media_id,
                    choice: choice
                },
                success: function (result) {
                    obj.parent().parent().prev().children().find('input').val(result['state'])
                    obj.parent().parent().remove()
                    if (result['documentsValidated'] === true) {
                        if (confirm('L’ensemble des documents obligatoire est approuvé, voulez-vous approuver la candidature ?')) {
                            $.ajax({
                                url: url_callback,
                                method: 'post',
                                data: {
                                    media: media_id
                                },
                                success: function () {
                                    location.reload()
                                }
                            })
                        }
                    }
                    if (reload === true) {
                        location.reload()
                    }
                }
            })
        }
    })

    $(document).on('click', '.media_form .notif', function(){
        let media_id = $(this).attr('data-id')
        let choice = $(this).attr('data-choice')
        let url = $(this).attr('data-url')
        $.ajax({
            url: url,
            method: 'post',
            data: {
                media: media_id,
                choice: choice
            },
            success: function (result) {
                $('#popupModal #model_container').html(result)
                $('#popupModal').modal('show')
            }
        })
    })

    $('.resignation.popup').click(function(){
        let url = $(this).attr('data-url')
        $.ajax({
            url: url,
            method: 'get',
            success: function (result) {
                $('#popupModal #model_container').html(result)
                $('#popupModal').modal('show')
            }
        })
    })

    $(document).on("click", '#popup_notification_resignation #send', function () {
        let url = $(this).attr('data-url')
        let motif = $('#popup_notification_resignation textarea[name=content]').val()

        if (motif !== "") {
            $.ajax({
                url: url,
                method: 'post',
                data: {
                    motif: motif
                },
                success: function () {
                    location.reload()
                }
            })
        }
    })

    $(document).on("change", '#popup_notification_media select[name=subject]', function(){
        let content = $(this).children('option:selected').attr("data-content")

        CKEDITOR.instances.notification_popup_content.setData( content );
    })

    $(document).on("click", '#popup_notification_media #send', function(){
        let url = $(this).attr('data-url')

        if ($('#popup_notification_media select[name=subject]').val() !== '' && $('#popup_notification_media textarea[name=content]').val() !== '') {
            $.ajax({
                url: url,
                method: 'post',
                data: {
                    subject: $('#popup_notification_media select[name=subject] option:selected').html(),
                    content: CKEDITOR.instances.notification_popup_content.getData(),
                    media: $('#popup_notification_media input[name=media]').val(),
                    tag: $('#popup_notification_media input[name=tag]').val(),
                    user: $('input[type=hidden][name=current_user]').val(),
                    receiver: $('#popup_notification_media select[name=receiver] option:selected').val(),
                },
                success: function (result) {
                    let media = '#validation-media-' + result.id
                    $(media).prev().children().find('input').val(result.state_label)
                    if (result.state !== 'transfered' && result.state !== 'to_check') {
                        $(media).remove();
                    }
                    $('#popupModal').modal('hide')
                }
            })
        }
    })

    $('#experience_form .process').click(function (){
        if (confirm('Êtes-vous sûr de vouloir refuser cet élément ?')) {
            let obj = $(this)
            let url = $(this).attr('data-url')
            let choice = $(this).attr('data-choice')

            $.ajax({
                url: url,
                method: 'post',
                data: {
                    choice: choice
                },
                success: function (result) {
                    obj.parent().parent().prev().children().find('input[type=text]').val(result)
                    obj.parent().hide()
                }
            })
        }
    })

    $('.bloc_media .media_delete').click(function (){
        let url = $(this).attr('data-url')
        $.ajax({
            url: url,
            method: 'post',
            success: function () {
                location.reload()
            }
        })
    })
})