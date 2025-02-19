<html>

<head>
  <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System</title>
  <script language="JavaScript">
    <!-- Begin
    function return_value(p_url) {

      opener.document.formmenu.id_klienti.value = p_url;

      self.close();
      return false;
    }

    //  End 
    -->
  </script>
</head>

<body>
  <center>
    <?php

    require_once('ConMySQL.php');

    // Initialize the where clause with prepared statement parameters
    $where = " WHERE TRUE ";
    $params = [];
    $types = "";

    if (!empty($_POST["emri"])) {
        $search_term = "%" . $_POST["emri"] . "%";
        $where = " WHERE emri LIKE ? OR mbiemri LIKE ? ";
        $params = [$search_term, $search_term];
        $types = "ss";
    }

    $rec_limit = 10;

    // Get total count using prepared statement
    $count_sql = "SELECT COUNT(id) FROM klienti" . $where;
    $stmt = mysqli_prepare($MySQL, $count_sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die('Could not get data: ' . mysqli_error($MySQL));
    }
    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    $rec_count = $row[0];
    mysqli_stmt_close($stmt);

    // Calculate pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] + 1 : 0;
    $offset = $rec_limit * $page;
    $left_rec = $rec_count - ($page * $rec_limit);

    // Get client data using prepared statement
    $sql_info = "SELECT id, emri, mbiemri, telefon, nrpashaporte, docname 
                 FROM klienti " . 
                 $where . 
                 " ORDER BY id 
                 LIMIT ?, ?";

    $stmt = mysqli_prepare($MySQL, $sql_info);
    if ($params) {
        // Add pagination parameters
        $params[] = $offset;
        $params[] = $rec_limit;
        mysqli_stmt_bind_param($stmt, $types . "ii", ...$params);
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $offset, $rec_limit);
    }

    mysqli_stmt_execute($stmt);
    $h_menu = mysqli_stmt_get_result($stmt);
    if (!$h_menu) {
        die('Query failed: ' . mysqli_error($MySQL));
    }

    $row_h_menu = mysqli_fetch_assoc($h_menu);
    $totalRows_h_menu = mysqli_num_rows($h_menu);
    ?>
    <table width="300px" border="0">
      <tr>
        <td colspan="3">
          <DIV class=ctxheading>Perzgjidh nga lista</DIV>
        </td>
      </tr>
      <form ACTION="klient_list.php" METHOD="POST" name="formmenu">
        <tr>
          <td colspan="3"><input name="emri" type="text" id="emri" placeholder="Kerko" value="" onChange="JanaScript: document.formmenu.submit();" size="20"></td>
        </tr>
      </form>
      <tr valign="top">
        <td width="90%" align="center">

          <table width="300" border="0" cellspacing="0" cellpadding="0">
            <?php
            while ($row_h_menu) { ?>
              <tr bgcolor="#080570">
                <td height="1" colspan="3"></td>
              </tr>
              <tr bgcolor="#99FFCC">
                <td class="titull"></td>
                <td height="16"><a href="JavaScript: return_value('<?php echo $row_h_menu['id']; ?>');" class="link4"><b><?php echo $row_h_menu['emri'] . " " . $row_h_menu['mbiemri']; ?></b></a></td>
                <td class="titull"></td>
              </tr>
            <?php $row_h_menu = mysqli_fetch_assoc($h_menu);
            };
            mysqli_free_result($h_menu);
            ?>
            <tr bgcolor="#080570">
              <td height="1" colspan="3"></td>
            </tr>
            <tr>
              <td height="5" colspan="3"></td>
            </tr>
            <tr>
              <td height="5" colspan="3" align="center" class="titull">
                <?php
                // if ($page > 0) {
                //     $last = $page - 2;
                //     echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=$last\">Last 10 Records</a> |";
                //     echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=$page\">Next 10 Records</a>";
                // } else if ($page == 0) {
                //     echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=$page\">Next 10 Records</a>";
                // } else if ($left_rec < $rec_limit) {
                //     $last = $page - 2;
                //     echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=$last\">Last 10 Records</a>";
                // }
                ?>
              </td>
            </tr>
          </table>
  </center>
  </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  </table>

</body>

</html>
