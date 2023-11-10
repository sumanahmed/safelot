//create offer
$("#save").click(function (e) {
    e.preventDefault();

    const formData = {
        first_name: $("#first_name").val(),
        last_name:  $("#last_name").val(),
        email:      $("#email").val(),
        password:   $("#password").val(),
    }
    
    $.ajax({
        type:'POST',
        url: '/users/store',
        headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
        data: formData,
        success:function(response){
            console.log(response)
            if((response.errors)){
                $('.firstNameError').text(response.errors.first_name ? response.errors.first_name : '');                       
                $('.lastNameError').text(response.errors.last_name ? response.errors.last_name : '');                       
                $('.emailError').text(response.errors.email ? response.errors.email : '');                       
                $('.passwordError').text(response.errors.password ? response.errors.password : '');                       
            }

            if(response.status == 201){
                window.location.reload();

                // const type =  response.data.type == 1 ? 'Admin User' : (response.data.type == 2 ? 'Dealer' : 'Consumer')

                // var statusbutton =  response.data.status == 2 ?
                //                         '<a href="#" class="btn btn-raised btn-xs btn-success" data-toggle="modal" id="changeStatus" data-target="#statusModal" data-id="'+ response.data.id +'" title="Active"><i class="fas fa-check"></i></a>'
                //                     :
                //                         '<a href="#" class="btn btn-raised btn-xs btn-warning" data-toggle="modal" id="changeStatus" data-target="#statusModal" data-id="'+ response.data.id +'" title="Inactive"><i class="fas fa-ban"></i></a>' 

                // $('#createModal').modal('hide');
                // $("#allUser").append('' +
                //     '<tr class="user-'+ response.data.id +'">\n' +
                //         '<td>'+ response.data.first_name + ' ' + response.data.last_name +'</td>\n' +
                //         '<td>'+ response.data.email +'</td>\n' +
                //         '<td>'+ type +'</td>\n' +
                //         '<td style="vertical-align: middle;text-align: center;">\n' +  
                //             statusbutton
                //            +'<a href="#" class="btn btn-raised btn-xs btn-primary" title="View"  data-id="'+ data.id +'" data-first_name="'+ data.first_name +'" data-last_name="'+ data.last_name +'" data-email="'+ data.email +'"><i class="fas fa-eye"></i></a>' +
                //             '<a href="#" class="btn btn-raised btn-xs btn-info" title="Edit" data-id="'+ data.id +'" data-first_name="'+ data.first_name +'" data-last_name="'+ data.last_name +'" data-email="'+ data.email +'"><i class="fas fa-edit"></i></a>' +
                //             '<a href="#" class="btn btn-raised btn-xs btn-danger" data-toggle="modal" id="deleteRow" data-target="#deleteModal" data-id="{{ $item->id }}" data-status="0" title="Delete"><i class="fas fa-trash"></i></a>'+                     
                //         '</td>\n' +
                //     '</tr>'+
                // '');
                // $("#first_name").val('');
                // $("#last_name").val('');
                // $("#email").val('');
                // $("#password").val('');
                // toastr.success('User created successfully')
            }
        }
    });
});


//open edit City modal
$(document).on('click', '.edit', function () {
    $('#edit_id').val($(this).data('id'));
    $('#edit_first_name').val($(this).data('first_name'));
    $('#edit_last_name').val($(this).data('last_name'));
    $('#edit_email').val($(this).data('email'));


    $('#editModal').modal('show');
 });

// update
$("#update").click(function (e) {
    e.preventDefault();

    var id      = $("#edit_id").val();

    const formData = {
        first_name: $("#edit_first_name").val(),
        last_name:  $("#edit_last_name").val(),
        email:      $("#edit_email").val(),
        password:   $("#edit_password").val(),
    }

    $.ajax({
        type:'POST',
        url: `/users/update/${ id }`,
        headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
        data: formData,
        success:function(response){
            if((response.errors)){
                $('.editFirstNameError').text(response.errors.first_name ? response.errors.first_name : '');                       
                $('.editLastNameError').text(response.errors.last_name ? response.errors.last_name : '');                       
                $('.editEmailError').text(response.errors.email ? response.errors.email : '');                       
                $('.editPasswordError').text(response.errors.password ? response.errors.password : '');                       
            }

            if(response.status == 201){
                window.location.reload();
                // $('#editCityModal').modal('hide');
                // $("tr.city-"+ response.data.id).replaceWith('' +
                //     '<tr class="city-'+ response.data.id +'">\n' +
                //         '<td>'+ response.data.name +'</td>\n' +
                //         '<td>'+ response.data.bn_name +'</td>\n' +
                //         '<td>'+ response.data.district_name +'</td>\n' +
                //         '<td style="vertical-align: middle;text-align: center;">\n' +
                //             '<button class="btn btn-warning" data-toggle="modal" id="editCity" data-target="#editCityModal" data-id="'+ response.data.id +'" data-name="'+ response.data.name +'" data-bn_name="'+ response.data.bn_name +'" data-district_id="'+ response.data.district_id +'" title="Edit"><i class="fas fa-edit"></i></button>\n' +
                //             '<button class="btn btn-danger" data-toggle="modal" id="deleteCity" data-target="#deleteCityModal" data-id="'+ response.data.id +'" title="Delete"><i class="fas fa-trash"></i></button>\n' +
                //         '</td>\n' +
                //     '</tr>'+
                // '');
                // toastr.success('City Updated.')
            }
        }
    });
});

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