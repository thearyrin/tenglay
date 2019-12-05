<script type="text/javascript">
    //this script for onclick on new button
    $("body").on("click", ".btn-new", function () {
        clear();
        $("#username").focus();
        $(".modal-title").text("Form Create User");
        $("#users-modal").modal("show");
        $(".status_show").addClass("hidden");
    });

    //this script for close on click
    $("body").on("click", ".close-form-user", function () {
        clear();
        $(".user_list").attr("disabled", false).prop("checked", false);

        $(".btn-edit,.btn-delete").addClass("disabled");
        $(".btn-new").removeClass("disabled");
    });

    //this script is for reset data script
    $("body").on("click", ".btn-cancel", function () {
        clear();
    });


    //this script is for save data
    $("body").on("click", ".btn-save", function () {

        var id = $("#id").val();
        var user_id = $("#user_id").val();
        var username = $("#username").val();
        var displayname = $("#displayname").val();
        var password = $("#password").val();
        var phone = $("#phone").val();
        var old_user = $("#old_user").val();
        var old_pass = $("#old_pass").val();
        var group = $(".list_group option:selected").val();

        if (username == "") {

            $(".msg-form-users").text("Please Fill UserName").removeClass("hidden text-success").addClass("text-danger");
            $("#username").focus();

        } else if ((password == "") || ($("#password").val().length < 4)) {
            // } else if ((password == "") || (!$("#password").val().match(/^\d{4}$|^\d{8}$/))) {

            if (password == "") {
                $(".msg-form-users").text("Please Fill Password").removeClass("hidden text-success").addClass("text-danger");

            } else if ((id != "") && (($("#old_pass").val().length) > ($("#password").val().length))) {
                $(".msg-form-users").text("Password at least " + ($("#old_pass").val().length) + " digits.").removeClass("hidden text-success").addClass("text-danger");

            } else if ((id == "") && ($("#password").val().length < 6)) {
                $(".msg-form-users").text("Password at least 6 digits.").removeClass("hidden text-success").addClass("text-danger");
            }

            $("#password").focus();

        } else if (displayname == "") {

            $(".msg-form-users").text("Please Fill Display Name").removeClass("hidden text-success").addClass("text-danger");
            $("#displayname").focus();

        } else if (phone == "") {

            $(".msg-form-users").text("Please Fill Phone Number").removeClass("hidden text-success").addClass("text-danger");
            $("#phone").focus();

        } else if (group == "") {
            $(".msg-form-users").text("Please Select Group").removeClass("hidden text-success").addClass("text-danger");

        } else {
            var html = "";

            $.ajax({
                // type: "post",
                url: "<?php echo e(url('admin/setting/users/save')); ?>",
                // data: $(".form-show").serialize(),
                data: new FormData($(".form-show-user")[0]),
                dataType: 'json',
                async: false,
                type: 'post',
                processData: false,
                contentType: false,
                success: function (response) {

                    if (response.error) {
                        // $(".text-msg").text(response.error).removeClass("hidden text-success").addClass("text-danger");
                        call_toast('error', response.error);
                        // return false;

                    } else {
                        var style = '';
                        if (response.data.PhotoBase64 == "") {
                            style = "style='width:50px;height:50px;'";
                        }

                        var status = "Active";
                        if (response.data.Status == 0) {
                            status = "Inactive";
                        }

                        var re = "*";
                        var re_password = response.data.Password;
                        re_password = re.repeat(re_password.length);


                        if (id) {

                            // html+='<tr class="remove'+response.data.ID+'">';
                            html += '<td>';
                            html += '<input type="checkbox" id="check-all" class="user_list" value="' + response.data.ID + '" style="width: 18px; height: 18px;">';
                            html += '<input type="hidden" id="username' + response.data.ID + '" value="' + response.data.UserName + '">';
                            html += '<input type="hidden" id="password' + response.data.ID + '" value="' + response.data.Password + '">';
                            html += '<input type="hidden" id="displayname' + response.data.ID + '" value="' + response.data.DisplayName + '">';
                            html += '<input type="hidden" id="phone' + response.data.ID + '" value="' + response.data.PhoneNumber + '">';
                            html += '<input type="hidden" id="photo' + response.data.ID + '" value="' + response.data.PhotoBase64 + '">';
                            html += '<input type="hidden" id="ImageBase64' + response.data.ID + '" value="' + response.data.ImageBase64 + '">';
                            html += '<input type="hidden" id="status' + response.data.ID + '" value="' + response.data.Status + '">';
                            html += '<input type="hidden" id="group_id_user' + response.data.ID + '" value="' + response.data.GroupID + '">';
                            html += '</td>';
                            html += '<td>' + response.data.ID + '</td>';
                            html += '<td>' + response.data.UserName + '</td>';
                            html += '<td>' + re_password + '</td>';
                            html += '<td>' + response.data.DisplayName + '</td>';
                            html += '<td>' + response.data.PhoneNumber + '</td>';
                            html += '<td>' + response.data.Name + '</td>';
                            html += '<td>' + status + '</td>';
                            html += '<td ' + style + '><img src="data:image/jpeg;base64,' + response.data.ImageBase64 + '" alt="' + response.data.UserName + '" style="width: 50px; height: 50px;"></td>';
                            html += '<td><button class="btn btn-sm btn-primary btn-user-group" data-name="' + response.data.UserName + '" data-id="' + response.data.ID + '"><i class="fa fa-plus"></i></button></td>';
                            html += '<td><button class="btn btn-sm btn-primary btn-round-trip" data-name="' + response.data.UserName + '" data-id="' + response.data.ID + '"><i class="fa fa-plus"></i></button></td>';
                            html += '<td><button class="btn btn-sm btn-primary btn-permission" data-name="' + response.data.UserName + '" data-id="' + response.data.ID + '"><i class="fa fa-plus"></i></button></td>';


                            $(".remove" + id).html(html);
                            // $(".text-msg").text("Data User Edited").removeClass("hidden text-danger").addClass("text-success");
                            call_toast('success', "Data User Edited");

                            if (id == user_id) {

                                if ((username != old_user) || (password != old_pass)) {
                                    window.location = "<?php echo e(url('admin/logout')); ?>";
                                }
                            }

                            $(".modal-title").text("");
                            $("#users-modal").modal("hide");

                            $(".user_list").attr("disabled", false);
                            $(".btn-edit,.btn-delete").addClass("disabled");
                            $(".btn-new").removeClass("disabled");

                        } else {

                            html += '<tr class="remove' + response.data.ID + '">';
                            html += '<td>';
                            html += '<input type="checkbox" id="check-all" class="user_list" value="' + response.data.ID + '" style="width: 18px; height: 18px;">';
                            html += '<input type="hidden" id="username' + response.data.ID + '" value="' + response.data.UserName + '">';
                            html += '<input type="hidden" id="password' + response.data.ID + '" value="' + response.data.Password + '">';
                            html += '<input type="hidden" id="displayname' + response.data.ID + '" value="' + response.data.DisplayName + '">';
                            html += '<input type="hidden" id="phone' + response.data.ID + '" value="' + response.data.PhoneNumber + '">';
                            html += '<input type="hidden" id="photo' + response.data.ID + '" value="' + response.data.PhotoBase64 + '">';
                            html += '<input type="hidden" id="ImageBase64' + response.data.ID + '" value="' + response.data.ImageBase64 + '">';
                            html += '<input type="hidden" id="status' + response.data.ID + '" value="' + response.data.Status + '">';
                            html += '<input type="hidden" id="group_id_user' + response.data.ID + '" value="' + response.data.GroupID + '">';
                            html += '</td>';
                            html += '<td>' + response.data.ID + '</td>';
                            html += '<td>' + response.data.UserName + '</td>';
                            html += '<td>' + re_password + '</td>';
                            html += '<td>' + response.data.DisplayName + '</td>';
                            html += '<td>' + response.data.PhoneNumber + '</td>';
                            html += '<td>' + response.data.Name + '</td>';
                            html += '<td>' + status + '</td>';
                            html += '<td ' + style + '><img src="data:image/jpeg;base64,' + response.data.ImageBase64 + '" alt="' + response.data.UserName + '" style="width: 50px; height: 50px;"></td>';
                            html += '<td><button class="btn btn-sm btn-primary btn-user-group" data-name="' + response.data.UserName + '" data-id="' + response.data.ID + '"><i class="fa fa-plus"></i></button></td>';
                            html += '<td><button class="btn btn-sm btn-primary btn-round-trip" data-name="' + response.data.UserName + '" data-id="' + response.data.ID + '"><i class="fa fa-plus"></i></button></td>';
                            html += '<td><button class="btn btn-sm btn-primary btn-permission" data-name="' + response.data.UserName + '" data-id="' + response.data.ID + '"><i class="fa fa-plus"></i></button></td>';

                            html += '</tr>';

                            $(".body-table").prepend(html);
                            // $(".text-msg").text("Data User saved").removeClass("hidden text-danger").addClass("text-success");
                            call_toast("success", "Data User saved");

                        }

                        $(".dataTables_empty").parent().remove();
                    }

                    // $(".btn-new,.btn-edit,.btn-delete").attr("disabled", false);
                    // $(".user_list").attr("disabled", false).prop("checked", false);
                    // $(".form-show,.save_cancel,.text-title").addClass("hidden");
                    clear();
                }
            });

            $(".msg-form-users").text("").removeClass("text-danger text-success").addClass("hidden");
        }
    });

    //this script is for on check on station list
    $("body").on("change", ".user_list:checkbox", function () {
        if ($(this).is(":checked")) {

            $(".user_list").attr("disabled", true);
            $(this).attr("disabled", false);
            $(".btn-edit,.btn-delete").removeClass("disabled");
            $(".btn-new").addClass("disabled");
            $(".text-msg").text("").addClass("hidden");

        } else {

            $(".user_list").attr("disabled", false);
            $(".btn-edit,.btn-delete").addClass("disabled");
            $(".btn-new").removeClass("disabled");
            clear();
        }
    });

    //this script for onclick button editing
    $("body").on("click", ".btn-edit", function () {

        var id = "";
        $(".user_list:checked").each(function () {
            id = $(this).val();
        });

        if (id) {

            swal({
                title: 'Are you sure?',
                text: "You want to edit this users " + $("#displayname" + id).val() + " ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {

                if (result.value) {
                    $("#username").val($("#username" + id).val());
                    $("#password").val($("#password" + id).val());
                    $("#displayname").val($("#displayname" + id).val());
                    $("#phone").val($("#phone" + id).val());
                    $("#file_name").val($("#photo" + id).val());
                    var photo = $("#photo" + id).val();
                    var image_base = $("#ImageBase64" + id).val();

                    $("#id").val(id);

                    if (image_base != "") {
                        $('#photo_show').attr('src', 'data:image/jpeg;base64,' + image_base);
                    } else {
                        $('#photo_show').attr('src', '<?php echo e(asset('images')); ?>/' + photo);
                    }

                    console.log(image_base);

                    $(".status_show").removeClass("hidden");
                    var status = $("#status" + id).val();
                    $(".select_status").val(status).trigger('change.select2');
                    $(".list_group").val($("#group_id_user" + id).val()).trigger('change.select2');

                    $(".btn-delete").addClass("disabled");

                    $(".modal-title").text("Form Update User " + $("#displayname" + id).val());
                    $("#users-modal").modal("show");

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $(".btn-edit,.btn-delete").addClass("disabled");
                    $(".btn-new").removeClass("disabled");
                    $(".user_list").attr("disabled", false).prop("checked", false);
                }
            });

        } else {
            // $(".text-msg").text("Please Select Item to Edit").removeClass("hidden text-success").addClass("text-danger");
            call_toast('error', "Please Select Item to Edit");
        }
    });

    //this script is for onclick of button delete
    $("body").on("click", ".btn-delete", function () {
        var id = "";
        var user_id = $("#user_id").val();

        $(".user_list:checked").each(function () {
            id = $(this).val();
        });

        if (id) {

            swal({
                title: 'Are you sure?',
                text: "You want to delete this users " + $("#displayname" + id).val() + " ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'

            }).then((result) => {

                if (result.value) {

                    $.ajax({
                        type: "post",
                        url: "<?php echo e(url("admin/setting/users/delete")); ?>",
                        data: {
                            'id': id,
                            'user_id': user_id
                        },
                        success: function (response) {

                            if (response.error) {

                                // $(".text-msg").text(response.error).removeClass("hidden text-success").addClass("text-danger");
                                call_toast('error', response.error);

                            } else {

                                // $(".text-msg").text(response.message).removeClass("hidden text-danger").addClass("text-success");
                                call_toast('success', response.message);

                                $(".btn-new").removeClass("disabled");
                                $(".user_list").attr("disabled", false).prop("checked", false);
                                $(".btn-edit,.btn-delete").addClass("disabled");
                                $(".remove" + id).remove();
                            }
                        }
                    });

                    // $(".btn-edit").attr("disabled", true);
                } else if (result.dismiss === Swal.DismissReason.cancel) {

                    $(".btn-edit,.btn-delete").addClass("disabled");
                    $(".btn-new").removeClass("disabled");
                    $(".user_list").attr("disabled", false).prop("checked", false);
                }
            });
        } else {
            // $(".text-msg").text("Please Select Item to Delete").removeClass("hidden text-success").addClass("text-danger");
            call_toast('error', "Please Select Item to Delete");
        }

    });

    //this function is for clear data
    function clear() {
        $("#username,#password,#displayname,#phone").val("");
        $("#username").focus();
        $('#photo_show').attr('src', '');
        $('#user_photo').val("");
        $('#file_name').val("");
        $('#id').val("");
        $(".list_group").val("").trigger('change.select2');
    }

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo_show').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#user_photo").change(function () {
        readURL(this);
    });

    $('#username').keydown(function (e) {
        if (e.keyCode == 32) {
            return false;
        }
    });

</script>
