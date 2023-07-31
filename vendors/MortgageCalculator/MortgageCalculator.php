<?php

class MortgageCalculator {

	private $interest_rate  = 5.0;
	private $years          = 30;
	private $loan_amt       = 200000;
	private $down_payment;

	private $observed_cost  = null;

	private $interests      = array();
	private $principals     = array();
	private $balances       = array();
	private $extra_payments = array();
	private $total_extra_payments = null;

	private $yearlyInterest  = array();
	private $yearlyPrincipal = array();
	private $yearlyExtra     = array();
	private $payments        = array();



	/*********** Setters ***********/
	public function setInterestRate( $interest ) {
		$this->interest_rate = $interest;
	}

	public function setYears( $years ) {
		$this->years = $years;
	}

	public function setLoanAmount( $amount ) {
        $amount = str_replace( "," , "" , $amount );
		$this->loan_amt = $amount;
	}

	public function setDownPayment( $amount , $type = 'dollars' ) {

        $amount = str_replace( "," , "" , $amount );

		if ( $type == 'dollars' ) {
			$this->down_payment = $amount;
		} else {
			$this->down_payment = $this->getLoanAmount() * ( $amount / 100 );
		}

	}

	public function addExtraPayment( $paymentNo , $amount ) {
        $amount = str_replace( "," , "" , $amount );
		$this->extra_payments[$paymentNo + 1] = $amount;
	}



	/*********** Simple Getters ***********/
	public function getInterestRate() {
		return $this->interest_rate;
	}

	public function getYears() {
		return $this->years;
	}

	public function getLoanAmount() {
		return $this->loan_amt;
	}

	public function getDownPayment() {
		return $this->down_payment;
	}

	public function getTotalExtraPayments() {
		if ( !empty( $this->total_extra_payments ) ) {
			return $this->total_extra_payments;
		}

		$total_extra_payments = 0;

		foreach ($this->extra_payments as $key => $value) {
			$floatedValue = floatval($value);
			$total_extra_payments += $floatedValue;
		}

		$this->total_extra_payments = $total_extra_payments;
		return $total_extra_payments;
	}



	/*********** Methods for Calculating Constant Values ***********/
	public function getMonthlyRate() {
		$monthly_rate = ( $this->getInterestRate() / 100 ) / 12;
		return $monthly_rate;
	}

	public function getNumberOfPayments() {
		$number_of_payments = $this->getYears() * 12;
		return $number_of_payments;
	}

	public function getInitialBalance() {
		$initBalance = $this->getLoanAmount() - $this->getDownPayment();
		return $initBalance;
	}

	public function getMonthlyPayment() {

        return ( ($this->getInitialBalance() * $this->getMonthlyRate()) / ( 1 - pow( (1 + $this->getMonthlyRate()) , (-1 * $this->getNumberOfPayments()) ) ) * 100) / 100;
	}



	/*********** Methods for Calculating 'Final Data' ***********/
	public function getMorgageCost() {
		$morgage_cost = $this->getMonthlyPayment() * $this->getNumberOfPayments();
		return $morgage_cost;
	}

	public function getObservedCost() {

		if( $this->observed_cost !== null )
			return $this->observed_cost;

		$i             = 1;
		$observed_cost = 0;
		do{
			$principal      = $this->getPrincipalPayment($i);
			$interest       = $this->getInterestPayment($i);
			$balance        = $this->getBalance($i - 1);
			$observed_cost += $principal + $interest;
			$i++;
		}while( $balance >= 0 );

		return ($this->observed_cost = $observed_cost );
	}

	public function getSavings() {
		$savings = $this->getMorgageCost() - $this->getObservedCost();
		return $savings;
	}



	/*********** Methods for Calculating Payments ***********/
	public function getInterestPayment($paymentNo){
		if ( !empty( $this->interests[$paymentNo] ) ) {
			return $this->interests[$paymentNo];
		}

		$interest = ( $this->getBalance( $paymentNo - 1 ) - $this->getExtraPayment( $paymentNo ) ) * $this->getMonthlyRate();

		$this->interests[$paymentNo] = $interest;
		return $interest;
	}

	public function getPrincipalPayment($paymentNo) {
		if ( !empty( $this->principals[$paymentNo] ) ) {
			return $this->principals[$paymentNo];
		}

		$principal = $this->getMonthlyPayment() - $this->getInterestPayment($paymentNo) + $this->getExtraPayment( $paymentNo );

		$this->principals[$paymentNo] = $principal;
		return $principal;
	}

