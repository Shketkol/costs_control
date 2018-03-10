$(document).on('click', '.delete-btn', function() {
    // Get name
    let name = $(this).data('name');
    let deleteUrl = $(this).data('href');
    let warningText = $(this).data('success') ? $(this).data('success') : 'Do you really want to delete "' + name + '"?';
    let successMessage = $(this).data('success');
    let errorMessage = $(this).data('error');

    swal({
        title: "Are you sure?",
        text: warningText,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    }, function(isConfirm) {
        if (!isConfirm) return;

        $.ajax({
            type: "POST",
            url: deleteUrl,
            success: function(response) {
                if (response.status === 'success') {
                    swal("Success!", response.message, "success");
                } else {
                    swal("Error!", response.message, "error");
                }
                // $.pjax.reload({container:"#pjax-container"});
            },
            error: function() {
                swal("Error", "Request send error", "error");
            }
        });
    });
});