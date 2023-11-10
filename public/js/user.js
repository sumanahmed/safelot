
//open status change modal
$(document).on('click', '#changeStatus', function () {
    $('#statusModal').modal('show');
    $('input[name=status_row_id]').val($(this).data('id'));
 });

//status change
$("#statusUpdate").click(function(){

    const rowId  = $('input[name=status_row_id]').val();
    const url    = `/users/status-change/${rowId}`

    $.ajax({
        type: 'POST',
        url: url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
        success: function (data) {
            $('#statusModal').modal('hide');
            toastr.success('Status updated successfully.')
            window.location.reload();
        }
    });
});

//open delete modal
$(document).on('click', '#deleteRow', function () {
    $('#deleteModal').modal('show');
    $('input[name=delete_row_id]').val($(this).data('id'));
 });

//destroy
$("#destroy").click(function(){

    const rowId  = $('input[name=delete_row_id]').val();
    const url    = `/users/destroy/${rowId}`

    $.ajax({
        type: 'POST',
        url: url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
        success: function (data) {
            $('#deletModal').modal('hide');
            // $('.city-' + $('input[name=delete_row_id]').val()).remove();
            toastr.success('Data deleted successfully.')
            window.location.reload();
        }
    });
});