$(document).ready(function(){
    function getCoefficientParams () {
        ids = [];
        $('input[name="ranking[programChannels][]"]:checked').each((index, el) => {
            ids[index] = $(el).val();
        })

        $.ajax({
            url: $('#coefficient-list').attr('data-url'),
            method: 'POST',
            data: {
                programChannelIds: ids
            },
            success: function(html){
                $('#coefficient-list').html(html)
            }
        })
    }

    $('input[name="ranking[programChannels][]"]').change(() => {
        getCoefficientParams();
    })
})