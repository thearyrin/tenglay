<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/colReorder.dataTables.min.css')); ?>">

    <style>
        .select2 {
            width: 100% !important;
        }

        .content-form {
            width: 100%;
            padding: 10px 17px;
            display: inline-block;
            background: #fff;
            border: 2px solid #E6E9ED;
            -webkit-column-break-inside: avoid;
            -moz-column-break-inside: avoid;
            column-break-inside: avoid;
            opacity: 1;
            transition: all .2s ease;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Round Trip List
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-form">
                        <form id="form-horizontal form-label-left" class="form-data" method="post"
                              action="<?php echo e(url('admin/round/list')); ?>" autocomplete="off">
                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                            <div class="table-responsive">
                                <table class="table table-striped table-responsive table-bordered col-lg-12 col-md-12 col-sm-12 col-xs-12 table_parent"
                                       width="100%">
                                    <tbody>
                                    <tr>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">From Date</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left"
                                                       name="from_date"
                                                       id="from_date" placeholder="From Date"
                                                       style="padding-left: 33px;"
                                                       aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['from_date']); ?>" required>
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      aria-hidden="true"
                                                      style="margin-left: -6% !important; margin-top: 5px !important;"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">To Date</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left" name="to_date"
                                                       id="to_date" placeholder="To Date" style="padding-left: 33px;"
                                                       aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['to_date']); ?>"
                                                       required>
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      aria-hidden="true"
                                                      style="margin-left: -6% !important; margin-top: 5px !important;"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Truck No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="plate_number" id="plate_number"
                                                        class="js-example-basic-single js-states form-control plate_number-list">
                                                    <option value="<?php echo e($data['fleet_id']); ?>"><?php echo e($data['fleet_name']); ?>

                                                    </option>

                                                </select>
                                                <input type="hidden" id="fleet_name" name="fleet_name"
                                                       value="<?php echo e($data['fleet_name']); ?>">
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Ticket No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="ticket_id" id="ticket_id"
                                                        class="js-example-basic-single js-states form-control ticket_number-list">
                                                    <option value="<?php echo e($data['ticket_id']); ?>"><?php echo e($data['ticket_number']); ?>

                                                    </option>
                                                </select>
                                                <input type="hidden" name="ticket_number" id="ticket_number"
                                                       value="<?php echo e($data['ticket_number']); ?>">
                                            </div>
                                        </td>

                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">RoundTrip</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="roundtrip_id" id="roundtrip_id"
                                                        class="js-example-basic-single js-states form-control roundtrip_list">
                                                    <?php if($data['round_data_id']): ?>
                                                        <?php $__currentLoopData = $data['round_data_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($round->RoundTrip); ?>" <?php echo e(($round->RoundTrip==$data['round_id']?"selected":"")); ?>><?php echo e($round->RoundTrip); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                    <label for="button">
                                        <button type="submit" class="btn btn-primary btn-sm"><i
                                                    class="fa fa-search"></i> Filter
                                        </button>

                                        <?php if($data['id']): ?>
                                            <a href="<?php echo e(url('admin/round/export_list')); ?>"
                                               class="btn btn-success btn-sm">
                                                <i class="fa fa-file-excel-o"></i>
                                                Export</a>
                                        <?php endif; ?>

                                        <?php if($data['search']): ?>
                                            <a href="<?php echo e(url('admin/round/list')); ?>"
                                               class="btn btn-danger btn-sm">Clear</a>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table id="round-list-table" class="table table-striped table-bordered" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>DateTime</th>
                                <th>Ticket No.</th>
                                <th>RoundTrip</th>
                                <th>Type</th>
                                <th>Truck No.</th>
                                <th>Trailer No.</th>
                                <th>Driver</th>
                                <th>Driver ID</th>
                                <th>Containers(Feet)</th>
                                <th>Customers</th>
                                <th>Destination</th>
                                <th>Delivery Location</th>
                                <th>Remark</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="data-show">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="edit_modal" class="modal fade">
        <div class="modal-dialog" style="width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-form-empty" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title text-center edit_round_trip_title">Edit Round Trip For Ticket Number: </h4>
                </div>
                <div class="modal-body">
                    <div class="row content-body-edit-round-trip" id="data-edit-round-trip">
                        <p class="text-center hidden text-msg-round-empty" style="font-size: 16px;"></p>
                        <form action="#" class="form-edit-round-trip">

                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-form-edit-round-trip" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('vendors/select2/dist/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/moment/min/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrapValidator.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.colReorder.min.js')); ?>"></script>
    <script>

        $(".status-list,.roundtrip_list").select2();

        $(".plate_number-list").select2({
            placeholder: '--Please Select--',
            // tags: true,
            // tokenSeparators: [',', ' '],
            maximumInputLength: 13,
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/round/get_fleet_in_round_trip')); ?>',
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
                                text: item.TruckNo,
                                id: item.ID,
                            }
                        })
                    };
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".plate_number-list").on("change", function (evt) {
            evt.preventDefault();
            $("#fleet_name").val($(".plate_number-list option:selected").text());
        });

        $(".ticket_number-list").select2({
            placeholder: '--Please Select--',
            // tags: true,
            // tokenSeparators: [',', ' '],
            maximumInputLength: 13,
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/round/get_ticket')); ?>',
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
                                text: item.TicketNumber,
                                id: item.TicketID,
                            }
                        })
                    };
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".ticket_number-list").on("change", function (evt) {
            evt.preventDefault();
            $("#ticket_number").val($(".ticket_number-list option:selected").text());
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
                minDate: sd,
            });

            $('#from_date').on("dp.change dp.update blur", function (e) {
                $('#to_date').data("DateTimePicker").setMinDate(e.date);
            });

            $('#to_date').on("dp.change dp.update blur", function (e) {
                $('#from_date').data("DateTimePicker").setMaxDate(e.date);
            });

        });

        var data = '';
        var edit_per = "<?php echo $data['edit_per']?>";
        <?php if($data['id']): ?>
            data = <?php echo json_encode($data['list']) . ';'?>
        <?php endif; ?>
        $(function () {
            $('#round-list-table').DataTable({
                'paging': true,
                'lengthChange': true,
                "pageLength": 10,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true,
                "lengthMenu": [[10, 15, 25, 35, 50, -1], [10, 15, 25, 35, 50, "All"]],
                'order': [[0, 'DESC']],
                'data': data,
                stateSave: true,
                colReorder: true,
                columns: [
                    {data: 'ID'},
                    {data: 'DateTimeRoundTrip'},
                    {data: 'TicketNumber'},
                    {data: 'RoundTrip'},
                    {
                        data: 'TypeName'
                    },
                    {data: 'TruckNo'},
                    {data: 'TrailerNumber'},
                    {data: 'NameKh'},
                    {data: 'CodeID'},
                    {
                        data: 'ContainerNumber1', render: function (data, type, row) {
                            if ((data != "") || (row['ContainerNumber2'] != "")) {
                                return data + "(" + row['Feet1'] + ")," + row['ContainerNumber2'] + "(" + row['Feet2'] + ")";
                            } else if (data != "") {
                                return data + "(" + row['Feet1'] + ")";
                            } else if (row['ContainerNumber2'] != '') {
                                return row['ContainerNumber2'] + "(" + row['Feet2'] + ")";
                            }
                            return '';
                        }
                    },
                    {
                        data: 'CusName1', render: function (data, type, row) {
                            if ((data != null) || (row['CusName2'] != null)) {
                                return data + "," + row['CusName2'];
                            } else if (data != null) {
                                return data;
                            } else if (row['CusName2'] != null) {
                                return row['CusName2'];
                            }
                            return '';
                        }
                    },
                    {data: 'Code'},
                    {data: 'DeliveryLocation'},
                    {data: 'Remark'},
                    {
                        data: 'ID', render: function (data, type, row) {
                            if (edit_per) {
                                return '<a href="#" class="btn btn-primary btn-sm btn_edit_round_trip" data-ticket="' + row["TicketNumber"] + '" data-id="' + data + '"><i class="fa fa-pencil-square-o"></i></a>';
                                // return '';
                            }
                            return '';
                        }
                    },

                ],
            });
        });

        $("body").on("click", ".btn_edit_round_trip", function (evt) {
            evt.preventDefault();

            var id = $(this).data("id");
            var ticket = $(this).data("ticket");

            if (id) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo e(url('admin/round/edit')); ?>',
                    data: {
                        'id': id,
                        'ticket': ticket
                    },
                    success: function (response) {

                        $(".form-edit-round-trip").html(response.result);
                        // var now = new Date();

                        $('#date_time').datetimepicker({
                            pickTime: true,
                            format: "DD/MM/YYYY hh:mm A",
                            // defaultDate: now,
                            // maxDate: ed
                        });

                        $(".type_status").select2();

                        call_select2_container1();
                        call_select2_container2();
                        call_select2_customer1();
                        call_select2_customer2();
                        call_select2_trailer();
                        call_select2_destination();
                    }
                });
                $(".edit_round_trip_title").text("Edit Round Trip For Ticket Number: " + ticket);
                $("#edit_modal").modal("show");
            }
        });

        //this script for status type
        $("body").on("change", ".type_status", function (evt) {
            evt.preventDefault();

            var stauts = $(this).val();

            if (stauts != "") {
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(url("/admin/round/get_data_type")); ?>",
                    data: {
                        'status': stauts,
                        'id': $("#round_trip_id").val(),
                    },
                    success: function (response) {
                        $(".data_change").html(response.result);
                        $(".select_truck,.select_trailer,.select_destination,.select_customer1,.select_customer2").select2();

                        call_select2_container1();
                        call_select2_container2();
                    }
                });
            } else {

                $(".data_show").html('');
            }
        });

        //script for button save
        $("body").on("click", ".btn_save_info", function (evt) {

            evt.preventDefault()
            if (!$(this).hasClass('disabled')) {
                $(this).addClass("disabled");

                var type = $("#type").val();

                if ($("#date_time").val() == "") {
                    $(".text-msg-round").text("Please input datetime").addClass("text-danger").removeClass("hidden text-success");
                    $(".btn_save_info").removeClass("disabled");
                    return false;
                } else if ($("#truck_number").val() == "") {
                    $(".text-msg-round").text("Please input truck number").addClass("text-danger").removeClass("hidden text-success");
                    $(".btn_save_info").removeClass("disabled");
                    return false;
                } else if ($("#type").val() == "") {
                    $(".text-msg-round").text("Please select type").addClass("text-danger").removeClass("hidden text-success");
                    $(".btn_save_info").removeClass("disabled");
                    return false;
                } else {

                    if ((type == 2)) {

                        if ($("#truck_number").val() == "") {
                            $(".text-msg-round").text("Please select truck number").addClass("text-danger").removeClass("hidden text-success");
                            $(".btn_save_info").removeClass("disabled");
                            return false;
                        } else if ($("#trailer_number").val() == "") {
                            $(".text-msg-round").text("Please select trailer number").addClass("text-danger").removeClass("hidden text-success");
                            $(".btn_save_info").removeClass("disabled");
                            return false;
                        } else if (($("#container_number1").val() == "") && ($("#container_number2").val() == "")) {
                            $(".text-msg-round").text("Please select container number").addClass("text-danger").removeClass("hidden text-success");
                            $(".btn_save_info").removeClass("disabled");
                            return false;
                        } else if (($("#customer1").val() == "") && ($("#customer2").val() == "")) {
                            $(".text-msg-round").text("Please select container number").addClass("text-danger").removeClass("hidden text-success");
                            $(".btn_save_info").removeClass("disabled");
                            return false;
                        }
                    }

                    $.ajax({
                        url: "<?php echo e(url('admin/round/update')); ?>",
                        type: 'POST',
                        data: $('.form-edit-round-trip').serialize(),
                        success: function (response) {
                            if (response.error == 0) {
                                setTimeout(function () {
                                    window.location = "<?php echo e(url('admin/round/round_list')); ?>";
                                }, 500);
                            } else {
                                $(".text-msg-round").text(response.error).removeClass("hidden").addClass("text-danger");
                                $(".btn_save_info").removeClass("disabled");
                            }
                        }
                    });
                }
            }

        });

        function call_select2_container1() {
            $('.select_container1').select2({
                placeholder: '--Please Select--',
                tags: false,
                // tokenSeparators: [',', ' '],
                // minimumInputLength: 1,
                maximumInputLength: 13,
                ajax: {
                    dataType: 'json',
                    url: '<?php echo e(url('/admin/ticket/get_container')); ?>',
                    // delay: 250,
                    data: function (params) {
                        return {q: params.term}
                    },
                    processResults: function (data, params) {

                        var resData = [];
                        data.forEach(function (value) {
                            if (value.ContainerNumber.indexOf(params.term) != -1)
                                resData.push(value)
                        })
                        return {
                            results: $.map(resData, function (item) {
                                return {
                                    text: item.ContainerNumber,
                                    id: item.ContainerNumber
                                }
                            })
                        };
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        }

        //this function for creating function
        function call_select2_container2() {
            $('.select_container2').select2({
                placeholder: '--Please Select--',
                tags: false,
                // tokenSeparators: [',', ' '],
                // minimumInputLength: 1,
                maximumInputLength: 13,
                ajax: {
                    dataType: 'json',
                    url: '<?php echo e(url('/admin/ticket/get_container')); ?>',
                    // delay: 250,
                    data: function (params) {
                        return {q: params.term}
                    },
                    processResults: function (data, params) {

                        var resData = [];
                        data.forEach(function (value) {
                            if (value.ContainerNumber.indexOf(params.term) != -1)
                                resData.push(value)
                        })
                        return {
                            results: $.map(resData, function (item) {
                                return {
                                    text: item.ContainerNumber,
                                    id: item.ContainerNumber
                                }
                            })
                        };
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        }

        //script for change of container number
        $("body").on("change", ".select_container1", function () {

            var container_id = $(this).val();
            // alert(container_id);
            var row = $(this).closest('tr');

            if (container_id != "") {

                $.ajax({
                    type: "post",
                    url: "<?php echo e(url('admin/ticket/get_feet')); ?>",
                    data: {
                        'container_id': container_id,
                    },
                    success: function (response) {
                        if (response.error) {
                            $('.msg').removeClass("hidden").addClass("text-danger").text(response.error);
                        }

                        if (response.id == 1) {

                            row.find('.feet1').val(response.data.Feet);
                            $('.msg').addClass("hidden").removeClass("text-danger").text("");
                        } else {
                            row.find('.feet1').val("");
                        }
                    }
                });

            } else {
                $('.msg').removeClass("hidden").addClass("text-danger").text("Please Select Reason");
                $(".feet1").text("");
                return false;
            }
        });

        //script for change of container number
        $("body").on("change", ".select_container2", function () {

            var container_id = $(this).val();
            // alert(container_id);
            var row = $(this).closest('tr');

            if (container_id != "") {

                $.ajax({
                    type: "post",
                    url: "<?php echo e(url('admin/ticket/get_feet')); ?>",
                    data: {
                        'container_id': container_id,
                    },
                    success: function (response) {
                        if (response.error) {
                            $('.msg').removeClass("hidden").addClass("text-danger").text(response.error);
                        }

                        if (response.id == 1) {

                            row.find('.feet2').val(response.data.Feet);
                            $('.msg').addClass("hidden").removeClass("text-danger").text("");
                        } else {
                            row.find('.feet2').val("");
                        }
                    }
                });

            } else {
                $('.msg').removeClass("hidden").addClass("text-danger").text("Please Select Reason");
                $(".feet2").text("");
                return false;
            }
        });

        //this function for creating function customer1
        function call_select2_customer1() {
            $('.select_customer1').select2({
                placeholder: "--Please Select--",
                tags: false,
                ajax: {
                    dataType: 'json',
                    url: '<?php echo e(url('/admin/ticket/get_customer')); ?>',
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
                                    text: item.CustomerName,
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
        }

        //this function for creating function customer2
        function call_select2_customer2() {
            $('.select_customer2').select2({
                placeholder: "--Please Select--",
                tags: false,
                ajax: {
                    dataType: 'json',
                    url: '<?php echo e(url('/admin/ticket/get_customer')); ?>',
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
                                    text: item.CustomerName,
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
        }

        //this function for creating function trailer
        function call_select2_trailer() {
            $('.select_trailer').select2({
                placeholder: "--Please Select--",
                tags: false,
                ajax: {
                    dataType: 'json',
                    url: '<?php echo e(url('/admin/ticket/get_trailer')); ?>',
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
                                    id: item.TrailerNumber,
                                }
                            })
                        };
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }

        //this function for creating function destination
        function call_select2_destination() {
            $('.select_destination').select2({
                placeholder: "--Please Select--",
                tags: false,
                ajax: {
                    dataType: 'json',
                    url: '<?php echo e(url('/admin/ticket/get_destination')); ?>',
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
        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>