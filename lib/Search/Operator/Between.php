<?php

    class Search_Operator_Between extends Search_Operator
    {

        /* Returns the dql for that value */
        function compile( $field , $value )
        {

            $connection = Doctrine_Manager::connection();
            $values     = explode('!', $value);

            $from = $this->normalizeValue( $values[0] . '00:00:00' );
            $to   = $this->normalizeValue( $values[1] . '23:59:59' );

            $from = $connection->quote( $from );
            $to   = $connection->quote( $to );

            $dql = array();

            if ( $from )
                $dql[] = "$field >= $from";

            if ( $to )
                $dql[] = "$field <= $to";

            return implode(' AND ', $dql);
        }

        protected function normalizeValue( $value ){

            if( !strtotime($value) ) {
                return null;
            }
            else {

                $date = new DateTime($value, new DateTimeZone(Configure::read('datetime.timezone')));
                $date->setTimezone(new DateTimeZone('UTC'));

                return $date->format('Y-m-d H:i:s');
            }
        }

    }
