<div class="homepage">
    <div class="container h-100 w-100">
        <div class="h-100 w-100 row align-items-center">
            <div class="col-12 col-md-8">
                <h1 class="font-weight-bold text-center text-coekku">Donation</h1>
                <div class="d-flex justify-content-center"><img src="../static/elements/promptpay.png" class="z-depth-1 mb-3" width="300" /></div>
                <h5 class="text-center font-weight-bold">Palapon Soontornpas / พลภณ สุนทรภาส</h5>
                <hr>
                <p class="text-center">
                    <b>Promptpay / TrueWallet</b> : <code>090-8508007</code><br>
                    <b>SCB</b> : <code>551-442288-3</code><br>
                    <b>KBank</b> : <code>084-3-24454-8</code> [ไม่ใช่เบอร์โทรศัพท์!]<br>
                </p>
            </div>
            <div class="col-12 col-md-4">
                <h4 class="font-weight-bold text-center text-coekku">Donator</h4>
                <p class="text-center"><small class="text-muted">ตอนนี้เป็น Manual Update นะงับ ยังเขียนไม่เสร็จ 555</small></p>
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="submissionTable">
                        <thead>
                            <tr class="text-nowrap me">
                                <th scope="col" class="font-weight-bold text-coekku">User</th>
                                <th scope="col" class="font-weight-bold text-coekku">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="text-nowrap">
                            <?php
                    $target = "../donator.txt";
                    if (file_exists($target)) {
                        $f = fopen("../donator.txt", "r");
                        $i = 0;
                        $total = 0;
                        while(!feof($f)) {
                            $l = explode(" ", fgets($f));
                            $i++;
                            $total += isset($l[1]) ? (double) $l[1] : 0;
                            if ($i <= 10 && isset($l[1])) //Display only last 10
                                echo "<tr><th scope='row'>".$l[0]."</th><td>".$l[1]." ฿</td></tr>";
                        }
                        fclose($f);
                    }
                ?>
                        </tbody>
                    </table>
                </div>
                <?php $val = ($total / 150)*100;
                if ($val > 100) $val = 100; ?>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-coekku" role="progressbar" style="width: <?php echo $val;?>%" aria-valuenow="<?php echo $val;?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small><?php echo $total; ?>/150 THB (<?php echo number_format((float) $val, 2, '.', ''); ?>%)</small><hr>
                ทุกคนที่บริจาคจะได้รับ <text class="rainbow font-weight-bold">ชื่อสีรุ้ง</text>
            </div>
        </div>
    </div>
</div>