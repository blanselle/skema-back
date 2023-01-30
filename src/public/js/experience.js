$(document).ready(function() {
    function refreshExperiences(obj)
    {
        let $experience = obj.closest('.experience_item');
        if (obj.find(":selected").val() === 'professional' && $experience.find('.time_type').find(':selected').val() !== 'full') {
            $($experience.find('.time_type').get(0)).attr('readonly', false)
        } else {
            $($experience.find('.time_type').get(0)).attr('readonly', true).val('full').change()
        }
        if (obj.find(":selected").val() === 'international' || obj.find(":selected").val() === 'associative') {
            $($experience.find('.time_type').first().get(0)).prop('selected', true).prop('disabled', true)
            $($experience).find('input[name="'+$($experience.find('.time_type').get(0)).attr('name')+'"]').remove()
            $($experience).append('<input type="hidden" name="'+$($experience.find('.time_type').get(0)).attr('name')+'" value="'+$($experience.find('.time_type').get(0)).find('option:selected').val()+'">')
        } else  {
            $($experience).find('input[name="'+$($experience.find('.time_type').get(0)).attr('name')+'"]').remove()
            $($experience.find('.time_type').get(0)).prop('disabled', false);
        }

        // refresh label for duration
        let label = '';
        switch (obj.find(":selected").val()) {
            case 'professional':
            case 'international':
                label = 'Durée en mois';
                break;
            default:
                label = 'Durée en année';
        }
        $($experience.find('.duration_label').get(0)).html(label);
    }

    function refreshHoursPerWeek(obj)
    {
        let $experience = obj.closest('.experience_item');

        if (obj.find(":selected").val() !== 'full' && $experience.find('.experience_type').find('option:selected').val() === 'professional') {
            $($experience.find('.hours_per_week')).attr('readonly', false)
        } else {
            $($experience.find('.hours_per_week')).attr('readonly', true).val('')
        }
    }

    $('.experience_type').each(function(){
        refreshExperiences($(this))
    }).change(function(){
        refreshExperiences($(this))
    })

    $('.time_type').each(function(){
        refreshHoursPerWeek($(this))
    }).change(function(){
        refreshHoursPerWeek($(this))
    })
})