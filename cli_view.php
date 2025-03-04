<?php
declare(strict_types=1);
include 'header.php';


if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['Username'] ?? '';

  $v_reptype = $_POST['reptype'] ?? '';

  $v_begindate = '';
  $v_perioddate = '';
  $v_perioddate2 = '';
  $v_view_dt = '';
  
  if (!empty($_POST['p_date1'])) {
    $date1 = DateTime::createFromFormat('d.m.Y', $_POST['p_date1']);
    if ($date1) {
      $v_perioddate = " and ek.date_trans = '" . $date1->format('Y-m-d') . "'";
      $v_perioddate2 = " and hyrjedalje.date_trans = '" . $date1->format('Y-m-d') . "'";
      
      // Month name mapping using array instead of multiple if statements
      $monthNames = [
        '01' => 'Jan', '02' => 'Shk', '03' => 'Mar', '04' => 'Pri',
        '05' => 'Maj', '06' => 'Qer', '07' => 'Kor', '08' => 'Gus',
        '09' => 'Sht', '10' => 'Tet', '11' => 'Nen', '12' => 'Dhj'
      ];
      
      $v_monthdisp = $monthNames[$date1->format('m')] ?? '';
      $v_view_dt = $date1->format('d') . " " . $v_monthdisp . " " . $date1->format('Y');
    }
  }

  if (!empty($_POST['p_date2'])) {
    $date2 = DateTime::createFromFormat('d.m.Y', $_POST['p_date2']);
    if ($date2) {
      $v_perioddate = " and ek.date_trans >= '" . $date1->format('Y-m-d') . "'
                      and ek.date_trans <= '" . $date2->format('Y-m-d') . "' ";
      
      $v_perioddate2 = " and hyrjedalje.date_trans >= '" . $date1->format('Y-m-d') . "'
                       and hyrjedalje.date_trans <= '" . $date2->format('Y-m-d') . "' ";
      
      $v_monthdisp = $monthNames[$date2->format('m')] ?? '';
      $v_view_dt .= " - " . $date2->format('d') . " " . $v_monthdisp . " " . $date2->format('Y');
    }
  }

  $v_klient_id = (int)($_POST['id_klienti'] ?? 0);

?>


  <!-- --------------------------------------- -->
  <!--          Aplikacioni xChange            -->
  <!--                                         -->
  <!--  Kontakt:                               -->
  <!--                                         -->
  <!--           GlobalTech.al                 -->
  <!--                                         -->
  <!--        info@globaltech.al               -->
  <!-- --------------------------------------- -->



