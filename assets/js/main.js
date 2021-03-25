$('#review-table').DataTable({
    dom: '<"complete-filter"><lf<t>ip>',
    initComplete: function () {
        // we only need day and location filter.
        this.api().columns([6]).every(function (index) {
            // below function will add filter to remove previous/completed appointments
            var column = this;
            $('<input type="checkbox" id="complete-filter" name="old" checked/>')
                .appendTo($('.complete-filter'))
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    if (document.getElementById('complete-filter').checked) {

                        column
                            .search('^((?!Completed).)*$', true, false)
                            .draw();
                    } else {
                        column
                            .search("$", true, false)
                            .draw();
                    }
                });

        });
    }
});

$(".dataTables_wrapper").css("width", "100%");

//once page loads trigger completed filter
window.onload = function () {
    $("#complete-filter").trigger('change')
}
