<?php
class PrequalCalculator {

	private $salary;
	private $other_income;
	private $expense_autos = 0;
	private $expense_cards = 0;
	private $expense_loans = 0;
	private $years = 30;
	private $interest = 6.5;
	private $hcr = 0.31;
	private $dsr = 0.43;

	private $firstQualNo;
	private $secondQualNo;
	private $averageLoanQty;



	/*********** Setters ***********/
	public function setSalary( $salary ) {
		$this->salary = $salary;
	}

	public function setOtherIncome( $other_income ) {
		$this->other_income = $other_income;
	}

	public function setExpenseAutos( $expense_autos ) {
		$this->expense_autos = $expense_autos;
	}

	public function setExpenseCards( $expense_cards ) {
		$this->expense_cards = $expense_cards;
	}

	public function setExpenseLoans( $expense_loans ) {
		$this->expense_loans = $expense_loans;
	}

	public function setYears( $years ) {
		$this->years = $years;
	}

	public function setInterest( $interest ) {
		$this->interest = $interest;
	}



	/*********** Simple Getters ***********/
	public function getSalary() {
		return $this->salary;
	}

	public function getOtherIncome() {
		return $this->other_income;
	}

	public function getExpenseAutos() {
		return $this->expense_autos;
	}

	public function getExpenseCards() {
		return $this->expense_cards;
	}

	public function getExpenseLoans() {
		return $this->expense_loans;
	}

	public function getYears() {
		return $this->years;
	}

	public function getInterest() {
		return $this->interest;
	}

	public function getHcr () {
		return $this->hcr ;
	}

	public function getDsr () {
		return $this->dsr ;
	}



	/*********** Methods for Calculating Constant Values ***********/
	public function getTotalIncome() {
		$total_income = $this->getSalary() + $this->getOtherIncome();
		return $total_income;
	}

	public function getTotalDeductions() {
		$total_deductions = $this->getExpenseAutos() + $this->getExpenseCards() + $this->getExpenseLoans();
		return $total_deductions;
	}



	/*********** Methods for Calculating Results ***********/
	public function getFirstQualNo() {
		if ( !empty( $this->firstQualNo ) ) {
			return $this->firstQualNo;
		}

		$firstQualNo = ( $this->getTotalIncome() * $this->getHcr() ) / 12;

		$this->firstQualNo = $firstQualNo;
		return $firstQualNo;
	}

	public function getSecondQualNo() {
		if ( !empty( $this->secondQualNo ) ) {
			return $this->secondQualNo;
		}

		$secondQualNo = ( ( $this->getTotalIncome() * $this->getDsr() ) / 12 ) - $this->getTotalDeductions();

		$this->secondQualNo = $secondQualNo;
		return $secondQualNo;
	}

	public function getAverageLoanQty() {
		if ( !empty( $this->averageLoanQty ) ) {
			return $this->averageLoanQty;
		}

		// some precalculations
		$itu     = ( $this->getInterest() / 100) / 12;
		$termval = $this->getYears() * -12;
		$res     = pow( $itu + 1 , $termval );
		$fcalcr  = $itu / ( 1 - $res );

		// final calculations
		if ( $this->getFirstQualNo() <= $this->getSecondQualNo() && $this->getSecondQualNo() > 0 ) {
			$averageLoanQty = $this->getFirstQualNo() / $fcalcr;
		} elseif ( $this->getFirstQualNo() > $this->getSecondQualNo() && $this->getSecondQualNo() <= 0) {
			$averageLoanQty = $this->getFirstQualNo() / $fcalcr;
		} else {
			$averageLoanQty = $this->getSecondQualNo() / $fcalcr;
		}

		$this->averageLoanQty = $averageLoanQty;
		return $averageLoanQty;
	}



	/*********** Method for Calculating Everything Needed ***********/

	public function calculate( ) {
	}

}
?>