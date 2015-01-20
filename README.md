# PerfectMoney component for Yii2 #

Payment gateway and api client for [PerfectMoney](http://yiidreamteam.com/link/perfect-money) service.

## Installation ##

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

    php composer.phar require --prefer-dist yii-dream-team/yii2-perfect-money "*"

or add

    "yii-dream-team/yii2-perfect-money": "*"

to the `require` section of your composer.json.

## Usage ##

TBD


### Component configuration ###

    'pm' => [
        'accountId' => '1234567',
        'accountPassword' => 'xxxxxxxxx',
        'walletNumber' => 'U1234567',
        'hash' => '827CCB0EEA8A706C4C34A16891F84E7B',
        'resultUrl' => ['/perfect-money/result'],
        'successUrl' => ['/site/payment-success'],
        'failureUrl' => ['/site/payment-failure'],
    ],

### Gateway controller ###

You will need to create controller that will handle result requests from PerfectMoney service.
Sample controller code:

    <?php
    namespace frontend\controllers;
    
    use common\models\Invoice;
    use yii\base\Event;
    use yii\helpers\ArrayHelper;
    use yii\helpers\VarDumper;
    use yii\web\Controller;
    use yiidreamteam\perfectmoney\actions\ResultAction;
    use yiidreamteam\perfectmoney\Api;
    use yiidreamteam\perfectmoney\events\GatewayEvent;
    
    class PerfectMoneyController extends Controller
    {
        public $enableCsrfValidation = false;
    
        public function init()
        {
            parent::init();
            /** @var Api $pm */
            $pm = \Yii::$app->get('pm');
            $pm->on(GatewayEvent::EVENT_PAYMENT_REQUEST, [$this, 'handlePaymentRequest']);
            $pm->on(GatewayEvent::EVENT_PAYMENT_SUCCESS, [$this, 'handlePaymentSuccess']);
        }
    
        public function actions()
        {
            return [
                'result' => [
                    'class' => ResultAction::className(),
                    'componentName' => 'pm',
                    'redirectUrl' => ['/site/index'],
                ],
            ];
        }
    
        /**
         * @param GatewayEvent $event
         * @return bool
         */
        public function handlePaymentRequest($event)
        {
            $invoice = Invoice::findOne(ArrayHelper::getValue($event->gatewayData, 'PAYMENT_ID'));
    
            if (!$invoice instanceof Invoice ||
                $invoice->status != Invoice::STATUS_NEW ||
                ArrayHelper::getValue($event->gatewayData, 'PAYMENT_AMOUNT') != $invoice->amount ||
                ArrayHelper::getValue($event->gatewayData, 'PAYEE_ACCOUNT') != \Yii::$app->get('pm')->walletNumber
            )
                return;
    
            $invoice->debugData = VarDumper::dumpAsString($event->gatewayData);
            $event->invoice = $invoice;
            $event->handled = true;
        }
    
        /**
         * @param GatewayEvent $event
         * @return bool
         */
        public function handlePaymentSuccess($event)
        {
            $invoice = $event->invoice;
            
            // TODO: invoice processing goes here 
        }
    }

## Licence ##

MIT
    
## Links ##

* [Official site](http://yiidreamteam.com/yii2/perfect-money)
* [Source code on GitHub](https://github.com/yii-dream-team/yii2-perfect-money)
* [Composer package on Packagist](https://packagist.org/packages/yii-dream-team/yii2-perfect-money)
* [PerfectMoney service](http://yiidreamteam.com/link/perfect-money)
