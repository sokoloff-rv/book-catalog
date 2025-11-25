<?php

namespace app\models;

use yii\base\Model;

class TopAuthorsReportForm extends Model
{
    public int $year;

    /** @var int[] */
    public array $availableYears = [];

    public function rules(): array
    {
        return [
            [['year'], 'required'],
            ['year', 'integer'],
            ['year', 'in', 'range' => $this->availableYears, 'when' => function () {
                return !empty($this->availableYears);
            }],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'year' => 'Год выпуска',
        ];
    }
}
