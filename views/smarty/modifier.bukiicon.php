<?php
function smarty_modifier_bukiicon(string $key) : string
{
    return \Yii::$app->getAssetManager()->getAssetUrl(
        \app\assets\BukiiconsAsset::register(\Yii::$app->view),
        "{$key}.png"
    );
}