<div class="page-wrapper">
    <div class="container-fluid">
        <div class="table-responsive">
            <!-- Header/Logo table -->
            <table class="table">
                <tbody>
                    <tr>
                        
                        <td class="text-end align-middle">
                            <span class="text-muted">Printuar dt. </span>
                            <span class="fw-bold"><?php echo strftime('%Y-%m-%d'); ?></span>
                            <span class="text-muted">Përdoruesi: </span>
                            <span class="fw-bold"><?php echo $user_info; ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Main content table -->
             <div class="text-center">
               <p class="text-center fw-bold h5">Raport per klient</p>
               <br/>
                   <?php if ($v_klient_id > 0) {
                       $query_filiali_info = "SELECT * FROM klienti WHERE id = ?";
                       $stmt = mysqli_prepare($MySQL, $query_filiali_info);
                       mysqli_stmt_bind_param($stmt, 'i', $v_klient_id);
                       mysqli_stmt_execute($stmt);
                       $result = mysqli_stmt_get_result($stmt);
                       
                       while ($row_filiali_info = mysqli_fetch_assoc($result)) {
                           ?>
                           <p><span class="ReportSubTitle"> <?= htmlspecialchars(strtoupper($row_filiali_info['emri'] . " " . $row_filiali_info['mbiemri'])) ?> </span></p>
                           <?php
                       }
                       mysqli_stmt_close($stmt);
                   } ?>
                   <p class="text-center"><?php echo $v_view_dt; ?></p>
             </div>
            <table class="table">
                
                
                <thead class="bg-primary text-white">
                    <?php if ($v_reptype == "kembim") { ?>
                        <tr>
                            <th colspan="12" class="text-center">Këmbime valutore</th>
                        </tr>
                        <tr>
                            <th>Nr.</th>
                            <th>Nr. Fature</th>
                            <th>Dt. Trans.</th>
                            <th>Emri</th>
                            <th>Mbiemri</th>
                            <th>Hyrë</th>
                            <th>Dalë</th>
                            <th>Shuma e Hyrë</th>
                            <th>Kursi</th>
                            <th>Shuma e Dalë</th>
                            <th>Komisioni</th>
                            <th>Totali i Dalë</th>
                        </tr>
                    <?php } ?>
                    
                    <?php if ($v_reptype == "hyrdal") { ?>
                        <tr>
                            <th colspan="12" class="text-center">Hyrje / Dalje</th>
                        </tr>
                        <tr>
                            <th>Nr.</th>
                            <th>Emri / Mbiemri</th>
                            <th>Monedha</th>
                            <th colspan="3">Hyrë</th>
                            <th colspan="3">Dalë</th>
                        </tr>
                    <?php } ?>
                </thead>
                <tbody>
                    <?php
                    set_time_limit(0);

                    $RepInfo_sql = " select ek.*, ed.*, k.emri, k.mbiemri, m1.monedha as mon1, m2.monedha as mon2
                         from exchange_koke as ek,
                              exchange_detaje as ed,
                              klienti as k,
                              monedha as m1,
                              monedha as m2
                        where ek.chstatus       = 'T'
                          and ek.id             = ed.id_exchangekoke
                          and ek.id_klienti     = " . $v_klient_id . "
                          " . $v_perioddate . "
                          and ek.id_klienti     = k.id
                          and ek.id_monkreditim = m1.id
                          and ed.id_mondebituar = m2.id
                     ";

                    $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
                    $row_RepInfo = $RepInfoRS->fetch_assoc();

                    $rowno   = 0;

                    while ($row_RepInfo) {
                        $rowno++;

                        $v_kursi = 0;
                        if ($row_RepInfo['kursi'] > $row_RepInfo['kursi1']) {
                            $v_kursi = $row_RepInfo['kursi'];
                        } else {
                            $v_kursi = $row_RepInfo['kursi1'];
                        }
                    ?>
                       
                        <tr>
                            <td class="text-center"> <?php echo $rowno;?> </td>
                            <td class="text-center"> <?php echo $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id']; ?> </td>
                            <td class="text-center"> <?php echo substr($row_RepInfo['datarregjistrimit'], 8, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 5, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 0, 4) . " " . substr($row_RepInfo['datarregjistrimit'], 11, 8); ?> </td>
                            <td class="text-center"> <?php echo $row_RepInfo['emri']; ?> </td>
                            <td class="text-center"> <?php echo $row_RepInfo['mbiemri']; ?> </td>
                            <td class="text-center"> <?php echo $row_RepInfo['mon2']; ?> </td>
                            <td class="text-center"> <?php echo $row_RepInfo['mon1']; ?> </td>
                            <td class="text-end"> <?php echo number_format((int)($row_RepInfo['vleftadebituar']), 2, '.', ','); ?> </td>
                            <td class="text-end"> <?php echo number_format((int)$v_kursi, 4, '.', ','); ?> </td>
                            <td class="text-end"> <?php echo number_format((int)$row_RepInfo['vleftakredituar'], 2, '.', ','); ?> </td>
                            <td class="text-end"> <?php echo number_format((int)$row_RepInfo['vleftakomisionit'], 2, '.', ','); ?> </td>
                            <td class="text-end"> <?php echo number_format((int)$row_RepInfo['vleftapaguar'], 2, '.', ','); ?> </td>
                        </tr>
                    <?php $row_RepInfo = $RepInfoRS->fetch_assoc();
                    };
                    mysqli_free_result($RepInfoRS);
                    ?>
                <?php  }  ?>
                <?php if ($v_reptype == "hyrdal") {  ?>
                    <?php

                    $query_gjendje_info = " SELECT hyrjedalje.id_klienti, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe, monedha.monedha,
                                             SUM( case when hyrjedalje.drcr = 'Debitim'  then hyrjedalje.vleftapaguar else 0 end) vleftadebit,
                                             SUM( case when hyrjedalje.drcr = 'Kreditim' then hyrjedalje.vleftapaguar else 0 end) vleftakredit
                                        FROM hyrjedalje, monedha, klienti
                                       WHERE hyrjedalje.id_monedhe = monedha.id
                                         AND hyrjedalje.id_klienti = klienti.id
                                         AND hyrjedalje.chstatus   = 'T'
                                         AND hyrjedalje.id_klienti = " . $v_klient_id . "
                                         " . $v_perioddate2 . "
                                  GROUP BY hyrjedalje.id_klienti, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe, monedha.monedha
                                  ORDER BY klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe ";
                    $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
                    $row_gjendje_info = $gjendje_info->fetch_assoc();;
                    $rowno2 = 0;

                    while ($row_gjendje_info) {
                        $rowno2++;

                    ?>
                        <tr>
                            <td class="text-center"> <?php echo $rowno2; ?> </td>
                            <td class="text-center"> <?php echo $row_gjendje_info['emri']; ?> <?php echo $row_gjendje_info['mbiemri']; ?> </td>
                            <td class="text-center"> <?php echo $row_gjendje_info['monedha']; ?> </td>
                            <td class="text-end" colspan="3"> <?php echo number_format((int)$row_gjendje_info['vleftadebit'], 2, '.', ','); ?> </td>
                            <td class="text-end" colspan="3"> &nbsp;&nbsp; </td>
                            <td class="text-end" colspan="3"> <?php echo number_format((int)$row_gjendje_info['vleftakredit'], 2, '.', ','); ?> </td>
                        </tr>
                    <?php $row_gjendje_info = $gjendje_info->fetch_assoc();;
                    }
                    mysqli_free_result($gjendje_info);
                    // ---------------------------------------------------------------------------------
                    ?>


                <?php  }  ?>

              
                <tr>
                    <td class="text-center" colspan="3"><b>Monedha</b></td>
                    <td class="text-end" colspan="3"><b>Shuma e hyrë</b></td>
                    <td class="text-end" colspan="3"><b>Komisioni</b></td>
                    <td class="text-end" colspan="3"><b>Shuma e dalë</b></td>
                </tr>
                
                <?php


                if ($v_reptype == "kembim") {

                    $RepInfo_sql = " select info.mon, sum(info.vlerakredit) as vlerakredit, sum(info.komision) as komision, sum(info.vleradebit) as vleradebit
                           from (
                                        select 0 as vlerakredit, sum(ek.vleftakomisionit) as komision, sum(ek.vleftapaguar) as vleradebit, m1.id, m1.monedha as mon
                                          from exchange_koke as ek,
                                               klienti as k,
                                               monedha as m1
                                         where ek.chstatus       = 'T'
                                           and ek.id_klienti     = " . $v_klient_id . "
                                           " . $v_perioddate . "
                                           and ek.id_klienti     = k.id
                                           and ek.id_monkreditim = m1.id
                                      group by m1.id, m1.monedha
                                        union all
                                        select sum(ed.vleftadebituar) as vlerakredit, 0 as komision, 0 as vleradebit, m2.id, m2.monedha as mon
                                          from exchange_koke as ek,
                                               exchange_detaje as ed,
                                               klienti as k,
                                               monedha as m2
                                         where ek.chstatus       = 'T'
                                           and ek.id             = ed.id_exchangekoke
                                           and ek.id_klienti     = " . $v_klient_id . "
                                           " . $v_perioddate . "
                                           and ek.id_klienti     = k.id
                                           and ed.id_mondebituar = m2.id
                                      group by m2.id, m2.monedha
                                ) info
                         group by info.mon, info.id
                         order by info.id ";
                } else {

                    $RepInfo_sql = " select info.mon, sum(info.vlerakredit) as vlerakredit, sum(info.komision) as komision, sum(info.vleradebit) as vleradebit
                           from (
                                        SELECT SUM( case when hyrjedalje.drcr = 'Debitim'  then hyrjedalje.vleftapaguar else 0 end) as vleftakredit,
                                               0 as komision,
                                               SUM( case when hyrjedalje.drcr = 'Kreditim' then hyrjedalje.vleftapaguar else 0 end) as vleftadebit,
                                               hyrjedalje.id_monedhe as id, monedha.monedha as mon
                                          FROM hyrjedalje, monedha, klienti
                                         WHERE hyrjedalje.id_monedhe = monedha.id
                                           AND hyrjedalje.id_klienti = klienti.id
                                           AND hyrjedalje.chstatus   = 'T'
                                           AND hyrjedalje.id_klienti = " . $v_klient_id . "
                                           " . $v_perioddate2 . "
                                      GROUP BY hyrjedalje.id_monedhe, monedha.monedha
                                ) info
                         group by info.mon, info.id
                         order by info.id ";
                }

                $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
                $row_RepInfo = $RepInfoRS->fetch_assoc();

                while ($row_RepInfo) {
                ?>
                    <tr>
                        <td class="text-center" colspan="3"> <?php echo $row_RepInfo['mon']; ?> </td>
                        <td class="text-end" colspan="3"> <?php echo number_format((int)$row_RepInfo['vlerakredit'], 2, '.', ','); ?> </td>
                        <td class="text-end" colspan="3"> <?php echo number_format((int)$row_RepInfo['komision'], 2, '.', ','); ?> </td>
                        <td class="text-end" colspan="3"> <?php echo number_format((int)$row_RepInfo['vleradebit'], 2, '.', ','); ?> </td>
                    </tr>
                   
                <?php $row_RepInfo = $RepInfoRS->fetch_assoc();
                };
                mysqli_free_result($RepInfoRS);
                ?>
                <tr>
                    <td class="text-start ps-4" colspan="12">
                        <b>Totali i transaksioneve: [ <?php echo $rowno; ?> ] [ <?php echo isset($rowno2) ? $rowno2 : 0; ?> ]</b>
                    </td>
                </tr>
                
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>