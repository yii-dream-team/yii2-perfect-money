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

### Component configuration ###

Configure `pm` component in the `components` section of your application.

    'pm' => [
        'class' => '\yiidreamteam\perfectmoney\Api',
        'accountId' => '1234567',
        'accountPassword' => 'xxxxxxxxx',
        'walletNumber' => 'U1234567',
        'merchantName' => 'My Merchant',
        'alternateSecret' => 'X00O8cT08pOEZTJdFmSiAwxyu', 
        'resultUrl' => ['/perfect-money/result'],
        'successUrl' => ['/site/payment-success'],
        'failureUrl' => ['/site/payment-failure'],
    ],
    
### Redirecting to the payment system ###

To redirect user to PerfectMoney site you need to create the page with RedirectForm widget.
User will redirected right after page load.

    <?php echo \yiidreamteam\perfectmoney\RedirectForm::widget([
        'api' => Yii::$app->get('pm'),
        'invoiceId' => $invoice->id,
        'amount' => $invoice->amount,
        'description' => $invoice->description,
    ]); ?>

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
