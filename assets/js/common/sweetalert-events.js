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

        // window.location.href = deleteUrl;
        $.ajax({
            type: "POST",
            url: deleteUrl,
            success: function(response) {
                if (response.status === 'success') {
                    // Replace document content
                    if (typeof response.html !== 'undefined') {
                        let newDoc = document.open("text/html", "replace");
                        newDoc.write(response.html);
                        newDoc.close();
                    }

                    swal("Success!", response.message, "success");
                } else {
                    swal("Error!", response.message, "error");
                }
            },
            error: function() {
                swal("Error", "Request send error", "error");
            }
        });
    });
});