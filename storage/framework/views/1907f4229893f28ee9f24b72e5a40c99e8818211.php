<style>
    /*.table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {*/
    /*line-height: 0.428571 !important;*/
    /*}*/

    /*.font-size {*/
    /*font-size: 12px !important;*/
    /*}*/

    /*td span {*/
    /*line-height: 8px !important;*/
    /*}*/

    /*.header-td {*/
    /*line-height: 0.2 !important;*/
    /*}*/

    .table-content > table {
        border-collapse: collapse;
        table-layout: fixed;
        width: 310px;
    }

    .table-content > table td {
        border: solid 1px #fab;
        width: 100px;
        word-wrap: break-word;
    }

    .padding_td {
        padding-left: 2px !important;
        padding-right: 0px !important;
    }
</style>
<?php $row = $data['row']?>
<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered text-center table-content">
            <tbody>
            <tr>
                <th>Issued At</th>
                <td><?php echo e($row->issued_date); ?></td>
                <th>Expired At</th>
                <td><?php echo e($row->expired_date); ?></td>
                <th>Issued By</th>
                <td><?php echo e($row->DisplayName); ?></td>
            </tr>
            <tr>
                <th>Truck No</th>
                <td><?php echo e($row->PlateNumber); ?></td>
                <th>Team</th>
                <td><?php echo e($row->Team); ?></td>
                <th>Driver Name</th>
                <td><?php echo e($row->NameKh); ?></td>
            </tr>
            <tr>
                <th>Trailer No</th>
                <td><?php echo e($row->TrailerNumber); ?></td>
                <th>Status</th>
                <td><?php echo e($row->StatusName); ?></td>
                <th>Remark</th>
                <td><?php echo e($row->RemarkTicket); ?></td>
            </tr>
            </tbody>
        </table>
        <table class="table table-bordered text-center table-content"
               style="padding-right: 0px !important;padding-left: 0px !important;">
            <tr>
                <td class="padding_td" width="5%">Team Leader</td>
                <td class="padding_td" width="5%">Ref No</td>
                <td class="padding_td" width="8%">Purpose</td>
                <td class="padding_td" width="8%">Destination</td>
                <td class="padding_td" width="5%">Containers(Feet).</td>
                <td class="padding_td" width="5%">Customer</td>
                <td class="padding_td" width="3%">DR(L)</td>
                <td class="padding_td" width="3%">+/-(L)</td>
                <td class="padding_td" width="5%">Fuel(L)</td>
                <td class="padding_td" width="5%">Total(L)</td>
                <td class="padding_td" width="4%">PayTrip(៛)</td>
                <td class="padding_td" width="5%">+/-(៛)</td>
                <td class="padding_td" width="5%">Total(៛)</td>
                <td class="padding_td" width="5%">MT Pickup</td>
                <td class="padding_td" width="5%">Lolo($)</td>
                <td class="padding_td" width="12%">Note Fuel</td>
                <td class="padding_td" width="12%">Note PayTrip</td>
            </tr>
            <?php
            $j = 1;
            $LoloAmount = 0;
            ?>
            <?php $__currentLoopData = $data['list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="padding_td"><?php echo e($item->TeamLeader); ?></td>
                    <td class="padding_td"><?php echo e($item->ReferenceNumber); ?></td>
                    <td class="padding_td"><?php echo e($item->Reason); ?></td>
                    <td class="padding_td"><?php echo e($item->Code); ?></td>
                    <td class="padding_td"><?php echo e($item->ConNum1."(".$item->Feet1."),"); ?>

                        <br/><?php echo e($item->ConNum2."(".$item->Feet2.")"); ?></td>
                    <td class="padding_td"><?php echo e($item->CustomerName); ?></td>
                    <td class="padding_td"><?php echo e(($item->DieselReturnAmount==0?"":$item->DieselReturnAmount)); ?></td>
                    <td class="padding_td"><?php echo e(($item->FuelAdd==0?"":$item->FuelAdd)); ?></td>
                    <td class="padding_td"><?php echo e(($item->Fuel==0?"":$item->Fuel)); ?></td>
                    <td class="padding_td"><?php echo e(($item->TotalFuel==0?"":$item->TotalFuel)); ?></td>
                    <td class="padding_td"><?php echo e(($item->PayTrip==0?"":number_format($item->PayTrip))); ?></td>
                    <td class="padding_td"><?php echo e(($item->PayTripAdd==0?"":number_format($item->PayTripAdd))); ?></td>
                    <td class="padding_td"><?php echo e(($item->TotalPayTrip==0?"":number_format($item->TotalPayTrip))); ?></td>
                    <td class="padding_td"><?php echo e(($item->MTPickupName==""?"":$item->MTPickupName)); ?></td>
                    <td class="padding_td"><?php echo e(($item->LoloAmount==0?"":number_format($item->LoloAmount))); ?></td>
                    <td class="padding_td"><?php echo e($item->Note); ?></td>
                    <td class="padding_td"><?php echo e($item->PayTripNote); ?></td>
                </tr>
                <?php
                $j++;
                $LoloAmount += $item->LoloAmount;
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td colspan="6"
                    style="border-left-color:white !important;border-bottom-color: white !important; text-align: right !important;">
                    &nbsp;
                </td>
                <td style="text-align: left !important;" colspan="11"> &nbsp;&nbsp;Total
                    Fuel:&nbsp;&nbsp;<?php echo e($row->TotalAmountFuel." L"); ?>

                    ,&nbsp;&nbsp;&nbsp;
                    Total Pay Trip: &nbsp;&nbsp;<?php echo e(number_format($row->TotalPayTripAmount)); ?>&nbsp;&#6107;,&nbsp;&nbsp;&nbsp;
                    Total Lolo: &nbsp;&nbsp;$&nbsp;<?php echo e(number_format($LoloAmount)); ?></td>
            </tr>
        </table>
    </div>
</div>