$(document).ready(function() {
    // Add trigger on click nav-tabs
    $('#configuration-tab').find('.nav-link').each(function () {
        let target = $(this).data('bs-target');
        $(this).click(function(e) {
            e.preventDefault();
            // hide all tab-pane
            $('.tab-pane').each(function() {
                $(this).removeClass('show active');
                $($(this).closest('div.campus-configuration-form').get(0)).hide();
            })
            $(target).addClass('show active');
            $($(target).closest('div.campus-configuration-form').get(0)).show();
        });
    });


    // Display the first form (tab-pane)
    $('#configuration-tab li:first-child button').click();

    let diffMinutes = function (dt2, dt1)
    {
        if (dt2.getTime() < dt1.getTime()) {
            return 0;
        }

        var diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= 60;
        return Math.abs(Math.round(diff));

    }
    /**
     * Calcul :
     * diffTime = heure de fin - heure de début
     *
     * (diffTime + temps de pause - ((diffTime - temps de pause) / (durée du test + durée de préparation) * durée debrief jury)) / (durée du test + durée debrief jury)
     */
    let updateNbOfCandidatesPerJury = function(form) {
        let juryDebriefDuration = Number($(form).find('.jury-debrief-duration').get(0).value);

        $(form).find('.test-configuration').each(function() {
            let durationOfTest = Number($(this).find('.duration-of-test').get(0).value);
            let preparationTime = Number($(this).find('.preparation-time').get(0).value);

            // loop on .slot-form to update all nbOfCandidatesPerJury
            $(form).find('.slot-form').each(function() {
                let target = $(this).find('.nb-of-candidates-per-jury').get(0);
                let nbOfCandidatesPerJury = null;
                let startTime = $(this).find('.start-time').get(0).value;
                let endTime = $(this).find('.end-time').get(0).value;
                let breakDuration = Number($(this).find('.break-duration').get(0).value);
                let diffTime = 0;

                if (startTime !== '' && endTime !== '') {
                    diffTime = diffMinutes(new Date("1970-1-1 " + endTime), new Date("1970-1-1 " + startTime));
                }

                try {
                    nbOfCandidatesPerJury = Math.floor(Math.abs((diffTime + breakDuration - ((diffTime - breakDuration) / (durationOfTest + preparationTime) * juryDebriefDuration)) / (durationOfTest + juryDebriefDuration)));
                } catch (e) {
                }

                if (Number.isNaN(nbOfCandidatesPerJury)) {
                    nbOfCandidatesPerJury = null;
                }

                $(target).val(nbOfCandidatesPerJury);
            });
        })
    };

    $(document).on('change', '.nb-of-candidates-event', function(e) {
        let form = $(this.closest('form')).get(0);

        updateNbOfCandidatesPerJury(form);
    });

    $(document).on('submit', 'form[name="campus_configuration"]', function(e) {
        e.preventDefault();
        updateNbOfCandidatesPerJury(this);

        let element = $(this.closest('.campus-configuration-form')).get(0);

        $.ajax({
            url: $(this).attr('action'),
            data: new FormData(this),
            method: $(this).attr('method'),
            processData: false,
            contentType: false,
        })
        .done(function(data) {

            // Replace content with errors
            $(element).html(data);
            // display the form (nav-tabs)
            $($(element).find('.tab-pane').get(0)).addClass('show active');

            window.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
        })
        .fail(function (xhr) {
            // Replace content with errors
            $(element).html(xhr.responseText);
            // display the form (nav-tabs)
            $($(element).find('.tab-pane').get(0)).addClass('show active');
            // focus on the first error
            $(element).find('.invalid-feedback').get(0).focus();
        })
    })
})