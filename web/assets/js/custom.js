function activateTables()
{
    $('.datatable').dataTable({
        responsive: {
            details: true
        },
        language: dataTableLithuanian,
        bSort: false
    });
}

$(document).ready(function(){
    activateTables();
});
