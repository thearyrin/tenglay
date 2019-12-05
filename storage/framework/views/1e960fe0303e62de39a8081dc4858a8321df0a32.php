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
                    <h2>Create Round Trip
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-form">
                        <form id="form-horizontal form-label-left" class="form-data" method="post"
                              action="<?php echo e(url('admin/round/create')); ?>" autocomplete="off">
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
                                                       name="from_date" style="padding-left: 33px;"
                                                       id="from_date" placeholder="From Date"
                                                       aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['from_date']); ?>">
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
                                                       value="<?php echo e($data['to_date']); ?>">
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
                                                <select name="fleet_id" id="fleet_id"
                                                        class="js-example-basic-single js-states form-control plate_number-list">
                                                    <option value="<?php echo e($data['fleet_id']); ?>"><?php echo e($data['fleet_name']); ?></option>
                                                </select>
                                                <input type="hidden" name="fleet_name" id="fleet_name"
                                                       value="<?php echo e($data['fleet_name']); ?>">
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Ticket No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="ticket_id" id="ticket_id"
                                                        class="js-example-basic-single js-states form-control ticket_number-list">
                                                    <option value="<?php echo e($data['ticket_id']); ?>"><?php echo e($data['ticket_number']); ?></option>
                                                </select>
                                                <input type="hidden" name="ticket_number" id="ticket_number"
                                                       value="<?php echo e($data['ticket_number']); ?>">
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Round Trip</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="rountrip_id" id="rountrip_id"
                                                        class="js-example-basic-single js-states form-control roundtrip_list">
                                                    <?php if($data['round_data_id']): ?>
                                                        <?php $__currentLoopData = $data['round_data_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($round->RoundTrip); ?>" <?php echo e(($round->RoundTrip==$data['round_id']?'selected':'')); ?>><?php echo e($round->RoundTrip); ?></option>
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

                                        <?php if($data['search']): ?>
                                            <a href="<?php echo e(url('admin/round/create')); ?>"
                                               class="btn btn-danger btn-sm">Clear</a>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <table id="ticket-table" class="table table-striped table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th style="text-align: center !important; width: 15px;">
                                <label for="">
                                    <button class="btn btn-success btn-sm btn-empty">Only Truck</button>
                                    <input type="checkbox" id="check-all" class="round_trip_list check_parent"
                                           style="width: 68px; height: 20px;">
                                </label>
                            </th>
                            <th>Ticket No.</th>
                            <th>Date Time</th>
                            <th>Issue By</th>
                            <th>Plate No.</th>
                            <th>Trailer No.</th>
                            <th>Purpose</th>
                            <th>Driver</th>
                            <th>Destination</th>
                            <th>Fuel</th>
                            <th>RoundTrip</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody class="data-show">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="detail_modal" class="modal fade">
        <div class="modal-dialog" style="width: 70% !important; margin-left: 16% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-form-detail" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h3 class="modal-title text-center detail_title"></h3>
                </div>
                <div class="modal-body">
                    <div class="row content-body-detail" id="data-detail">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-form-detail" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="back_modal" class="modal fade">
        <div class="modal-dialog" style="width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-form-back" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h3 class="modal-title text-center back_title"></h3>
                </div>
                <div class="modal-body">
                    <div class="row content-body-back" id="data-back">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p class="text-center hidden text-msg-round" style="font-size: 16px;"></p>
                            <form id="form-horizontal form-label-left" class="form-round-trip" autocomplete="off">
                                <input type="hidden" value="" id="ticket_id_number" name="ticket_id_number">
                                <input type="hidden" value="" id="detail_id" name="detail_id">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label col-lg-2 col-md-2 col-sm-6 col-xs-12"
                                           for="ticket_no">Ticket No:<span
                                                class="required text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <input type="text" name="ticket_no" id="ticket_no" value="" readonly
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label col-lg-2 col-md-2 col-sm-6 col-xs-12"
                                           for="date_time">DateTime:<span
                                                class="required text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control has-feedback-left" name="date_time"
                                               id="date_time" placeholder="Date Time"
                                               aria-describedby="inputSuccess2Status3"
                                               value="" required>
                                        <span class="fa fa-calendar-o form-control-feedback left"
                                              aria-hidden="true"></span>
                                        <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label col-lg-2 col-md-2 col-sm-6 col-xs-12"
                                           for="type">Return Type:<span
                                                class="required text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <select name="type" id="type" class="form-control type_status col-xs-12">
                                            <option value="">--Select--</option>
                                            <option value="1">Empty</option>
                                            <option value="2">Laden</option>
                                            <option value="3">Only Truck</option>
                                            <option value="4">Other</option>
                                        </select>
                                        <input type="hidden" name="type_name" id="type_name" value="">
                                    </div>
                                </div>
                                <div class="data_show">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-form-back" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div id="empty_modal" class="modal fade">
        <div class="modal-dialog" style="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-form-empty" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h3 class="modal-title text-center empty_title">Return Only Truck</h3>
                </div>
                <div class="modal-body">
                    <div class="row content-body-empty" id="data-empty">
                        <p class="text-center hidden text-msg-round-empty" style="font-size: 16px;"></p>
                        <form action="#" class="form-empty">
                            <input type="hidden" name="ticket_num" id="ticket_num" value="">
                            <input type="hidden" name="truck_no" id="truck_no" value="">
                            <input type="hidden" name="trailer_no" id="trailer_no" value="">
                            <input type="hidden" name="round_trip" id="round_trip" value="">
                            <input type="hidden" name="ticket_detail_id" id="ticket_detail_id" value="">
                            <input type="hidden" name="driver_id" id="driver_id" value="">

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12"
                                       for="delvery_loca">Delivery Place:
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" name="delvery_loca" id="delvery_loca"
                                           class="form-control delvery_loca">
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12"
                                       for="remark">Remark:
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" name="round_remark" id="round_remark"
                                           class="form-control round_remark">
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-right">
                                <label class="control-label col-lg-1 col-md-1 col-sm-2 col-xs-12">&nbsp;</label>
                                <button type="button" class="btn  btn-sm btn-success btn_save_empty"><i
                                            class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-form-empty" data-dismiss="modal">Close
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
        //start tab index
        var tabindex = 1;
        $('select').each(function () {
            if (this.type != "hidden") {
                $(this).attr("tabindex", tabindex);
                tabindex++;
            }
        });

        // https://stackoverflow.com/a/50535297/2782670
        $(document).on('focus', '.select2', function (e) {
            if (e.originalEvent) {
                var s2element = $(this).siblings('select');
                s2element.select2('open');
                s2element.on('select2:closing', function (e) {
                    s2element.select2('focus');
                });
            }
        });

        $(".status-list,.roundtrip_list").select2();

        $(".plate_number-list").select2({
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/round/get_fleet_num_roundtrip')); ?>',
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
            }
        });

        $(".ticket_number-list").select2({
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/round/get_ticket_no_return')); ?>',
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
                minDate: sd
            });

            $('#from_date').on("dp.change dp.update blur", function (e) {
                $('#to_date').data("DateTimePicker").setMinDate(e.date);
            });

            $('#to_date').on("dp.change dp.update blur", function (e) {
                $('#from_date').data("DateTimePicker").setMaxDate(e.date);
            });

            var now = new Date();

            $('#date_time').datetimepicker({
                pickTime: true,
                format: "DD/MM/YYYY hh:mm A",
                defaultDate: now,
                // maxDate: ed
            });

        });
        var data = '';
        <?php if($data['id']): ?>
            data = <?php echo json_encode($data['list']) . ';'?>
        <?php endif; ?>

        $(function () {
            $('#ticket-table').DataTable({
                'paging': true,
                'lengthChange': true,
                "pageLength": 10,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true,
                "lengthMenu": [[10, 15, 25, 35, 50, -1], [10, 15, 25, 35, 50, "All"]],
                'order': [[1, 'DESC']],
                stateSave: true,
                colReorder: true,
                'data': data,
                columns: [
                    {
                        data: 'ID', "orderable": false, render: function (data, type, row) {
                            return '<input type="checkbox" id="check-all" class="round_trip_list text-center check_child" ' +
                                'value="' + data + '" data-truck="' + row["PlateNumber"] + '"  data-trailer="' + row['TrailerNumber'] + '" ' +
                                'data-round_trip="' + row["RoundTrip"] + '" data-detail="' + row["TicketDetailID"] + '" ' +
                                'data-driver_id="' + row["DriverID"] + '"' +
                                'style="width: 68px; height: 20px;">';
                        }
                    },
                    {data: 'TicketNo'},
                    {data: 'issue_date_time'},
                    {data: 'Issuer'},
                    {data: 'PlateNumber'},
                    {data: 'TrailerNumber'},
                    {data: 'Reason'},
                    {data: 'NameKh'},
                    {data: 'Code'},
                    {
                        data: 'TotalFuel', render: function (data, row, type) {
                            return data + ' L';
                        }
                    },
                    {data: 'RoundTrip'},
                    {
                        data: 'Status', render: function (data, type, row) {
                            return '<a href="#" class="modal_back btn btn-primary btn-sm" data-id="' + row['ID'] + '" data-number="' + row['TicketNo'] + '" data-detail_id="' + row['TicketDetailID'] + '">Return</a>' +
                                '<a href="#" class="modal_detail btn btn-success btn-sm" data-id="' + row['ID'] + '" data-number="' + row['TicketNo'] + '">Detail</a>';
                        }
                    },

                ],
            });
        });

        //this script for modal detail
        $("body").on("click", ".modal_detail", function (evt) {
            evt.preventDefault();
            var id = $(this).data("id");
            var number = $(this).data("number");

            $.ajax({
                type: 'GET',
                url: '<?php echo e(url('/admin/ticket/get_reference')); ?>/' + id,
                success: function (response) {
                    $(".content-body-detail").html(response.result);
                }
            });

            $(".detail_title").text("Detail of ticket number: " + number);
            $("#detail_modal").modal("show");
        });

        //this script for modal back
        $("body").on("click", ".modal_back", function (evt) {
            evt.preventDefault();

            var id = $(this).data("id");
            var number = $(this).data("number");
            var detail_id = $(this).data("detail_id");


            $(".truck_no").focus();
            $(".type_status").select2();
            $(".data_show").html("");
            $(".type_status").val("").trigger("change.select2");

            $("#ticket_no").val(number);
            $("#ticket_id_number").val(id);
            $("#detail_id").val(detail_id);

            $(".back_title").text("Form round trip of ticket number: " + number);
            $("#back_modal").modal("show");
        });

        //script for button save
        $("body").on("click", ".btn_save_info", function (evt) {
            evt.preventDefault();

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
                        url: "<?php echo e(url('admin/round/save')); ?>",
                        type: 'POST',
                        data: $('.form-round-trip').serialize(),
                        success: function (response) {
                            if (response.error == 0) {
                                setTimeout(function () {
                                    window.location = "<?php echo e(url('admin/round/create_round')); ?>";
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

        //this script for status type
        $("body").on("change", ".type_status", function (evt) {
            evt.preventDefault();

            var stauts = $(this).val();
            $("#type_name").val($(".type_status option:selected").text());
            if (stauts != "") {
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(url("/admin/round/data")); ?>",
                    data: {
                        'status': stauts,
                        'ticket_id': $("#ticket_id_number").val(),
                        'detail_id': $("#detail_id").val(),
                    },
                    success: function (response) {
                        $(".data_show").html(response.result);
                        $(".select_trailer,.select_destination,.select_customer1,.select_customer2").select2();

                        call_select2_container1();
                        call_select2_container2();
                        call_select2_trailer();
                        call_select2_destination();
                        call_select2_customer1();
                        call_select2_customer2();
                    }
                });
            } else {

                $(".data_show").html('');
            }
        });

        //this function for creating function container1
        function call_select2_container1() {
            $('.select_container1').select2({
                placeholder: '--Please Select--',
                tags: true,
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
                            resData.push(value)
                        });

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

        //this function for creating function container2
        function call_select2_container2() {
            $('.select_container2').select2({
                placeholder: '--Please Select--',
                tags: true,
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
                            resData.push(value)
                        });

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

        //this script for checkbox
        //this for checkbox all and sub
        $(".check_parent").change(function () {

            if ($(".check_parent").is(':checked')) {

                $(".check_child").each(function () {
                    $(this).prop("checked", true);
                });

            } else {
                $(".check_child").each(function () {
                    $(this).prop("checked", false);
                });
            }
        });

        //this script for approving many
        $("body").on("click", ".btn-empty", function (evt) {
            evt.preventDefault();

            var id = [];
            var plate_number = [];
            var trailer_number = [];
            var round_trip = [];
            var detail_id = [];
            var driver_id = [];

            $(".check_child:checked").each(function () {
                id.push($(this).val());
                plate_number.push($(this).data("truck"));
                trailer_number.push($(this).data("trailer"));
                round_trip.push($(this).data("round_trip"));
                detail_id.push($(this).data("detail"));
                driver_id.push($(this).data("driver_id"));
            });

            if (id.length > 0) {

                swal({
                    title: 'Are you sure?',
                    text: "You want to Only Truck all these round trip?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.value) {
                        $("#ticket_num").val(id);
                        $("#truck_no").val(plate_number);
                        $("#trailer_no").val(trailer_number);
                        $("#round_trip").val(round_trip);
                        $("#ticket_detail_id").val(detail_id);
                        $("#driver_id").val(driver_id);
                        $('#empty_modal').modal('show');
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        $(".check_child").prop("checked", false);
                        $(".check_parent").prop("checked", false);
                    }
                });
            } else {
                call_toast("info", "Please select item");
            }
        });

        //this script for close form empty modal
        $("body").on("click", ".close-form-empty", function (evt) {
            evt.preventDefault();
            $(".check_child").prop("checked", false);
            $(".check_parent").prop("checked", false);
            $("#ticket_num").val('');
            $("#truck_no").val('');
            $("#trailer_no").val('');
            $("#delvery_loca").val('');
            $("#round_remark").val('');
            $("#round_trip").val('');
            $("#ticket_detail_id").val('');
            $("#driver_id").val('');
        });

        //this script for save empty data
        $("body").on("click", ".btn_save_empty", function (evt) {
            evt.preventDefault();
            if (!$(this).hasClass('disabled')) {
                $(this).addClass("disabled");
                $.ajax({
                    url: "<?php echo e(url('admin/round/save_empty')); ?>",
                    type: 'POST',
                    data: $('.form-empty').serialize(),
                    success: function (response) {
                        if (response.error == 0) {
                            setTimeout(function () {
                                window.location = "<?php echo e(url('admin/round/create_round')); ?>";
                            }, 500);
                        } else {
                            $(".text-msg-round-empty").text(response.error).removeClass("hidden").addClass("text-danger");
                            $(".btn_save_empty").removeClass("disabled");
                        }
                    }
                });
            }
        })

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>