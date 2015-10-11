<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_stat".
 *
 * @property integer $user_id
 * @property integer $battle_count
 * @property string $wp
 * @property string $wp_short
 * @property integer $total_kill
 * @property integer $total_death
 * @property integer $nawabari_count
 * @property string $nawabari_wp
 * @property integer $nawabari_kill
 * @property integer $nawabari_death
 * @property integer $gachi_count
 * @property string $gachi_wp
 * @property integer $gachi_kill
 * @property integer $gachi_death
 *
 * @property User $user
 */
class UserStat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_stat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'battle_count', 'total_kill', 'total_death'], 'integer'],
            [['nawabari_count', 'nawabari_kill', 'nawabari_death'], 'integer'],
            [['gachi_count', 'gachi_kill', 'gachi_death'], 'integer'],
            [['wp', 'wp_short', 'nawabari_wp', 'gachi_wp'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'battle_count' => 'Battle Count',
            'wp' => 'Wp',
            'wp_short' => 'Wp Short',
            'total_kill' => 'Total Kill',
            'total_death' => 'Total Death',
            'nawabari_count' => 'Nawabari Count',
            'nawabari_wp' => 'Nawabari Wp',
            'nawabari_kill' => 'Nawabari Kill',
            'nawabari_death' => 'Nawabari Death',
            'gachi_count' => 'Gachi Count',
            'gachi_wp' => 'Gachi Wp',
            'gachi_kill' => 'Gachi Kill',
            'gachi_death' => 'Gachi Death',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function createCurrentData()
    {
        static $nawabari = null;
        if ($nawabari === null) {
            $nawabari = Rule::findOne(['key' => 'nawabari'])->id;
        }

        $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $shortCondition = sprintf(
            '(({{battle}}.[[end_at]] IS NOT NULL) AND ({{battle}}.[[end_at]] BETWEEN %s AND %s))',
            Yii::$app->db->quoteValue(gmdate('Y-m-d H:i:sO', $now - 86400 + 1)),
            Yii::$app->db->quoteValue(gmdate('Y-m-d H:i:sO', $now))
        );

        $column_battle_count = "COUNT(*)";
        $column_wp = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            "SUM(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)",
            "SUM(CASE WHEN {{battle}}.[[is_win]] IS NULL THEN 0 ELSE 1 END)"
        );
        $column_wp_short = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            "SUM(CASE WHEN {$shortCondition} AND {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)",
            "SUM(CASE WHEN {$shortCondition} AND {{battle}}.[[is_win]] IS NOT NULL THEN 1 ELSE 0 END)"
        );
        $column_total_kill = sprintf(
            "SUM(%s)",
            "CASE WHEN " .
            "{{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL " .
            "THEN {{battle}}.[[kill]] " .
            "ELSE 0 END"
        );
        $column_total_death = sprintf(
            "SUM(%s)",
            "CASE WHEN {{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL " .
            "THEN {{battle}}.[[death]] " .
            "ELSE 0 END"
        );
        $column_nawabari_count = "SUM(CASE WHEN {{battle}}.[[rule_id]] = {$nawabari} THEN 1 ELSE 0 END)";
        $column_nawabari_wp = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            "SUM(CASE WHEN {{battle}}.[[rule_id]] = {$nawabari} AND {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)",
            "SUM(CASE WHEN {{battle}}.[[rule_id]] = {$nawabari} AND {{battle}}.[[is_win]] IS NOT NULL " .
            "THEN 1 ELSE 0 END)"
        );
        $column_nawabari_kill = sprintf(
            "SUM(%s)",
            "CASE WHEN {{battle}}.[[rule_id]] = {$nawabari} AND " .
            "{{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL " .
            "THEN {{battle}}.[[kill]] " .
            "ELSE 0 END"
        );
        $column_nawabari_death = sprintf(
            "SUM(%s)",
            "CASE WHEN {{battle}}.[[rule_id]] = {$nawabari} AND " .
            "{{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL " .
            "THEN {{battle}}.[[death]] " .
            "ELSE 0 END"
        );
        $column_gachi_count = "SUM(CASE WHEN {{battle}}.[[rule_id]] <> {$nawabari} THEN 1 ELSE 0 END)";
        $column_gachi_wp = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            "SUM(CASE WHEN {{battle}}.[[rule_id]] <> {$nawabari} AND {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)",
            "SUM(CASE WHEN {{battle}}.[[rule_id]] <> {$nawabari} AND {{battle}}.[[is_win]] IS NOT NULL " .
            "THEN 1 ELSE 0 END)"
        );
        $column_gachi_kill = sprintf(
            "SUM(%s)",
            "CASE WHEN {{battle}}.[[rule_id]] <> {$nawabari} AND " .
            "{{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL " .
            "THEN {{battle}}.[[kill]] " .
            "ELSE 0 END"
        );
        $column_gachi_death = sprintf(
            "SUM(%s)",
            "CASE WHEN {{battle}}.[[rule_id]] <> {$nawabari} AND " .
            "{{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL " .
            "THEN {{battle}}.[[death]] " .
            "ELSE 0 END"
        );

        $query = (new \yii\db\Query())
            ->select([
                'battle_count'      => $column_battle_count,
                'wp'                => $column_wp,
                'wp_short'          => $column_wp_short,
                'total_kill'        => $column_total_kill,
                'total_death'       => $column_total_death,
                'nawabari_count'    => $column_nawabari_count,
                'nawabari_wp'       => $column_nawabari_wp,
                'nawabari_kill'     => $column_nawabari_kill,
                'nawabari_death'    => $column_nawabari_death,
                'gachi_count'       => $column_gachi_count,
                'gachi_wp'          => $column_gachi_wp,
                'gachi_kill'        => $column_gachi_kill,
                'gachi_death'       => $column_gachi_death,
            ])
            ->from('battle')
            ->andWhere(['{{battle}}.[[user_id]]' => $this->user_id]);
        $this->attributes =  $query->createCommand()->queryOne();
        
        $keys = [
            'battle_count', 'total_kill', 'total_death',
            'nawabari_count', 'nawabari_kill', 'nawabari_death',
            'gachi_count', 'gachi_kill', 'gachi_death',
        ];
        foreach ($keys as $key) {
            $this->$key = (int)$this->$key;
        }
    }
}
