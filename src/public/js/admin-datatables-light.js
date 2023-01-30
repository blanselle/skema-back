$(document).ready(function(){
    $('.admin-datatable').each((count, element) => {
        const $d = $(element);
        const options = {
            "searching": false,
            "columnDefs": [{
                "targets": $(this).find('th:last').index(),
                "orderable": false
            }],
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $(nRow).find('td').each(function(index){
                    $(this).addClass('class' + index)
                })
            },
        };
        $d.DataTable(options);
    });
})