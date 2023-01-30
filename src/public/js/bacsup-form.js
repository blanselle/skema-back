const ajax_diploma_channel=document.currentScript.getAttribute('data-ajax-diploma-channel');
const ajax_diploma_need_detail=document.currentScript.getAttribute('data-ajax-diploma-need-detail');

function refreshDiplomaChannels(parent, obj, diploma)
{
    let route = ajax_diploma_channel;
    $.ajax({
        url: route,
        data: {id: diploma.value},
        method: 'get',
        success: function (result) {
            obj.html(result)
            obj.children('option').first().prop('selected', true);
            let diplomaDetailDest = parent.find('.detail').first().children('input').first();
            let _query = {};
            _query.diplomaId = diploma.value;
            _query.diplomaChannelId = $(obj).val();
            isDiplomaNeedDetail(diplomaDetailDest, _query);
        }
    })
}

function isDiplomaNeedDetail(obj, _query)
{
    let route = ajax_diploma_need_detail;

    $.ajax({
        url: route,
        data: _query,
        method: 'get',
        success: function (result) {
            obj.val(null);
            obj.prop('disabled', !result.needDetail);
            obj.prop('required', result.needDetail)
        }
    })
}

const addFormToCollection = (e) => {

    const collectionHolder = e.target.parentElement.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
        /__name__/g,
        collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};

$(".diploma select").change(function() {
    let parent = $(this).parent('.diploma').parent().parent();
    let diplomaChannelDest = parent.find('.diploma-channel').first().children('select').first();
    refreshDiplomaChannels(parent, diplomaChannelDest, this);
})

$(".diploma-channel select").change(function() {
    let parent = $(this).parent('.diploma-channel').parent().parent();
    let diplomaDetailDest = parent.find('.detail').first().children('input').first();
    let _query = {};
    _query.diplomaId = parent.find('.diploma').first().children('select').first().val();
    _query.diplomaChannelId = this.value;
    isDiplomaNeedDetail(diplomaDetailDest, _query);
})

document
    .querySelectorAll('.add-item-link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });