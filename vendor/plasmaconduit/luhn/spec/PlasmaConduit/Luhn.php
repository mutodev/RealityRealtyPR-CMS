<?php
namespace spec\PlasmaConduit;
use PHPSpec2\ObjectBehavior;

class Luhn extends ObjectBehavior {

    function it_should_not_validate_an_invalid_luhn() {
        self::validate("1234864522")->shouldReturn(false);
    }

    function it_should_return_a_correct_check_digit_for_partial_luhn() {
        $number = "1234864522";
        $luhn   = $number . self::getCheckDigit($number)->getWrappedSubject();
        self::validate($luhn)->shouldReturn(true);
    }

    function it_should_validate_a_valid_american_express() {
        self::validate("378282246310005")->shouldReturn(true);
    }

    function it_should_validate_anotyer_valid_american_express() {
        self::validate("371449635398431")->shouldReturn(true);
    }

    function it_should_validate_a_valid_american_express_corporate() {
        self::validate("378734493671000")->shouldReturn(true);
    }

    function it_should_validate_a_valid_australian_bankcard() {
        self::validate("5610591081018250")->shouldReturn(true);
    }

    function it_should_validate_a_valid_diners_club() {
        self::validate("30569309025904")->shouldReturn(true);
    }

    function it_should_validate_another_valid_diners_club() {
        self::validate("38520000023237")->shouldReturn(true);
    }

    function it_should_validate_a_valid_discover_card() {
        self::validate("6011111111111117")->shouldReturn(true);
    }

    function it_should_validate_another_valid_discover_card() {
        self::validate("6011000990139424")->shouldReturn(true);
    }

    function it_should_validate_a_valid_jcb() {
        self::validate("3530111333300000")->shouldReturn(true);
    }

    function it_should_validate_another_valid_jcb() {
        self::validate("3566002020360505")->shouldReturn(true);
    }

    function it_should_validate_a_valid_mastercard() {
        self::validate("5555555555554444")->shouldReturn(true);
    }

    function it_should_validate_another_valid_mastercard() {
        self::validate("5105105105105100")->shouldReturn(true);
    }

    function it_should_validate_a_valid_visa() {
        self::validate("4111111111111111")->shouldReturn(true);
    }

    function it_should_validate_another_valid_visa() {
        self::validate("4012888888881881")->shouldReturn(true);
    }

    function it_should_validate_yet_another_valid_visa() {
        self::validate("4222222222222")->shouldReturn(true);
    }

}