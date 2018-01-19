<?php

echo "<div class='adminfooter'>" . "<div class='txtcenter'>"
     . "<a href='https://xoops.org' rel='external' target='_blank'><img src='"
     . \Xmf\Module\Admin::iconUrl('xoopsmicrobutton.gif', '32')
     . "' alt='XOOPS' title='XOOPS'></a>" . '  </div>' . '  '
     . _AM_MODULEADMIN_ADMIN_FOOTER
     . "\n" . '</div>';

xoops_cp_footer();
