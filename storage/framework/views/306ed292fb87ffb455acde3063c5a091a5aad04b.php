<?php echo $__env->make('pages.backend.js.request.create_ticket', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<script>

    //function for tab key
    var tabindex = 1;
    $('input,select,button,.btn-remove').each(function () {
        if (this.type != "hidden") {
            $(this).attr("tabindex", tabindex);
            tabindex++;
        }
    });

    // https://stackoverflow.com/a/50535297/2782670
    $(document).on('focus', '.select2,input', function (e) {
        $(".swal2-container").addClass("hidden");
        $(".select2").find('.select2-selection__rendered').css({"background-color": "white"});
        $(this).find('.select2-selection__rendered').css({"background-color": "yellow"});

        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');
            s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
            });
        }
    });

    $(function () {

        var sd = new Date();
        var ed = new Date();
        sd = $("#from_date").val().replace(/\//g, '-');
        var m = moment(sd, 'YYYY-MM-DD');

        if ($("#from_date").val()) {
            var date = $("#from_date").val();
            var newdate = date.split("/").reverse().join("-");
            sd = new Date(newdate)
        }

        if ($("#to_date").val()) {
            var date = $("#to_date").val();
            var newdate = date.split("/").reverse().join("-");
            ed = new Date(newdate)
        }


        $('#from_date').datetimepicker({
            pickTime: false,
            format: "DD/MM/YYYY",
            maxDate: ed
        });

        $('#to_date').datetimepicker({
            pickTime: false,
            format: "DD/MM/YYYY",
            minDate: sd
        });

        $('#from_date').on("dp.change dp.update blur", function (e) {
            $('#to_date').data("DateTimePicker").setMinDate(e.date);
        });

        $('#to_date').on("dp.change dp.update blur", function (e) {
            $('#from_date').data("DateTimePicker").setMaxDate(e.date);
        });

    });

    $(".username-list,.status-list,.status_list").select2();

    var data = '';

    <?php if($data['id_list']): ?>
        data = <?php echo json_encode($data['data_list']) . ';'?>
    <?php endif; ?>

    call_data_table(data);

    //select2 for plate number
    $(".plate_number-list").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_fleet_in_request')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.PlateNumber,
                            id: item.ID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for driver
    $(".driver-list").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_driver_in_request')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.NameKh,
                            id: item.ID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for trailer
    $(".trailer_number-list").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_trailer_in_request')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.TrailerNumber,
                            id: item.ID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for reason
    $(".purpose-list").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_reason_in_request')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.Reason,
                            id: item.ID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for destination
    $(".destination-list").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_purpose_in_request')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.Code,
                            id: item.ID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for destination
    $(".request-list").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_request_number')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.RequestNumber,
                            id: item.ID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for reference number
    $(".select_reference_number").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_reference_number')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.ReferenceNumber,
                            id: item.ReferenceID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //select2 for reference number
    $(".select_supervisor_id").select2({
        tags: false,
        ajax: {
            dataType: 'json',
            url: '<?php echo e(url('/admin/request/get_supervisor_id')); ?>',
            data: function (params) {
                return {q: params.term}
            },
            processResults: function (data, params) {

                var resData = [];
                data.forEach(function (value) {
                    resData.push(value)
                });

                return {
                    results: $.map(resData, function (item) {
                        return {
                            text: item.NextName,
                            id: item.SupervisorID,
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    //script for fleet changing
    $("body").on("change", ".plate_number-list", function (evt) {
        evt.preventDefault();

        var FleetID = $(this).val();
        $("#fleet_name").val($(".plate_number-list option:selected").text());

        if (FleetID != "") {


            $.ajax({
                type: "get",
                url: "<?php echo e(url('admin/request/get_team')); ?>/" + FleetID,
                success: function (response) {
                    if (response.error) {
                        call_toast("error", response.error);
                    }

                    var option = '';

                    if (response.driver_id == 1) {

                        $.each(response.driver_list, function (key, val) {
                            option += '<option value="' + val.DriverID + '">' + val.NameKh + '</option>';
                            $("#driver_name").val(val.NameKh);
                        });

                        $(".driver-list").html(option);

                    } else {
                        $("#driver_name").val("All");
                        $(".driver-list").html("<option value='-1'>All</option>");
                    }

                }
            });
        }
    });

    //this for changing driver
    $("body").on("change", ".driver-list", function (evt) {
        evt.preventDefault();
        $("#driver_name").val($(".driver-list option:selected").text());
    });

    //this for changing trailer
    $("body").on("change", ".trailer_number-list", function (evt) {
        evt.preventDefault();
        $("#trailer_number").val($(".trailer_number-list option:selected").text());
    });

    //this for changing purpose
    $("body").on("change", ".purpose-list", function (evt) {
        evt.preventDefault();
        $("#purpose_name").val($(".purpose-list option:selected").text());
    });

    //this for changing destination
    $("body").on("change", ".destination-list", function (evt) {
        evt.preventDefault();
        $("#destination_name").val($(".destination-list option:selected").text());
    });

    //this for changing request
    $("body").on("change", ".request-list", function (evt) {
        evt.preventDefault();
        $("#request_name").val($(".request-list option:selected").text());
    });

    //this for changing driver
    $("body").on("change", ".select_reference_number", function (evt) {
        evt.preventDefault();
        $("#reference_name").val($(".select_reference_number option:selected").text());
    });

    //this for changing driver
    $("body").on("change", ".select_supervisor_id", function (evt) {
        evt.preventDefault();
        $("#supervisor_name").val($(".select_supervisor_id option:selected").text());
    });

    //this for delete request
    $("body").on("click", ".btn-delete", function (evt) {
        evt.preventDefault();

        var id = $(this).data("id");
        if (!$(".btn_delete" + id).hasClass('disabled')) {
            $(".btn_delete" + id).addClass("disabled");

            if (id > 0) {
                swal({
                    title: 'Are you sure?',
                    text: "You want to delete request?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    if (result.value) {
                        if (!$(".swal2-confirm").hasClass('disabled')) {
                            $(".swal2-confirm").addClass("disabled");
                            $.ajax({
                                url: "<?php echo e(url('admin/request/delete')); ?>",
                                type: 'POST',
                                data: {
                                    'id': id
                                },
                                success: function (response) {

                                    if (response.error == 0) {

                                        $(".remove_" + id).remove();
                                    } else {
                                        call_toast('error', response.error);
                                        $(".swal2-confirm").removeClass("disabled");
                                    }

                                    $(".btn_delete" + id).removeClass("disabled");
                                }
                            });
                        }


                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        $(".btn_delete" + id).removeClass("disabled");
                    }
                });
            }
        }

    });

</script>