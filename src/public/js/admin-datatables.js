$(document).ready(function(){
    $('.admin-datatable').each((count, element) => {
        const $d = $(element);
        const options = {
            "aaSorting": [],
            "processing": true,
            "serverSide": true,
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $(nRow).find('td').each(function(index){
                    $(this).addClass('class' + index)
                })
            },
            "ajax": {
                url: $d.data("controller"),
                type: "POST",
                data: function (data) {
                    new FormData($($d.data('refer-form'))[0]).forEach(function (v, k) {
                        if (v !== '') {
                            data[k] = v;
                        }
                    });
                }
            }
        };
        $d.DataTable(options);
    });

    $('.select-filter').change(function() {
        $('#list-search').submit();
    })
})
