<?php

use backend\helpers\GridHelper;
use common\models\Apple;

/** @var yii\web\View $this */
/** @var Apple $model */
/** @var GridHelper $gridHelper */

?>

<div class="auth-item-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'gridHelper' => $gridHelper,
    ]); ?>

</div>
