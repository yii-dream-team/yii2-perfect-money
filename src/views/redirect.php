<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 *
 * @var \yii\web\View $this
 * @var \yiidreamteam\perfectmoney\Api $api
 * @var $invoiceId
 * @var $amount
 * @var $description
 */
?>
<div class="perfect-money-checkout">
    <p><?= \Yii::t('PerfectMoney', 'Now you will be redirected to the payment system.') ?></p>
    <form id="perfect-money-checkout-form" action="https://perfectmoney.is/api/step1.asp" method="POST">
        <input type="hidden" name="PAYEE_ACCOUNT" value="<?= $api->walletNumber ?>">
        <input type="hidden" name="PAYEE_NAME" value="<?= $api->merchantName ?>">
        <input type="hidden" name="PAYMENT_UNITS" value="<?= $api->walletCurrency ?>">
        <input type="hidden" name="STATUS_URL" value="<?= $api->resultUrl ?>">
        <input type="hidden" name="PAYMENT_URL" value="<?= $api->successUrl ?>">
        <input type="hidden" name="NOPAYMENT_URL" value="<?= $api->failureUrl ?>">
        <input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
        <input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
        <input type="hidden" name="PAYMENT_ID" value="<?= $invoiceId ?>">
        <input type="hidden" name="PAYMENT_AMOUNT" value="<?= $amount ?>">
        <input type="hidden" name="SUGGESTED_MEMO" value="<?= $description ?>">
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#perfect-money-checkout-form').submit();
    });
</script>
