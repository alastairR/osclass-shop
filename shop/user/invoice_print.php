<?php
$params = array();
if (!empty($_POST['params'])) {
    $params = json_decode(urldecode($_POST['params']), true);
}
$taxRate = osc_get_preference('taxRate','shop');
?>
<html>
    <head>
        <title><?php echo 'TAX INVOICE' ?></title>
    </head>
    <body onload="window.print()" bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#0000FF"
          marginheight="1" marginwidth="0" leftmargin="0" topmargin="1"
          style="font-family: Arial, Helvetica, Sans Serif">

        <font face="font-family: Arial, Helvetica, Sans Serif">
        <table width="600" border=0 cellspacing=0 cellpadding=2 align="center"
               style="font-family: Arial, Helvetica, Sans Serif; font-size: 14px">
            <tr>
                <td colspan="4"><hr width="100%" height="1"></td>
            </tr>
            <tr style="font-size: 16px">
                <td colspan="2"><font size="6"><b><?php echo $params['company']['name']; ?></b></font></td>
            <?php if (osc_get_preference('show_taxes', 'payment_pro') == 1 || !empty($params['invoice']['taxes'])) { ?>
                </td><td colspan="2" align="right" valign="top"><font size="2">
                <?php echo 'Tax No: ' . $params['company']['taxno']; ?></font>
                </td>
            <?php } ?>
            </tr>
            <tr style="font-size: 16px">
                <td colspan="2" align="left" valign="top">
                    <?php if (!empty($params['company']['address1'])) echo $params['company']['address1'] . '<br>';  ?>
                    <?php if (!empty($params['company']['address2'])) echo $params['company']['address2'] . '<br>'; ?>
                    <?php if (!empty($params['company']['address3'])) echo $params['company']['address3'] . '<br>'; ?>
                    <?php if (!empty($params['company']['address4'])) echo $params['company']['address4'] . '<br>'; ?>
                    <?php if (!empty($params['company']['address5'])) echo $params['company']['address5'] . '<br>'; ?>
            </tr>
        </tr>
        <tr>
        <?php if (osc_get_preference('show_taxes', 'payment_pro') == 1 || !empty($params['invoice']['taxes'])) { ?>
            <td colspan="4" align="center"><font size="4"><?php echo 'TAX INVOICE' ?></font></td>
        <?php } else { ?>
            <td colspan="4" align="center"><font size="4"><?php echo 'INVOICE' ?></font></td>
        <?php } ?>
        </tr>
        <tr>
            <td colspan="2" align="left" valign="top"><font size="2">
                <?php if (isset($params['customer'])) { ?>
                <?php echo $params['customer']['name']; ?><br>
                <?php echo $params['customer']['address']; ?><br></font>
                <?php } ?>
            </td>
            <td colspan="2" align="right" valign="top"><font size="2">
                <?php echo $params['invoice']['date']; ?><br>
                <?php echo 'Invoice No: ' . $params['invoice']['number']; ?><br></font>
            </td>
        </tr><tr>
            <td colspan="4"><hr width="100%" height="1"></td>
        </tr>
        <tr >
            <td width="5%"></td>
            <td colspan="2" width="45%"><b><?php echo 'Items'; ?></b></td><td width="10%"></td>
        </tr>
        <tr>
            <td width="5%"></td>
            <td colspan='2'><font size="3"><?php echo $params['invoice']['items']; ?></font></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;  &nbsp;</td>
        </tr>
        <?php if (osc_get_preference('show_taxes', 'payment_pro') == 1 || !empty($params['invoice']['taxes'])) { ?>
        <tr>
            <td colspan="3" align="right"><font size="3"><?php echo 'Subtotal'; ?>:</font></td>
            <td align="right"><font size="3"><b><nobr><?php echo $params['invoice']['subtotal']; ?></nobr></b></font></td>
        </tr>
        <tr>
            <td colspan="3" align="right"><font size="3"><?php echo 'Tax'; ?>:</font></td>
            <td align="right"><font size="3"><b><nobr><?php echo $params['invoice']['taxes']; ?></nobr></b></font></td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="3" align="right"><font size="3"><?php echo 'Total'; ?>:</font></td>
            <td align="right"><font size="3"><b><nobr><?php echo $params['invoice']['total']; ?></nobr></b></font></td>
        </tr>
        <tr>
            <td colspan="4"><hr width="100%" height="1"></td>
        </tr>
    </table>
    </font>
</body>
</html>
