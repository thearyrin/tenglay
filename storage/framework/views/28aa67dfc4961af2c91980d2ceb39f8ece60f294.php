<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/dataTables.fixedColumns/fixedColumns.bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/buttons.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/select2/dist/css/select2.min.css')); ?>">
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
                    <h2>Account Balance History Report</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-form">
                        <form id="form-horizontal form-label-left" class="form-data" method="post"
                              action="<?php echo e(url('admin/report/account')); ?>" autocomplete="off">
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
                                                       name="from_date" style="padding-left: 33px;" id="from_date"
                                                       placeholder="From Date" aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['date_start']); ?>" required>
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      aria-hidden="true"
                                                      style="margin-left: -8% !important; margin-top: 5px !important;"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">To Date</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left"
                                                       name="to_date" style="padding-left: 33px;" id="to_date"
                                                       placeholder="To Date" aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['date_end']); ?>" required>
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      aria-hidden="true"
                                                      style="margin-left: -8% !important; margin-top: 5px !important;"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Truck No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="fleet_id" id="plate_num"
                                                        class="js-example-basic-single js-states form-control plate_number_list">
                                                    <option value="<?php echo e($data['truck_id']); ?>"><?php echo e($data['truck_number']); ?></option>
                                                </select>
                                                <input type="hidden" name="fleet_name" id="fleet_name"
                                                       value="<?php echo e($data['truck_number']); ?>">
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Ticket No</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="ticket_id" id="ticket_id"
                                                        class="js-example-basic-single js-states form-control ticket_number_list">
                                                    <option value="<?php echo e($data['ticket_id']); ?>"><?php echo e($data['ticket_number']); ?></option>
                                                </select>
                                                <input type="hidden" name="ticket_number" id="ticket_number"
                                                       value="<?php echo e($data['ticket_number']); ?>">
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">User</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">

                                                <select name="user_id" id="user_id"
                                                        class="js-example-basic-single js-states form-control user_list">
                                                    <?php $__currentLoopData = $data['user_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($user->ID); ?>" <?php echo e(($user->ID==$data['login_user']?'selected':'')); ?>><?php echo e($user->DisplayName); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <label for="button">
                                        <button type="submit" class="btn btn-primary btn-sm"><i
                                                    class="fa fa-search"></i> Filter
                                        </button>

                                        <?php if($data['search']): ?>
                                            <a href="<?php echo e(url('admin/report/account')); ?>"
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
                    <table id="account_balance_history"
                           class="table table-striped table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>DateTime</th>
                            <th>User Name</th>
                            <th>CashAdvancedNo</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Ticket No</th>
                            <th>Truck No</th>
                            <th>Driver</th>
                            <th>FirstBalance($)</th>
                            <th>Amount($)</th>
                            <th>Balance($)</th>
                            <th>FirstBalance(៛)</th>
                            <th>Amount(៛)</th>
                            <th>Balance(៛)</th>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('vendors/select2/dist/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/moment/min/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/dataTables.fixedColumns/dataTables.fixedColumns.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrapValidator.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jszip.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.html5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.print.min.js')); ?>"></script>
    <script>

        $(".user_list,.plate_number_list,.ticket_number_list").select2();
        //this function for date
        $(function () {

            var sd = new Date();
            var ed = new Date();
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

            var filename = 'Account Balance History List Report At <?php echo e($data['from_date']); ?>-To-<?php echo e($data['to_date']); ?>';
            var data = '';
            <?php if($data['id']): ?>
                data = <?php echo json_encode($data['list']) . ';'?>
            <?php endif; ?>

            $(function () {
                var table1 = $('#account_balance_history').DataTable({
                    'paging': true,
                    'lengthChange': false,
                    "pageLength": 30,
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': true,
                    "lengthMenu": [[10, 15, 25, 35, 50, -1], [10, 15, 25, 35, 50, "All"]],
                    "order": [[0, "asc"]],
                    scrollY: "400px",
                    scrollX: true,
                    scrollCollapse: true,
                    fixedColumns: true,
                    data: data,
                    orderCellsTop: true,
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

                            },
                            messageTop: null,
                            title: '',
                        },
                    ],
                    columns: [
                        {data: 'ID'},
                        {data: 'DateTime'},
                        {data: 'DisplayName'},
                        {data: 'CashAdvanceNumber'},
                        {data: 'Type'},
                        {data: 'Description'},
                        {data: 'TicketNumber'},
                        {data: 'PlateNumber'},
                        {data: 'NameKh'},
                        {
                            data: 'FirstBalanceDollar', render: function (data, type, row) {
                                return formatNumber(data);
                            }
                        },
                        {
                            data: 'AmountDollar', name: 'amount_dollar', render: function (data, type, row) {
                                return formatNumber(data);
                            }
                        },
                        {
                            data: 'BalanceDollar', render: function (data, type, row) {
                                return formatNumber(data);
                            }
                        },
                        {
                            data: 'FirstBalanceKHR', render: function (data, type, row) {
                                return formatNumber(data);
                            }
                        },
                        {
                            data: 'AmountKHR', name: 'amount_khr', render: function (data, type, row) {
                                return formatNumber(data);
                            }
                        },
                        {
                            data: 'BalanceKHR', render: function (data, type, row) {
                                return formatNumber(data);
                            }
                        },
                    ],
                    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                        var api = this.api();
                        var col_dollar = api.column("amount_dollar:name").index('visible');
                        var col_khr = api.column("amount_khr:name").index('visible');

                        if (aData['AmountKHR'] < 0) {
                            $('td', nRow).eq(col_khr).css({'color': 'black', 'background-color': 'yellow'});
                        } else if (aData['AmountDollar'] < 0) {
                            $('td', nRow).eq(col_dollar).css({'color': 'black', 'background-color': 'yellow'});
                        }
                    },
                    "footerCallback": function () {
                        var api = this.api(), data, count_all = 0, amount_kh = 0, amount_dollar = 0;
                        data = api.rows({search: 'applied'}).data();

                        $.each(data, function (key, val) {

                            amount_dollar += val['AmountDollar'];
                            amount_kh += val['AmountKHR'];
                            count_all++;
                        });

                        // Update footer
                        var html = "<b>Last Balance($): </b><span style='font-size:12px; color:red; font-weight: bold;'>" + count_all + "($ " + formatNumber(amount_dollar) + ")</span>, " +
                            "<b>Last Balance(៛): </b><span style='font-size:12px; color:red; font-weight: bold;'>" + count_all + "(" + formatNumber(amount_kh) + " ៛)</span>";
                        $("#all").html(html);
                    }
                });
            });
        });

        //this for changing plate number
        //this for changing ticket
        $("body").on("change", ".ticket_number_list", function (evt) {
            evt.preventDefault();
            $("#ticket_number").val($(".ticket_number_list option:selected").text());
        });

        $("body").on("change", ".plate_number_list", function (eve) {
            eve.preventDefault();
            $("#fleet_name").val($(".plate_number_list option:selected").text());

        });

        //for select2 of ticket-list
        $(".ticket_number_list").select2({
            tags: false,
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/report/get_ticket_intopup')); ?>',
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

        //for select2 of plate number
        $(".plate_number_list").select2({
            tags: false,
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/report/get_fleet_intopup')); ?>',
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


    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>