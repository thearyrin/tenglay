<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>">
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
                    <h2>Tank Delivery Report</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-form">
                        <form id="form-horizontal form-label-left" class="form-data" method="post"
                              action="<?php echo e(url('admin/report/tank')); ?>" autocomplete="off">
                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                            <div class="row">
                                <div class="col-lg-5 col-md-7 col-sm-12 col-xs-12 form-group">
                                    <label for="from_date" class="control-label col-lg-3 col-md-4 col-sm-12 col-xs-12">From
                                        Date<span
                                                class="required text-danger">*</span>:</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control has-feedback-left" name="from_date"
                                               id="from_date" placeholder="From Date"
                                               aria-describedby="inputSuccess2Status3"
                                               value="<?php echo e($data['date_start']); ?>" required>
                                        <span class="fa fa-calendar-o form-control-feedback left"
                                              aria-hidden="true"></span>
                                        <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-7 col-sm-12 col-xs-12 form-group">
                                    <label for="to_date" class="control-label col-lg-2 col-md-4 col-sm-12 col-xs-12">To
                                        Date<span
                                                class="required text-danger">*</span>:</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control has-feedback-left" name="to_date"
                                               id="to_date" placeholder="To Date"
                                               aria-describedby="inputSuccess2Status3" value="<?php echo e($data['date_end']); ?>"
                                               required>
                                        <span class="fa fa-calendar-o form-control-feedback left"
                                              aria-hidden="true"></span>
                                        <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-5 col-md-9 col-sm-12 col-xs-12 form-group">
                                    <label for="button"
                                           class="control-label col-lg-1 col-md-1 col-sm-6 col-xs-12 hidden-lg hidden-md"></label>
                                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                        <label for="button">
                                            <button type="submit" class="btn btn-primary btn-sm"><i
                                                        class="fa fa-search"></i> Filter
                                            </button>

                                            
                                            
                                            
                                            
                                            
                                            

                                            <?php if($data['search']): ?>
                                                <a href="<?php echo e(url('admin/report/tank')); ?>"
                                                   class="btn btn-danger btn-sm">Clear</a>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <table id="diesel_return"
                           class="table table-striped table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>DateTime Start</th>
                            <th>DateTime End</th>
                            <th>Tank Name</th>
                            <th>Start Volume(L)</th>
                            <th>End Volume(L)</th>
                            <th>Total Used(L)</th>
                            <th>Total Delivery(L)</th>
                            <th>Height Start</th>
                            <th>Height End</th>
                            <th>Water Start</th>
                            <th>Water End</th>
                            <th>Water Height Start</th>
                            <th>Water Height End</th>
                            <th>SourceType</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    
                    
                    
                    
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
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
    <script src="<?php echo e(asset('js/dataTables.colReorder.min.js')); ?>"></script>
    <script>

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

            var filename = 'Tank Delivery List Report At <?php echo e($data['from_date']); ?>-To-<?php echo e($data['to_date']); ?>';
            var data = '';
            <?php if($data['id']): ?>
                data = <?php echo json_encode($data['list']) . ';'?>
            <?php endif; ?>

            $(function () {
                var table1 = $('#diesel_return').DataTable({
                    'paging': true,
                    'lengthChange': true,
                    "pageLength": 10,
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': true,
                    "lengthMenu": [[10, 15, 25, 35, 50, -1], [10, 15, 25, 35, 50, "All"]],
                    'order': [[0, 'DESC']],
                    scrollY: "400px",
                    scrollX: true,
                    scrollCollapse: true,
                    fixedColumns: true,
                    data: data,
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
                                        return ((column === 4) || (column === 5) || (column === 6) || (column === 7)) ?
                                            data.replace(/[L,]/g, '') :
                                            data;
                                    }
                                }
                            },
                            messageTop: null,
                            title: '',
                        },
                    ],
                    columns: [
                        {data: 'delivery_id'},
                        {data: 'datetime_start'},
                        {data: 'datetime_end'},
                        {data: 'TankName'},

                        {
                            data: 'StartVolume', render: function (data, row, type) {
                                return data + " L";
                            }
                        },
                        {
                            data: 'VolumeEnd', render: function (data, row, type) {
                                return data + " L";
                            }
                        },
                        {
                            data: 'sale_volumne', render: function (data, row, type) {
                                return data + " L";
                            }
                        },
                        {
                            data: 'total_delivery', render: function (data, row, type) {
                                return data + " L";
                            }
                        },
                        {data: 'VolumeHeighStart'},
                        {data: 'VolumeHeighEnd'},
                        {data: 'water_start'},
                        {data: 'water_end'},
                        {data: 'water_height_start'},
                        {data: 'water_height_end'},
                        {data: 'source_type'},
                    ],

                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>