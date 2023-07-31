<?php

vendor('MortgageCalculator');

$loan_amt      = get('loan_amt', '200000');
$down_payment  = get('down_payment', '0');
$down_type     = get('down_type', 'dollars');
$years         = get('years', '30');
$interest_rate = get('interest_rate', '6.5');

$MortgageCalculator = new MortgageCalculator;
$MortgageCalculator->setLoanAmount( $loan_amt );
$MortgageCalculator->setDownPayment( $down_payment , $down_type );
$MortgageCalculator->setYears( $years );
$MortgageCalculator->setInterestRate( $interest_rate );

//Set values
Action::set( 'loan_amt'           , $loan_amt           );
Action::set( 'down_payment'       , $down_payment       );
Action::set( 'down_type'          , $down_type          );
Action::set( 'years'              , $years              );
Action::set( 'interest_rate'      , $interest_rate      );
Action::set( 'MortgageCalculator' , $MortgageCalculator );