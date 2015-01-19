<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yiidreamteam\perfectmoney;

use yii\bootstrap\Widget;

class RedirectForm extends Widget
{
    public $api;
    public $invoiceId;
    public $amount;
    public $description = '';

    public function init()
    {
        parent::init();
        assert(isset($this->api));
        assert(isset($this->invoiceId));
        assert(isset($this->amount));
    }

    public function run()
    {
        return $this->render('redirect', [
            'api' => $this->api,
            'invoiceId' => $this->invoiceId,
            'amount' => number_format($this->amount, 2, '.', ''),
            'description' => $this->description,
        ]);
    }
}