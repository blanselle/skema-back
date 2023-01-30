$(document).ready(function() {
    // Setting (programChannel)
    $('#programChannelModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget) // Button that triggered the modal
        $('input[type=hidden][name="program_channel_settings_form[campus]"]').val(button.data('id'))
    })
    // end

    // Settings languages
    let checkReservedPlaces = function(firstLanguageId, secondLanguageId) {
        return new Promise((resolve, reject) =>  {
            let url = $('#javascript-attributes').data('check-reserved-places-path');
            let formData = new FormData();
            if (null != firstLanguageId) {
                formData.append('firstLanguageId', firstLanguageId);
            }
            if (null != secondLanguageId) {
                formData.append('secondLanguageId', secondLanguageId);
            }

            $.ajax({
                url: url,
                data: formData,
                method: 'post',
                processData: false,
                contentType: false,
            }).done(function() {
                resolve();
            })
            .fail(function(data) {
                reject(data.responseJSON.message);
            })
        })
    };

    $('form[name="language_settings_form"]').submit(function() {
        $('#language-settings-form-submit-button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    })

    $('input[name="language_settings_form[firstLanguages][]"]').click(function(e) {
        let checked = this.checked;

        if (!checked) {
            checkReservedPlaces($(this).val(), null).catch((error) => {
                displayMessage(error, 'error');
                $(this).prop("checked", true);
            })
        }
    })
    $('input[name="language_settings_form[secondLanguages][]"]').click(function(e) {
        let checked = this.checked;

        if (!checked) {
            checkReservedPlaces(null, $(this).val()).catch((error) => {
                displayMessage(error, 'error');
                $(this).prop("checked", true);
            })
        }
    })
    // end

    // Slot page
    let table = $('#campus-slot-table').DataTable( {
        scrollY: "500px",
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        ordering: false,
        info: false,
        searching: false,
        fixedColumns: {
            left: 1
        }
    } );

    let updateTotalOfPlaces = function () {
        // Update TOTAL
        let totalNbOfReservedPlaces = 0;
        let totalNbOfAvailablePlaces = 0;
        $('.slot-nb-of-reserved-places-col').each(function() {
            totalNbOfReservedPlaces += Number($(this).data('value'));
        });
        $('.slot-input-col').each(function() {
            totalNbOfAvailablePlaces += Number($(this).val());
        });

        $('#total-score').html("<span>"+'TOTAL '+ totalNbOfReservedPlaces + ' / ' + totalNbOfAvailablePlaces+"</span>");

        // Update Total of column
        $('.col-total-slot').each(function(index) {
            let totalColNbOfReservedPlaces = 0;
            let totalColNbOfAvailablePlaces = 0;
            let colNumber = index + 1;
            // total-row-
            $('.slot-nb-of-reserved-places-col-' + colNumber).each(function() {
                totalColNbOfReservedPlaces += Number($(this).data('value'));
            });
            $('.slot-input-col-' + colNumber).each(function() {
                totalColNbOfAvailablePlaces += Number($(this).val());
            });

            $('#slot-total-col-' + colNumber).html("<span>"+'TOTAL '+ totalColNbOfReservedPlaces + ' / ' + totalColNbOfAvailablePlaces+"</span>");
        });

        // Update TOTAL row
        $('.slot-row').each(function(index) {
            let totalRowNbOfReservedPlaces = 0;
            let totalRowNbOfAvailablePlaces = 0;
            $(this).find('.slot-nb-of-reserved-places-col').each(function() {
                totalRowNbOfReservedPlaces += Number($(this).data('value'))
            });
            $(this).find('.slot-input-col').each(function() {
                totalRowNbOfAvailablePlaces += Number($(this).val());
            });

            $('#total-row-' + index).html("<span>"+'TOTAL '+ totalRowNbOfReservedPlaces + ' / ' + totalRowNbOfAvailablePlaces+"</span>");
        });
    };
    let updateNbOfAvailablePlaces = function(form, element) {

        let formData = new FormData(form);
        // prevents manual entry of a negative or decimal number
        formData.set('nbOfAvailablePlaces', Math.abs(Math.floor(Number(formData.get('nbOfAvailablePlaces')))).toString());
        $.ajax({
            url: $(form).attr('action'),
            data: formData,
            method: $(form).attr('method'),
            processData: false,
            contentType: false
        })
        .done(function(response) {
            $(element).html(response);

            updateTotalOfPlaces();
        })
        .fail(function(data) {
            $(form).find('.refresh-places-errors').first().html(data.responseJSON.message)
        })
    };

    $(document).on('change', 'input[name=nbOfAvailablePlaces]', function (e) {
        e.preventDefault();
        let form = $(this.closest('form')).get(0);
        let element = '#' + $(form).data('element-id');

        updateNbOfAvailablePlaces(form, element);
    })

    $(document).on('submit', 'form[name=refresh-places]', function(e) {
        e.preventDefault();
        updateNbOfAvailablePlaces(this)
    })
    // end
})