	public function getExtraPayment($paymentNo) {
		return isset( $this->extra_payments[$paymentNo] ) ? $this->extra_payments[$paymentNo] : 0;
	}

	public function getBalance($paymentNo) {

		if ( !empty( $this->balances[$paymentNo] ) ) {
			return $this->balances[$paymentNo];
		}

		$balance = 0;

		if( $paymentNo <= 0 ){
			$balance = $this->balances[ 0 ] = $this->getInitialBalance();
		} else {
			$balance = $this->balances[ $paymentNo - 1] - $this->getExtraPayment( $paymentNo ) - $this->getPrincipalPayment( $paymentNo );
		}

		$this->balances[ $paymentNo ] = $balance;
		return $balance;
	}



	/*********** Methods for Calculating 'Yearly Data' ***********/
	public function isPaymentLastOfYear( $paymentNo  ) {
		return ( $paymentNo % 12 == 0 );
	}

	public function getPaymentYear( $paymentNo ) {
		return floor( $paymentNo / 12 );
	}

	public function getYearInterest( $yearNo ) {
		if ( !empty( $this->yearlyInterest[$yearNo] ) ) {
			return $this->yearlyInterest[$yearNo];
		}

		$minPaymentNo = ( $yearNo - 1 ) * 12;
		$maxPaymentNo = $yearNo * 12;
		$yearlyInterest = 0;

		for ( $c = ( 1 + $minPaymentNo ) ; $c <= $maxPaymentNo ; $c++ ) {
			$yearlyInterest += $this->getInterestPayment($c);
		}

		$this->yearlyInterest[$yearNo] = $yearlyInterest;
		return $yearlyInterest;
	}

	public function getYearPrincipal( $yearNo ) {
		if ( !empty( $this->yearlyPrincipal[$yearNo] ) ) {
			return $this->yearlyPrincipal[$yearNo];
		}

		$minPaymentNo = ( $yearNo - 1 ) * 12;
		$maxPaymentNo = $yearNo * 12;
		$yearlyPrincipal = 0;

		for ( $c = ( 1 + $minPaymentNo ) ; $c <= $maxPaymentNo ; $c++ ) {
			$yearlyPrincipal += $this->getPrincipalPayment($c);
		}

		$this->yearlyPrincipal[$yearNo] = $yearlyPrincipal;
		return $yearlyPrincipal;
	}

	function getYearExtraPayments( $yearNo ){
		if ( !empty( $this->yearlyExtra[$yearNo] ) ) {
			return $this->yearlyExtra[$yearNo];
		}

		$minPaymentNo = ( $yearNo - 1 ) * 12;
		$maxPaymentNo = $yearNo * 12;
		$yearlyExtra  = 0;

		for ( $c = ( 1 + $minPaymentNo ) ; $c <= $maxPaymentNo ; $c++ ) {
			$yearlyExtra += $this->getExtraPayment($c);
		}

		$this->yearlyExtra[$yearNo] = $yearlyExtra;
		return $yearlyExtra;
	}

	public function getYearTotalPayment( $yearNo ) {
		return $this->getYearInterest( $yearNo ) + $this->getYearPrincipal( $yearNo ) + $this->getYearExtraPayments($yearNo  );

	}

	/*********** Method for Calculating All Payments ***********/

	public function calculate( ) {
		if ( !empty( $this->payments ) ) {
			return $this->payments;
		}

		$noOfPayments = $this->getNumberOfPayments();
		for ( $c = 1; $c <= $noOfPayments; $c++ ) {
			// gets payment info
			$interest  = $this->getInterestPayment($c);
			$principal = $this->getPrincipalPayment($c);
			$extra     = $this->getExtraPayment($c);
			$balance   = $this->getBalance($c);

			$yearlyInterest  = null;
			$yearlyPrincipal = null;
			$yearlyBalance = null;

			// gets yearly data
			if ( $this->isPaymentLastOfYear($c) ) {
				$year            = $this->getPaymentYear($c);
				$yearlyInterest  = $this->getYearInterest($year);
				$yearlyPrincipal = $this->getYearPrincipal($year);
				$yearlyBalance   = $this->getYearTotalPayment($year);
			}
			if ($balance > 0 ||  $this->getBalance($c - 1) > 0 ) {
				$this->payments[] = compact('interest', 'principal', 'extra', 'balance', 'yearlyInterest', 'yearlyPrincipal', 'yearlyBalance');
			}
		}

		return $this->payments;
	}

}
?>
