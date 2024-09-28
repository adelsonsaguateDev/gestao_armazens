<?php @session_start();
$user_ip = $_SERVER['REMOTE_ADDR'];
$result = filter_var(
    $user_ip,
    FILTER_VALIDATE_IP,
    FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
)
?>
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 100%;text-align: center; color: #444444;" class="text-center">
                <div class="invoice-desc" style="text-align: center; padding-top: 0; margin-top: 0">
                    <!-- <img style="width: 80px" src="../img/image.png" alt="Logo"><br><br> -->
                    Universidade Pedagógica de Maputo<br/>
                    Nuit: 100469261<br/>
                    Contacto: 871944848 / 847876424<br/>
                    Email: up.ac.@gmail.com
                </div>
            </td>
        </tr>
    </table>



