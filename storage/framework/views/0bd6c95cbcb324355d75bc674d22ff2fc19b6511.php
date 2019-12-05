<script>
    //this script for onclick on button permission
    $("body").on("click", ".btn-permission", function () {

        $("input[name='current_user']").val($(this).data("id"));

        var logged_user_id = $("input[name='logged_id']").val();
        var option_add = '';
        var option_re = '';
        var current_id = $(this).data("id");

        if ($(".data-user-permission-show")[0]) {
            $.ajax({
                type: "post",
                url: "<?php echo e(url('admin/setting/permission/get')); ?>",
                data: {
                    'current_user_id': $(this).data("id"),
                    'logged_user_id': logged_user_id
                },
                beforeSend: function () {
                    $(".image_more_loading").removeClass("hidden");
                },
                success: function (response) {
                    if (response.error) {
                        call_toast("error", response.error);
                        $("#user-permission-modal").modal("hide");
                        return false;
                    }

                    if (response.id) {

                        $.each(response.data, function (key, val) {
							var check_view = '';
							var check_add = '';
							var check_edit = '';
							var check_delete = '';
							if (val.ViewPage == 1) {
                                check_view = 'checked';
                            }

                            if (val.AddPage == 1) {
                                check_add = 'checked';
                            }

                            if (val.EditPage == 1) {
                                check_edit = 'checked';
                            }

                            if (val.DeletePage == 1) {
                                check_delete = 'checked';
                            }

                            option_re += '<tr>';
                            option_re += '<td>' + val.ID + '</td>';
                            option_re += '<td>' + val.Name + '</td>';
                            option_re += '<td>' +
                                '<input type="checkbox" name="view' + val.ID + '" ' + check_view + ' class="view" style="width: 18px; height: 18px;"/>' +
                                '<input type="hidden" name="menu_id[]" class="menu_id"  value="' + val.ID + '"/>' +
                                '<input type="hidden" name="logged_id" class="logged_id"  value="' + logged_user_id + '"/>' +
                                '<input type="hidden" name="current_id" class="current_id"  value="' + current_id + '"/>' +
                                '</td>';
                            option_re += '<td><input type="checkbox" name="add' + val.ID + '" ' + check_add + ' class="add" style="width: 18px; height: 18px;"/></td>';
                            option_re += '<td><input type="checkbox" name="edit' + val.ID + '" ' + check_edit + ' class="edit" style="width: 18px; height: 18px;"/></td>';
                            option_re += '<td><input type="checkbox" name="delete' + val.ID + '" ' + check_delete + ' class="delete" style="width: 18px; height: 18px;"/></td>';
                            option_re += '</tr>';
                        });

                        $(".body-menu").html(option_re);
                    }
                },
                complete: function () {
                    $(".image_more_loading").addClass("hidden");
                }
            });
        }

        var title = $(this).data("name");
        $(".modal-title").text("Add Permission For User: " + title);
        $("#user-permission-modal").modal("show");
    });

    //this script for onclicking on button right
    $("body").on("click", "#btnRightCode", function () {

        var current_user_id = $("input[name='current_user']").val();
        var logged_user_id = $("input[name='logged_id']").val();

        var name = $("#canselect_code option:selected").text();
        var id = $("#canselect_code option:selected").val();

        if (id) {
            $.ajax({

                type: "post",
                url: "<?php echo e(url('admin/setting/permission/save')); ?>",
                data: {
                    'process_code': id,
                    'user_logged': logged_user_id,
                    'current_user': current_user_id
                },

                success: function (response) {

                    if (response.error) {
                        call_toast("error", response.error);
                        return false;
                    }

                    if (response.id) {

                        $("#canselect_code option[value='" + id + "']").remove();

                        $("#isselect_code").append('<option value="' + id + '">' + name + '</option>');

                        var selectList = $('#isselect_code option');

                        selectList.sort(function (a, b) {
                            a = a.value;
                            b = b.value;
                            a = a.split("SYS")[1];
                            b = b.split("SYS")[1];

                            return a - b;
                        });

                        console.log(selectList);
                        $('#isselect_code').html(selectList);
                        //
                        call_toast("success", response.msg);

                        return true;
                    }

                    call_toast("error", response.msg);
                    return false;
                }

            });
        } else {
            call_toast("error", "Please select menu to add");
        }


    });

    //this script for onclicking on button left
    $("body").on("click", "#btnLeftCode", function () {

        var current_user_id = $("input[name='current_user']").val();
        var logged_user_id = $("input[name='logged_id']").val();

        var name = $("#isselect_code option:selected").text();
        var id = $("#isselect_code option:selected").val();
        if (id) {
            $.ajax({
                type: "post",
                url: "<?php echo e(url('admin/setting/permission/delete')); ?>",
                data: {
                    'process_code': id,
                    'user_logged': logged_user_id,
                    'current_user': current_user_id
                },
                success: function (response) {
                    if (response.error) {
                        call_toast("error", response.error);
                        return false;
                    }

                    if (response.id) {
                        $("#isselect_code option[value='" + id + "']").remove();

                        $("#canselect_code").append('<option value="' + id + '">' + name + '</option>');

                        var selectList = $('#canselect_code option');

                        selectList.sort(function (a, b) {
                            a = a.value;
                            b = b.value;
                            a = a.split("SYS")[1];
                            b = b.split("SYS")[1];

                            return a - b;
                        });

                        $('#canselect_code').html(selectList);

                        call_toast("success", response.msg);

                        return true;
                    }
                    call_toast("error", response.msg);
                    return false;
                }
            });
        } else {
            call_toast("error", "Please select menu to move.")
        }
    });

    //this script for checkbox view permission
    $("body").on("change", ".check_view_all", function (evt) {
        evt.preventDefault();
        if ($(this).is(":checked")) {
            $(".view").prop("checked", true);
        } else {
            $(".view").prop("checked", false);
        }
    });

    //this script for checkbox add permission
    $("body").on("change", ".check_add_all", function (evt) {
        evt.preventDefault();
        if ($(this).is(":checked")) {
            $(".add").prop("checked", true);
        } else {
            $(".add").prop("checked", false);
        }
    });

    //this script for checkbox edit permission
    $("body").on("change", ".check_edit_all", function (evt) {
        evt.preventDefault();
        if ($(this).is(":checked")) {
            $(".edit").prop("checked", true);
        } else {
            $(".edit").prop("checked", false);
        }
    });

    //this script for checkbox delete permission
    $("body").on("change", ".check_delete_all", function (evt) {
        evt.preventDefault();
        if ($(this).is(":checked")) {
            $(".delete").prop("checked", true);
        } else {
            $(".delete").prop("checked", false);
        }
    });

    //this script for click add button on permission
    $("body").on("click", ".btn_save_permission", function (evt) {
        evt.preventDefault();

        $.ajax({
            url: "<?php echo e(url('admin/setting/permission/save')); ?>",
            type: 'POST',
            data: $('.form-permission').serialize(),
            success: function (response) {
                $(".image_more_loading").addClass("hidden");
                if (response.error == 0) {
                    call_toast("success", response.msg);
                } else {
                    call_toast("error", response.error);
                }
            },
            beforeSend: function () {
                $(".image_more_loading").removeClass("hidden");
            },
        });
    });
</script>