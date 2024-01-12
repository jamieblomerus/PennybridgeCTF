<?php
namespace PBCTF\Views\Components;

use PBCTF\Actions;
?>
<footer class="site-footer">
    <p>Detta projekt är skapat av <a href="https://github.com/jamieblomerus">Jamie Blomerus</a> och får enbart attackeras i enlighet med reglerna, alla andra attacker utgör dataintrång.</p>
    <?php Actions::do_action('footer'); ?>
</footer>
<?php Actions::do_action('body_end'); ?>
</body>
</html>