<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/dataTables.fixedColumns/fixedColumns.bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/buttons.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/colReorder.dataTables.min.css')); ?>">
    <style>

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

        .select2 {
            width: 100% !important;
        }

        th, td {
            white-space: nowrap;
        }

        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }

        div.ColVis {
            float: left;
        }

        .table-bordered {
            /*border: 0px solid #00ccff !important;*/
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Write Off Report</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-form">
                        <form id="form-horizontal form-label-left" class="form-data" method="post"
                              action="<?php echo e(url('admin/report/writeoff')); ?>" autocomplete="off">
                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                            <div class="table-responsive">
                                <table class="table table-striped table-responsive table-bordered col-lg-12 col-md-12 col-sm-12 col-xs-12 table_parent"
                                       width="100%">
                                    <tbody>
                                    <tr>
                                        <td width="15%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">From
                                                Date</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left"
                                                       name="from_date"
                                                       id="from_date" placeholder="From Date"
                                                       aria-describedby="inputSuccess2Status3"
                                                       style="padding-left: 33px;"
                                                       value="<?php echo e($data['date_start']); ?>">
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      style="margin-left: -10% !important; margin-top: 5px !important;"
                                                      aria-hidden="true"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">To
                                                Date </label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left" name="to_date"
                                                       id="to_date" placeholder="To Date" style="padding-left: 33px;"
                                                       aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['date_end']); ?>">
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      style="margin-left: -10% !important; margin-top: 5px !important;"
                                                      aria-hidden="true"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">Truck
                                                No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="fleet_id" id="fleet_id"
                                                        class="js-example-basic-single js-states form-control plate_num-list">
                                                    <option value="<?php echo e($data['fleet_id']); ?>"><?php echo e($data['fleet_name']); ?></option>
                                                </select>
                                                <input type="hidden" name="fleet_name" id="fleet_name"
                                                       value="<?php echo e($data['fleet_name']); ?>">
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">Driver </label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="driver_id" id="driver_id"
                                                        class="js-example-basic-single js-states form-control driver-list">
                                                    <option value="<?php echo e($data['driver_id']); ?>"><?php echo e($data['driver_name']); ?></option>
                                                </select>
                                                <input type="hidden" name="driver_name" id="driver_name"
                                                       value="<?php echo e($data['driver_name']); ?>">
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">Ticket
                                                No
                                            </label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="ticket_id" id="ticket_id"
                                                        class="js-example-basic-single js-states form-control ticket-list">
                                                    <option value="<?php echo e($data['ticket_id']); ?>"><?php echo e($data['ticket_number']); ?></option>
                                                </select>
                                                <input type="hidden" name="ticket_number" id="ticket_number"
                                                       value="<?php echo e($data['ticket_number']); ?>">
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">WriteOff
                                                No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="writeoff_id" id="writeoff_id"
                                                        class="js-example-basic-single js-states form-control writeoff-list">
                                                    <option value="<?php echo e($data['writeoff_id']); ?>"><?php echo e($data['writeoff_number']); ?></option>
                                                </select>
                                                <input type="hidden" name="writeoff_number" id="writeoff_number"
                                                       value="<?php echo e($data['writeoff_number']); ?>">
                                            </div>
                                        </td>
                                        <td width="10%">
                                            <label class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12">Status</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="status" id="status"
                                                        class="js-example-basic-single js-states form-control status-list">
                                                    </option>
                                                    <?php
                                                    $status_list = array(
                                                        "-1" => "All",
                                                        "0" => "Pending",
                                                        "1" => "Approved",
                                                        "2" => "Cancel",
                                                    );
                                                    ?>
                                                    <?php $__currentLoopData = $status_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>" <?php echo e(($data['status_id']==$key?'selected':'')); ?>><?php echo e($val); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                    <label for="button">
                                        <button type="submit" class="btn btn-primary btn-sm"><i
                                                    class="fa fa-search"></i> Filter
                                        </button>

                                        <?php if($data['search']): ?>
                                            <a href="<?php echo e(url('admin/report/writeoff')); ?>"
                                               class="btn btn-danger btn-sm">Clear</a>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table id="write_off_report"
                               class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Request Date</th>
                                <th>Request By</th>
                                <th>Approve Date</th>
                                <th>Approve By</th>
                                <th>Ticket No</th>
                                <th>Truck No</th>
                                <th>Driver ID</th>
                                <th>Driver</th>
                                <th>Amount(L)</th>
                                <th>Lolo($)</th>
                                <th>PayTrip(៛)</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Remark</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div>
                        <span id="all"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('vendors/select2/dist/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/moment/min/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/dataTables.fixedColumns/dataTables.fixedColumns.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrapValidator.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jszip.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.html5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.print.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.colReorder.min.js')); ?>"></script>
    <script>
        //this function for select2
        $(".status-list").select2();
        //this function for date
        $(function () {

            var sd = new Date();
            var ed = new Date();
            // sd = $("#from_date").val().replace(/\//g, '-');
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
        //this function for data table
        var filename = 'Write Off List Report At <?php echo e($data['from_date']); ?>-To-<?php echo e($data['to_date']); ?>';
        var data = '';
        <?php if($data['id']): ?>
            data = <?php echo json_encode($data['list']) . ';'?>
        <?php endif; ?>

        $(function () {
            var table1 = $('#write_off_report').DataTable({
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
                // scrollY: "400px",
                // scrollX: true,
                scrollCollapse: true,
                fixedColumns: true,
                orderCellsTop: true,
                stateSave: true,
                colReorder: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"></i> Export',
                        className: 'btn btn-sm exportExcel',
                        filename: filename,
                        exportOptions: {
                            modifier: {
                                page: 'all',
                                search: 'none'
                            },
                            format: {
                                body: function (data, row, column, node) {
                                    // Strip $ from salary column to make it numeric
                                    if (column === 9) {
                                        return data.replace(/[L,]/g, '')
                                    } else if (column === 10) {
                                        return data.replace(/[$,]/g, '')
                                    } else if (column === 11) {
                                        return data.replace(/[៛,]/g, '')
                                    }
                                    return data;
                                }
                            }
                        },
                        messageTop: null,
                        title: '',
                    },
                ],
                columns: [
                    {data: 'WriteOffNumber'},
                    {data: 'RequestDate'},
                    {data: 'RequestName'},
                    {data: 'ApproveDate'},
                    {data: 'ApproveName'},
                    {data: 'TicketNumber'},
                    {data: 'PlateNumber'},
                    {data: 'CodeID'},
                    {data: 'NameKh'},
                    {
                        data: 'Amount', render: function (data, type, row) {
                            return data + " L";
                        }
                    },
                    {
                        data: 'LoloAmount', render: function (data, type, row) {
                            if (data != null) {
                                return "$ " + formatNumber(data);
                            }
                            return '$ 0';
                        }
                    },
                    {
                        data: 'PayTripAmount', render: function (data, type, row) {
                            if (data != null) {
                                return formatNumber(data) + " ៛";
                            }
                            return '0 ៛';
                        }
                    },
                    {data: 'Reason'},
                    {
                        data: 'Status', render: function (data, type, row) {
                            if (data == 0) {
                                return "Pending";
                            } else if (data == 1) {
                                return "Approved";
                            } else if (data == 2) {
                                return "Cancel";
                            }
                            return '';
                        }
                    },
                    {data: "Remark"}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    switch (aData['Status']) {
                        case "0":
                            $('td', nRow).eq(13).css({'color': 'black', 'background-color': 'yellow'});
                            break;
                        case "1":
                            $('td', nRow).eq(13).css({'color': 'white', 'background-color': 'green'});
                            break;
                        case "2":
                            $('td', nRow).eq(13).css({'color': 'white', 'background-color': 'red'});
                            break;
                    }
                },
                "footerCallback": function () {
                    var api = this.api(), data, count_all = 0, amount_all = 0, count_pending = 0, amount_pending = 0,
                        paytrip = 0, lolo = 0, paytrip_cancel = 0, lolo_cancel = 0, paytrip_approve = 0,
                        lolo_approve = 0, paytrip_pending = 0, lolo_pending = 0,
                        count_approve = 0, amount_approve = 0, count_cancel = 0, amount_cancel = 0;
                    data = api.rows({search: 'applied'}).data();

                    $.each(data, function (key, val) {

                        if (val['Status'] == 1) {
                            count_approve++;
                            amount_approve += val['Amount'];
                            paytrip_approve += val['PayTripAmount'];
                            lolo_approve += val['LoloAmount'];
                        } else if (val['Status'] == 0) {
                            count_pending++;
                            amount_pending += val['Amount'];
                            paytrip_pending += val['PayTripAmount'];
                            lolo_pending += val['LoloAmount'];
                        } else if (val['Status'] == 2) {
                            count_cancel++;
                            amount_cancel += val['Amount'];
                            paytrip_cancel += val['PayTripAmount'];
                            lolo_cancel += val['LoloAmount'];
                        }

                        amount_all += val['Amount'];
                        paytrip += val['PayTripAmount'];
                        lolo += val['LoloAmount'];

                        count_all++;
                    });

                    // Update footer
                    var html = "<b>All Write Off Return: </b><span style='font-size:12px; color:red; font-weight: bold;'>" + count_all + "(" + amount_all.toFixed(3) + "L, $ " + lolo.toFixed(3) + ", " + paytrip.toFixed(3) + " ៛)</span>, " +
                        "<b>Approved: </b><span style='font-size:12px; color:red; font-weight: bold;'>" + count_approve + "(" + amount_approve.toFixed(3) + "L, $ " + lolo_approve.toFixed(3) + ", " + paytrip_approve.toFixed(3) + " ៛)</span>, " + "" +
                        "<b>Pending: </b><span style='font-size:12px; color:red; font-weight: bold;'>" + count_pending + "(" + amount_pending.toFixed(3) + "L, $ " + lolo_pending.toFixed(3) + ", " + paytrip_pending.toFixed(3) + " ៛)</span>, " + "" +
                        "<b>Cancel: </b><span style='font-size:12px; color:red; font-weight: bold;'>" + count_cancel + "(" + amount_cancel.toFixed(3) + "L, $ " + lolo_cancel.toFixed(3) + ", " + paytrip_cancel.toFixed(3) + " ៛)</span>" + "" +
                        "";
                    $("#all").html(html);
                }
            });
        });

        //this for changing plate number
        $("body").on("change", ".plate_num-list", function (eve) {
            eve.preventDefault();
            var plate_nubmer = $(this).val();
            $("#fleet_name").val($(".plate_num-list option:selected").text());

            if (plate_nubmer != "") {
                $.ajax({
                    type: "get",
                    url: "<?php echo e(url('admin/ticket/get_driver')); ?>/" + plate_nubmer,
                    success: function (response) {
                        if (response.error) {
                            call_toast("error", response.error);
                            return false;
                        }

                        var option = '';
                        if (response.id == 1) {

                            $.each(response.data, function (key, val) {
                                option += '<option value="' + val.DriverID + '">' + val.NameKh + '</option>';
                                $("#driver_name").val(val.NameKh);
                            });
                            $(".driver-list").html(option);
                            return true;
                        } else {
                            $("#driver_name").val("All");
                            $(".driver-list").html("<option value='-1'>All</option>");
                            return false;
                        }

                    }
                });
            }
        });

        //this for changing ticket
        $("body").on("change", ".ticket-list", function (evt) {
            evt.preventDefault();
            $("#ticket_number").val($(".ticket-list option:selected").text());
        });

        //this for changing driver
        $("body").on("change", ".driver-list", function (evt) {
            evt.preventDefault();
            $("#driver_name").val($(".driver-list option:selected").text());
        });

        //this for changing driver
        $("body").on("change", ".credit-list", function (evt) {
            evt.preventDefault();
            $("#credit_number").val($(".credit-list option:selected").text());
        });

        //this for fleet selection
        $(".plate_num-list").select2({
            tags: false,
            // tokenSeparators: [',', ' '],
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/report/get_fleet_in_writeoff')); ?>',
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

        //this for fleet selection
        $(".driver-list").select2({
            tags: false,
            // tokenSeparators: [',', ' '],
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/report/get_driver_in_writeoff')); ?>',
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

        //this for fleet selection
        $(".ticket-list").select2({
            tags: false,
            // tokenSeparators: [',', ' '],
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/report/get_ticket_in_writeoff')); ?>',
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
            },
        });

        //this for fleet selection
        $(".writeoff-list").select2({
            tags: false,
            // tokenSeparators: [',', ' '],
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/report/get_writeoff_number')); ?>',
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
                                text: item.WriteOffNumber,
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
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>