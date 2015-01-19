<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yiidreamteam\perfectmoney\actions;

use yii\base\Action;
use yii\base\InvalidConfigException;
use yiidreamteam\perfectmoney\Api;

class ResultAction extends Action
{
    public $componentName;
    public $redirectUrl;

    public $silent = false;

    /** @var Api */
    private $api;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->api = \Yii::$app->get($this->componentName);
        if (!$this->api instanceof Api)
            throw new InvalidConfigException('Invalid PerfectMoney component configuration');

        parent::init();
    }

    public function run()
    {
        try {
            $this->api->processResult(\Yii::$app->request->post());
        } catch (\Exception $e) {
            if (!$this->silent)
                throw $e;
        }

        if (isset($this->redirectUrl))
            return \Yii::$app->response->redirect($this->redirectUrl);
    }
}